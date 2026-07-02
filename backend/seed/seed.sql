-- Demo accounts, password for all seeded users is: password123
INSERT INTO users (id, name, email, password_hash, role) VALUES
    (1, 'Admin Account', 'admin@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'admin'),
    (2, 'Kak Lah', 'vendor@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'vendor'),
    (3, 'Ali Student', 'customer@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'customer');

INSERT INTO users (name, email, password_hash, role) VALUES
    ('Test User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- Team accounts, password: password123
INSERT INTO users (name, email, password_hash, role) VALUES
    ('Ahmat', 'ahmat@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'customer'),
    ('Muaz Ahmed', 'muaz@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'customer'),
    ('Hisham', 'hisham@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'customer');

INSERT INTO vendors (id, owner_id, name, location, opening_hours, image_url, prep_time_mins, is_active, status) VALUES
    (1, 2, 'Kak Lah Nasi Lemak', 'UTM Arkib - Counter B, Stall 7', '8:00 AM - 4:00 PM', '/images/kak_lah.jpg', 15, 1, 'approved');

INSERT INTO menu_items (id, vendor_id, name, description, price, category, image_url, in_stock) VALUES
    (101, 1, 'Nasi Lemak Ayam Regular', 'Fragrant coconut rice, sambal, fried egg & crispy chicken', 5.50, 'Rice', NULL, 1),
    (102, 1, 'Teh Ais Kaw', 'Sweet iced milk tea', 2.00, 'Drinks', NULL, 1);

INSERT INTO reviews (id, user_id, vendor_id, rating, comment) VALUES
    (1, 3, 1, 5, 'Best nasi lemak on campus, fast pickup!'),
    (2, 3, 1, 4, 'Great food, queue moves quick with pre-order.');
