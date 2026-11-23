# INSTRUKSI SISTEM WAJIB UNTUK SEMUA AI AGENT (AGENTS.MD)

Dokumen ini adalah instruksi operasional mengikat untuk seluruh AI Agent (Antigravity, Gemini CLI, Cursor, Cline, Roo Code, Copilot, dll) yang bekerja di dalam repositori ini.

---

## 1. Misi Utama & Pursuit Goals
Setiap tindakan, pembuatan kode, atau refactoring yang Anda lakukan **HARUS FOKUS PENUH PADA PENYELESAIAN SOFTWARE DEVELOPMENT** dan mengacu pada 4 Pursuit Goals:
1. **Target Fungsional:** Menyelesaikan fitur per modul (Vertical Slice) hingga siap digunakan. Dilarang menambah fitur di luar kesepakatan (*no gold-plating*).
2. **Target Keamanan:** Nol celah kritis/tinggi (*Zero Critical Vulnerabilities*).
3. **Target Kualitas:** Lolos pemeriksaan deterministik: PHP Lint, Gitleaks, PHPStan level 6+, dan PHPUnit.
4. **Target PWA:** Kompatibel mobile-first, Service Worker aman (hanya cache aset statis), dan siap PWA.

---

## 2. Aturan Mutlak Penulisan Kode (Non-Negotiable Guardrails)

### 2.1 Akses Basis Data (Database)
- **WAJIB:** Menggunakan PHP PDO dengan Prepared Statements murni.
- **DILARANG:** Melakukan penggabungan string (concatenation) pada query SQL.
- **WAJIB:** Menolak nama kolom atau tabel dinamis dari input pengguna kecuali dicocokkan dengan daftar putih (*whitelist*).

### 2.2 Keamanan Input & Output
- **WAJIB:** Menggunakan fungsi `e()` (`htmlspecialchars(..., ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`) pada setiap pencetakan variabel di file View/HTML.
- **DILARANG:** Menggunakan `innerHTML` atau `eval()` di JavaScript yang memproses data pengguna.
- **WAJIB:** Menyertakan verifikasi token CSRF dan pemeriksaan header Origin pada setiap endpoint dengan metode `POST`, `PUT`, `PATCH`, dan `DELETE`.

### 2.3 Hak Akses Objek (Anti-IDOR)
- **WAJIB:** Memeriksa kepemilikan objek pada setiap manipulasi data (`WHERE id = :id AND user_id = :auth_user_id`). Dilarang berasumsi bahwa pengguna yang sudah login berhak mengakses data milik pengguna lain.

### 2.4 Kredensial & Rahasia (Anti-Gitleaks)
- **DILARANG KERAS:** Menuliskan password, token, string koneksi database, atau kunci API langsung di dalam kode (*hardcoded credentials*). Seluruh konfigurasi sensitif harus dibaca dari environment variable (`.env`).

### 2.5 Standar PWA
- **DILARANG:** Menyimpan data pribadi atau token pengguna ke dalam Service Worker `caches.open()` atau `localStorage`.
- **WAJIB:** Menyediakan fallback ke `/offline.html` untuk request halaman yang terputus koneksi.

---

## 3. Protokol Setiap Selesai Menulis Kode
Setiap kali Anda selesai membuat atau memodifikasi modul:
1. Pastikan kode lulus sintaks `php -l`.
2. Sediakan unit test PHPUnit di direktori `tests/` yang menguji alur sukses dan alur gagal/eksploitasi.
3. Laporkan kepatuhan terhadap checklist *Definition of Done (DoD)*.
