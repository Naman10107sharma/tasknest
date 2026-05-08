<?php
$host     = getenv('MYSQL_HOST')     ?: 'localhost';
$port     = getenv('MYSQL_PORT')     ?: 3306;
$dbname   = getenv('MYSQL_DATABASE') ?: 'pmapp';
$username = getenv('MYSQL_USER')     ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: '';

$conn = new mysqli($host, $username, $password, $dbname, (int)$port);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
