<?php
$host = 'localhost';
$user = 'gym_user';
$pass = 'gym_password';
$dbname = 'gym_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>