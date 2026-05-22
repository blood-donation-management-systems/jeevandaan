<?php
require_once 'config.php';
startSession();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$fullName = trim($_POST['full_name'] ?? '');
$age      = intval($_POST['age'] ?? 0);
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$fullName || $age < 18 || !$email || !$password) {
    jsonResponse(['error' => 'All fields are required and age must be 18+'], 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['error' => 'Invalid email address'], 400);
}
if (strlen($password) < 8) {
    jsonResponse(['error' => 'Password must be at least 8 characters'], 400);
}

$conn = getDB();
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param('s', $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    jsonResponse(['error' => 'An account with this email already exists'], 409);
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare(
    "INSERT INTO users (full_name, age, email, password, role, status) VALUES (?, ?, ?, ?, 'user', 'pending')"
);
$stmt->bind_param('siss', $fullName, $age, $email, $hash);

if ($stmt->execute()) {
    $_SESSION['user_id']   = $conn->insert_id;
    $_SESSION['full_name'] = $fullName;
    $_SESSION['role']      = 'user';
    $_SESSION['status']    = 'pending';
    jsonResponse(['success' => true, 'redirect' => '/lifelink/verification.html']);
} else {
    jsonResponse(['error' => 'Registration failed: ' . $stmt->error], 500);
}
$conn->close();
