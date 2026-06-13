USE inventory_db;

CREATE TABLE IF NOT EXISTS product_order_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rid VARCHAR(30) NOT NULL UNIQUE,
    product_name VARCHAR(80) NOT NULL,
    material_inventory_id INT NOT NULL,
    material_product_id VARCHAR(50) NOT NULL,
    material_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    location_id VARCHAR(20) NOT NULL,
    order_date DATE NOT NULL,
    order_time TIME NOT NULL,
    production_order_id INT NULL,
    status VARCHAR(60) NOT NULL DEFAULT 'รอผลิต',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_rid (rid),
    INDEX idx_material_inventory_id (material_inventory_id),
    INDEX idx_order_datetime (order_date, order_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
