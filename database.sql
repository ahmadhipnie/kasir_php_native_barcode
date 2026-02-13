-- Database Schema untuk Aplikasi Kasir Barcode

CREATE DATABASE IF NOT EXISTS kasir_barcode;
USE kasir_barcode;

-- Tabel Products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barcode VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Transactions
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_code VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_amount DECIMAL(10, 2) NOT NULL,
    change_amount DECIMAL(10, 2) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Transaction Items
CREATE TABLE IF NOT EXISTS transaction_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data untuk testing
INSERT INTO products (barcode, name, category, price, stock) VALUES
('8992761121109', 'Indomie Goreng', 'Makanan', 3500, 100),
('8992753161005', 'Teh Botol Sosro 500ml', 'Minuman', 5000, 50),
('8996001355008', 'Aqua 600ml', 'Minuman', 3000, 80),
('8992753171004', 'Teh Pucuk Harum 350ml', 'Minuman', 4000, 60),
('8992761121116', 'Indomie Soto', 'Makanan', 3500, 100);
