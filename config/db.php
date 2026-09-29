<?php
$host = getenv('GYM_DB_HOST') ?: 'localhost';
$user = getenv('GYM_DB_USER') ?: 'gym_user';
$pass = getenv('GYM_DB_PASSWORD') ?: 'J8!vQ2#nR5@xL9p';
$dbname = getenv('GYM_DB_NAME') ?: 'gym_db';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    error_log('Gym database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Database connection failed. Check the local database settings.');
}
?>