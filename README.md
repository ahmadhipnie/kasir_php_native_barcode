# Aplikasi Kasir Barcode

Aplikasi kasir dengan fitur scan barcode menggunakan PHP Native dan MVC Pattern.

## Struktur Folder

```
aplikasi-kasir-scan-barcode/
├── app/
│   ├── controllers/        # Controller files
│   │   ├── Dashboard.php
│   │   ├── Products.php
│   │   └── Transactions.php
│   ├── models/            # Model files
│   │   ├── Product.php
│   │   └── Transaction.php
│   ├── views/             # View files
│   │   ├── layouts/       # Layout templates
│   │   ├── dashboard/
│   │   ├── products/
│   │   └── transactions/
│   └── core/              # Core MVC classes
│       ├── App.php        # Router
│       ├── Controller.php # Base Controller
│       ├── Model.php      # Base Model
│       └── Database.php   # Database connection
├── config/
│   ├── config.php         # App configuration
│   └── database.php       # Database configuration
├── public/                # Web root
│   ├── index.php         # Entry point
│   ├── .htaccess
│   └── assets/           # CSS, JS, Images
├── storage/
│   ├── logs/
│   └── uploads/
└── database.sql          # Database schema

```

## Instalasi

### 1. Setup Database
```sql
# Import database schema
mysql -u root -p < database.sql
```

### 2. Konfigurasi Database
Edit `config/database.php` sesuai dengan setting MySQL Anda:
```php
return [
    'host' => 'localhost',
    'database' => 'kasir_barcode',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];
```

### 3. Konfigurasi Base URL
Edit `config/config.php` dan sesuaikan BASE_URL:
```php
define('BASE_URL', 'http://localhost/aplikasi-kasir-scan-barcode/public/');
```

### 4. Setup Apache
Pastikan mod_rewrite enabled di Apache:
```bash
# Untuk Linux/Mac
sudo a2enmod rewrite
sudo service apache2 restart

# Pastikan AllowOverride All di Apache config
```

### 5. Template Bootstrap
Letakkan file Bootstrap Anda di:
- CSS: `public/assets/css/bootstrap.min.css`
- JS: `public/assets/js/bootstrap.bundle.min.js`

Atau gunakan CDN dengan edit `app/views/layouts/header.php`

## Panduan Integrasi Template Bootstrap

### Langkah 1: Copy File Bootstrap
1. Copy file CSS Bootstrap ke: `public/assets/css/`
2. Copy file JS Bootstrap ke: `public/assets/js/`
3. Copy images/icons ke: `public/assets/images/`

### Langkah 2: Modifikasi Layout
Edit file-file layout di `app/views/layouts/`:
- `header.php` - Main layout dengan Bootstrap structure
- `navbar.php` - Top navigation
- `sidebar.php` - Side navigation

### Langkah 3: Sesuaikan Views
Edit views di `app/views/` untuk menyesuaikan dengan template Bootstrap Anda:
- `dashboard/index.php`
- `products/index.php`, `add.php`, `edit.php`
- `transactions/create.php`, `index.php`, `detail.php`

### Tips Integrasi:
1. Gunakan class Bootstrap yang sudah ada di template Anda
2. Sesuaikan warna dan style di `public/assets/css/style.css`
3. JavaScript khusus untuk barcode scanner ada di `public/assets/js/transaction.js`

## Fitur

### 1. Dashboard
- Ringkasan penjualan hari ini
- Jumlah transaksi
- Transaksi terakhir

### 2. Manajemen Produk
- CRUD Produk
- Pencarian berdasarkan barcode
- Update stok otomatis

### 3. Transaksi
- Scan barcode untuk menambah produk
- Keranjang belanja
- Hitung kembalian otomatis
- Simpan transaksi dengan detail items

## Barcode Scanner Setup

### Hardware Scanner
Barcode scanner USB bekerja seperti keyboard. Cukup fokuskan cursor di input barcode dan scan.

### Software Scanner (Testing)
Untuk testing tanpa hardware scanner, gunakan:
1. Manual input barcode
2. Webcam barcode scanner (implementasi tambahan dengan library JavaScript seperti QuaggaJS)

## URL Routes

- `/` atau `/dashboard` - Dashboard
- `/products` - Daftar produk
- `/products/add` - Tambah produk
- `/products/edit/{id}` - Edit produk
- `/transactions` - Riwayat transaksi
- `/transactions/create` - Transaksi baru
- `/transactions/detail/{id}` - Detail transaksi

## Teknologi

- **PHP** 7.4+
- **MySQL** 5.7+
- **Bootstrap** 5.x (template Anda)
- **PDO** untuk database
- **MVC Pattern**

## Development

### Menambah Controller Baru
```php
<?php
class NamaController extends Controller {
    public function index() {
        $this->view('nama/index', $data);
    }
}
```

### Menambah Model Baru
```php
<?php
class NamaModel extends Model {
    protected $table = 'nama_table';
    
    // Custom methods here
}
```

### Menambah View Baru
Buat file di `app/views/folder/file.php` dan gunakan output buffering:
```php
<?php ob_start(); ?>
<!-- Content here -->
<?php 
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>
```

## Best Practices

1. **Security**: Selalu validasi dan sanitize input
2. **Database**: Gunakan prepared statements (sudah implemented di Model)
3. **Error Handling**: Enable error reporting saat development
4. **Code Style**: Ikuti PSR-12 coding standard
5. **Version Control**: Gunakan Git untuk track changes

## Troubleshooting

### 404 Not Found
- Pastikan mod_rewrite enabled
- Check .htaccess file ada dan readable
- Periksa BASE_URL di config

### Database Connection Failed
- Cek credentials di config/database.php
- Pastikan MySQL service running
- Cek database sudah dibuat

### Barcode Scanner Tidak Berfungsi
- Pastikan scanner terdeteksi sebagai keyboard
- Test di notepad apakah scanner bisa input
- Periksa focus pada input barcode

## License

Free to use untuk project pribadi atau komersial.
