<?php
require_once 'config.php';
startSession();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    jsonResponse(['error' => 'Email and password are required'], 400);
}

$conn = getDB();
$stmt = $conn->prepare(
    "SELECT id, full_name, password, role, status, blood_group, donations
     FROM users WHERE email = ? LIMIT 1"
);
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(['error' => 'Invalid credentials'], 401);
}

$_SESSION['user_id']     = $user['id'];
$_SESSION['full_name']   = $user['full_name'];
$_SESSION['role']        = $user['role'];
$_SESSION['status']      = $user['status'];
$_SESSION['blood_group'] = $user['blood_group'];
$_SESSION['donations']   = $user['donations'];

$redirect = match($user['role']) {
    'admin'    => '/lifelink/admin.html',
    'redcross' => '/lifelink/redcross.html',
    default    => '/lifelink/user.html',
};

jsonResponse(['success' => true, 'redirect' => $redirect]);
$conn->close();
