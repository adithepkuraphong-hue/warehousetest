USE inventory_db;

ALTER TABLE FGwarehouse
    ADD COLUMN warehouse VARCHAR(1) DEFAULT 'A' AFTER source_machine,
    ADD COLUMN row_location VARCHAR(1) DEFAULT 'A' AFTER warehouse,
    ADD COLUMN column_location INT DEFAULT 1 AFTER row_location,
    ADD COLUMN level INT DEFAULT 0 AFTER column_location,
    ADD COLUMN location_id VARCHAR(20) AFTER level,
    ADD INDEX idx_fg_location_id (location_id);

UPDATE FGwarehouse
SET warehouse = COALESCE(NULLIF(warehouse, ''), 'A'),
    row_location = COALESCE(NULLIF(row_location, ''), 'A'),
    column_location = COALESCE(column_location, 1),
    level = COALESCE(level, 0),
    location_id = CONCAT(
        COALESCE(NULLIF(warehouse, ''), 'A'),
        COALESCE(NULLIF(row_location, ''), 'A'),
        '-',
        COALESCE(column_location, 1),
        '-',
        COALESCE(level, 0)
    )
WHERE location_id IS NULL OR location_id = '';
