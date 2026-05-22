<?php
require_once 'config.php';
requireAuth();
header('Content-Type: application/json');

$conn = getDB();
$stmt = $conn->prepare(
    "SELECT u.full_name, u.email, u.phone, u.blood_group, u.donations, u.role, u.status,
            v.status AS verification_status
     FROM users u
     LEFT JOIN verifications v ON v.user_id = u.id
     WHERE u.id = ? LIMIT 1"
);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) jsonResponse(['error' => 'User not found'], 404);

$nameParts = explode(' ', $row['full_name'], 2);
jsonResponse([
    'firstName'    => $nameParts[0],
    'fullName'     => $row['full_name'],
    'email'        => $row['email'] ?? $row['phone'],
    'bloodGroup'   => $row['blood_group'] ?? '—',
    'donations'    => $row['donations'],
    'role'         => $row['role'],
    'status'       => $row['status'],
    'verified'     => ($row['verification_status'] === 'approved'),
]);
$conn->close();
