-- Database Schema for Inventory Management System
-- Run this SQL script in phpMyAdmin or MySQL client

CREATE DATABASE IF NOT EXISTS inventory_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventory_db;

CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(50) NOT NULL UNIQUE,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    status ENUM('Keep', 'Empty') NOT NULL DEFAULT 'Keep',
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

-- Sample data (optional)
INSERT INTO inventory (product_id, product_name, quantity, status, warehouse, row_location, column_location, level, location_id) VALUES 
('P001', 'สินค้า A', 100, 'Keep', 'A', 'A', 1, 0, 'AA-1-0'),
('P002', 'สินค้า B', 50, 'Keep', 'B', 'B', 2, 1, 'BB-2-1'),
('P003', 'สินค้า C', 0, 'Empty', 'C', 'C', 3, 2, 'CC-3-2');
