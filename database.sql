CREATE DATABASE IF NOT EXISTS gym_db;
USE gym_db;

CREATE USER IF NOT EXISTS 'gym_user'@'localhost' IDENTIFIED BY 'J8!vQ2#nR5@xL9p';
ALTER USER 'gym_user'@'localhost' IDENTIFIED BY 'J8!vQ2#nR5@xL9p';
GRANT ALL PRIVILEGES ON gym_db.* TO 'gym_user'@'localhost';

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration VARCHAR(20) NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NULL,
    class_name VARCHAR(100) NOT NULL,
    branch VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    end_date DATE NULL,
    exercise_type VARCHAR(50) NOT NULL DEFAULT 'General Fitness',
    months_paid INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_method VARCHAR(50) NOT NULL DEFAULT 'Chapa',
    payment_status VARCHAR(20) NOT NULL DEFAULT 'pending',
    payment_reference CHAR(20) NULL,
    payment_transaction_code VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_bookings_plan_id (plan_id),
    UNIQUE KEY uq_bookings_payment_reference (payment_reference),
    CONSTRAINT fk_bookings_plan FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

SET @plan_id_column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bookings' AND COLUMN_NAME = 'plan_id'
);
SET @plan_id_column_migration = IF(
    @plan_id_column_exists = 0,
    'ALTER TABLE bookings ADD COLUMN plan_id INT NULL AFTER user_id',
    'SELECT 1'
);
PREPARE plan_id_column_stmt FROM @plan_id_column_migration;
EXECUTE plan_id_column_stmt;
DEALLOCATE PREPARE plan_id_column_stmt;

SET @plan_id_index_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bookings' AND INDEX_NAME = 'idx_bookings_plan_id'
);
SET @plan_id_index_migration = IF(
    @plan_id_index_exists = 0,
    'ALTER TABLE bookings ADD INDEX idx_bookings_plan_id (plan_id)',
    'SELECT 1'
);
PREPARE plan_id_index_stmt FROM @plan_id_index_migration;
EXECUTE plan_id_index_stmt;
DEALLOCATE PREPARE plan_id_index_stmt;

SET @plan_id_foreign_key_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bookings' AND CONSTRAINT_NAME = 'fk_bookings_plan'
);
SET @plan_id_foreign_key_migration = IF(
    @plan_id_foreign_key_exists = 0,
    'ALTER TABLE bookings ADD CONSTRAINT fk_bookings_plan FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE RESTRICT',
    'SELECT 1'
);
PREPARE plan_id_foreign_key_stmt FROM @plan_id_foreign_key_migration;
EXECUTE plan_id_foreign_key_stmt;
DEALLOCATE PREPARE plan_id_foreign_key_stmt;

SET @branch_column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bookings' AND COLUMN_NAME = 'branch'
);
SET @branch_migration = IF(
    @branch_column_exists = 0,
    'ALTER TABLE bookings ADD COLUMN branch VARCHAR(100) NOT NULL DEFAULT ''Bole Atlas'' AFTER class_name',
    'SELECT 1'
);
PREPARE branch_migration_stmt FROM @branch_migration;
EXECUTE branch_migration_stmt;
DEALLOCATE PREPARE branch_migration_stmt;

SET @payment_reference_column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bookings' AND COLUMN_NAME = 'payment_reference'
);
SET @payment_reference_column_migration = IF(
    @payment_reference_column_exists = 0,
    'ALTER TABLE bookings ADD COLUMN payment_reference CHAR(20) NULL AFTER payment_status',
    'SELECT 1'
);
PREPARE payment_reference_column_stmt FROM @payment_reference_column_migration;
EXECUTE payment_reference_column_stmt;
DEALLOCATE PREPARE payment_reference_column_stmt;

SET @payment_reference_index_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bookings' AND INDEX_NAME = 'uq_bookings_payment_reference'
);
SET @payment_reference_index_migration = IF(
    @payment_reference_index_exists = 0,
    'ALTER TABLE bookings ADD UNIQUE INDEX uq_bookings_payment_reference (payment_reference)',
    'SELECT 1'
);
PREPARE payment_reference_index_stmt FROM @payment_reference_index_migration;
EXECUTE payment_reference_index_stmt;
DEALLOCATE PREPARE payment_reference_index_stmt;

SET @payment_transaction_code_column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bookings' AND COLUMN_NAME = 'payment_transaction_code'
);
SET @payment_transaction_code_migration = IF(
    @payment_transaction_code_column_exists = 0,
    'ALTER TABLE bookings ADD COLUMN payment_transaction_code VARCHAR(100) NULL AFTER payment_reference',
    'SELECT 1'
);
PREPARE payment_transaction_code_stmt FROM @payment_transaction_code_migration;
EXECUTE payment_transaction_code_stmt;
DEALLOCATE PREPARE payment_transaction_code_stmt;

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

UPDATE plans SET name = '2 Days / Week' WHERE name = 'Basic Fitness';
UPDATE plans SET name = '4 Days / Week' WHERE name = 'Pro Athlete';
UPDATE plans SET name = 'Whole Week' WHERE name = 'VIP Elite';
UPDATE plans SET price = 2000, duration = 'month', description = 'Access to the gym for two days each week. Great for building a consistent routine without overcommitting.' WHERE name = '2 Days / Week';
UPDATE plans SET price = 3500, duration = 'month', description = 'Four training days each week to balance strength, mobility and cardio with room to progress.' WHERE name = '4 Days / Week';
UPDATE plans SET price = 5000, duration = 'month', description = 'Seven-day access for members who want a full weekly routine and maximum flexibility.' WHERE name = 'Whole Week';

DELETE older FROM plans older
JOIN plans newer ON older.name = newer.name AND older.id > newer.id;

INSERT INTO plans (name, price, duration, description)
SELECT '2 Days / Week', 2000, 'month', 'Access to the gym for two days each week. Great for building a consistent routine without overcommitting.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM plans WHERE name = '2 Days / Week')
UNION ALL
SELECT '4 Days / Week', 3500, 'month', 'Four training days each week to balance strength, mobility and cardio with room to progress.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM plans WHERE name = '4 Days / Week')
UNION ALL
SELECT 'Whole Week', 5000, 'month', 'Seven-day access for members who want a full weekly routine and maximum flexibility.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM plans WHERE name = 'Whole Week');

UPDATE bookings AS b
JOIN plans AS p ON p.name = b.class_name
SET b.plan_id = p.id
WHERE b.plan_id IS NULL;