# Laravel CRUD REST API

REST API CRUD berbasis Laravel dengan autentikasi token menggunakan Laravel Sanctum.  
Data pencarian (NAMA, NIM, YMD) diambil secara **real-time** dari external API: [https://bit.ly/48ejMhW](https://bit.ly/48ejMhW)

## 📋 Daftar Endpoint

### A. Authentication (Public)

| Method | Endpoint         | Deskripsi                  |
|--------|------------------|----------------------------|
| POST   | `/api/register`  | Register user baru         |
| POST   | `/api/login`     | Login dan mendapatkan token |

### B. CRUD User (Protected - Perlu Token)

| Method | Endpoint           | Deskripsi              |
|--------|---------------------|----------------------|
| GET    | `/api/users`        | List semua user       |
| POST   | `/api/users`        | Buat user baru        |
| GET    | `/api/users/{id}`   | Detail user           |
| PUT    | `/api/users/{id}`   | Update user           |
| DELETE | `/api/users/{id}`   | Hapus user            |

### C-E. Pencarian Data External (Protected - Perlu Token)

| Method | Endpoint                                    | Deskripsi                     |
|--------|----------------------------------------------|-------------------------------|
| GET    | `/api/external-data/search/nama?nama=Turner Mia` | Cari berdasarkan NAMA    |
| GET    | `/api/external-data/search/nim?nim=9352078461`    | Cari berdasarkan NIM     |
| GET    | `/api/external-data/search/ymd?ymd=20230405`      | Cari berdasarkan YMD     |
| GET    | `/api/external-data`                              | Ambil semua data         |

### Extra Endpoints

| Method | Endpoint         | Deskripsi              |
|--------|------------------|------------------------|
| GET    | `/api/profile`   | Lihat profil user      |
| POST   | `/api/logout`    | Logout (revoke token)  |

## 🛠️ Teknologi

- **Framework**: Laravel 13.x
- **Authentication**: Laravel Sanctum (Token-based)
- **Database**: MySQL / PostgreSQL / SQLite
- **PHP**: >= 8.2
- **External Data**: Real-time fetch dari [https://bit.ly/48ejMhW](https://bit.ly/48ejMhW)

## 🚀 Instalasi & Setup

### 1. Clone Repository

```bash
git clone https://github.com/yudigf/laravel-CRUD-Rest-API-.git
cd laravel-CRUD-Rest-API-
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

**Untuk MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_crud_api
DB_USERNAME=root
DB_PASSWORD=
```

**Untuk PostgreSQL:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel_crud_api
DB_USERNAME=postgres
DB_PASSWORD=
```

**Untuk SQLite (default development):**
```env
DB_CONNECTION=sqlite
```

### 5. Jalankan Migrasi & Seeder

```bash
php artisan migrate
php artisan db:seed
```

### 6. Jalankan Server

```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

## 📦 Default User (Seeder)

| Email              | Password      | Role  |
|--------------------|---------------|-------|
| admin@example.com  | password123   | Admin |
| user@example.com   | password123   | User  |

## 🔑 Cara Penggunaan API

### 1. Login untuk mendapatkan token

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email": "admin@example.com", "password": "password123"}'
```

Response:
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": { "id": 1, "name": "Admin User", "email": "admin@example.com" },
    "access_token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

### 2. Gunakan token pada request berikutnya

```bash
curl -X GET "http://localhost:8000/api/external-data/search/nama?nama=Turner%20Mia" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|abc123..."
```

### 3. Contoh Response Pencarian NAMA = Turner Mia

```json
{
  "status": "success",
  "message": "Search by NAMA",
  "query": "Turner Mia",
  "total_results": 1,
  "data": [
    {
      "YMD": "20220713",
      "NIM": "9352078461",
      "NAMA": "Turner Mia"
    }
  ]
}
```

## 📮 Postman Collection

File Postman Collection tersedia di:  
📁 `postman/Laravel_CRUD_REST_API.postman_collection.json`

**Cara import:**
1. Buka Postman
2. Klik **Import** > **Upload Files**
3. Pilih file `postman/Laravel_CRUD_REST_API.postman_collection.json`
4. Jalankan request **Login** terlebih dahulu untuk mendapatkan token
5. Token akan otomatis tersimpan di collection variable

## 📂 Struktur Project

```
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php          # Login, Register, Logout, Profile
│   │   ├── UserController.php          # CRUD User
│   │   └── ExternalDataController.php  # Search NAMA, NIM, YMD
│   └── Models/
│       └── User.php                    # User Model + HasApiTokens
├── database/
│   ├── migrations/                     # Database migrations
│   ├── seeders/
│   │   └── DatabaseSeeder.php          # Default users seeder
│   └── database.sqlite                 # SQLite database (dev)
├── routes/
│   └── api.php                         # API routes definition
├── postman/
│   └── Laravel_CRUD_REST_API.postman_collection.json  # Postman Collection
└── README.md
```

## 📝 Catatan

- Endpoint **C, D, E** mengambil data secara **real-time** dari external API [https://bit.ly/48ejMhW](https://bit.ly/48ejMhW)
- Semua endpoint kecuali `/api/login` dan `/api/register` memerlukan autentikasi Bearer Token
- Token didapatkan dari response login/register
- Database backup (SQLite) tersedia di `database/database.sqlite`
