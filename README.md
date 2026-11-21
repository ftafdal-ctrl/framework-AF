# Framework AF: Model Kerja Berbasis AI

Repositori ini memuat panduan, standar keamanan, dan model kerja berbasis AI untuk pembangunan sistem informasi web modern (PHP 8.2+, MySQL 8, PWA) yang aman dan siap dipublikasikan ke aplikasi mobile.

## Dokumentasi Utama

Dokumen lengkap model kerja dapat dibaca di:
- 📖 [Model Kerja Berbasis AI: Pembangunan Sistem Informasi Web Aman (PHP, MySQL, PWA)](docs/model-kerja.md)

## Struktur Folder Repositori

```text
framework-AF/
├── docs/
│   └── model-kerja.md     # Panduan lengkap model kerja & standar keamanan
└── README.md              # Ringkasan repositori
```

## Inti Model Kerja

1. **Human-in-the-loop**: AI mengusulkan, manusia meninjau, menguji, dan memutuskan.
2. **Security by Design**: Memenuhi standar OWASP, prepared statement PDO, proteksi CSRF, cookie hardening, sanitasi file upload, dan CSP.
3. **PWA & Mobile Ready**: Satu basis kode web yang dapat dipasang di layar utama dan dikemas ke Google Play via TWA (Bubblewrap) atau Capacitor.
4. **4 Skill Pendukung**:
   - `analis-sistem-php` (Fase Kebutuhan)
   - `perancang-database-mysql` (Fase Desain Database)
   - `perancang-ui-ux-pwa` (Fase Desain Tampilan & Interaksi)
   - `pengecekan-keamanan-web` (Fase Audit & Review Keamanan)
