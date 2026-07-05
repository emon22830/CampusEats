CREATE TABLE IF NOT EXISTS menu_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(500) NULL,
    price DECIMAL(8, 2) NOT NULL,
    category VARCHAR(60) NOT NULL,
    image_url VARCHAR(255) NULL,
    in_stock TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_menu_items_vendor FOREIGN KEY (vendor_id) REFERENCES vendors (id) ON DELETE CASCADE,
    KEY idx_menu_items_vendor (vendor_id),
    KEY idx_menu_items_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
