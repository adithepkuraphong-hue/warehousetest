USE inventory_db;

ALTER TABLE production_orders
    ADD COLUMN fg_product_id VARCHAR(80) DEFAULT NULL AFTER source_product_name,
    ADD COLUMN fg_product_name VARCHAR(255) DEFAULT NULL AFTER fg_product_id;

UPDATE production_orders
SET fg_product_id = COALESCE(fg_product_id, final_product_id),
    fg_product_name = COALESCE(fg_product_name, final_product_name)
WHERE fg_product_id IS NULL OR fg_product_name IS NULL;

ALTER TABLE production_orders
    MODIFY next_destination ENUM('FG Warehouse', 'FP Warehouse', 'Printer', 'Cutter') DEFAULT NULL;

UPDATE production_orders
SET next_destination = 'FG Warehouse'
WHERE next_destination = 'FP Warehouse';

ALTER TABLE production_orders
    MODIFY next_destination ENUM('FG Warehouse', 'Printer', 'Cutter') DEFAULT NULL;
