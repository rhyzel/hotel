CREATE TABLE housekeeping_tasks (
  task_id INT AUTO_INCREMENT PRIMARY KEY,
  room_id INT NOT NULL,
  assigned_to VARCHAR(100) NOT NULL,
  assigned_by VARCHAR(100) NOT NULL,
  task_status ENUM('assigned','in progress','completed') DEFAULT 'assigned',
  assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  start_time TIMESTAMP NULL,
  end_time TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS maintenance_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    issue_description TEXT NOT NULL,
    priority ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Medium',
    status VARCHAR(100),
    requested_by VARCHAR(100) NOT NULL,
    requester_staff_id VARCHAR(20) NULL,
    assigned_staff_id VARCHAR(20) NULL,
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    assigned_to VARCHAR(100) NULL,
    completed_at TIMESTAMP NULL,
    notes TEXT NULL
);

CREATE TABLE IF NOT EXISTS hp_tasks_items (
    hp_items_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity_needed INT NOT NULL DEFAULT 0,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES housekeeping_tasks(task_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES hp_inventory(item_id) ON DELETE CASCADE
);

CREATE TABLE room_items (
    room_id INT NOT NULL,
    item_id VARCHAR(50) DEFAULT 'System',
    item_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    status ENUM('clean', 'dirty') DEFAULT 'clean',
    PRIMARY KEY (room_id, item_name)
);

-- Dummy data for inventory
INSERT INTO inventory (item_name, category, quantity_in_stock, unit_price, unit) VALUES
('Toilet Paper Rolls', 'Toiletries', 100, 0.00, 'rolls'),
('Bath Towels', 'Laundry & Linen', 50, 0.00, 'pcs'),
('All-Purpose Cleaner', 'Cleaning & Sanitation', 30, 0.00, 'bottles'),
('Hand Soap Dispensers', 'Toiletries', 20, 0.00, 'pcs'),
('Bed Sheets', 'Laundry & Linen', 40, 0.00, 'pcs'),
('Shampoo Bottles', 'Toiletries', 25, 0.00, 'bottles'),
('Vacuum Cleaner Bags', 'Hotel Supplies', 15, 0.00, 'pcs'),
('Pillows', 'Laundry & Linen', 35, 0.00, 'pcs'),
('Blankets', 'Laundry & Linen', 40, 0.00, 'pcs'),
('Floor Wax', 'Cleaning & Sanitation', 10, 0.00, 'cans'),
('Mats', 'Hotel Supplies', 60, 0.00, 'pcs'),
('Curtains', 'Hotel Supplies', 12, 0.00, 'pcs');

-- Additional inventory items
INSERT INTO inventory (item_name, category, quantity_in_stock, unit_price, unit) VALUES
('Laundry Detergent', 'Cleaning & Sanitation', 50, 5.00, 'bottles'),
('Fabric Softener', 'Cleaning & Sanitation', 30, 4.00, 'bottles'),
('Bleach', 'Cleaning & Sanitation', 20, 3.00, 'bottles'),
('Starch Spray', 'Cleaning & Sanitation', 25, 2.50, 'cans'),

CREATE TABLE IF NOT EXISTS hp_inventory (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    status ENUM('clean', 'dirty', 'other') DEFAULT 'clean',
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE hp_inventory AUTO_INCREMENT = 8211;

-- Dummy data for hp_inventory
INSERT INTO hp_inventory (item_name, quantity, status) VALUES
('Toilet Paper Rolls', 100, 'other'),
('Bath Towels', 50, 'clean'),
('All-Purpose Cleaner', 30, 'other'),
('Hand Soap Dispensers', 20, 'other'),
('Bed Sheets', 40, 'clean'),
('Shampoo Bottles', 25, 'other'),
('Vacuum Cleaner Bags', 15, 'other'),
('Pillows', 35, 'clean'),
('Blankets', 40, 'clean'),
('Floor Wax', 10, 'other'),
('Mats', 60, 'clean'),
('Curtains', 12, 'clean'),
('Laundry Detergent', 50, 'other'),
('Fabric Softener', 30, 'other'),
('Bleach', 20, 'other'),
('Starch Spray', 25, 'other');

CREATE TABLE room_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    status ENUM('clean', 'dirty') DEFAULT 'clean',
    UNIQUE KEY (room_id, item_name)
);

ALTER TABLE room_items AUTO_INCREMENT = 9211;

-- Insert room_items for Single Rooms (room_type = 'Single Room')
INSERT INTO room_items (room_id, item_name, quantity, status) VALUES
(1, 'Bath Towels', 2, 'clean'), (1, 'Bed Sheets', 1, 'clean'), (1, 'Pillows', 2, 'clean'), (1, 'Mats', 2, 'clean'), (1, 'Curtains', 1, 'clean'), (1, 'Blankets', 1, 'clean'),
(8, 'Bath Towels', 2, 'clean'), (8, 'Bed Sheets', 1, 'clean'), (8, 'Pillows', 2, 'clean'), (8, 'Mats', 2, 'clean'), (8, 'Curtains', 1, 'clean'), (8, 'Blankets', 1, 'clean'),
(11, 'Bath Towels', 2, 'clean'), (11, 'Bed Sheets', 1, 'clean'), (11, 'Pillows', 2, 'clean'), (11, 'Mats', 2, 'clean'), (11, 'Curtains', 1, 'clean'), (11, 'Blankets', 1, 'clean'),
(18, 'Bath Towels', 2, 'clean'), (18, 'Bed Sheets', 1, 'clean'), (18, 'Pillows', 2, 'clean'), (18, 'Mats', 2, 'clean'), (18, 'Curtains', 1, 'clean'), (18, 'Blankets', 1, 'clean'),
(21, 'Bath Towels', 2, 'clean'), (21, 'Bed Sheets', 1, 'clean'), (21, 'Pillows', 2, 'clean'), (21, 'Mats', 2, 'clean'), (21, 'Curtains', 1, 'clean'), (21, 'Blankets', 1, 'clean'),
(28, 'Bath Towels', 2, 'clean'), (28, 'Bed Sheets', 1, 'clean'), (28, 'Pillows', 2, 'clean'), (28, 'Mats', 2, 'clean'), (28, 'Curtains', 1, 'clean'), (28, 'Blankets', 1, 'clean'),
(31, 'Bath Towels', 2, 'clean'), (31, 'Bed Sheets', 1, 'clean'), (31, 'Pillows', 2, 'clean'), (31, 'Mats', 2, 'clean'), (31, 'Curtains', 1, 'clean'), (31, 'Blankets', 1, 'clean'),
(38, 'Bath Towels', 2, 'clean'), (38, 'Bed Sheets', 1, 'clean'), (38, 'Pillows', 2, 'clean'), (38, 'Mats', 2, 'clean'), (38, 'Curtains', 1, 'clean'), (38, 'Blankets', 1, 'clean'),
(41, 'Bath Towels', 2, 'clean'), (41, 'Bed Sheets', 1, 'clean'), (41, 'Pillows', 2, 'clean'), (41, 'Mats', 2, 'clean'), (41, 'Curtains', 1, 'clean'), (41, 'Blankets', 1, 'clean'),
(48, 'Bath Towels', 2, 'clean'), (48, 'Bed Sheets', 1, 'clean'), (48, 'Pillows', 2, 'clean'), (48, 'Mats', 2, 'clean'), (48, 'Curtains', 1, 'clean'), (48, 'Blankets', 1, 'clean'),
(56, 'Bath Towels', 2, 'clean'), (56, 'Bed Sheets', 1, 'clean'), (56, 'Pillows', 2, 'clean'), (56, 'Mats', 2, 'clean'), (56, 'Curtains', 1, 'clean'), (56, 'Blankets', 1, 'clean'),
(61, 'Bath Towels', 2, 'clean'), (61, 'Bed Sheets', 1, 'clean'), (61, 'Pillows', 2, 'clean'), (61, 'Mats', 2, 'clean'), (61, 'Curtains', 1, 'clean'), (61, 'Blankets', 1, 'clean'),
(68, 'Bath Towels', 2, 'clean'), (68, 'Bed Sheets', 1, 'clean'), (68, 'Pillows', 2, 'clean'), (68, 'Mats', 2, 'clean'), (68, 'Curtains', 1, 'clean'), (68, 'Blankets', 1, 'clean'),
(75, 'Bath Towels', 2, 'clean'), (75, 'Bed Sheets', 1, 'clean'), (75, 'Pillows', 2, 'clean'), (75, 'Mats', 2, 'clean'), (75, 'Curtains', 1, 'clean'), (75, 'Blankets', 1, 'clean'),
(82, 'Bath Towels', 2, 'clean'), (82, 'Bed Sheets', 1, 'clean'), (82, 'Pillows', 2, 'clean'), (82, 'Mats', 2, 'clean'), (82, 'Curtains', 1, 'clean'), (82, 'Blankets', 1, 'clean'),
(89, 'Bath Towels', 2, 'clean'), (89, 'Bed Sheets', 1, 'clean'), (89, 'Pillows', 2, 'clean'), (89, 'Mats', 2, 'clean'), (89, 'Curtains', 1, 'clean'), (89, 'Blankets', 1, 'clean'),
(96, 'Bath Towels', 2, 'clean'), (96, 'Bed Sheets', 1, 'clean'), (96, 'Pillows', 2, 'clean'), (96, 'Mats', 2, 'clean'), (96, 'Curtains', 1, 'clean'), (96, 'Blankets', 1, 'clean');

-- Insert for Double Rooms
INSERT INTO room_items (room_id, item_name, quantity, status) VALUES
(2, 'Bath Towels', 3, 'clean'), (2, 'Bed Sheets', 2, 'clean'), (2, 'Pillows', 4, 'clean'), (2, 'Mats', 3, 'clean'), (2, 'Curtains', 2, 'clean'), (2, 'Blankets', 2, 'clean'),
(9, 'Bath Towels', 3, 'clean'), (9, 'Bed Sheets', 2, 'clean'), (9, 'Pillows', 4, 'clean'), (9, 'Mats', 3, 'clean'), (9, 'Curtains', 2, 'clean'), (9, 'Blankets', 2, 'clean'),
(12, 'Bath Towels', 3, 'clean'), (12, 'Bed Sheets', 2, 'clean'), (12, 'Pillows', 4, 'clean'), (12, 'Mats', 3, 'clean'), (12, 'Curtains', 2, 'clean'), (12, 'Blankets', 2, 'clean'),
(19, 'Bath Towels', 3, 'clean'), (19, 'Bed Sheets', 2, 'clean'), (19, 'Pillows', 4, 'clean'), (19, 'Mats', 3, 'clean'), (19, 'Curtains', 2, 'clean'), (19, 'Blankets', 2, 'clean'),
(22, 'Bath Towels', 3, 'clean'), (22, 'Bed Sheets', 2, 'clean'), (22, 'Pillows', 4, 'clean'), (22, 'Mats', 3, 'clean'), (22, 'Curtains', 2, 'clean'), (22, 'Blankets', 2, 'clean'),
(29, 'Bath Towels', 3, 'clean'), (29, 'Bed Sheets', 2, 'clean'), (29, 'Pillows', 4, 'clean'), (29, 'Mats', 3, 'clean'), (29, 'Curtains', 2, 'clean'), (29, 'Blankets', 2, 'clean'),
(32, 'Bath Towels', 3, 'clean'), (32, 'Bed Sheets', 2, 'clean'), (32, 'Pillows', 4, 'clean'), (32, 'Mats', 3, 'clean'), (32, 'Curtains', 2, 'clean'), (32, 'Blankets', 2, 'clean'),
(39, 'Bath Towels', 3, 'clean'), (39, 'Bed Sheets', 2, 'clean'), (39, 'Pillows', 4, 'clean'), (39, 'Mats', 3, 'clean'), (39, 'Curtains', 2, 'clean'), (39, 'Blankets', 2, 'clean'),
(42, 'Bath Towels', 3, 'clean'), (42, 'Bed Sheets', 2, 'clean'), (42, 'Pillows', 4, 'clean'), (42, 'Mats', 3, 'clean'), (42, 'Curtains', 2, 'clean'), (42, 'Blankets', 2, 'clean'),
(49, 'Bath Towels', 3, 'clean'), (49, 'Bed Sheets', 2, 'clean'), (49, 'Pillows', 4, 'clean'), (49, 'Mats', 3, 'clean'), (49, 'Curtains', 2, 'clean'), (49, 'Blankets', 2, 'clean'),
(57, 'Bath Towels', 3, 'clean'), (57, 'Bed Sheets', 2, 'clean'), (57, 'Pillows', 4, 'clean'), (57, 'Mats', 3, 'clean'), (57, 'Curtains', 2, 'clean'), (57, 'Blankets', 2, 'clean'),
(62, 'Bath Towels', 3, 'clean'), (62, 'Bed Sheets', 2, 'clean'), (62, 'Pillows', 4, 'clean'), (62, 'Mats', 3, 'clean'), (62, 'Curtains', 2, 'clean'), (62, 'Blankets', 2, 'clean'),
(69, 'Bath Towels', 3, 'clean'), (69, 'Bed Sheets', 2, 'clean'), (69, 'Pillows', 4, 'clean'), (69, 'Mats', 3, 'clean'), (69, 'Curtains', 2, 'clean'), (69, 'Blankets', 2, 'clean'),
(76, 'Bath Towels', 3, 'clean'), (76, 'Bed Sheets', 2, 'clean'), (76, 'Pillows', 4, 'clean'), (76, 'Mats', 3, 'clean'), (76, 'Curtains', 2, 'clean'), (76, 'Blankets', 2, 'clean'),
(83, 'Bath Towels', 3, 'clean'), (83, 'Bed Sheets', 2, 'clean'), (83, 'Pillows', 4, 'clean'), (83, 'Mats', 3, 'clean'), (83, 'Curtains', 2, 'clean'), (83, 'Blankets', 2, 'clean'),
(90, 'Bath Towels', 3, 'clean'), (90, 'Bed Sheets', 2, 'clean'), (90, 'Pillows', 4, 'clean'), (90, 'Mats', 3, 'clean'), (90, 'Curtains', 2, 'clean'), (90, 'Blankets', 2, 'clean'),
(97, 'Bath Towels', 3, 'clean'), (97, 'Bed Sheets', 2, 'clean'), (97, 'Pillows', 4, 'clean'), (97, 'Mats', 3, 'clean'), (97, 'Curtains', 2, 'clean'), (97, 'Blankets', 2, 'clean');

-- Insert for Twin Rooms (same as Double)
INSERT INTO room_items (room_id, item_name, quantity, status) VALUES
(3, 'Bath Towels', 4, 'clean'), (3, 'Bed Sheets', 2, 'clean'), (3, 'Pillows', 4, 'clean'), (3, 'Mats', 3, 'clean'), (3, 'Curtains', 3, 'clean'), (3, 'Blankets', 2, 'clean'),
(13, 'Bath Towels', 4, 'clean'), (13, 'Bed Sheets', 2, 'clean'), (13, 'Pillows', 4, 'clean'), (13, 'Mats', 3, 'clean'), (13, 'Curtains', 3, 'clean'), (13, 'Blankets', 2, 'clean'),
(23, 'Bath Towels', 4, 'clean'), (23, 'Bed Sheets', 2, 'clean'), (23, 'Pillows', 4, 'clean'), (23, 'Mats', 3, 'clean'), (23, 'Curtains', 3, 'clean'), (23, 'Blankets', 2, 'clean'),
(33, 'Bath Towels', 4, 'clean'), (33, 'Bed Sheets', 2, 'clean'), (33, 'Pillows', 4, 'clean'), (33, 'Mats', 3, 'clean'), (33, 'Curtains', 3, 'clean'), (33, 'Blankets', 2, 'clean'),
(43, 'Bath Towels', 4, 'clean'), (43, 'Bed Sheets', 2, 'clean'), (43, 'Pillows', 4, 'clean'), (43, 'Mats', 3, 'clean'), (43, 'Curtains', 3, 'clean'), (43, 'Blankets', 2, 'clean'),
(51, 'Bath Towels', 4, 'clean'), (51, 'Bed Sheets', 2, 'clean'), (51, 'Pillows', 4, 'clean'), (51, 'Mats', 3, 'clean'), (51, 'Curtains', 3, 'clean'), (51, 'Blankets', 2, 'clean'),
(58, 'Bath Towels', 4, 'clean'), (58, 'Bed Sheets', 2, 'clean'), (58, 'Pillows', 4, 'clean'), (58, 'Mats', 3, 'clean'), (58, 'Curtains', 3, 'clean'), (58, 'Blankets', 2, 'clean'),
(63, 'Bath Towels', 4, 'clean'), (63, 'Bed Sheets', 2, 'clean'), (63, 'Pillows', 4, 'clean'), (63, 'Mats', 3, 'clean'), (63, 'Curtains', 3, 'clean'), (63, 'Blankets', 2, 'clean'),
(70, 'Bath Towels', 4, 'clean'), (70, 'Bed Sheets', 2, 'clean'), (70, 'Pillows', 4, 'clean'), (70, 'Mats', 3, 'clean'), (70, 'Curtains', 3, 'clean'), (70, 'Blankets', 2, 'clean'),
(77, 'Bath Towels', 4, 'clean'), (77, 'Bed Sheets', 2, 'clean'), (77, 'Pillows', 4, 'clean'), (77, 'Mats', 3, 'clean'), (77, 'Curtains', 3, 'clean'), (77, 'Blankets', 2, 'clean'),
(84, 'Bath Towels', 4, 'clean'), (84, 'Bed Sheets', 2, 'clean'), (84, 'Pillows', 4, 'clean'), (84, 'Mats', 3, 'clean'), (84, 'Curtains', 3, 'clean'), (84, 'Blankets', 2, 'clean'),
(91, 'Bath Towels', 4, 'clean'), (91, 'Bed Sheets', 2, 'clean'), (91, 'Pillows', 4, 'clean'), (91, 'Mats', 3, 'clean'), (91, 'Curtains', 3, 'clean'), (91, 'Blankets', 2, 'clean');

-- Insert for Deluxe Rooms
INSERT INTO room_items (room_id, item_name, quantity, status) VALUES
(4, 'Bath Towels', 5, 'clean'), (4, 'Bed Sheets', 4, 'clean'), (4, 'Pillows', 5, 'clean'), (4, 'Mats', 4, 'clean'), (4, 'Curtains', 4, 'clean'), (4, 'Blankets', 3, 'clean'),
(10, 'Bath Towels', 5, 'clean'), (10, 'Bed Sheets', 4, 'clean'), (10, 'Pillows', 5, 'clean'), (10, 'Mats', 4, 'clean'), (10, 'Curtains', 4, 'clean'), (10, 'Blankets', 3, 'clean'),
(14, 'Bath Towels', 5, 'clean'), (14, 'Bed Sheets', 4, 'clean'), (14, 'Pillows', 5, 'clean'), (14, 'Mats', 4, 'clean'), (14, 'Curtains', 4, 'clean'), (14, 'Blankets', 3, 'clean'),
(20, 'Bath Towels', 5, 'clean'), (20, 'Bed Sheets', 4, 'clean'), (20, 'Pillows', 5, 'clean'), (20, 'Mats', 4, 'clean'), (20, 'Curtains', 4, 'clean'), (20, 'Blankets', 3, 'clean'),
(24, 'Bath Towels', 5, 'clean'), (24, 'Bed Sheets', 4, 'clean'), (24, 'Pillows', 5, 'clean'), (24, 'Mats', 4, 'clean'), (24, 'Curtains', 4, 'clean'), (24, 'Blankets', 3, 'clean'),
(30, 'Bath Towels', 5, 'clean'), (30, 'Bed Sheets', 4, 'clean'), (30, 'Pillows', 5, 'clean'), (30, 'Mats', 4, 'clean'), (30, 'Curtains', 4, 'clean'), (30, 'Blankets', 3, 'clean'),
(34, 'Bath Towels', 5, 'clean'), (34, 'Bed Sheets', 4, 'clean'), (34, 'Pillows', 5, 'clean'), (34, 'Mats', 4, 'clean'), (34, 'Curtains', 4, 'clean'), (34, 'Blankets', 3, 'clean'),
(40, 'Bath Towels', 5, 'clean'), (40, 'Bed Sheets', 4, 'clean'), (40, 'Pillows', 5, 'clean'), (40, 'Mats', 4, 'clean'), (40, 'Curtains', 4, 'clean'), (40, 'Blankets', 3, 'clean'),
(44, 'Bath Towels', 5, 'clean'), (44, 'Bed Sheets', 4, 'clean'), (44, 'Pillows', 5, 'clean'), (44, 'Mats', 4, 'clean'), (44, 'Curtains', 4, 'clean'), (44, 'Blankets', 3, 'clean'),
(50, 'Bath Towels', 5, 'clean'), (50, 'Bed Sheets', 4, 'clean'), (50, 'Pillows', 5, 'clean'), (50, 'Mats', 4, 'clean'), (50, 'Curtains', 4, 'clean'), (50, 'Blankets', 3, 'clean'),
(52, 'Bath Towels', 5, 'clean'), (52, 'Bed Sheets', 4, 'clean'), (52, 'Pillows', 5, 'clean'), (52, 'Mats', 4, 'clean'), (52, 'Curtains', 4, 'clean'), (52, 'Blankets', 3, 'clean'),
(59, 'Bath Towels', 5, 'clean'), (59, 'Bed Sheets', 4, 'clean'), (59, 'Pillows', 5, 'clean'), (59, 'Mats', 4, 'clean'), (59, 'Curtains', 4, 'clean'), (59, 'Blankets', 3, 'clean'),
(64, 'Bath Towels', 5, 'clean'), (64, 'Bed Sheets', 4, 'clean'), (64, 'Pillows', 5, 'clean'), (64, 'Mats', 4, 'clean'), (64, 'Curtains', 4, 'clean'), (64, 'Blankets', 3, 'clean'),
(71, 'Bath Towels', 5, 'clean'), (71, 'Bed Sheets', 4, 'clean'), (71, 'Pillows', 5, 'clean'), (71, 'Mats', 4, 'clean'), (71, 'Curtains', 4, 'clean'), (71, 'Blankets', 3, 'clean'),
(78, 'Bath Towels', 5, 'clean'), (78, 'Bed Sheets', 4, 'clean'), (78, 'Pillows', 5, 'clean'), (78, 'Mats', 4, 'clean'), (78, 'Curtains', 4, 'clean'), (78, 'Blankets', 3, 'clean'),
(85, 'Bath Towels', 5, 'clean'), (85, 'Bed Sheets', 4, 'clean'), (85, 'Pillows', 5, 'clean'), (85, 'Mats', 4, 'clean'), (85, 'Curtains', 4, 'clean'), (85, 'Blankets', 3, 'clean'),
(92, 'Bath Towels', 5, 'clean'), (92, 'Bed Sheets', 4, 'clean'), (92, 'Pillows', 5, 'clean'), (92, 'Mats', 4, 'clean'), (92, 'Curtains', 4, 'clean'), (92, 'Blankets', 3, 'clean'),
(98, 'Bath Towels', 5, 'clean'), (98, 'Bed Sheets', 4, 'clean'), (98, 'Pillows', 5, 'clean'), (98, 'Mats', 4, 'clean'), (98, 'Curtains', 4, 'clean'), (98, 'Blankets', 3, 'clean');

-- Insert for Suites
INSERT INTO room_items (room_id, item_name, quantity, status) VALUES
(5, 'Bath Towels', 7, 'clean'), (5, 'Bed Sheets', 5, 'clean'), (5, 'Pillows', 7, 'clean'), (5, 'Mats', 5, 'clean'), (5, 'Curtains', 5, 'clean'), (5, 'Blankets', 4, 'clean'),
(15, 'Bath Towels', 7, 'clean'), (15, 'Bed Sheets', 5, 'clean'), (15, 'Pillows', 7, 'clean'), (15, 'Mats', 5, 'clean'), (15, 'Curtains', 5, 'clean'), (15, 'Blankets', 4, 'clean'),
(25, 'Bath Towels', 7, 'clean'), (25, 'Bed Sheets', 5, 'clean'), (25, 'Pillows', 7, 'clean'), (25, 'Mats', 5, 'clean'), (25, 'Curtains', 5, 'clean'), (25, 'Blankets', 4, 'clean'),
(35, 'Bath Towels', 7, 'clean'), (35, 'Bed Sheets', 5, 'clean'), (35, 'Pillows', 7, 'clean'), (35, 'Mats', 5, 'clean'), (35, 'Curtains', 5, 'clean'), (35, 'Blankets', 4, 'clean'),
(45, 'Bath Towels', 7, 'clean'), (45, 'Bed Sheets', 5, 'clean'), (45, 'Pillows', 7, 'clean'), (45, 'Mats', 5, 'clean'), (45, 'Curtains', 5, 'clean'), (45, 'Blankets', 4, 'clean'),
(53, 'Bath Towels', 7, 'clean'), (53, 'Bed Sheets', 5, 'clean'), (53, 'Pillows', 7, 'clean'), (53, 'Mats', 5, 'clean'), (53, 'Curtains', 5, 'clean'), (53, 'Blankets', 4, 'clean'),
(60, 'Bath Towels', 7, 'clean'), (60, 'Bed Sheets', 5, 'clean'), (60, 'Pillows', 7, 'clean'), (60, 'Mats', 5, 'clean'), (60, 'Curtains', 5, 'clean'), (60, 'Blankets', 4, 'clean'),
(65, 'Bath Towels', 7, 'clean'), (65, 'Bed Sheets', 5, 'clean'), (65, 'Pillows', 7, 'clean'), (65, 'Mats', 5, 'clean'), (65, 'Curtains', 5, 'clean'), (65, 'Blankets', 4, 'clean'),
(72, 'Bath Towels', 7, 'clean'), (72, 'Bed Sheets', 5, 'clean'), (72, 'Pillows', 7, 'clean'), (72, 'Mats', 5, 'clean'), (72, 'Curtains', 5, 'clean'), (72, 'Blankets', 4, 'clean'),
(79, 'Bath Towels', 7, 'clean'), (79, 'Bed Sheets', 5, 'clean'), (79, 'Pillows', 7, 'clean'), (79, 'Mats', 5, 'clean'), (79, 'Curtains', 5, 'clean'), (79, 'Blankets', 4, 'clean'),
(86, 'Bath Towels', 7, 'clean'), (86, 'Bed Sheets', 5, 'clean'), (86, 'Pillows', 7, 'clean'), (86, 'Mats', 5, 'clean'), (86, 'Curtains', 5, 'clean'), (86, 'Blankets', 4, 'clean'),
(93, 'Bath Towels', 7, 'clean'), (93, 'Bed Sheets', 5, 'clean'), (93, 'Pillows', 7, 'clean'), (93, 'Mats', 5, 'clean'), (93, 'Curtains', 5, 'clean'), (93, 'Blankets', 4, 'clean'),
(99, 'Bath Towels', 7, 'clean'), (99, 'Bed Sheets', 5, 'clean'), (99, 'Pillows', 7, 'clean'), (99, 'Mats', 5, 'clean'), (99, 'Curtains', 5, 'clean'), (99, 'Blankets', 4, 'clean');

-- Insert for Family Rooms
INSERT INTO room_items (room_id, item_name, quantity, status) VALUES
(6, 'Bath Towels', 7, 'clean'), (6, 'Bed Sheets', 6, 'clean'), (6, 'Pillows', 7, 'clean'), (6, 'Mats', 5, 'clean'), (6, 'Curtains', 5, 'clean'), (6, 'Blankets', 4, 'clean'),
(16, 'Bath Towels', 7, 'clean'), (16, 'Bed Sheets', 6, 'clean'), (16, 'Pillows', 7, 'clean'), (16, 'Mats', 5, 'clean'), (16, 'Curtains', 5, 'clean'), (16, 'Blankets', 4, 'clean'),
(26, 'Bath Towels', 7, 'clean'), (26, 'Bed Sheets', 6, 'clean'), (26, 'Pillows', 7, 'clean'), (26, 'Mats', 5, 'clean'), (26, 'Curtains', 5, 'clean'), (26, 'Blankets', 4, 'clean'),
(36, 'Bath Towels', 7, 'clean'), (36, 'Bed Sheets', 6, 'clean'), (36, 'Pillows', 7, 'clean'), (36, 'Mats', 5, 'clean'), (36, 'Curtains', 5, 'clean'), (36, 'Blankets', 4, 'clean'),
(46, 'Bath Towels', 7, 'clean'), (46, 'Bed Sheets', 6, 'clean'), (46, 'Pillows', 7, 'clean'), (46, 'Mats', 5, 'clean'), (46, 'Curtains', 5, 'clean'), (46, 'Blankets', 4, 'clean'),
(54, 'Bath Towels', 7, 'clean'), (54, 'Bed Sheets', 6, 'clean'), (54, 'Pillows', 7, 'clean'), (54, 'Mats', 5, 'clean'), (54, 'Curtains', 5, 'clean'), (54, 'Blankets', 4, 'clean'),
(66, 'Bath Towels', 7, 'clean'), (66, 'Bed Sheets', 6, 'clean'), (66, 'Pillows', 7, 'clean'), (66, 'Mats', 5, 'clean'), (66, 'Curtains', 5, 'clean'), (66, 'Blankets', 4, 'clean'),
(73, 'Bath Towels', 7, 'clean'), (73, 'Bed Sheets', 6, 'clean'), (73, 'Pillows', 7, 'clean'), (73, 'Mats', 5, 'clean'), (73, 'Curtains', 5, 'clean'), (73, 'Blankets', 4, 'clean'),
(80, 'Bath Towels', 7, 'clean'), (80, 'Bed Sheets', 6, 'clean'), (80, 'Pillows', 7, 'clean'), (80, 'Mats', 5, 'clean'), (80, 'Curtains', 5, 'clean'), (80, 'Blankets', 4, 'clean'),
(87, 'Bath Towels', 7, 'clean'), (87, 'Bed Sheets', 6, 'clean'), (87, 'Pillows', 7, 'clean'), (87, 'Mats', 5, 'clean'), (87, 'Curtains', 5, 'clean'), (87, 'Blankets', 4, 'clean'),
(94, 'Bath Towels', 7, 'clean'), (94, 'Bed Sheets', 6, 'clean'), (94, 'Pillows', 7, 'clean'), (94, 'Mats', 5, 'clean'), (94, 'Curtains', 5, 'clean'), (94, 'Blankets', 4, 'clean'),
(100, 'Bath Towels', 7, 'clean'), (100, 'Bed Sheets', 6, 'clean'), (100, 'Pillows', 7, 'clean'), (100, 'Mats', 5, 'clean'), (100, 'Curtains', 5, 'clean'), (100, 'Blankets', 4, 'clean');

-- Insert for VIP Rooms
INSERT INTO room_items (room_id, item_name, quantity, status) VALUES
(7, 'Bath Towels', 9, 'clean'), (7, 'Bed Sheets', 5, 'clean'), (7, 'Pillows', 9, 'clean'), (7, 'Mats', 5, 'clean'), (7, 'Curtains', 6, 'clean'), (7, 'Blankets', 5, 'clean'),
(17, 'Bath Towels', 9, 'clean'), (17, 'Bed Sheets', 5, 'clean'), (17, 'Pillows', 9, 'clean'), (17, 'Mats', 5, 'clean'), (17, 'Curtains', 6, 'clean'), (17, 'Blankets', 5, 'clean'),
(27, 'Bath Towels', 9, 'clean'), (27, 'Bed Sheets', 5, 'clean'), (27, 'Pillows', 9, 'clean'), (27, 'Mats', 5, 'clean'), (27, 'Curtains', 6, 'clean'), (27, 'Blankets', 5, 'clean'),
(37, 'Bath Towels', 9, 'clean'), (37, 'Bed Sheets', 5, 'clean'), (37, 'Pillows', 9, 'clean'), (37, 'Mats', 5, 'clean'), (37, 'Curtains', 6, 'clean'), (37, 'Blankets', 5, 'clean'),
(47, 'Bath Towels', 9, 'clean'), (47, 'Bed Sheets', 5, 'clean'), (47, 'Pillows', 9, 'clean'), (47, 'Mats', 5, 'clean'), (47, 'Curtains', 6, 'clean'), (47, 'Blankets', 5, 'clean'),
(55, 'Bath Towels', 9, 'clean'), (55, 'Bed Sheets', 5, 'clean'), (55, 'Pillows', 9, 'clean'), (55, 'Mats', 5, 'clean'), (55, 'Curtains', 6, 'clean'), (55, 'Blankets', 5, 'clean'),
(67, 'Bath Towels', 9, 'clean'), (67, 'Bed Sheets', 5, 'clean'), (67, 'Pillows', 9, 'clean'), (67, 'Mats', 5, 'clean'), (67, 'Curtains', 6, 'clean'), (67, 'Blankets', 5, 'clean'),
(74, 'Bath Towels', 9, 'clean'), (74, 'Bed Sheets', 5, 'clean'), (74, 'Pillows', 9, 'clean'), (74, 'Mats', 5, 'clean'), (74, 'Curtains', 6, 'clean'), (74, 'Blankets', 5, 'clean'),
(81, 'Bath Towels', 9, 'clean'), (81, 'Bed Sheets', 5, 'clean'), (81, 'Pillows', 9, 'clean'), (81, 'Mats', 5, 'clean'), (81, 'Curtains', 6, 'clean'), (81, 'Blankets', 5, 'clean'),
(88, 'Bath Towels', 9, 'clean'), (88, 'Bed Sheets', 5, 'clean'), (88, 'Pillows', 9, 'clean'), (88, 'Mats', 5, 'clean'), (88, 'Curtains', 6, 'clean'), (88, 'Blankets', 5, 'clean'),
(95, 'Bath Towels', 9, 'clean'), (95, 'Bed Sheets', 5, 'clean'), (95, 'Pillows', 9, 'clean'), (95, 'Mats', 5, 'clean'), (95, 'Curtains', 6, 'clean'), (95, 'Blankets', 5, 'clean');

-- Insert blankets for all rooms
INSERT INTO room_items (room_id, item_name, quantity, status) VALUES
-- Single Rooms: 1 Blanket
(1, 'Blankets', 1, 'clean'), (8, 'Blankets', 1, 'clean'), (11, 'Blankets', 1, 'clean'), (18, 'Blankets', 1, 'clean'), (21, 'Blankets', 1, 'clean'),
(28, 'Blankets', 1, 'clean'), (31, 'Blankets', 1, 'clean'), (38, 'Blankets', 1, 'clean'), (41, 'Blankets', 1, 'clean'), (48, 'Blankets', 1, 'clean'),
(56, 'Blankets', 1, 'clean'), (61, 'Blankets', 1, 'clean'), (68, 'Blankets', 1, 'clean'), (75, 'Blankets', 1, 'clean'), (82, 'Blankets', 1, 'clean'),
(89, 'Blankets', 1, 'clean'), (96, 'Blankets', 1, 'clean'),
-- Double/Twin Rooms: 2 Blankets
(2, 'Blankets', 2, 'clean'), (3, 'Blankets', 2, 'clean'), (9, 'Blankets', 2, 'clean'), (12, 'Blankets', 2, 'clean'), (13, 'Blankets', 2, 'clean'),
(19, 'Blankets', 2, 'clean'), (22, 'Blankets', 2, 'clean'), (23, 'Blankets', 2, 'clean'), (29, 'Blankets', 2, 'clean'), (32, 'Blankets', 2, 'clean'),
(33, 'Blankets', 2, 'clean'), (39, 'Blankets', 2, 'clean'), (42, 'Blankets', 2, 'clean'), (43, 'Blankets', 2, 'clean'), (49, 'Blankets', 2, 'clean'),
(51, 'Blankets', 2, 'clean'), (57, 'Blankets', 2, 'clean'), (58, 'Blankets', 2, 'clean'), (62, 'Blankets', 2, 'clean'), (63, 'Blankets', 2, 'clean'),
(69, 'Blankets', 2, 'clean'), (70, 'Blankets', 2, 'clean'), (76, 'Blankets', 2, 'clean'), (77, 'Blankets', 2, 'clean'), (83, 'Blankets', 2, 'clean'),
(84, 'Blankets', 2, 'clean'), (90, 'Blankets', 2, 'clean'), (91, 'Blankets', 2, 'clean'), (97, 'Blankets', 2, 'clean'),
-- Deluxe Rooms: 3 Blankets
(4, 'Blankets', 3, 'clean'), (10, 'Blankets', 3, 'clean'), (14, 'Blankets', 3, 'clean'), (20, 'Blankets', 3, 'clean'), (24, 'Blankets', 3, 'clean'),
(30, 'Blankets', 3, 'clean'), (34, 'Blankets', 3, 'clean'), (40, 'Blankets', 3, 'clean'), (44, 'Blankets', 3, 'clean'), (50, 'Blankets', 3, 'clean'),
(52, 'Blankets', 3, 'clean'), (59, 'Blankets', 3, 'clean'), (64, 'Blankets', 3, 'clean'), (71, 'Blankets', 3, 'clean'), (78, 'Blankets', 3, 'clean'),
(85, 'Blankets', 3, 'clean'), (92, 'Blankets', 3, 'clean'), (98, 'Blankets', 3, 'clean'),
-- Suite Rooms: 4 Blankets
(5, 'Blankets', 4, 'clean'), (15, 'Blankets', 4, 'clean'), (25, 'Blankets', 4, 'clean'), (35, 'Blankets', 4, 'clean'), (45, 'Blankets', 4, 'clean'),
(53, 'Blankets', 4, 'clean'), (60, 'Blankets', 4, 'clean'), (65, 'Blankets', 4, 'clean'), (72, 'Blankets', 4, 'clean'), (79, 'Blankets', 4, 'clean'),
(86, 'Blankets', 4, 'clean'), (93, 'Blankets', 4, 'clean'), (99, 'Blankets', 4, 'clean'),
-- VIP Rooms: 5 Blankets
(7, 'Blankets', 5, 'clean'), (17, 'Blankets', 5, 'clean'), (27, 'Blankets', 5, 'clean'), (37, 'Blankets', 5, 'clean'), (47, 'Blankets', 5, 'clean'),
(55, 'Blankets', 5, 'clean'), (67, 'Blankets', 5, 'clean'), (74, 'Blankets', 5, 'clean'), (81, 'Blankets', 5, 'clean'), (88, 'Blankets', 5, 'clean'),
(95, 'Blankets', 5, 'clean'),
-- Family Rooms: 4 Blankets
(6, 'Blankets', 4, 'clean'), (16, 'Blankets', 4, 'clean'), (26, 'Blankets', 4, 'clean'), (36, 'Blankets', 4, 'clean'), (46, 'Blankets', 4, 'clean'),
(54, 'Blankets', 4, 'clean'), (66, 'Blankets', 4, 'clean'), (73, 'Blankets', 4, 'clean'), (80, 'Blankets', 4, 'clean'), (87, 'Blankets', 4, 'clean'),
(94, 'Blankets', 4, 'clean'), (100, 'Blankets', 4, 'clean');
