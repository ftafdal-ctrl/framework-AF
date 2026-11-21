# Model Kerja Berbasis AI: Pembangunan Sistem Informasi Web Aman (PHP, MySQL, PWA)

Nov 21, 2026 · @afdal

## 1. Ringkasan dan Tujuan

Model kerja ini memakai AI sebagai asisten pengembang di setiap fase pembuatan sistem informasi web berbasis PHP dan MySQL, sementara manusia tetap memegang keputusan, review, dan persetujuan akhir. Hasilnya adalah aplikasi yang aman menurut standar web modern, berjalan sebagai PWA, dan siap dipublikasikan ke aplikasi mobile.

**Tujuan**

- Mempercepat analisis, perancangan, pengodean, pengujian, dan dokumentasi dengan bantuan AI.
- Menjadikan keamanan sebagai syarat bawaan (security by design), bukan tambahan di akhir.
- Menghasilkan satu basis kode web yang dapat dipasang di layar utama ponsel dan dikemas ke Google Play (dan App Store bila perlu).

**Prinsip dasar**

1. **Human-in-the-loop.** AI mengusulkan, manusia memeriksa dan memutuskan. Tidak ada kode AI yang masuk produksi tanpa review dan pengujian.
2. **Aman secara default.** Semua kode hasil AI dinilai terhadap daftar kontrol keamanan di bagian 4 sebelum diterima.
3. **Jejak yang dapat diaudit.** Prompt, keluaran AI, dan keputusan review disimpan di repositori bersama kode.
4. **Tidak ada rahasia di prompt.** Kata sandi, kunci API, dan data pengguna nyata tidak pernah ditempel ke alat AI.
5. **Iteratif dan terukur.** Setiap iterasi lolos gerbang kualitas (pengujian, pemindaian keamanan, review) sebelum lanjut.

---

## 2. Arsitektur dan Stack Acuan

Stack acuan adalah PHP 8.2 atau lebih baru dengan PDO, MySQL 8 (InnoDB, utf8mb4), Apache atau Nginx dengan HTTPS wajib, dan front-end PWA berbasis HTML, CSS, dan JavaScript. Framework (Laravel atau CodeIgniter 4) dianjurkan karena sudah menyediakan proteksi CSRF, hashing, dan query builder; PHP native tetap bisa dipakai jika mengikuti pola di bagian 4.

