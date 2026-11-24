# Matriks Pelacak Sasaran Capaian (Pursuit Goals Matrix)

Dokumen ini digunakan oleh tim rekayasa dan AI Agent untuk melacak progress penyelesaian perangkat lunak secara modular. Setiap fitur harus memiliki korelasi langsung ke sasaran fungsional dan keamanan.

---

## 1. Status Capaian Global (Global Pursuit Goals)

| Sasaran (Goal) | Target Indikator | Status Saat Ini | Catatan Verifikasi |
| :--- | :--- | :---: | :--- |
| **G-01: Fungsionalitas Modul** | 100% User Story selesai dan dapat diuji | 🟡 Dalam Pengerjaan | Sesuai backlog prioritas |
| **G-02: Zero Vulnerability** | 0 Celah Kritis / Tinggi (OWASP Top 10) | 🟢 Terjaga | SAST & Review aktif |
| **G-03: Zero Secret Leak** | 0 Kredensial / Token bocor ke Git | 🟢 Terjaga | Gitleaks aktif di pre-commit |
| **G-04: Deterministic Quality** | PHP Lint 100%, PHPStan Lv 6, PHPUnit 100% | 🟢 Lulus Terverifikasi | Pipeline CI/CD GitHub Actions lulus 100% |
| **G-05: PWA & Mobile Ready** | Lighthouse PWA ≥ 90, Offline Fallback | 🟡 Dalam Pengerjaan | Service Worker terpasang |

---

## 2. Pelacakan Fitur per Modul (Vertical Slice Tracking)

| ID Modul | Nama Modul / Fitur | Ruang Lingkup (Scope) | Deterministic Gate | Dual-Agent Review | Status DoD | Tanggal Selesai |
| :--- | :--- | :--- | :---: | :---: | :---: | :---: |
| **MOD-01** | Fondasi & Otentikasi | Login, CSRF, Session, Argon2id | [ ] Pass | [ ] Approved | 🟡 WIP | - |
| **MOD-02** | Manajemen Pengguna | CRUD User, RBAC, Profil | [ ] Pass | [ ] Approved | ⚪ Pending | - |
| **MOD-03** | [Fitur Bisnis 1] | [Jelaskan aksi CRUD/Logika] | [ ] Pass | [ ] Approved | ⚪ Pending | - |
| **MOD-04** | [Fitur Bisnis 2] | [Jelaskan aksi CRUD/Logika] | [ ] Pass | [ ] Approved | ⚪ Pending | - |
| **MOD-05** | Service Worker PWA | Offline cache, Manifest, AAB TWA | [ ] Pass | [ ] Approved | ⚪ Pending | - |

---

## 3. Catatan Kendali Perubahan & Anti-Scope Creep
Setiap permintaan penambahan fitur di luar daftar di atas harus dinilai terhadap dampak rilis:
- Apakah fitur ini wajib untuk rilis awal (*Must-Have*)? Jika tidak, tunda ke versi berikutnya.
- Dilarang merekayasa ulang komponen yang sudah berstatus **Done** kecuali ditemukan celah keamanan kritis.
