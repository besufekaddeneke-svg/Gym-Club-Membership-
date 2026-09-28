# Gym Club Membership System (CoSc3091 Project)

## Project Title
PowerPulse Gym Club Membership System

## Core Features
- **User Authentication**: Secure registration and login with `password_hash()` and `password_verify()`.
- **Session Management**: Session persistence across protected pages with secure logout.
- **Member bookings**: Registered members can reserve a class at one of four branches, view upcoming bookings, and cancel future reservations.
- **Interactive Contact Form**: Stores user queries directly in the database.
- **Responsive UI**: Mobile navigation, saved light/dark theme, responsive membership and class pages.
- **Validation & security**: Client and server validation, prepared statements, password hashing, and CSRF protection on forms.

## Technologies Used
- **Frontend**: HTML5, CSS3 (Flexbox/Grid), JavaScript
- **Backend**: PHP 8.x
- **Database**: MySQL / MariaDB (using MySQLi prepared statements)

## Setup & Running Instructions (Local XAMPP)
1. Copy the project folder to `xampp/htdocs/gym_system`.
2. Start Apache and MySQL in XAMPP Control Panel.
3. Import the `database.sql` file as a MySQL administrator. It creates `gym_db`, the local app account, and sample membership plans.
4. Access the application at `http://localhost/gym_system`.

### Linux development server
1. Ensure MySQL or MariaDB is running.
2. Import `database.sql` as a MySQL administrator (for example, through phpMyAdmin). The SQL creates the local development user `gym_user` with a strong local password and grants access to `gym_db`.
3. Start the app from the project directory with `php -S 127.0.0.1:8000` and open `http://127.0.0.1:8000`.
4. If you use different credentials, update `config/db.php` to match your local database account.

## Try the member experience
Register a member account from the website, then sign in to book and cancel classes. The SQL setup does not insert a demo user.