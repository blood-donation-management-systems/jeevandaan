<?php
require_once 'config.php';
startSession();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    jsonResponse(['authenticated' => false, 'redirect' => '/lifelink/login.html']);
}

jsonResponse([
    'authenticated' => true,
    'role'          => $_SESSION['role'],
    'status'        => $_SESSION['status'],
]);
