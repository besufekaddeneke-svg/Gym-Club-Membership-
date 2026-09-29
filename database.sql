CREATE DATABASE IF NOT EXISTS gym_db;
USE gym_db;

-- Development account used by config/db.php. Run this file as a MySQL admin.
CREATE USER IF NOT EXISTS 'gym_user'@'localhost' IDENTIFIED BY 'J8!vQ2#nR5@xL9p';
ALTER USER 'gym_user'@'localhost' IDENTIFIED BY 'J8!vQ2#nR5@xL9p';
GRANT ALL PRIVILEGES ON gym_db.* TO 'gym_user'@'localhost';

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Membership Plans Table
CREATE TABLE IF NOT EXISTS plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration VARCHAR(20) NOT NULL,
    description TEXT
);

-- 3. Membership Bookings Table (Core Project Functionality)
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    class_name VARCHAR(100) NOT NULL,
    branch VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    end_date DATE NULL,
    exercise_type VARCHAR(50) NOT NULL DEFAULT 'General Fitness',
    months_paid INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_method VARCHAR(50) NOT NULL DEFAULT 'Chapa',
    payment_status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Bring forward databases created before branch selection was added.
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

-- 4. Contact Messages Table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Convert the original sample plans into locally priced membership packages.
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