<?php

$host = $_ENV['MYSQL_HOST'] ?? '';
$port = $_ENV['MYSQL_PORT'] ?? '';
$db   = $_ENV['MYSQL_DATABASE'] ?? '';
$user = $_ENV['MYSQL_USER'] ?? '';
$pass = $_ENV['MYSQL_PASSWORD'] ?? '';

$conn = new mysqli($host, $user, $pass, $db, (int)$port);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