| Lapisan | Pilihan acuan | Catatan keamanan |
| --- | --- | --- |
| Web server | Nginx/Apache + TLS 1.2+ (Let's Encrypt) | HSTS, tutup port tak perlu, sembunyikan versi server |
| Aplikasi | PHP 8.2+, PDO, Composer | `display_errors=Off`, `expose_php=Off`, dependensi dipindai |
| Database | MySQL 8, InnoDB, utf8mb4 | Akun DB khusus aplikasi dengan hak minimum, bukan root |
| Autentikasi | `password_hash()` (Argon2id/bcrypt), sesi server | Cookie `HttpOnly`, `Secure`, `SameSite` |
| Front-end PWA | `manifest.webmanifest`, service worker, HTTPS | Tidak menyimpan data sensitif di cache atau localStorage |
| API | Endpoint JSON di bawah `/api` | Validasi Origin, token CSRF, pembatasan laju |
| Mobile | TWA (Bubblewrap) atau Capacitor | Digital Asset Links, penandatanganan rilis |
| Pelindung tepi | Cloudflare atau WAF setara | Mitigasi DDoS, aturan bot, cache statis |

**Struktur folder yang disarankan**

```text
project/
  public/            # satu-satunya folder yang dapat diakses web
    index.php
    manifest.webmanifest
    sw.js
    assets/ icons/
  app/
    Controllers/ Models/ Views/ Middleware/
    Security/        # csrf, origin, rate limit, reauth, upload
  config/            # .env di luar public, tidak masuk Git
  storage/           # log, upload privat, di luar webroot
  tests/
  composer.json
```

Semua kode, konfigurasi, dan unggahan berada di luar `public/`, sehingga kebocoran konfigurasi dan eksekusi berkas unggahan dapat dicegah sejak struktur.

---

## 3. Alur Kerja Berbasis AI per Fase

Model ini terdiri dari tujuh fase berurutan. Di tiap fase AI mengerjakan draf, manusia memeriksa, lalu gerbang kualitas menentukan apakah fase boleh dilanjutkan.

| Fase | Tugas AI | Tugas manusia | Artefak | Gerbang kualitas |
| --- | --- | --- | --- | --- |
| 1. Analisis kebutuhan | Menyusun daftar kebutuhan, user story, aktor, dan kasus penyalahgunaan (abuse case) dari deskripsi sistem | Memvalidasi dengan pemangku kepentingan, menetapkan prioritas | SRS, user story, threat list awal | Kebutuhan disetujui, risiko keamanan teridentifikasi |
| 2. Perancangan | Mengusulkan ERD, skema MySQL, alur layar, kontrak API, dan threat model (STRIDE) | Memilih desain, memeriksa normalisasi dan hak akses | ERD, DDL SQL, wireframe, threat model | Setiap ancaman punya kontrol pencegah |
| 3. Implementasi | Menghasilkan kode PHP, query PDO, middleware keamanan, dan komponen PWA | Review baris demi baris, menjalankan kode, menolak pola tidak aman | Kode di Git, pesan commit | Lolos linter dan review sejawat |
| 4. Pengujian | Membuat unit test, test kasus serangan (SQLi, XSS, CSRF), dan data uji | Menjalankan tes, menilai cakupan | Suite tes, laporan cakupan | Semua tes lulus, tidak ada temuan kritis |
| 5. Audit keamanan | Meninjau kode terhadap OWASP Top 10 dan checklist bagian 6, menyarankan perbaikan | Memverifikasi temuan, memperbaiki, uji penetrasi manual | Laporan temuan, hasil SAST/DAST | Tidak ada celah tinggi atau kritis terbuka |
| 6. Rilis | Menyusun skrip deploy, konfigurasi server, dan catatan rilis | Menyetujui rilis, mengelola kunci dan rahasia | Paket rilis, runbook, rencana rollback | Checklist pra-rilis lengkap |
| 7. Operasi dan pemeliharaan | Meringkas log, mengusulkan perbaikan dan pembaruan dependensi | Memantau, menanggapi insiden, memutuskan patch | Log, laporan insiden, changelog | Patch keamanan terpasang sesuai SLA |

**Template prompt yang direkomendasikan**

Prompt yang baik memuat peran, konteks, batasan keamanan, dan format keluaran. Contoh untuk fase implementasi:

```text
Peran: pengembang PHP 8.2 senior yang memahami keamanan aplikasi web.
Konteks: sistem informasi [nama], MySQL via PDO, tanpa framework.
Tugas: buat modul login dan manajemen sesi.
Batasan wajib: prepared statement, password_hash Argon2id,
  cookie HttpOnly+Secure+SameSite=Strict, token CSRF, regenerasi ID sesi,
  pembatasan percobaan login, tanpa menampilkan error internal.
Keluaran: kode per berkas, penjelasan singkat tiap kontrol keamanan,
  dan daftar kasus uji serangan untuk modul ini.
```

**Aturan penggunaan AI dalam tim**

- Simpan prompt penting dan hasilnya di folder `docs/ai-log/` pada repositori.
- Minta AI menjelaskan alasan setiap kontrol keamanan, lalu verifikasi sendiri ke dokumentasi resmi (PHP, MySQL, OWASP).
- Anggap kode AI sebagai kode pihak ketiga: wajib dipindai dan diuji.
- Jangan menempelkan data pribadi, kredensial, atau kunci ke alat AI.

---

## 4. Standar Keamanan Wajib

Setiap kode yang dihasilkan AI harus memenuhi kontrol berikut. Contoh kode adalah pola acuan dalam PHP; sesuaikan dengan struktur proyek dan tetap jalankan review serta pengujian.

| Ancaman | Kontrol utama |
| --- | --- |
| SQL Injection | Prepared statement PDO, tanpa emulasi, validasi tipe, akun DB hak minimum |
| XSS | Output encoding dengan `htmlspecialchars`, Content-Security-Policy, cookie HttpOnly |
| CSRF | Token acak per sesi pada setiap request pengubah data, dibandingkan dengan `hash_equals` |
| SameSite Cookie | `SameSite=Strict` (atau `Lax` bila perlu tautan masuk), `Secure`, `HttpOnly` |
| Origin/Referer | Tolak request POST/PUT/DELETE yang Origin atau Referer-nya bukan domain sendiri |
| Re-autentikasi | Minta kata sandi ulang sebelum aksi sensitif (ganti sandi, hapus akun, ekspor data) |
| Malware | Validasi unggahan (MIME, ekstensi, ukuran), simpan di luar webroot, pindai antivirus |
| DDoS | CDN/WAF, pembatasan laju, batas ukuran request, cache, timeout |
| Phishing | HTTPS+HSTS, SPF/DKIM/DMARC, MFA, pesan tautan aman, deteksi login mencurigakan |

### 4.1 SQL Injection

Semua query memakai prepared statement. Input pengguna tidak pernah digabung ke string SQL.

```php
$pdo = new PDO(
  'mysql:host=localhost;dbname=app;charset=utf8mb4',
  $user, $pass,
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]
);

$stmt = $pdo->prepare('SELECT id, nama FROM pengguna WHERE email = :email');
$stmt->execute([':email' => $email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
```

Nama tabel atau kolom dinamis (misalnya untuk pengurutan) hanya boleh dipilih dari daftar putih (whitelist) di kode, tidak pernah dari input mentah.

### 4.2 Cross-Site Scripting (XSS)

Setiap data yang ditampilkan di HTML di-encode sesuai konteksnya, dan CSP membatasi sumber skrip.

```php
function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
// di view: <p><?= e($komentar) ?></p>

header("Content-Security-Policy: default-src 'self'; script-src 'self'; "
     . "style-src 'self'; img-src 'self' data:; frame-ancestors 'none'; "
     . "base-uri 'self'; form-action 'self'");
```

Hindari `innerHTML` di JavaScript untuk data pengguna; gunakan `textContent`. Bila HTML dari pengguna memang diperlukan (editor teks kaya), bersihkan dengan pustaka sanitizer seperti HTMLPurifier.

### 4.3 Cross-Site Request Forgery (CSRF)

```php
session_start();
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// di form: <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
// untuk fetch/API: kirim di header X-CSRF-Token

function verify_csrf(): void {
  $t = $_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
  if (!hash_equals($_SESSION['csrf'] ?? '', $t)) {
    http_response_code(403);
    exit('Permintaan tidak valid');
  }
}
```

Semua request yang mengubah data wajib memakai metode POST/PUT/PATCH/DELETE (bukan GET) dan memanggil `verify_csrf()`. Perubahan state lewat GET dilarang.

### 4.4 Atribut SameSite Cookie

Cookie sesi diatur sebelum `session_start()`:

```php
session_set_cookie_params([
  'lifetime' => 0,
  'path'     => '/',
  'secure'   => true,       // hanya lewat HTTPS
  'httponly' => true,       // tidak terbaca JavaScript
  'samesite' => 'Strict',   // atau 'Lax' bila ada alur tautan dari luar
]);
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_start();
```

`Strict` paling aman tetapi cookie tidak terkirim saat pengguna datang dari tautan eksternal; gunakan `Lax` bila itu mengganggu pengalaman login. Jangan memakai `None` kecuali benar-benar perlu lintas situs, dan dalam kasus itu wajib `Secure`.

### 4.5 Validasi Header Origin/Referer

Lapisan tambahan di samping token CSRF. Untuk request pengubah data, Origin (atau Referer bila Origin tidak ada) harus cocok dengan domain aplikasi.

```php
function verify_origin(array $allowed): void {
  if (in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD', 'OPTIONS'], true)) return;

  $origin = $_SERVER['HTTP_ORIGIN'] ?? null;
  if ($origin === null && !empty($_SERVER['HTTP_REFERER'])) {
    $p = parse_url($_SERVER['HTTP_REFERER']);
    $origin = ($p['scheme'] ?? '') . '://' . ($p['host'] ?? '')
            . (isset($p['port']) ? ':' . $p['port'] : '');
  }
  if ($origin === null || !in_array($origin, $allowed, true)) {
    http_response_code(403);
    exit('Origin tidak diizinkan');
  }
}

verify_origin(['https://contoh.ac.id']);
```

Perbandingan harus persis (skema, host, port), bukan sekadar `strpos`, agar tidak dikelabui domain seperti `contoh.ac.id.penyerang.com`.

### 4.6 Re-autentikasi (Konfirmasi Kata Sandi)

Aksi sensitif meminta kata sandi ulang, lalu memberi jendela waktu singkat (misalnya 5 menit).

```php
function require_reauth(PDO $pdo, int $maxAge = 300): void {
  if (($_SESSION['reauth_at'] ?? 0) >= time() - $maxAge) return;
  header('Location: /konfirmasi-sandi?next=' . rawurlencode($_SERVER['REQUEST_URI']));
  exit;
}

// pada form konfirmasi (POST, dilindungi CSRF + pembatasan percobaan):
$stmt = $pdo->prepare('SELECT password_hash FROM pengguna WHERE id = :id');
$stmt->execute([':id' => $_SESSION['uid']]);
$hash = $stmt->fetchColumn();
if (password_verify($_POST['password'] ?? '', $hash)) {
  $_SESSION['reauth_at'] = time();
  // arahkan ke halaman tujuan yang divalidasi (hanya path internal)
}
```

Aksi yang wajib re-autentikasi: ganti kata sandi atau email, hapus akun, ubah hak akses, ekspor data massal, dan transaksi bernilai tinggi. Tautan `next` hanya boleh berupa path internal agar tidak menjadi open redirect.

### 4.7 Malware dan Unggahan Berkas

- Periksa ukuran, ekstensi daftar putih, dan MIME sebenarnya dengan `finfo_file()`, bukan `$_FILES['type']`.
- Ganti nama berkas dengan nama acak, simpan di luar webroot, dan sajikan lewat skrip yang mengatur `Content-Disposition` dan `X-Content-Type-Options: nosniff`.
- Jangan pernah mengeksekusi atau meng-`include` berkas unggahan; nonaktifkan eksekusi PHP di folder upload.
- Pindai dengan ClamAV (`clamdscan`) sebelum berkas diterima.
- Server: perbarui OS dan PHP, gunakan akun layanan terpisah, nonaktifkan fungsi berbahaya (`exec`, `shell_exec`, `system`, `passthru`) bila tidak dipakai, dan pantau integritas berkas.
- Dependensi: jalankan `composer audit` dan kunci versi lewat `composer.lock`.

```php
$f = $_FILES['berkas'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($f['tmp_name']);
$ok = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 2*1024*1024 || !isset($ok[$mime])) {
  http_response_code(422); exit('Berkas ditolak');
}
$nama = bin2hex(random_bytes(16)) . '.' . $ok[$mime];
move_uploaded_file($f['tmp_name'], __DIR__ . '/../storage/upload/' . $nama);
```

### 4.8 DDoS

DDoS volumetrik tidak dapat dihentikan oleh kode PHP saja; pertahanan berlapis diperlukan.

1. **Tepi jaringan:** pasang CDN/WAF (misalnya Cloudflare) dengan proteksi DDoS, aturan bot, dan mode "under attack" untuk keadaan darurat.
2. **Web server:** atur `limit_req` dan `limit_conn` di Nginx, batasi ukuran body, dan turunkan timeout koneksi lambat (slowloris).
3. **Aplikasi:** pembatasan laju per IP dan per akun pada login, pendaftaran, reset sandi, dan API; cache halaman publik; antrean untuk tugas berat.
4. **Infrastruktur:** pantau metrik, siapkan autoscaling atau kapasitas cadangan, dan tulis runbook respons insiden.

```nginx
limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
location = /login {
  limit_req zone=login burst=5 nodelay;
  client_max_body_size 1m;
}
```

### 4.9 Phishing

Aplikasi tidak bisa mencegah penyerang membuat situs tiruan, tetapi dapat mempersulit dan mengurangi dampaknya.

- HTTPS dengan HSTS (`Strict-Transport-Security: max-age=31536000; includeSubDomains`) dan domain tunggal yang konsisten.
- Autentikasi multi-faktor (TOTP) dan notifikasi login dari perangkat atau lokasi baru.
- Email resmi dengan SPF, DKIM, dan DMARC; tautan reset sandi sekali pakai, kedaluwarsa singkat, dan tidak membocorkan keberadaan akun.
- Tidak pernah meminta kata sandi lewat email atau chat; sampaikan aturan ini di halaman bantuan.
- Cegah penyematan di situs lain dengan `frame-ancestors 'none'` (anti-clickjacking) dan lindungi dari open redirect.
- Edukasi pengguna dan sediakan kanal pelaporan situs tiruan.

### 4.10 Kerentanan Lain yang Wajib Ditangani

| Kerentanan | Kontrol |
| --- | --- |
| Brute force dan credential stuffing | Pembatasan percobaan, penundaan progresif, CAPTCHA/MFA, `password_hash` Argon2id |
| Session fixation dan hijacking | `session_regenerate_id(true)` setelah login, `use_strict_mode`, timeout idle dan absolut |
| Broken access control / IDOR | Otorisasi di server pada setiap objek (cek kepemilikan), RBAC, tolak secara default |
| Clickjacking | `frame-ancestors 'none'` atau `X-Frame-Options: DENY` |
| Kebocoran informasi | `display_errors=Off`, log di server, pesan error generik, sembunyikan versi |
| Konfigurasi dan rahasia | `.env` di luar webroot dan di luar Git, rotasi kunci, tanpa kredensial di kode |
| Open redirect dan SSRF | Daftar putih tujuan redirect dan host untuk request keluar |
| Deserialisasi tidak aman | Hindari `unserialize()` pada data pengguna, pakai JSON |
| Dependensi rentan | `composer audit`, pembaruan rutin, SCA di CI |
| Header keamanan | `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `Permissions-Policy`, CSP, HSTS |
| Logging dan deteksi | Catat login gagal dan aksi sensitif tanpa data rahasia, peringatan anomali |
| Backup dan pemulihan | Backup terenkripsi terjadwal, uji pemulihan berkala |

```php
// setelah login berhasil
session_regenerate_id(true);
$_SESSION['uid'] = $user['id'];
$_SESSION['login_at'] = time();

// hash kata sandi
$hash = password_hash($password, PASSWORD_ARGON2ID);
```

---

## 5. PWA dan Publikasi ke Aplikasi Mobile

Sistem dibangun sebagai PWA terlebih dahulu: satu basis kode yang dapat dipasang dari browser, bekerja offline untuk halaman statis, lalu dibungkus menjadi aplikasi Android (dan iOS bila diperlukan).

### 5.1 Syarat PWA

1. Seluruh situs dilayani lewat **HTTPS**.
2. Berkas **manifest** dengan nama, ikon, warna, dan `display: standalone`.
3. **Service worker** yang terdaftar dan menangani cache serta halaman offline.
4. Desain **responsif** (`<meta name="viewport">`) dan skor Lighthouse PWA yang lulus.

```json
{
  "name": "Sistem Informasi Contoh",
  "short_name": "SI Contoh",
  "start_url": "/?source=pwa",
  "scope": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#0b5fff",
  "icons": [
    { "src": "/icons/icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/icon-512.png", "sizes": "512x512", "type": "image/png" },
    { "src": "/icons/maskable-512.png", "sizes": "512x512", "type": "image/png" "purpose": "maskable" }
  ]
}
```

```javascript
// sw.js: cache hanya aset statis, bukan data pribadi
const CACHE = 'si-v1';
const ASSETS = ['/offline.html', '/assets/app.css', '/assets/app.js'];

self.addEventListener('install', e =>
  e.waitUntil(caches.open(CACHE).then(c => c.addAll(ASSETS))));

self.addEventListener('activate', e =>
  e.waitUntil(caches.keys().then(keys =>
    Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k))))));

