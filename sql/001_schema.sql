CREATE DATABASE IF NOT EXISTS receipt_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE receipt_app;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','staff') NOT NULL DEFAULT 'staff',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE code_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_code VARCHAR(50) NOT NULL,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (group_code) REFERENCES code_groups(code) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE accessory_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 0,
    data_json JSON NOT NULL,
    created_by INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE receipt_sequences (
    seq_date VARCHAR(8) PRIMARY KEY,
    last_seq INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE receipts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_no VARCHAR(30) NOT NULL UNIQUE,
    orderer_name VARCHAR(100),
    orderer_phone1 VARCHAR(50),
    orderer_phone2 VARCHAR(50),
    receiver_name VARCHAR(100),
    receiver_phone1 VARCHAR(50),
    receiver_phone2 VARCHAR(50),
    delivery_date DATE,
    delivery_time_id INT,
    delivery_place VARCHAR(255),
    postal_code VARCHAR(20),
    address VARCHAR(255),
    address_detail VARCHAR(255),
    delivery_request TEXT,
    delivery_method_id INT,
    pot_size_id INT,
    pot_type_id INT,
    pot_color_id INT,
    plant_size_id INT,
    plant_type_id INT,
    sale_amount INT DEFAULT 0,
    order_amount INT DEFAULT 0,
    status VARCHAR(50) NOT NULL DEFAULT 'PENDING',
    created_by INT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    batch_group_id VARCHAR(50) NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (delivery_time_id) REFERENCES codes(id) ON DELETE SET NULL,
    FOREIGN KEY (delivery_method_id) REFERENCES codes(id) ON DELETE SET NULL,
    FOREIGN KEY (pot_size_id) REFERENCES codes(id) ON DELETE SET NULL,
    FOREIGN KEY (pot_type_id) REFERENCES codes(id) ON DELETE SET NULL,
    FOREIGN KEY (pot_color_id) REFERENCES codes(id) ON DELETE SET NULL,
    FOREIGN KEY (plant_size_id) REFERENCES codes(id) ON DELETE SET NULL,
    FOREIGN KEY (plant_type_id) REFERENCES codes(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE receipt_accessories (
    receipt_id INT NOT NULL,
    accessory_item_id INT NOT NULL,
    PRIMARY KEY (receipt_id, accessory_item_id),
    FOREIGN KEY (receipt_id) REFERENCES receipts(id) ON DELETE CASCADE,
    FOREIGN KEY (accessory_item_id) REFERENCES accessory_items(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE receipt_status_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_id INT NOT NULL,
    from_status VARCHAR(50),
    to_status VARCHAR(50) NOT NULL,
    changed_by INT,
    changed_at DATETIME NOT NULL,
    change_batch_id VARCHAR(50),
    FOREIGN KEY (receipt_id) REFERENCES receipts(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE receipt_change_batches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50),
    changed_by INT,
    changed_at DATETIME,
    count INT,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
