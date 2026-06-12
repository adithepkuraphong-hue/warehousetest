-- Database Schema for Inventory Management System
-- Run this SQL script in phpMyAdmin or MySQL client

CREATE DATABASE IF NOT EXISTS inventory_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventory_db;

CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(50) NOT NULL UNIQUE,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    status ENUM('In Stock', 'Out Stock') NOT NULL DEFAULT 'In Stock',
    warehouse VARCHAR(1) DEFAULT 'A',
    row_location VARCHAR(1) DEFAULT 'A',
    column_location INT DEFAULT 1,
    level INT DEFAULT 0,
    location_id VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_product_id (product_id),
    INDEX idx_location_id (location_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS production_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pr_no VARCHAR(30) NOT NULL UNIQUE,
    source_inventory_id INT NULL,
    source_product_id VARCHAR(50) NOT NULL,
    source_product_name VARCHAR(255) NOT NULL,
    final_product_id VARCHAR(80) DEFAULT NULL,
    final_product_name VARCHAR(255) DEFAULT NULL,
    quantity INT NOT NULL DEFAULT 0,
    machine_type ENUM('Printer', 'Cutter') NOT NULL,
    status ENUM('รอผลิต', 'กำลังผลิต', 'เสร็จสิ้น') NOT NULL DEFAULT 'รอผลิต',
    next_destination ENUM('FP Warehouse', 'Printer', 'Cutter') DEFAULT NULL,
    parent_pr_id INT DEFAULT NULL,
    claimed_at TIMESTAMP NULL DEFAULT NULL,
    completed_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pr_no (pr_no),
    INDEX idx_machine_status (machine_type, status),
    INDEX idx_source_product (source_product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS FDwarehouse (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pr_no VARCHAR(30) NOT NULL,
    fp_product_id VARCHAR(80) NOT NULL,
    fp_product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    source_machine VARCHAR(20) NOT NULL,
    warehouse VARCHAR(1) DEFAULT 'A',
    row_location VARCHAR(1) DEFAULT 'A',
    column_location INT DEFAULT 1,
    level INT DEFAULT 0,
    location_id VARCHAR(20),
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fp_product_id (fp_product_id),
    INDEX idx_pr_no (pr_no),
    INDEX idx_fd_location_id (location_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS OrderHis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_type ENUM('Inbound', 'Outbound') NOT NULL,
    action VARCHAR(80) NOT NULL,
    reference_type VARCHAR(50) DEFAULT NULL,
    reference_id VARCHAR(50) DEFAULT NULL,
    product_id VARCHAR(80) DEFAULT NULL,
    product_name VARCHAR(255) DEFAULT NULL,
    quantity INT NOT NULL DEFAULT 0,
    source VARCHAR(120) DEFAULT NULL,
    destination VARCHAR(120) DEFAULT NULL,
    status VARCHAR(60) DEFAULT NULL,
    note TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_log_type (log_type),
    INDEX idx_created_at (created_at),
    INDEX idx_reference (reference_type, reference_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Location meaning:
-- warehouse: Warehouse building (A-B only)
-- row_location: Zone (A-C)
-- column_location: Row (1-5)
-- location_id format: {Warehouse}{Zone}-{Row}-{Level}, for example AA-1-0

-- Normalize existing databases that still contain Warehouse C.
UPDATE inventory
SET warehouse = 'B',
    location_id = CONCAT('B', row_location, '-', column_location, '-', level)
WHERE warehouse NOT IN ('A', 'B');

-- Sample data (optional)
INSERT IGNORE INTO inventory (product_id, product_name, quantity, status, warehouse, row_location, column_location, level, location_id) VALUES 
('P001', 'Paper', 100, 'In Stock', 'A', 'A', 1, 0, 'AA-1-0'),
('P002', 'Wood', 50, 'In Stock', 'B', 'B', 2, 1, 'BB-2-1'),
('P003', 'Plastic', 0, 'Out Stock', 'B', 'C', 3, 2, 'BC-3-2');