self.addEventListener('fetch', e => {
  const req = e.request;
  if (req.method !== 'GET') return;              // jangan sentuh POST
  if (req.mode === 'navigate') {                 // halaman: jaringan dulu
    e.respondWith(fetch(req).catch(() => caches.match('/offline.html')));
    return;
  }
  if (new URL(req.url).pathname.startsWith('/assets/')) {
    e.respondWith(caches.match(req).then(r => r || fetch(req)));
  }
});
```

```html
<link rel="manifest" href="/manifest.webmanifest">
<meta name="theme-color" content="#0b5fff">
<script>
  if ('serviceWorker' in navigator) navigator.serviceWorker.register('/sw.js');
</script>
```

### 5.2 Keamanan Khusus PWA

- Jangan men-cache respons API yang berisi data pribadi atau halaman setelah login; gunakan strategi jaringan dulu atau tanpa cache.
- Hapus cache dan data lokal saat logout.
- Service worker harus dilayani dari origin yang sama dengan `Content-Type: text/javascript` dan tidak boleh di-cache lama (`Cache-Control: no-cache` untuk `sw.js`).
- Token CSRF, SameSite, dan validasi Origin tetap berlaku pada request dari PWA terpasang.
- Hindari menyimpan token atau kata sandi di `localStorage`; andalkan cookie sesi HttpOnly.

### 5.3 Jalur Publikasi ke Mobile

| Jalur | Cocok untuk | Langkah utama | Catatan |
| --- | --- | --- | --- |
| TWA (Trusted Web Activity) lewat Bubblewrap/PWABuilder | Android, Google Play | Siapkan PWA lulus Lighthouse, jalankan `bubblewrap init` dan `build`, hasilkan AAB, unggah ke Play Console | Wajib `assetlinks.json` di `/.well-known/` agar tanpa bilah alamat |
| Capacitor | Android dan iOS, butuh API perangkat | Tambahkan Capacitor ke proyek web, `npx cap add android/ios`, build di Android Studio atau Xcode | Mendukung kamera, push, biometrik lewat plugin |
| PWA terpasang | Distribusi cepat tanpa toko | Pengguna memilih "Tambahkan ke layar utama" | Tanpa review toko, fitur iOS lebih terbatas |

**Langkah publikasi Android (TWA)**

1. Pastikan domain produksi berjalan HTTPS, manifest dan service worker valid, dan skor Lighthouse PWA lulus.
2. Buat proyek dengan Bubblewrap atau PWABuilder, isi nama paket (misalnya `id.ac.contoh.si`) dan ikon.
3. Buat keystore penandatangan, simpan aman, dan catat sidik jari SHA-256.
4. Unggah `assetlinks.json` berisi sidik jari tersebut ke `https://domain/.well-known/assetlinks.json`.
5. Build AAB, uji di perangkat nyata, lalu unggah ke Google Play Console (track pengujian internal dahulu).
6. Lengkapi kebijakan privasi, formulir keamanan data, dan konten toko, lalu ajukan review.

