USE inventory_db;

ALTER TABLE inventory
    MODIFY status ENUM('In Stock', 'Out Stock', 'Keep', 'Empty') NOT NULL DEFAULT 'In Stock';

UPDATE inventory
SET status = CASE
    WHEN quantity <= 0 THEN 'Out Stock'
    WHEN status = 'Empty' THEN 'Out Stock'
    ELSE 'In Stock'
END;

ALTER TABLE inventory
    MODIFY status ENUM('In Stock', 'Out Stock') NOT NULL DEFAULT 'In Stock';

UPDATE inventory
SET product_name = 'Paper'
WHERE product_name NOT IN ('Paper', 'Wood', 'Plastic');

ALTER TABLE inventory
    ADD INDEX idx_inventory_status (status);

ALTER TABLE production_orders
    ADD INDEX idx_production_completed_fp (status, next_destination);
