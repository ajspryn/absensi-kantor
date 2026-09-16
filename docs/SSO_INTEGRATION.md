# SSO Integration

Aplikasi Absensi berperan sebagai Identity Provider (IdP) dengan flow authorization code. Absensi menyimpan identitas user, daftar aplikasi client, role aplikasi, dan assignment akses per-user.

Role Absensi tidak otomatis menjadi role di aplikasi lain. Setiap aplikasi client mendaftarkan role-nya sendiri, lalu admin Absensi memilih role yang cocok untuk masing-masing karyawan.

## Ringkasnya

Jika Anda bingung, ini alurnya singkat:

1. Admin mendaftarkan aplikasi client di menu **Admin > Kelola SSO**.
2. Aplikasi client mengirim daftar role ke API Absensi.
3. Admin memberi akses role untuk karyawan tertentu.
4. User login ke Absensi dan diarahkan ke `/oauth/authorize`.
5. Client menukar `code` ke token.
6. Client memanggil `/api/oauth/userinfo` untuk mendapatkan data user.

## 1. Daftarkan aplikasi client

Jalankan command di server aplikasi Absensi:

```bash
php artisan migrate
php artisan sso:client "Portal HR" https://portal.example.com/auth/callback
```

Command menampilkan `client_id` dan `client_secret` satu kali. Simpan keduanya sebagai secret di aplikasi client. Jangan commit `client_secret` ke source control.

Admin juga dapat mendaftarkan aplikasi melalui menu **Admin > Kelola SSO**. Menu tersebut menampilkan aplikasi terhubung, role yang sudah didaftarkan, dan jumlah user yang memiliki akses.

## 2. Daftarkan role aplikasi

Aplikasi client mengirim katalog role menggunakan `client_id` dan `client_secret`:

```text
POST https://absensi.example.com/api/oauth/roles
Content-Type: application/json

{
  "client_id": "CLIENT_ID",
  "client_secret": "CLIENT_SECRET",
  "roles": [
    {"code": "finance_admin", "name": "Finance Admin"},
    {"code": "approver", "name": "Approver"},
    {"code": "staff", "name": "Staff"}
  ]
}
```

Role `code` hanya boleh berisi huruf kecil, angka, titik, garis bawah, dan tanda hubung. Request dapat dikirim ulang dengan aman karena bersifat idempotent.

Role yang terdaftar dapat dibaca kembali via:

```text
GET https://absensi.example.com/api/oauth/roles?client_id=CLIENT_ID&client_secret=CLIENT_SECRET
```

Admin Absensi kemudian membuka menu **Kelola Karyawan > Atur** dan memilih satu role aplikasi untuk setiap karyawan. User tanpa assignment tidak dapat login ke aplikasi tersebut.

## 3. Arahkan user ke SSO

Redirect browser user ke:

```text
GET https://absensi.example.com/oauth/authorize
    ?response_type=code
    &client_id=CLIENT_ID
    &redirect_uri=https%3A%2F%2Fportal.example.com%2Fauth%2Fcallback
    &scope=openid%20profile%20email
    &state=RANDOM_STATE
```

User memakai session login Absensi. Setelah berhasil, SSO akan redirect ke `redirect_uri` dengan parameter `code` dan `state`. Client wajib memeriksa bahwa `state` sama dengan nilai yang dibuat sebelum redirect.

Jika user tidak memiliki assignment pada aplikasi client, endpoint akan mengembalikan HTTP `403` dan authorization code tidak diterbitkan.

## 4. Tukar code menjadi access token

Server client mengirim request backend-to-backend:

```text
POST https://absensi.example.com/api/oauth/token
Content-Type: application/json

{
  "grant_type": "authorization_code",
  "code": "CODE_FROM_CALLBACK",
  "client_id": "CLIENT_ID",
  "client_secret": "CLIENT_SECRET",
  "redirect_uri": "https://portal.example.com/auth/callback"
}
```

Authorization code berlaku singkat dan hanya dapat digunakan sekali. Response berisi `access_token`, `token_type`, dan `expires_in`.

## 5. Ambil identitas dan role user

```text
GET https://absensi.example.com/api/oauth/userinfo
Authorization: Bearer ACCESS_TOKEN
```

Response menyertakan data profil dasar dan profil karyawan. Client sebaiknya memakai `sub` sebagai identifier akun eksternal dan tidak memakai email sebagai primary key.

Contoh response yang saat ini dikembalikan:

```json
{
    "sub": "15",
    "name": "Budi",
    "full_name": "Budi Santoso",
    "email": "budi@example.com",
    "email_verified": true,
    "phone": "081234567890",
    "mobile": "082233445566",
    "employee_id": "EMP001",
    "address": "Jl. Mawar No. 10",
    "gender": "M",
    "department": "HRD",
    "position": "Staff",
    "hire_date": "2024-01-15",
    "birth_date": "1995-05-10",
    "identity_role": "employee",
    "application": "Portal Keuangan",
    "application_role": "approver",
    "roles": ["approver"]
}
```

Gunakan `application_role` atau `roles` untuk authorization di aplikasi tujuan. `identity_role` hanya role global Absensi dan tidak boleh dianggap sebagai permission aplikasi tujuan.

## 6. Tanggung jawab aplikasi client

Aplikasi tujuan harus:

1. Membuat dan menyimpan `state` acak sebelum redirect.
2. Memeriksa `state` pada callback.
3. Menukar code dari server ke server menggunakan `client_secret`.
4. Membuat atau memperbarui user lokal berdasarkan `sub`.
5. Menyimpan role aplikasi dari `userinfo` ke session atau user lokal.
6. Menegakkan permission lokal pada setiap route/API.

SSO hanya membuktikan identitas dan entitlement aplikasi. SSO tidak menggantikan middleware, policy, atau permission system di aplikasi tujuan.

## 7. Keamanan transport dan JWT

Token akses yang dikirim ke client menggunakan JWT. Ini aman untuk autentikasi, tetapi keamanannya tetap bergantung pada:

- HTTPS aktif di semua endpoint
- `client_secret` disimpan aman di backend client
- `redirect_uri` sesuai dan diverifikasi
- `state` dibuat dan dicek dengan benar
- token tidak disimpan di browser tanpa pengamanan tambahan

## Konfigurasi production dan domain

Semua URL SSO harus diatur dari environment, bukan dari kode aplikasi. Tujuannya agar saat server atau domain berubah, Anda hanya perlu mengganti variabel `.env` tanpa harus mengubah source code.

Contoh konfigurasi production:

```env
APP_ENV=production
APP_URL=https://absensi.bprsbtb.co.id
SSO_ISSUER=https://absensi.bprsbtb.co.id
APP_DEBUG=false
JWT_SECRET=SECRET_YANG_BARU_DIBUAT
SESSION_SECURE_COOKIE=true
```

Aturan penting:

- `APP_URL` harus sama dengan domain utama aplikasi Absensi
- `SSO_ISSUER` harus sama dengan URL provider SSO
- semua callback client harus memakai domain yang sesuai dengan URL produksi
- jika pindah server/domain, cukup ubah nilai di `.env` lalu reload aplikasi atau restart service

Catatan:

- Jangan hardcode `https://absensi.example.com` di kode aplikasi
- Jangan hardcode callback URL di logika runtime jika bisa diubah lewat env/config
- Gunakan HTTPS di produksi, termasuk callback client dan seluruh pertukaran token

Untuk memudahkan migrasi server, pastikan semua domain dan callback yang aktif selalu berasal dari environment variables dan tidak tertanam di template, route, atau kode program.
