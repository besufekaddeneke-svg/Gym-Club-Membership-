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

-- 3. Class Bookings Table (Core Project Functionality)
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    class_name VARCHAR(50) NOT NULL,
    branch VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
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

-- Convert the original sample plans into locally priced weekly-access packages.
UPDATE plans SET name = '3 Days / Week' WHERE name = 'Basic Fitness';
UPDATE plans SET name = '4 Days / Week' WHERE name = 'Pro Athlete';
UPDATE plans SET name = 'Every Day Access' WHERE name = 'VIP Elite';
UPDATE plans SET price = 1500, duration = 'month', description = 'Gym access up to three days each week. A steady, flexible routine for building consistency.' WHERE name = '3 Days / Week';
UPDATE plans SET price = 2000, duration = 'month', description = 'Gym access up to four days each week, with room to mix strength and cardio.' WHERE name = '4 Days / Week';
UPDATE plans SET price = 2500, duration = 'month', description = 'Unlimited gym access every day of the week. Train whenever it fits your routine.' WHERE name = 'Every Day Access';

DELETE older FROM plans older
JOIN plans newer ON older.name = newer.name AND older.id > newer.id;

INSERT INTO plans (name, price, duration, description)
SELECT '3 Days / Week', 1500, 'month', 'Gym access up to three days each week. A steady, flexible routine for building consistency.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM plans WHERE name = '3 Days / Week')
UNION ALL
SELECT '4 Days / Week', 2000, 'month', 'Gym access up to four days each week, with room to mix strength and cardio.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM plans WHERE name = '4 Days / Week')
UNION ALL
SELECT 'Every Day Access', 2500, 'month', 'Unlimited gym access every day of the week. Train whenever it fits your routine.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM plans WHERE name = 'Every Day Access');