Untuk iOS, bungkus dengan Capacitor lalu publikasikan lewat akun Apple Developer; perhatikan bahwa Apple menolak aplikasi yang hanya membungkus situs tanpa nilai tambah, sehingga tambahkan fitur native seperti push notification atau biometrik. Persyaratan toko (target API level, kebijakan data) berubah berkala, jadi periksa dokumentasi resmi Google Play dan Apple sebelum rilis.

---

## 6. Checklist Verifikasi, Pengujian, dan DevSecOps

Sistem dinyatakan siap rilis hanya jika seluruh butir berikut terpenuhi dan terdokumentasi.

**Checklist keamanan aplikasi**

- [ ] Semua query memakai prepared statement PDO; tidak ada konkatenasi input ke SQL.
- [ ] Semua output di-encode; CSP aktif dan diuji; tidak ada `innerHTML` dengan data pengguna.
- [ ] Token CSRF terpasang di semua form dan request AJAX pengubah data; metode GET tidak mengubah state.
- [ ] Cookie sesi: `Secure`, `HttpOnly`, `SameSite` Strict/Lax; ID sesi diregenerasi setelah login.
- [ ] Validasi Origin/Referer aktif untuk semua request pengubah data.
- [ ] Re-autentikasi diwajibkan pada aksi sensitif dan dibatasi jendela waktunya.
- [ ] Unggahan divalidasi, disimpan di luar webroot, dan dipindai antivirus.
- [ ] Pembatasan laju aktif di login, reset sandi, pendaftaran, dan API.
- [ ] Otorisasi diperiksa di server untuk setiap objek (uji IDOR lolos).
- [ ] Header keamanan lengkap dan skor pemindaian header baik.
- [ ] Tidak ada kredensial di repositori; `.env` di luar webroot; error tidak tampil ke pengguna.

