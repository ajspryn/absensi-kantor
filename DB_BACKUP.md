# 📚 Dokumentasi Fitur Backup & Restore Database (Standalone)

Fitur ini dirancang sangat mandiri (_standalone_), mudah digunakan, dan **tidak bergantung pada package Laravel** (seperti Spatie Laravel Backup), sehingga sangat mudah di-porting/diimplementasikan ke aplikasi atau framework PHP/Node.js/Python lain yang menggunakan file environment standar (`.env`).

## 🌟 Pendekatan Utama (Konsep)

Aplikasi ini memanfaatkan Shell Script (`.sh`) native yang dikombinasikan dengan utilitas bawaan `mysqldump` dan `mysql` command-line tools.
Script ini dirancang dinamis dengan membaca root credentials dari file `.env` Laravel dan melakukan kompresi data secara _stream/pipe_ menggunakan `gzip`.

## 📂 Struktur File

Komponen utama berada di dalam folder proyek/scripts:

1. `db_backup.sh`: Script untuk membuat file dumper (terkompresi `.sql.gz`).
2. `db_restore.sh`: Script untuk mengembalikan (restore) database dari file tercadangkan.

---

## ⚙️ Bagaimana Cara Kerjanya?

### 1. Script Backup (`db_backup.sh`)

- **Membaca `.env` secara dinamis:** Mengambil variabel `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_HOST` melalui regex `grep`. Tidak perlu hardcode sandi/kredensial ke script, sehingga 100% aman disetor ke Git.
- **Cross-platform Support:** Mencari binari alat `mysqldump` (misal dari `/opt/homebrew/bin/mysqldump` di macOS atau `/usr/bin/` di Linux Server).
- **Dump & Compress Efisien:** Menjalankan ekstrak memakai `mysqldump` yang dikawinkan dengan query tuning (`--single-transaction`, `--quick`, `--skip-lock-tables`) supaya tidak menimbulkan lock pada database production yang sedang berjalan, kemudian output stream langsung dikompres pipeline ke ZIP lewat perintah `gzip > backup_file.sql.gz`.
- **Fitur "Clean-Up" (Retensi):** Terdapat siklus pembersihan menggunakan program Unix `find`. File `.sql.gz` dideteksi parameter modifikasinya; apabila sudah berumur melebihi `RETENTION_DAYS=14`, ia akan dihapus. Menjaga disk Cloud/VPS agar tidak _over-capacity_.

### 2. Script Restore (`db_restore.sh`)

- **Konfirmasi Anti-human-error:** Sebelum proses restore terjadi, script memblokir sementara aktivitas dan menanyakan konfirmasi `[y/N]`. Hal ini menghindari admin "kepeleset tekan Enter" yang dapat menghancurkan dataset production yang sempurna.
- **Wipe Table Utuh:** Bila terkonfirmasi, database bersangkutan akan di-DROP IF EXISTS via script, dan lalu di-CREATE ulang untuk memberikan _blank canvas_ database yang segar.
- **Ekstrak & Import Cerdas:** Terdiri dari detektor ekstensi string:
    - Jika format `.gz` atau terkompres $\rightarrow$ diproses melalui pipeline RAM (`gunzip -c | mysql ...`).
    - Jika format `.sql` reguler $\rightarrow$ diproses file biasa.

---

## 🚀 Cara Mengimplementasi/Porting di Aplikasi Eksternal

Bila Anda berniat mendeploy / mendesain sistem Backup sejenis ini ke berbagai project Anda lain, instruksinya sebagai barikut:

1. **Jiplak Scripts:** Download / copy seluruh folder (berisi `db_backup.sh` dan `db_restore.sh`) ke repo project baru Anda.
2. **Kesesuaian `.env`:** Pastikan pada root struktur web / system terdapat file balutan environment `.env` konvensional. Parameter utamanya adalah:
    - `DB_CONNECTION=mysql`
    - `DB_HOST=127.0.0.1` (Atau IP Server)
    - `DB_PORT=3306`
    - `DB_DATABASE=nama_dbnya`
    - `DB_USERNAME=root`
    - `DB_PASSWORD=xxxx`
3. **Set Eksekusi (_Permissions_):**
   Masuk ke terminal linux Anda:
    ```bash
    chmod +x scripts/db_backup.sh
    chmod +x scripts/db_restore.sh
    ```
4. **Pasang Jadwal Automasi Menggunakan Cronjob (Untuk Prod / VPS):**
    - Ketik `crontab -e` di _shell_.
    - Letakkan instruksi backup berjadwal jam 2 pagi secara harian di baris bawah:
    ```text
    0 2 * * * /path/to/your/project/scripts/db_backup.sh >> /path/to/your/project/backups/backup_cron.log 2>&1
    ```

Fitur eksternal terminal bash ini adalah solusi penopang yang paling reliabel tanpa batas resource Time-Out RAM (tidak seperti limit per-menit dari Framework PHP/Javascript reguler).
Silakan di-bookmark dan porting sepuasnya!
