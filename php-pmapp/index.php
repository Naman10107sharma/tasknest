<?php
require_once __DIR__ . '/config/session.php';

if (isLoggedIn()) {
    header('Location: /php-pmapp/dashboard/index.php');
} else {
    header('Location: /php-pmapp/auth/login.php');
}
exit;