**Checklist infrastruktur dan PWA**

- [ ] HTTPS + HSTS, TLS modern, sertifikat diperbarui otomatis.
- [ ] CDN/WAF aktif, backup terenkripsi terjadwal, pemulihan sudah diuji.
- [ ] Lighthouse PWA lulus; cache tidak memuat data pribadi; logout menghapus cache lokal.
- [ ] `assetlinks.json` benar; keystore tersimpan aman; build diuji di perangkat nyata.

**Pengujian dan otomasi (DevSecOps)**

| Tahap | Alat contoh | Tujuan |
| --- | --- | --- |
| Lint dan analisis statis | PHPStan/Psalm, PHP\_CodeSniffer, Semgrep | Temukan pola tidak aman sejak commit |
| Audit dependensi | `composer audit`, Dependabot | Deteksi pustaka rentan |
| Tes otomatis | PHPUnit, tes kasus serangan | Cegah regresi keamanan |
| Pemindaian dinamis | OWASP ZAP | Uji aplikasi berjalan terhadap OWASP Top 10 |
| Pemindaian rahasia | gitleaks | Cegah kunci bocor ke Git |
| Uji penetrasi manual | Burp Suite, checklist OWASP ASVS | Verifikasi akhir sebelum rilis |

Jalankan tahap-tahap ini di pipeline CI sehingga rilis otomatis gagal jika ada temuan tinggi atau kritis.

