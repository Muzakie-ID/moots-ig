# Mots IG - Docker Setup

## Cara Pakai

### 1. Pastikan MySQL sudah berjalan di network `db_master_shared`

### 2. Copy environment file
```bash
cp .env.example .env
```

### 3. Edit `.env` sesuai konfigurasi MySQL kamu
```env
DB_HOST=192.168.1.100
DB_USER=root
DB_PASS=passwordmysql
DB_NAME=mots_ig
```

### 4. Buat tabel di MySQL
```sql
CREATE DATABASE IF NOT EXISTS mots_ig;
USE mots_ig;

CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5. Jalankan Docker
```bash
docker-compose up -d
```

### 6. Buka di browser
```
http://localhost:8080
```

## Struktur File
```
├── Dockerfile           # PHP 8.2 + Apache + MySQL ext
├── docker-compose.yml  # Connect ke network db_master_shared
├── .env.example        # Contoh environment variables
├── .gitignore          # Jangan commit .env
├── config/
│   └── database.php   # Koneksi DB dengan env support
└── README.md
```
