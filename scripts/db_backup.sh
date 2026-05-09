#!/bin/bash

# Pindah ke direktori root proyek (satu tingkat di atas folder scripts)
cd "$(dirname "$0")/.." || exit

echo "========================================"
echo "    Pencadangan Database Otomatis       "
echo "========================================"

# Pastikan file .env ada
if [ ! -f .env ]; then
    echo "❌ Error: File .env tidak ditemukan di root directory!"
    exit 1
fi

# Mengambil kredensial dari .env secara dinamis tanpa mengeksposnya
DB_CONNECTION=$(grep -E '^DB_CONNECTION=' .env | cut -d '=' -f 2- | tr -d '\r')
DB_HOST=$(grep -E '^DB_HOST=' .env | cut -d '=' -f 2- | tr -d '\r')
DB_PORT=$(grep -E '^DB_PORT=' .env | cut -d '=' -f 2- | tr -d '\r')
DB_DATABASE=$(grep -E '^DB_DATABASE=' .env | cut -d '=' -f 2- | tr -d '\r')
DB_USERNAME=$(grep -E '^DB_USERNAME=' .env | cut -d '=' -f 2- | tr -d '\r')
DB_PASSWORD=$(grep -E '^DB_PASSWORD=' .env | cut -d '=' -f 2- | tr -d '\r')

# Jika bukan MySQL/MariaDB, hentikan (Bisa modif jika ingin full support SQLite)
if [ "$DB_CONNECTION" != "mysql" ] && [ "$DB_CONNECTION" != "mariadb" ]; then
    echo "⚠️ Warning: DB_CONNECTION di set ke '$DB_CONNECTION'."
    echo "Script ini dioptimalkan untuk MySQL/MariaDB menggunakan mysqldump."
    exit 1
fi

# Cross-platform: Mencari lokasi binary mysqldump (Mendukung Homebrew & standard Linux)
MYSQLDUMP_CMD=$(command -v mysqldump)
if [ -z "$MYSQLDUMP_CMD" ]; then
    for p in /opt/homebrew/bin/mysqldump /usr/local/bin/mysqldump /usr/bin/mysqldump; do
        if [ -x "$p" ]; then
            MYSQLDUMP_CMD=$p
            break
        fi
    done
fi

if [ -z "$MYSQLDUMP_CMD" ]; then
    echo "❌ Error: Command 'mysqldump' tidak ditemukan. Pastikan MySQL Client sudah terinstall."
    exit 1
fi

# Folder penyimpanan Output
BACKUP_DIR="backups"
mkdir -p "$BACKUP_DIR"

TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_FILE="${BACKUP_DIR}/${DB_DATABASE}_backup_${TIMESTAMP}.sql.gz"

echo "Memulai backup database: $DB_DATABASE ..."

# Mengeksekusi dump dengan parameter performa (Single Transaction & Tanpa lock)
if [ -z "$DB_PASSWORD" ]; then
    "$MYSQLDUMP_CMD" -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" \
        --single-transaction --quick --skip-lock-tables "$DB_DATABASE" | gzip > "$BACKUP_FILE"
else
    "$MYSQLDUMP_CMD" -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" \
        --single-transaction --quick --skip-lock-tables "$DB_DATABASE" 2>/dev/null | gzip > "$BACKUP_FILE"
fi

if [ $? -eq 0 ]; then
    echo "✅ Backup sukses tersimpan di: $BACKUP_FILE"
else
    echo "❌ Gagal melakukan backup database!"
    rm -f "$BACKUP_FILE"
    exit 1
fi

# Fitur CleanUp: Menghapus backup yang berumur lebih dari batas retensi
RETENTION_DAYS=14
echo "Memeriksa dan menghapus file backup yang berumur lebih dari $RETENTION_DAYS hari..."

find "$BACKUP_DIR" -name "*.sql.gz" -type f -mtime +$RETENTION_DAYS -exec rm -f {} \;

echo "✅ Pencadangan Selesai."