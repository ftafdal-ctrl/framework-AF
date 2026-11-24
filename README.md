# Framework-AF

[![DevSecOps CI Pipeline](https://github.com/ftafdal-ctrl/framework-AF/actions/workflows/ci.yml/badge.svg)](https://github.com/ftafdal-ctrl/framework-AF/actions/workflows/ci.yml)
![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![MySQL 8](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![PWA Ready](https://img.shields.io/badge/PWA-Ready-5A0FC8?logo=pwa&logoColor=white)
![License MIT](https://img.shields.io/badge/license-MIT-green)

> **Production-Hardened AI Agent Governance & Execution-Driven Engineering**  
> *Standar Rekayasa Perangkat Lunak Aman Berbasis PHP 8.2+, MySQL 8, PWA, dan DevSecOps.*

Framework-AF adalah model kerja dan tata kelola rekayasa perangkat lunak yang dirancang khusus untuk memandu kolaborasi antara pengembang manusia dan AI Agent (Gemini, Antigravity, Cursor, Cline, dll.) agar **fokus penuh pada penyelesaian software development** secara aman dan modular.

---

## 🎯 4 Pursuit Goals (Sasaran Utama)

Setiap pengerjaan modul dalam Framework-AF wajib mengacu pada 4 sasaran:
1. **Target Fungsional:** Menyelesaikan fitur per modul utuh (*Vertical Slice*).
2. **Target Keamanan:** Nol celah keamanan kritis/tinggi (OWASP Top 10) & bebas kebocoran kredensial (*Zero Secret Leak*).
3. **Target Kualitas:** Lulus pemeriksaan deterministik 100% (PHP Syntax Lint, Gitleaks, PHPStan level 6+, PHPUnit).
4. **Target PWA & Mobile:** Kompatibel mobile-first, skor Lighthouse PWA $\ge 90$, Service Worker aman, dan siap rilis TWA/Capacitor.

---

## 🛡️ Mekanisme Penegakan Kepatuhan (4-Layer Hard Enforcement)

Framework-AF tidak hanya mengandalkan instruksi teks, melainkan membatasi pelanggaran secara sistematis:

```
[Developer / AI Agent Menulis Kode]
               │
               ▼
┌──────────────────────────────────────────────┐
│ LAPIS 1: Contextual Guardrail (AGENTS.md)    │ ➔ AI terikat sejak awal prompt
└──────────────────────┬───────────────────────┘
                       │ (Kode dibuat)
                       ▼
┌──────────────────────────────────────────────┐
│ LAPIS 2: Local Pre-Commit Gate (Git Hooks)   │ ➔ Blokir commit jika sintaks rusak / secret bocor
└──────────────────────┬───────────────────────┘
                       │ (Commit berhasil, Push)
                       ▼
┌──────────────────────────────────────────────┐
│ LAPIS 3: Remote CI/CD Hard Gate (Pipeline)   │ ➔ Blokir merge jika tes/analisis statis gagal
└──────────────────────┬───────────────────────┘
                       │ (CI/CD Hijau 100%)
                       ▼
┌──────────────────────────────────────────────┐
│ LAPIS 4: Dual-Agent Review & Human Sign-off  │ ➔ Auditor independen & persetujuan pengembang
└──────────────────────┬───────────────────────┘
                       │
                       ▼
             [FITUR SELESAI (DONE)]
```

---

## 📂 Struktur Repositori

```text
├── .github/
│   └── workflows/
│       └── ci.yml               # Pipeline GitHub Actions (Lint, Gitleaks, PHPStan, Test)
├── .githooks/
│   └── pre-commit               # Git hook lokal (PHP Lint, Gitleaks, PHPStan)
├── docs/
│   ├── model-kerja.md           # Dokumentasi teknis & standar keamanan lengkap
│   └── PURSUIT_GOALS.md         # Matriks pelacakan kemajuan fitur
├── AGENTS.md                    # Aturan wajib bagi AI Agent (Lapis 1)
├── phpstan.neon                 # Konfigurasi analisis statis PHPStan
└── .gitignore                   # Proteksi berkas privat & kredensial
```

---

## 🚀 Panduan Memulai Cepat (Quick Start)

### 1. Inisialisasi & Konfigurasi Git Hook Lokal
Jalankan perintah berikut di terminal repositori Anda untuk mengaktifkan gerbang pre-commit lokal:

```bash
# Aktifkan path githooks bawaan repositori
git config core.hooksPath .githooks

# Berikan hak eksekusi pada hook (untuk Linux / macOS / Git Bash)
chmod +x .githooks/pre-commit
```

### 2. Alur Pengerjaan Fitur (Vertical Slice)
1. Tentukan modul pada [docs/PURSUIT_GOALS.md](docs/PURSUIT_GOALS.md).
2. Gunakan AI dengan pedoman di [AGENTS.md](AGENTS.md) untuk membuat backend, antarmuka PWA, dan pengujian unit.
3. Jalankan `git commit`. Hook lokal akan otomatis memvalidasi kebersihan kode.
4. Buat Pull Request. Pipeline GitHub Actions akan menguji modul sebelum di-merge.

---

## 📖 Dokumentasi Lengkap
Pelajari arsitektur dan kontrol keamanan secara mendalam pada:
- 📘 [docs/model-kerja.md](docs/model-kerja.md) (Dokumentasi Model Kerja & Standar Keamanan PHP, MySQL, PWA)
- 🎯 [docs/PURSUIT_GOALS.md](docs/PURSUIT_GOALS.md) (Matriks Sasaran Capaian & Pelacakan Modul)
- 🤖 [AGENTS.md](AGENTS.md) (Instruksi Mutlak untuk AI Coding Agents)
