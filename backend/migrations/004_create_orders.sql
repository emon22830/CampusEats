CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    vendor_id INT UNSIGNED NOT NULL,
    status ENUM('placed', 'preparing', 'ready', 'collected', 'cancelled') NOT NULL DEFAULT 'placed',
    total DECIMAL(8, 2) NOT NULL,
    pickup_at VARCHAR(60) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT,
    CONSTRAINT fk_orders_vendor FOREIGN KEY (vendor_id) REFERENCES vendors (id) ON DELETE RESTRICT,
    KEY idx_orders_user (user_id),
    KEY idx_orders_vendor_status (vendor_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
