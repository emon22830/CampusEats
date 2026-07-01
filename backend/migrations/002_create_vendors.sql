CREATE TABLE IF NOT EXISTS vendors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(190) NOT NULL,
    opening_hours VARCHAR(120) NULL,
    image_url VARCHAR(255) NULL,
    prep_time_mins SMALLINT UNSIGNED NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vendors_owner FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE,
    KEY idx_vendors_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
