# Model Kerja Berbasis AI: Software Development Execution-Driven
## Production-Hardened Agentic Engineering (PHP 8.2+, MySQL 8, PWA, DevSecOps)

**Status:** FINAL (Ready for Production Project)  
**Versi:** 2.1 (Final Production Edition)  
**Klasifikasi:** Pedoman Rekayasa & Tata Kelola Pengembangan Sistem Informasi  
**Penyusun:** Tim Rekayasa Perangkat Lunak & Sistem Keamanan  

---

## 1. Filosofi Kerja & Pursuit Goals (Sasaran Capaian)

Model kerja ini dirancang untuk memastikan seluruh proses rekayasa perangkat lunak berorientasi pada **penyelesaian produk (delivery-first)** dengan standar keamanan industri perbankan/pemerintahan (*security by design*).

```
   ┌─────────────────────────────────────────────────────────────┐
   │                    PURSUIT GOALS MATRIX                     │
   ├──────────────────────────────┬──────────────────────────────┤
   │ 1. TARGET FUNGSIONAL         │ 2. TARGET KEAMANAN           │
   │    • 100% Fitur Berjalan     │    • Zero Vulnerability      │
   │    • Zero Dead Code          │    • Zero Secret Leak        │
   ├──────────────────────────────┼──────────────────────────────┤
   │ 3. TARGET KUALITAS TEKNIS    │ 4. TARGET PWA & MOBILE       │
   │    • Deterministic Gate 100% │    • Lighthouse PWA ≥ 90     │
   │    • Test Coverage Terarah   │    • TWA / Mobile Verified   │
   └──────────────────────────────┴──────────────────────────────┘
```

### 1.1 Definisi Pursuit Goals (Sasaran Utama)
Setiap iterasi, tugas, dan prompt AI **hanya boleh dijalankan jika berkontribusi langsung pada 4 sasaran utama berikut**:

1. **Target Fungsional (Delivery Goal):**
   * Perangkat lunak selesai per modul secara vertikal (*Vertical Slice*).
   * Modul yang selesai langsung dapat diuji oleh pengguna nyata (*working software*).
   * Dilarang melakukan *gold-plating* (menambah fitur di luar ruang lingkup yang disepakati).
2. **Target Keamanan (Zero Vulnerabilities Goal):**
   * Nol celah keamanan berkategori *Critical* dan *High* (OWASP Top 10).
   * Nol kebocoran rahasia (*zero hardcoded credentials/API keys*).
   * Seluruh pertahanan keamanan (Anti-SQLi, CSRF, XSS, IDOR, SameSite) aktif secara default.
3. **Target Kualitas Teknis (Deterministic Code Quality Goal):**
   * Lulus uji sintaks PHP 8.2 (`php -l`) 100%.
   * Analisis statis PHPStan minimal Level 6 tanpa galat (*zero errors*).
   * Seluruh unit test dan security test lulus 100% di pipeline CI/CD.
4. **Target PWA & Aksesibilitas Mobile (Readiness Goal):**
   * Skor Google Lighthouse kategori PWA $\ge 90$.
   * Aset statis tersimpan aman di Service Worker; halaman dinamis memiliki fallback offline informatif.
   * Siap dipublikasikan ke Google Play Store via Trusted Web Activity (TWA) atau Capacitor.

---

## 2. Arsitektur Penegakan Kepatuhan (4-Layer Enforcement Mechanism)

Untuk memastikan model kerja ini **dipatuhi secara mutlak** oleh pengembang manusia maupun agen AI, diterapkan mekanisme penegakan 4 lapis (4-Layer Hard Enforcement):

