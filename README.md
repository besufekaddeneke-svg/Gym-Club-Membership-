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

## Default Test Credentials
- **Username**: `john_doe`
- **Password**: `password123`