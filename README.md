# Gym Club Membership System (CoSc3091 Project)

## Project Title
PowerPulse Gym Club Membership System

## Core Features
- **User Authentication**: Secure registration and login with `password_hash()` and `password_verify()`.
- **Session Management**: Session persistence across protected pages with secure logout.
- **Dynamic Database CRUD**: Registered members can select and book fitness classes; bookings are retrieved dynamically from MySQL.
- **Interactive Contact Form**: Stores user queries directly in the database.
- **Client & Server Validation**: JavaScript validation on registration forms and server-side validation against duplicate accounts and SQL injection.

## Technologies Used
- **Frontend**: HTML5, CSS3 (Flexbox/Grid), JavaScript
- **Backend**: PHP 8.x
- **Database**: MySQL / MariaDB (using MySQLi prepared statements)

## Setup & Running Instructions (Local XAMPP)
1. Copy the project folder to `xampp/htdocs/gym_system`.
2. Start Apache and MySQL in XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin` and create a database named `gym_db`.
4. Import the `database.sql` file into `gym_db`.
5. Access the application at `http://localhost/gym_system`.

### Linux development server
1. Ensure MySQL or MariaDB is running.
2. Import `database.sql` as a MySQL administrator (for example, through phpMyAdmin). The SQL creates the local development user `gym_user` with password `gym_password` and grants access to `gym_db`.
3. Start the app from the project directory with `php -S 127.0.0.1:8000` and open `http://127.0.0.1:8000`.
4. If you use different credentials, update `config/db.php` to match your local database account.

## Default Test Credentials
- **Username**: `john_doe`
- **Password**: `password123`