**Batasan penggunaan AI**

- AI dapat menghasilkan kode yang tampak benar tetapi tidak aman atau memakai API yang sudah usang; verifikasi ke dokumentasi resmi.
- Tidak ada sistem yang bebas celah. Standar ini menurunkan risiko, bukan menghilangkannya; pemantauan dan pembaruan berkelanjutan tetap diperlukan.
- Pertahanan DDoS bergantung pada layanan dan kapasitas di luar kode aplikasi.
- Persyaratan toko aplikasi dan versi pustaka berubah; periksa sumber resmi saat implementasi.

**Rujukan:** OWASP Top 10, OWASP ASVS, OWASP Cheat Sheet Series (CSRF, Session Management, File Upload), dokumentasi resmi PHP, MySQL, web.dev PWA, dan dokumentasi Google Play Console.

---

## 7. Skill Pendukung Model Kerja

Empat skill AI berikut menjalankan fase-fase pada bagian 3. Tiap skill adalah instruksi tetap yang dimuat saat tugas terkait dikerjakan, sehingga keluaran AI konsisten, aman, dan dapat diaudit. Keluaran satu skill menjadi masukan skill berikutnya.

| Skill | Fase yang dilayani | Masukan | Keluaran |
| --- | --- | --- | --- |
| Analis sistem | 1. Analisis kebutuhan | Deskripsi masalah, wawancara, proses saat ini | SRS, user story, use case, abuse case |
| Database MySQL | 2. Perancangan | SRS | ERD, `schema.sql`, kamus data, indeks |
| UI/UX PWA | 2. Perancangan | SRS, persona | Alur pengguna, wireframe, design token, komponen |
| Pengecekan keamanan | 5. Audit keamanan (dan review fase 3 dan 4) | Kode, konfigurasi server, endpoint | Laporan temuan dan checklist perbaikan |

Alur antarskill: **Analis sistem → Database dan UI/UX → implementasi → Pengecekan keamanan**.

### 7.1 Skill Analis Sistem

**Nama:** `analis-sistem-php`. **Kapan dipakai:** menganalisis kebutuhan dan merancang sistem informasi web PHP/MySQL.

**Langkah kerja**

