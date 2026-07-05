-- Demo accounts, password for all seeded users is: password123
INSERT INTO users (id, name, email, password_hash, role) VALUES
    (1, 'Admin Account', 'admin@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'admin'),
    (2, 'Kak Lah', 'vendor@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'vendor'),
    (3, 'Ali Student', 'customer@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'customer');

INSERT INTO users (name, email, password_hash, role) VALUES
    ('Test User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- Team accounts, password: password123
INSERT INTO users (name, email, password_hash, role) VALUES
    ('Ahmat', 'ahmat@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'admin'),
    ('Muaz Ahmed', 'muaz@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'vendor'),
    ('Hisham', 'hisham@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'customer');

-- Additional vendor-owner accounts for the extra food court stalls below, password: password123
INSERT INTO users (id, name, email, password_hash, role) VALUES
    (8, 'Pak Man', 'pakman.mee@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'vendor'),
    (9, 'Chef Amin', 'alamin.grill@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'vendor'),
    (10, 'Uncle Tan', 'unclechicken.rice@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'vendor'),
    (11, 'Kak Yah', 'vegdelight@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'vendor'),
    (12, 'Sarah Lim', 'sipspot@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'vendor'),
    (13, 'Pak Malik', 'mamakcorner@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'vendor'),
    (14, 'Kenji Wong', 'sakurabento@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'vendor'),
    (15, 'Ain Bakes', 'sweettreats@campuseats.test', '$argon2id$v=19$m=65536,t=4,p=1$eFJuYkRvLmZEZ1VhQTQwQQ$G6pPXcmk1caLvV5S0/ywR4SXt85YpG4MLe/RNV9IQWM', 'vendor');

INSERT INTO vendors (id, owner_id, name, location, opening_hours, image_url, prep_time_mins, is_active, status) VALUES
    (1, 2, 'Kak Lah Nasi Lemak', 'UTM Arkib - Counter B, Stall 7', '8:00 AM - 4:00 PM', '/images/kak_lah.jpg', 15, 1, 'approved'),
    (2, 8, 'Mee Bandung Pak Man', 'UTM Cafe Arked - Counter A, Stall 3', '7:30 AM - 5:00 PM', '/images/mee_bandung.jpg', 12, 1, 'approved'),
    (3, 9, 'Restoran Al-Amin Grill', 'UTM Cafe Arked - Counter A, Stall 5', '10:00 AM - 9:00 PM', '/images/al_amin_grill.jpg', 20, 1, 'approved'),
    (4, 10, 'Uncle Tan Chicken Rice', 'UTM Arkib - Counter B, Stall 2', '8:00 AM - 3:00 PM', '/images/uncle_tan.jpg', 15, 1, 'approved'),
    (5, 11, 'Vegetarian Delight', 'UTM Arkib - Counter C, Stall 1', '7:00 AM - 4:00 PM', '/images/veg_delight.jpg', 10, 1, 'approved'),
    (6, 12, 'The Sip Spot', 'UTM Cafe Arked - Kiosk 2', '9:00 AM - 8:00 PM', '/images/sip_spot.jpg', 5, 1, 'approved'),
    (7, 13, 'Mamak Corner', 'UTM Cafe Arked - Counter B, Stall 9', '24 hours', '/images/mamak_corner.jpg', 15, 1, 'approved'),
    (8, 14, 'Sakura Bento', 'UTM Arkib - Counter D, Stall 4', '11:00 AM - 8:00 PM', '/images/sakura_bento.jpg', 18, 1, 'approved'),
    (9, 15, 'Sweet Treats Bakery', 'UTM Cafe Arked - Kiosk 5', '8:00 AM - 6:00 PM', '/images/sweet_treats.jpg', 8, 1, 'approved');

INSERT INTO menu_items (id, vendor_id, name, description, price, category, image_url, in_stock) VALUES
    (101, 1, 'Nasi Lemak Ayam Regular', 'Fragrant coconut rice, sambal, fried egg & crispy chicken', 5.50, 'Rice', NULL, 1),
    (102, 1, 'Teh Ais Kaw', 'Sweet iced milk tea', 2.00, 'Drinks', NULL, 1),

    (103, 2, 'Mee Bandung', 'Spicy noodle soup with prawns, egg & vegetables', 6.00, 'Noodles', NULL, 1),
    (104, 2, 'Mee Goreng Mamak', 'Wok-fried yellow noodles with a tangy spicy kick', 5.50, 'Noodles', NULL, 1),
    (105, 2, 'Maggi Goreng', 'Stir-fried instant noodles with egg and vegetables', 5.00, 'Noodles', NULL, 1),
    (106, 2, 'Fishball Soup', 'Light broth with fishballs and greens', 4.50, 'Noodles', NULL, 1),
    (107, 2, 'Iced Lemon Tea', 'Refreshing chilled lemon tea', 2.00, 'Drinks', NULL, 1),

    (108, 3, 'Grilled Chicken Chop', 'Grilled chicken thigh with black pepper sauce, fries & coleslaw', 9.50, 'Western', NULL, 1),
    (109, 3, 'Fried Chicken Set', 'Crispy fried chicken with rice and gravy', 7.50, 'Western', NULL, 1),
    (110, 3, 'Fish & Chips', 'Battered fish fillet with fries and tartar sauce', 8.50, 'Western', NULL, 1),
    (111, 3, 'Beef Burger', 'Grilled beef patty with cheese, lettuce & special sauce', 7.00, 'Western', NULL, 1),
    (112, 3, 'Iced Milo', 'Chocolate malt drink served cold', 2.50, 'Drinks', NULL, 1),

    (113, 4, 'Hainanese Chicken Rice', 'Steamed chicken with fragrant rice & chili sauce', 6.50, 'Rice', NULL, 1),
    (114, 4, 'Roasted Chicken Rice', 'Roasted chicken with fragrant rice', 6.50, 'Rice', NULL, 1),
    (115, 4, 'Char Siew Rice', 'BBQ pork with rice', 7.00, 'Rice', NULL, 0),
    (116, 4, 'Soy Sauce Egg', 'Braised egg in savory soy sauce', 1.50, 'Snacks', NULL, 1),
    (117, 4, 'Chinese Tea', 'Traditional hot Chinese tea', 1.50, 'Drinks', NULL, 1),

    (118, 5, 'Vegetarian Fried Rice', 'Fried rice with mixed vegetables and mock meat', 5.00, 'Vegetarian', NULL, 1),
    (119, 5, 'Tofu & Vegetable Soup', 'Light soup with tofu and seasonal greens', 4.50, 'Vegetarian', NULL, 1),
    (120, 5, 'Mock Meat Noodles', 'Stir-fried noodles with plant-based protein', 5.50, 'Vegetarian', NULL, 1),
    (121, 5, 'Herbal Tea', 'House-brewed herbal tea', 2.00, 'Drinks', NULL, 1),

    (122, 6, 'Brown Sugar Boba Milk', 'Fresh milk with brown sugar pearls', 6.00, 'Drinks', NULL, 1),
    (123, 6, 'Taro Milk Tea', 'Creamy taro flavored milk tea', 5.50, 'Drinks', NULL, 1),
    (124, 6, 'Fresh Lime Juice', 'Freshly squeezed lime juice', 3.50, 'Drinks', NULL, 1),
    (125, 6, 'Crispy Popcorn Chicken', 'Bite-sized fried chicken snack', 5.00, 'Snacks', NULL, 1),
    (126, 6, 'French Fries', 'Golden crispy fries with seasoning', 4.00, 'Snacks', NULL, 1),

    (127, 7, 'Roti Canai', 'Flaky flatbread served with dhal & curry', 2.00, 'Snacks', NULL, 1),
    (128, 7, 'Roti Telur', 'Roti canai with egg', 2.50, 'Snacks', NULL, 1),
    (129, 7, 'Nasi Kandar', 'Steamed rice with mixed curries & sides', 7.00, 'Rice', NULL, 1),
    (130, 7, 'Teh Tarik', 'Frothy pulled milk tea', 2.20, 'Drinks', NULL, 1),
    (131, 7, 'Mee Goreng Basah', 'Wet-style fried noodles with gravy', 5.50, 'Noodles', NULL, 1),

    (132, 8, 'Chicken Katsu Bento', 'Crispy chicken cutlet with rice & pickles', 10.00, 'Japanese', NULL, 1),
    (133, 8, 'Salmon Teriyaki Bento', 'Grilled salmon glazed with teriyaki sauce', 12.50, 'Japanese', NULL, 1),
    (134, 8, 'California Roll (8pcs)', 'Crab stick, cucumber & avocado sushi roll', 8.00, 'Japanese', NULL, 1),
    (135, 8, 'Miso Soup', 'Traditional Japanese soybean soup', 3.00, 'Japanese', NULL, 1),
    (136, 8, 'Green Tea', 'Hot or cold Japanese green tea', 2.50, 'Drinks', NULL, 1),

    (137, 9, 'Chocolate Croissant', 'Buttery croissant filled with chocolate', 3.50, 'Dessert', NULL, 1),
    (138, 9, 'Chocolate Chip Cookie', 'Freshly baked cookie', 2.00, 'Dessert', NULL, 1),
    (139, 9, 'Red Velvet Cupcake', 'Moist cupcake with cream cheese frosting', 4.00, 'Dessert', NULL, 1),
    (140, 9, 'Iced Caramel Latte', 'Chilled espresso with caramel and milk', 5.00, 'Drinks', NULL, 1),
    (141, 9, 'Banana Bread Slice', 'Homemade moist banana bread', 3.00, 'Dessert', NULL, 0);

INSERT INTO reviews (id, user_id, vendor_id, rating, comment) VALUES
    (1, 3, 1, 5, 'Best nasi lemak on campus, fast pickup!'),
    (2, 3, 1, 4, 'Great food, queue moves quick with pre-order.'),

    (3, 3, 2, 5, 'Mee bandung here is fire, generous prawns!'),
    (4, 4, 2, 4, 'Good portion, a bit too spicy for me.'),

    (5, 7, 3, 5, 'Chicken chop was perfectly grilled.'),
    (6, 3, 3, 4, 'Great value western food on campus.'),

    (7, 4, 4, 5, 'Best chicken rice near Arkib, always fresh.'),
    (8, 7, 4, 3, 'Good but queue gets long during lunch.'),

    (9, 3, 5, 5, 'Finally a solid vegetarian option on campus!'),
    (10, 4, 5, 4, 'Tasty and healthy, will come back.'),

    (11, 7, 6, 5, 'Boba is legit, brown sugar pearls are soft.'),
    (12, 3, 6, 4, 'Great for a quick drink between classes.'),

    (13, 4, 7, 5, 'Roti canai as good as mamak outside campus.'),
    (14, 7, 7, 4, 'Teh tarik is solid, open late which helps.'),

    (15, 3, 8, 5, 'Bento sets are surprisingly authentic!'),
    (16, 4, 8, 4, 'A bit pricey but good quality.'),

    (17, 7, 9, 5, 'Cupcakes are so moist, perfect study snack.'),
    (18, 3, 9, 4, 'Croissant is great, would love more variety.');
