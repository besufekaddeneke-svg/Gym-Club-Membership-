# Besufkad Gym Club Membership System

CoSc3091 Web Programming individual assignment. A responsive PHP/MySQL website for presenting gym memberships, registering members, saving membership plans, and recording payment receipt transaction codes for manual verification.

## Features

- Home, About, Membership, Contact, Register, Login, and member Dashboard pages with shared navigation.
- Student developer profile on About page, including full name, student ID, department, and project motivation.
- Account registration with server-side validation, duplicate checks, and `password_hash()` password storage.
- Login/logout using PHP sessions and `password_verify()`.
- Membership selection by plan, exercise focus, branch, start date, payment method, and number of months.
- Membership bookings persisted in MySQL; each booking receives a unique payment reference.
- Members can submit or update the transaction code from their payment receipt. The code is stored on that membership while the admin verifies it manually.
- Contact enquiries stored in MySQL.
- CSRF-protected forms, prepared SQL statements, output escaping, responsive Flexbox/Grid styling, theme toggle, and popup dialogs.
- English and Amharic language toggle with translated navigation and core page content.

## Technology

- HTML5, CSS3, JavaScript
- PHP 8 with MySQLi
- MySQL 8 or MariaDB

## Pages

| Page | Purpose |
| --- | --- |
| `index.php` | Gym landing page |
| `about.php` | Gym information and student profile |
| `services.php` | Membership packages and payment information |
| `contact.php` | Contact form; stores enquiries |
| `register.php` | Member registration |
| `login.php` | Member sign-in |
| `dashboard.php` | Protected membership selection, payment reference, receipt code, and cancellation |
| `logout.php` | Ends the member session |

## Local setup

Requirements: PHP 8+, MySQL/MariaDB, and the PHP `mysqli` extension.

1. Start MySQL/MariaDB.
2. Import `database.sql` as a database administrator. It creates `gym_db`, the local development database user, the tables, and the three membership package rows.
3. The database connection reads `GYM_DB_HOST`, `GYM_DB_USER`, `GYM_DB_PASSWORD`, and `GYM_DB_NAME` environment variables. If unset, it uses the local defaults in `database.sql`. Set environment variables for hosted use and never deploy the development database password.
4. From the project directory, start PHP's development server with `php -S 127.0.0.1:8000`.
5. Open `http://127.0.0.1:8000`.

For XAMPP, place the project folder under `htdocs`, start Apache and MySQL, import `database.sql` in phpMyAdmin, and open the corresponding `http://localhost/<folder-name>/` address.

## Test account

There is no seeded member account. Use **Join the club** to register an account, then sign in and create a membership plan. This avoids shipping a shared demo password.

## Membership rates

- 2 Days / Week — ETB 2,000 per month
- 4 Days / Week — ETB 3,500 per month
- Whole Week — ETB 5,000 per month

The payment account numbers in this student demonstration are example destinations supplied for the project. Confirm ownership and authorization before accepting real payments. Payment status remains pending until an administrator manually verifies the receipt and submitted transaction code.

## Database design and report

The schema is in `database.sql`. The report, table summary, and ER diagram are in `docs/REPORT.pdf` and editable form in `docs/REPORT.md`. Page screenshots are in `docs/screenshots/`.

## Repository and deployment

Repository: <https://github.com/besufekaddeneke-svg/Gym-Club-Membership->

No public deployment is configured. Use the local setup above to evaluate the PHP/MySQL application.