1. **Klarifikasi konteks.** Tanyakan tujuan sistem, pengguna dan perannya, proses saat ini, data yang dikelola, serta batasan (waktu, anggaran, hosting, regulasi), maksimal 5 pertanyaan sekaligus. Asumsi yang belum terjawab ditulis eksplisit.
2. **Aktor dan peran.** Tabel aktor, deskripsi, dan hak akses tingkat tinggi.
3. **Kebutuhan fungsional.** User story berformat "Sebagai \<aktor>, saya ingin \<aksi> agar \<manfaat>", ber-ID (KF-01, ...) dan berprioritas MoSCoW.
4. **Kebutuhan non-fungsional.** Wajib memuat keamanan (autentikasi, otorisasi, CSRF, XSS, SQLi, re-autentikasi, rate limit), kinerja, ketersediaan, PWA/mobile, aksesibilitas, dan backup.
5. **Use case dan alur.** Use case ringkas (prasyarat, alur utama, alur alternatif, pascakondisi) dan alur proses sebagai diagram atau daftar bernomor.
6. **Abuse case dan threat model (STRIDE).** Per fitur sensitif: ancaman, dampak, kontrol pencegah, lalu jadikan kontrol itu kebutuhan non-fungsional.
7. **Ruang lingkup dan kriteria penerimaan** yang dapat diuji untuk setiap kebutuhan utama.

**Format keluaran:** SRS singkat (Ringkasan, Aktor, Kebutuhan Fungsional, Kebutuhan Non-Fungsional, Use Case, Abuse Case, Asumsi dan Risiko, Kriteria Penerimaan) dalam bahasa Indonesia baku.

**Aturan:** jangan mengarang kebutuhan; tandai asumsi dan minta konfirmasi; setiap kebutuhan harus dapat diuji dan punya ID; serahkan hasil ke skill database dan UI/UX tanpa merancang skema atau layar secara detail di tahap ini.

### 7.2 Skill Database MySQL

**Nama:** `perancang-database-mysql`. **Kapan dipakai:** merancang atau meninjau database MySQL.

**Langkah kerja**

1. **Baca kebutuhan** dari SRS: entitas, atribut, relasi, aturan bisnis.
2. **Model konseptual:** daftar entitas dan relasi (1:1, 1:N, N:M) sebagai ERD.
3. **Normalisasi** minimal 3NF; denormalisasi hanya disengaja dan dijelaskan alasannya.
4. **DDL MySQL 8:** InnoDB, `utf8mb4`, primary key `BIGINT UNSIGNED AUTO_INCREMENT`, foreign key dengan `ON DELETE`/`ON UPDATE` yang disengaja, `NOT NULL` secara default, `created_at` dan `updated_at`, `DECIMAL` untuk uang, `UNIQUE` untuk email/kode, soft delete bila data perlu diaudit.
5. **Indeks** berdasarkan pola query nyata (WHERE, JOIN, ORDER BY) dengan alasan tertulis; verifikasi dengan `EXPLAIN`.
6. **Keamanan data:** kata sandi hanya berupa hasil `password_hash`; pertimbangkan enkripsi kolom sensitif; tidak menyimpan data kartu pembayaran; akun DB aplikasi berhak minimum (bukan root) dan akun migrasi terpisah; tabel `audit_log` untuk aksi sensitif.
7. **Akses dari PHP:** PDO dengan prepared statement, `ERRMODE_EXCEPTION`, `EMULATE_PREPARES=false`, transaksi untuk operasi multilangkah, nama tabel/kolom dinamis hanya dari daftar putih.
8. **Migrasi, seed, dan backup:** skrip migrasi berurutan, data seed non-sensitif, backup terenkripsi, uji pemulihan.

**Format keluaran:** ERD, berkas `schema.sql`, kamus data (tabel, kolom, tipe, keterangan), daftar indeks beserta alasan, contoh query PDO.

**Aturan:** input pengguna tidak pernah digabung ke string SQL; tidak ada data pribadi nyata pada data contoh; asumsi dikonfirmasi ke analis sistem.

### 7.3 Skill UI/UX Web dan PWA

**Nama:** `perancang-ui-ux-pwa`. **Kapan dipakai:** merancang antarmuka web atau PWA yang responsif, mudah digunakan, dan siap dipasang di ponsel.

**Langkah kerja**

1. **Pahami pengguna:** persona (peran, tujuan, perangkat, kemampuan teknis) dan tugas utama dari SRS.
2. **Alur pengguna dan arsitektur informasi:** peta alur tugas utama dan struktur navigasi (maksimal 5 item utama di mobile).
3. **Wireframe** tiap layar kunci: tata letak, komponen, status (kosong, memuat, galat, sukses), aksi utama. Mulai dari mobile-first.
4. **Sistem desain ringkas:** token warna (kontras WCAG AA), tipografi, jarak, radius, dan komponen (tombol, form, tabel, kartu, modal, toast).
5. **Prinsip usability:** hierarki visual jelas, satu aksi utama per layar, umpan balik instan, validasi inline yang membantu, target sentuh minimal 44x44 px.
6. **Aksesibilitas WCAG 2.1 AA:** kontras, label form, urutan fokus keyboard, teks alternatif, tidak hanya mengandalkan warna.
7. **Spesifik PWA:** splash dan ikon (termasuk maskable), mode `standalone`, halaman offline yang informatif, indikator koneksi, panduan "pasang aplikasi", performa ringan (skeleton, lazy load).
8. **UX fitur keamanan:** pesan galat login yang generik, MFA, konfirmasi kata sandi sebelum aksi sensitif (dengan alasan yang jelas), sesi kedaluwarsa yang ramah, peringatan aksi destruktif, indikator kekuatan kata sandi.
9. **Serah terima:** HTML/CSS contoh yang bersih (tanpa `innerHTML` untuk data pengguna), daftar komponen, catatan interaksi.

