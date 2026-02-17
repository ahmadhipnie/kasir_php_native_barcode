CREATE DATABASE IF NOT EXISTS db_kasir_barcode;
USE db_kasir_barcode;

-- Users & Authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(150) DEFAULT '',
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','kasir') NOT NULL DEFAULT 'kasir',
    phone VARCHAR(20) DEFAULT '',
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Suppliers
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) DEFAULT '',
    email VARCHAR(150) DEFAULT '',
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barcode VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    category_id INT DEFAULT NULL,
    category VARCHAR(100) DEFAULT '',
    price DECIMAL(12,0) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_barcode (barcode),
    INDEX idx_category_id (category_id),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sales Transactions
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_code VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(12,0) NOT NULL DEFAULT 0,
    payment_amount DECIMAL(12,0) NOT NULL DEFAULT 0,
    change_amount DECIMAL(12,0) NOT NULL DEFAULT 0,
    user_id INT DEFAULT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (transaction_code),
    INDEX idx_date (transaction_date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS transaction_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    barcode VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(12,0) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,0) NOT NULL DEFAULT 0,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Purchase Transactions
CREATE TABLE IF NOT EXISTS purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_code VARCHAR(50) UNIQUE NOT NULL,
    supplier_id INT DEFAULT NULL,
    total_amount DECIMAL(12,0) NOT NULL DEFAULT 0,
    notes TEXT,
    user_id INT DEFAULT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_purchase_code (purchase_code),
    INDEX idx_purchase_date (purchase_date),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS purchase_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    barcode VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(12,0) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,0) NOT NULL DEFAULT 0,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin user (password: password)
INSERT IGNORE INTO users (name, username, email, password, role) VALUES
('Administrator', 'admin', 'admin@kasir.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Kasir Demo', 'kasir', 'kasir@kasir.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kasir');

-- Default categories
INSERT IGNORE INTO categories (name, description) VALUES
('Makanan', 'Produk makanan dan snack'),
('Minuman', 'Produk minuman'),
('Alat Tulis', 'Peralatan tulis kantor'),
('Kebersihan', 'Produk kebersihan dan toiletries');

-- Default suppliers
INSERT IGNORE INTO suppliers (name, phone, email, address) VALUES
('PT Indofood Sukses Makmur', '021-57951234', 'sales@indofood.co.id', 'Jakarta Selatan'),
('PT Sinar Sosro', '021-8711234', 'order@sosro.co.id', 'Bekasi, Jawa Barat'),
('PT Danone Aqua', '021-29955678', 'supply@danone.co.id', 'Jakarta Selatan');

-- Sample products (category_id references categories table)
INSERT IGNORE INTO products (barcode, name, category_id, category, price, stock) VALUES
('8992761121109', 'Indomie Goreng', 1, 'Makanan', 3500, 100),
('8992753161005', 'Teh Botol Sosro 500ml', 2, 'Minuman', 5000, 50),
('8996001355008', 'Aqua 600ml', 2, 'Minuman', 3000, 80),
('8992753171004', 'Teh Pucuk Harum 350ml', 2, 'Minuman', 4000, 60),
('8992761121116', 'Indomie Soto', 1, 'Makanan', 3500, 100),
('8886008101053', 'Pocari Sweat 500ml', 2, 'Minuman', 7500, 40),
('8992696421219', 'Chitato Sapi Panggang 68g', 1, 'Makanan', 10500, 35),
('8998866200318', 'Good Day Cappuccino 250ml', 2, 'Minuman', 5500, 45),
('8886012810019', 'Ultra Milk Coklat 250ml', 2, 'Minuman', 6000, 55),
('8991102231176', 'Roti Sari Roti Tawar', 1, 'Makanan', 15000, 25);
