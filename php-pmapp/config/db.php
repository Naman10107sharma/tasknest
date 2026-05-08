<?php

$host = getenv('MYSQL_HOST');
$port = getenv('MYSQL_PORT');
$db   = getenv('MYSQL_DATABASE');
$user = getenv('MYSQL_USER');
$pass = getenv('MYSQL_PASSWORD');

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die('<div style="font-family:Segoe UI,sans-serif;padding:40px;color:#7f1d1d;
                    background:#fef2f2;border:1px solid #fecaca;border-radius:12px;
                    max-width:560px;margin:60px auto;">
        <h2 style="margin:0 0 12px;">&#128274; Database Connection Failed</h2>
        <p style="margin:0 0 8px;"><strong>Error:</strong> ' . htmlspecialchars($conn->connect_error) . '</p>
        <p style="margin:0;color:#991b1b;font-size:.9em;">
            Check Railway environment variables configuration.
        </p>
    </div>');
}

$conn->set_charset('utf8mb4');