```
                      [Pengembang / AI Agent Menulis Kode]
                                        │
                                        ▼
   ┌─────────────────────────────────────────────────────────────────────────┐
   │ LAPIS 1: CONTEXTUAL GUARDRAIL (Prompt & Agent Rules)                   │
   │ • File AGENTS.md / .geminirules mengikat AI sejak prompt pertama.       │
   │ • AI menolak instruksi yang melanggar standar keamanan & arsitektur.    │
   └────────────────────────────────────┬────────────────────────────────────┘
                                        │ (Kode dibuat)
                                        ▼
   ┌─────────────────────────────────────────────────────────────────────────┐
   │ LAPIS 2: LOCAL PRE-COMMIT GATE (Git Hooks)                             │
   │ • File .githooks/pre-commit otomatis berjalan saat `git commit`.        │
   │ • Menjalankan: Gitleaks, PHP Lint, PHPStan.                             │
   │ • BLOKIR COMMIT jika ditemukan rahasia atau kesalahan sintaks/tipe.     │
   └────────────────────────────────────┬────────────────────────────────────┘
                                        │ (Commit berhasil, Push ke remote)
                                        ▼
   ┌─────────────────────────────────────────────────────────────────────────┐
   │ LAPIS 3: REMOTE CI/CD HARD GATE (GitHub Actions / GitLab CI)           │
   │ • Pipeline otomatis berjalan di server saat Pull Request / Push.        │
   │ • Menjalankan audit dependensi, pemindaian keamanan, dan PHPUnit.       │
   │ • BLOKIR MERGE jika ada tes yang gagal atau temuan audit kritis.        │
   └────────────────────────────────────┬────────────────────────────────────┘
                                        │ (Pipeline hijau 100%)
                                        ▼
   ┌─────────────────────────────────────────────────────────────────────────┐
   │ LAPIS 4: DUAL-AGENT AUDIT & HUMAN SIGN-OFF                              │
   │ • Inspector Agent melakukan review independen (OWASP Top 10).           │
   │ • Pengembang Manusia memverifikasi modul & menandatangani rilis.        │
   └────────────────────────────────────┬────────────────────────────────────┘
                                        │
                                        ▼
                            [FITUR DINYATAKAN SELESAI]
```

### Penjelasan Rinci Setiap Lapis Penegakan:

#### Lapis 1: Contextual Guardrail (`AGENTS.md`)
Berkas `AGENTS.md` ditempatkan di akar repositori proyek. Berkas ini bertindak sebagai instruksi sistem tetap yang dibaca oleh agen AI (Antigravity, Gemini CLI, Cursor, Claude Code, dll). Agen diwajibkan menolak membuat query konkatenasi mentah, menolak hardcoded secret, dan selalu menyertakan unit test.

#### Lapis 2: Local Pre-Commit Gate (Git Hooks)
Dipasang pada `.githooks/pre-commit` di komputer pengembang. Saat perintah `git commit` dijalankan, hook otomatis mengeksekusi:
1. `gitleaks protect --staged` $\rightarrow$ Jika ada token/password, commit otomatis dibatalkan.
2. `php -l` pada semua file PHP yang di-stage $\rightarrow$ Jika ada kesalahan sintaks, commit dibatalkan.
3. `phpstan analyse` $\rightarrow$ Jika ada pelanggaran tipe data, commit dibatalkan.

#### Lapis 3: Remote CI/CD Hard Gate (Pipeline)
Pada repositori pusat (GitHub/GitLab), branch `main` dan `develop` dilindungi (*Protected Branch*). Tidak ada seorang pun yang dapat melakukan *merge* tanpa melewati seluruh langkah di `.github/workflows/ci.yml`.

#### Lapis 4: Dual-Agent Audit & Human Sign-off
Agen pembuat kode (*Builder*) tidak diizinkan menyetujui kodenya sendiri. Agen auditor (*Inspector*) memberikan catatan independen. Manusia tetap menjadi pengambil keputusan akhir (*Human-in-the-Loop*).

---

## 3. Struktur Proyek Acuan (Clean & Isolated)

