<?php
require_once 'config.php';
startSession();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$code = trim($_POST['code'] ?? '');
if (!$code) {
    jsonResponse(['error' => 'Access code required'], 400);
}

$conn = getDB();
$stmt = $conn->prepare("SELECT code_hash FROM redcross_codes WHERE active = 1");
$stmt->execute();
$result = $stmt->get_result();

$valid = false;
while ($row = $result->fetch_assoc()) {
    if (password_verify($code, $row['code_hash'])) {
        $valid = true;
        break;
    }
}

if (!$valid) {
    jsonResponse(['error' => 'Invalid access code'], 401);
}

$_SESSION['user_id']   = 0;
$_SESSION['full_name'] = 'Red Cross Staff';
$_SESSION['role']      = 'redcross';
$_SESSION['status']    = 'active';

jsonResponse(['success' => true, 'redirect' => '/lifelink/redcross.html']);
$conn->close();