**Format keluaran:** persona singkat, peta alur, deskripsi atau wireframe per layar, tabel design token, checklist aksesibilitas, kode contoh komponen bila diminta.

**Aturan:** desain orisinal, tanpa menyalin desain berhak cipta atau merek; setiap keputusan dikaitkan dengan kebutuhan pengguna atau SRS; sarankan uji kegunaan sederhana dengan sekitar 5 pengguna.

### 7.4 Skill Pengecekan Keamanan

**Nama:** `pengecekan-keamanan-web`. **Kapan dipakai:** meninjau atau mengaudit keamanan aplikasi web PHP/MySQL/PWA milik sendiri atau yang diizinkan untuk diuji.

**Langkah kerja**

1. **Tentukan ruang lingkup:** kode, konfigurasi server, atau daftar endpoint. Jangan meminta kredensial atau data nyata.
2. **Tinjau per kategori** dan beri status Lulus, Gagal, atau Tidak Berlaku untuk setiap butir:

| Kategori | Butir yang diperiksa |
| --- | --- |
| SQL Injection | Prepared statement PDO, tanpa konkatenasi input, nama dinamis dari daftar putih, akun DB hak minimum |
| XSS | Output di-encode, CSP aktif, tidak ada `innerHTML`/`eval` dengan data pengguna |
| CSRF | Token per sesi di semua request pengubah data, `hash_equals`, GET tidak mengubah state |
| SameSite Cookie | `Secure`, `HttpOnly`, `SameSite` Strict/Lax, `use_strict_mode` |
| Origin/Referer | Validasi persis terhadap daftar putih untuk request pengubah data |
| Re-autentikasi | Kata sandi ulang untuk aksi sensitif, jendela waktu singkat, redirect hanya ke path internal |
| Autentikasi dan sesi | Argon2id/bcrypt, regenerasi ID sesi, timeout, pembatasan percobaan login, MFA |
| Otorisasi | Cek kepemilikan objek (IDOR), RBAC, tolak secara default |
| Malware dan unggahan | Validasi MIME dengan `finfo`, nama acak, di luar webroot, eksekusi PHP dimatikan, pemindaian antivirus |
| DDoS | CDN/WAF, `limit_req`/`limit_conn`, batas ukuran body, rate limit |
| Phishing | HTTPS+HSTS, SPF/DKIM/DMARC, tautan reset sekali pakai, `frame-ancestors`, tanpa open redirect |
| Konfigurasi | `display_errors=Off`, header keamanan, rahasia di luar webroot dan Git |
| Dependensi dan PWA | `composer audit`, service worker tidak men-cache data pribadi, cache dibersihkan saat logout |

3. **Klasifikasikan temuan:** tingkat keparahan (Kritis, Tinggi, Sedang, Rendah), lokasi (berkas:baris), bukti singkat, dampak, dan rekomendasi perbaikan dengan contoh kode aman.
4. **Rencana verifikasi ulang:** PHPStan/Semgrep, `composer audit`, gitleaks, OWASP ZAP, dan uji manual terarah.

**Format keluaran:** ringkasan eksekutif, tabel temuan (ID, kategori, keparahan, lokasi, perbaikan), daftar butir yang lulus, dan checklist tindak lanjut berurutan menurut prioritas.

**Aturan:** tidak membuat eksploit siap pakai atau kode berbahaya, cukup menjelaskan celah dan perbaikannya; hanya melaporkan yang terbukti dari bahan yang diberikan dan menandai bagian yang tidak dapat diverifikasi; mengingatkan bahwa tidak ada sistem yang bebas celah dan uji penetrasi manual tetap diperlukan sebelum rilis.

### 7.5 Cara Memakai Skill dalam Satu Proyek

1. Mulai dengan skill **analis sistem**: berikan deskripsi masalah, dapatkan SRS dan abuse case.
2. Berikan SRS ke skill **database** dan **UI/UX** (keduanya dapat dikerjakan paralel); simpan ERD, `schema.sql`, wireframe, dan design token di repositori.
3. Implementasikan dengan template prompt bagian 3 dan kontrol keamanan bagian 4; lakukan review manusia pada setiap berkas.
4. Jalankan skill **pengecekan keamanan** pada kode dan konfigurasi, perbaiki temuan, lalu ulangi sampai tidak ada temuan Tinggi atau Kritis.
5. Lanjutkan ke checklist bagian 6 dan publikasi PWA/mobile (bagian 5).