```text
project/
  .github/
    workflows/
      ci.yml                 # Pipeline penegak CI/CD otomatis
  .githooks/
    pre-commit               # Skrip git hook pencegah commit tidak aman
  docs/
    PURSUIT_GOALS.md         # Matriks pelacak sasaran & modul
    ai-audit-log.md          # Log keputusan penting & verifikasi keamanan
  public/                    # SATU-SATUNYA direktori yang terekspos ke internet
    index.php                # Front controller utama
    manifest.webmanifest     # Konfigurasi PWA
    sw.js                    # Service Worker PWA (Cache statis & offline fallback)
    offline.html             # Halaman fallback saat koneksi terputus
    assets/                  # CSS, JS, gambar, ikon PWA
  app/
    Core/                    # Engine dasar: Database, Router, Request, Response
    Middleware/              # CsrfMiddleware, AuthMiddleware, RateLimit, OriginVerify
    Modules/                 # VERTICAL SLICES (Modul Fungsional Utuh)
      Auth/                  # Modul Autentikasi
      Pengguna/              # Modul Manajemen Akun
      [NamaModul]/           # Modul fitur bisnis (Model, Controller, Views)
  config/                    # Konfigurasi aplikasi
    database.php
    security.php
  storage/                   # DI LUAR WEBROOT
    logs/                    # Log sistem dan audit trail
    uploads/                 # Berkas unggahan privat
  tests/
    Unit/                    # Tes unit fungsionalitas logika
    Security/                # Tes serangan (SQLi, CSRF, IDOR)
  .env.example               # Template environment (.env asli di luar webroot & Git)
  AGENTS.md                  # Aturan wajib bagi AI Agent (Lapis 1)
  composer.json
  phpstan.neon               # Konfigurasi level analisis statis PHPStan
```

---

## 4. Alur Kerja Harian Berbasis Fitur (Daily Vertical Slice Loop)

Pengembang dan AI mengikuti alur kerja terstruktur per modul untuk menjaga fokus:

```
[Mulai Fitur]
     │
     ▼
1. Tentukan Ruang Lingkup Fitur (Kaitkan dengan PURSUIT_GOALS.md)
     │
     ▼
2. Scaffold Data & Logic (Builder Agent)
   - Buat skrip migrasi MySQL DDL
   - Buat Model PDO Strict
   - Buat Controller & View Responsif (PWA)
     │
     ▼
3. Buat Unit & Security Tests (PHPUnit)
   - Uji alur positif
   - Uji alur negatif & eksploitasi (IDOR, input berbahaya)
     │
     ▼
4. Eksekusi Gerbang Lokal (Local Gate):
   $ git add .
   $ git commit -m "feat(modul): implementasi modul [nama]"
   -> Hook pre-commit otomatis memeriksa Gitleaks, PHP Lint, PHPStan
     │
     ▼
5. Ajukan Pull Request -> CI/CD Remote Pipeline berjalan otomatis
     │
     ▼
6. Dual-Agent Review (Inspector Agent memverifikasi OWASP Checklist)
     │
     ▼
7. Human Verification & Merge ke Branch Utama
     │
     ▼
[Tandai Fitur Selesai di PURSUIT_GOALS.md]
```

---

## 5. Standar Rekayasa & Keamanan Kode Produksi

Setiap kode yang dihasilkan harus mengikuti standar baku berikut:

### 5.1 Standar Database & Query PDO (Anti-SQLi)
```php
// app/Core/Database.php
namespace App\Core;

use PDO;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $db   = getenv('DB_NAME') ?: 'app_db';
            $user = getenv('DB_USER') ?: 'app_user';
            $pass = getenv('DB_PASS') ?: '';

            $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // Mencegah emulasi query
            ]);
        }
        return self::$instance;
    }
}
```

### 5.2 Helper Sanitasi Output (Anti-XSS)
```php
// app/Core/helpers.php
if (!function_exists('e')) {
    function e(?string $str): string {
        return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
```

### 5.3 Verifikasi CSRF & Header Origin (Double Defense)
```php
// app/Middleware/SecurityMiddleware.php
namespace App\Middleware;

class SecurityMiddleware {
    public static function enforceCsrfAndOrigin(array $allowedOrigins): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return;
        }

        // 1. Verifikasi Origin / Referer
        $origin = $_SERVER['HTTP_ORIGIN'] ?? null;
        if ($origin === null && !empty($_SERVER['HTTP_REFERER'])) {
            $parsed = parse_url($_SERVER['HTTP_REFERER']);
            $origin = ($parsed['scheme'] ?? '') . '://' . ($parsed['host'] ?? '') . (isset($parsed['port']) ? ':' . $parsed['port'] : '');
        }

        if ($origin === null || !in_array($origin, $allowedOrigins, true)) {
            http_response_code(403);
            exit(json_encode(['error' => 'Origin tidak diizinkan.']));
        }

        // 2. Verifikasi Token CSRF
        $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            exit(json_encode(['error' => 'Token CSRF tidak valid atau kedaluwarsa.']));
        }
    }
}
```

