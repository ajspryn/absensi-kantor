#!/bin/bash

# Pindah ke direktori root proyek
cd "$(dirname "$0")/.." || exit

echo "========================================"
echo "    Pemulihan Database Otomatis         "
echo "========================================"

if [ "$#" -ne 1 ]; then
    echo "Penggunaan: ./scripts/db_restore.sh <FILE_BACKUP>"
    echo "Contoh: ./scripts/db_restore.sh backups/absensi_db_backup_2026.sql.gz"
    exit 1
fi

BACKUP_FILE=$1

if [ ! -f "$BACKUP_FILE" ]; then
    echo "❌ Error: File backup '$BACKUP_FILE' tidak ditemukan!"
    exit 1
fi

# Pastikan file .env ada
if [ ! -f .env ]; then
    echo "❌ Error: File .env tidak ditemukan di root directory!"
    exit 1
fi

# Mengambil kredensial dari .env secara dinamis
DB_HOST=$(grep -E '^DB_HOST=' .env | cut -d '=' -f 2- | tr -d '\r')
DB_PORT=$(grep -E '^DB_PORT=' .env | cut -d '=' -f 2- | tr -d '\r')
DB_DATABASE=$(grep -E '^DB_DATABASE=' .env | cut -d '=' -f 2- | tr -d '\r')
DB_USERNAME=$(grep -E '^DB_USERNAME=' .env | cut -d '=' -f 2- | tr -d '\r')
DB_PASSWORD=$(grep -E '^DB_PASSWORD=' .env | cut -d '=' -f 2- | tr -d '\r')

echo "Database Target : $DB_DATABASE"
echo "File Restore    : $BACKUP_FILE"
echo ""

# Konfirmasi Anti-human-error
read -p "⚠️  PERINGATAN: Tindakan ini akan ME-WIPE (Drop & Recreate) seluruh data database '$DB_DATABASE'. Lanjutkan? [y/N]: " konfirmasi
if [[ "$konfirmasi" != "y" && "$konfirmasi" != "Y" ]]; then
    echo "Dibatalkan."
    exit 0
fi

# Cross-platform MySQL client
MYSQL_CMD=$(command -v mysql)
if [ -z "$MYSQL_CMD" ]; then
    for p in /opt/homebrew/bin/mysql /usr/local/bin/mysql /usr/bin/mysql; do
        if [ -x "$p" ]; then
            MYSQL_CMD=$p
            break
        fi
    done
fi

if [ -z "$MYSQL_CMD" ]; then
    echo "❌ Error: Command 'mysql' tidak ditemukan."
    exit 1
fi

# Menyiapkan credential string
if [ -z "$DB_PASSWORD" ]; then
    DB_AUTH="-h $DB_HOST -P $DB_PORT -u $DB_USERNAME"
else
    DB_AUTH="-h $DB_HOST -P $DB_PORT -u $DB_USERNAME -p$DB_PASSWORD"
fi

echo "Sedang melakukan DROP dan CREATE ulang database aman..."
$MYSQL_CMD $DB_AUTH 2>/dev/null -e "DROP DATABASE IF EXISTS \`$DB_DATABASE\`; CREATE DATABASE \`$DB_DATABASE\`;"

if [ $? -ne 0 ]; then
    echo "❌ Gagal me-reset database. Pastikan user '$DB_USERNAME' memiliki privileges DROP & CREATE."
    exit 1
fi

echo "Sedang mengekstrak dan mengimpor file ($BACKUP_FILE)..."

# Mendeteksi ekstensi file untuk Import
if [[ "$BACKUP_FILE" == *.gz ]]; then
    gunzip -c "$BACKUP_FILE" | $MYSQL_CMD $DB_AUTH "$DB_DATABASE" 2>/dev/null
elif [[ "$BACKUP_FILE" == *.sql ]]; then
    $MYSQL_CMD $DB_AUTH "$DB_DATABASE" 2>/dev/null < "$BACKUP_FILE"
else
    echo "❌ Format file tidak dikenali. Harap gunakan file berakhiran .sql atau .sql.gz"
    exit 1
fi

if [ $? -eq 0 ]; then
    echo "✅ Restore database selesai dengan sukses!"
else
    echo "❌ Terjadi kegagalan saat proses restore."
    exit 1
fi