### 5.4 Proteksi Hak Akses Objek (Anti-IDOR)
```php
// Setiap mutasi/bacaan data privat wajib menyertakan identitas pemilik
public function getDetailData(int $id, int $authUserId): ?array {
    $stmt = $this->pdo->prepare('SELECT * FROM transaksi WHERE id = :id AND user_id = :user_id');
    $stmt->execute([':id' => $id, ':user_id' => $authUserId]);
    $data = $stmt->fetch();
    return $data ?: null;
}
```

---

## 6. Protokol Kesiapan Progressive Web App (PWA)

1. **Service Worker Caching Rule:**
   * Aset statis (CSS, JS, Ikon) $\rightarrow$ *Cache-First*.
   * Navigasi halaman $\rightarrow$ *Network-First* dengan fallback ke `/offline.html`.
   * **Data sensitif, respons API berotentikasi, dan halaman privat DILARANG disimpan di CacheStorage.**
2. **Pembersihan Sesi & Cache:**
   * Saat pengguna melakukan logout, Service Worker mengosongkan cache aplikasi lokal yang berkaitan dengan sesi aktif.
3. **Penyajian Aman:**
   * File `sw.js` disajikan dengan header `Cache-Control: no-cache, no-store, must-revalidate` agar browser selalu memperbarui logika Service Worker.

---

## 7. Definition of Done (DoD) - Kriteria Selesai Modul

Sebuah modul dinyatakan **SELESAI** jika dan hanya jika seluruh kotak centang berikut terpenuhi:

```markdown
- [ ] 1. Fungsionalitas & Sasaran:
      - [ ] Semua acceptance criteria user story terpenuhi.
      - [ ] Terdaftar dan ditandai selesai di docs/PURSUIT_GOALS.md.
- [ ] 2. Lapis Penegakan Kualitas (Deterministic Gates):
      - [ ] PHP Syntax Lint (`php -l`) lulus 100%.
      - [ ] Pemindaian Gitleaks menyatakan 0 kebocoran kredensial/rahasia.
      - [ ] PHPStan Level 6+ lulus tanpa pesan galat.
- [ ] 3. Keamanan Kode (OWASP Standards):
      - [ ] Query basis data memakai PDO prepared statements (Anti-SQLi).
      - [ ] Output HTML di-escape dengan e() (Anti-XSS).
      - [ ] Proteksi CSRF & Origin aktif pada request mutasi (POST/PUT/DELETE).
      - [ ] Otorisasi kepemilikan data divalidasi di server (Anti-IDOR).
- [ ] 4. Pengujian Otomatis (PHPUnit):
      - [ ] Berkas tes unit untuk alur bisnis dibuat dan lulus 100%.
      - [ ] Terdapat pengujian keamanan untuk penanganan input ilegal.
- [ ] 5. Responsivitas Tampilan & PWA:
      - [ ] Tampilan responsif di layar mobile (360px) dan desktop.
      - [ ] Bebas dari error pada JavaScript console.
      - [ ] Service Worker berfungsi normal saat simulasi offline.
- [ ] 6. Persetujuan Manusia:
      - [ ] Ditinjau dan disetujui oleh Lead Developer / Pengembang Manusia.
```

---

## 8. Protokol Penanganan Deviasi & Self-Correction

Jika AI Agent atau pengembang menghasilkan kode yang melanggar standar:

1. **Kegagalan di Hook Pre-commit:**
   * Pengembang/AI membaca pesan error di terminal.
   * AI diminta secara spesifik: *"Perbaiki galat [sebutkan error sintaks/tipe/gitleaks] pada berkas [nama berkas] tanpa mengubah logika bisnis yang sudah benar."*
2. **Kegagalan di CI/CD Pipeline:**
   * PR otomatis dikunci dari status merge.
   * Perbaikan dilakukan di branch fitur, di-commit ulang, hingga seluruh checks di GitHub Actions berwarna hijau.
3. **Peringatan Anti-Distraksi:**
   * Jika AI mulai mengajukan saran perancangan di luar modul yang sedang dikerjakan (*scope creep*), pengembang wajib menghentikan dan mengembalikan fokus AI dengan perintah: *"Fokus hanya pada penyelesaian implementasi modul ini sesuai acceptance criteria."*
