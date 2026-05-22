<?php
require_once 'config.php';
requireAuth('user');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'Method not allowed'],405);

$bloodGroup = trim($_POST['blood_group'] ?? '');
$contact    = trim($_POST['contact'] ?? '');
$type       = trim($_POST['type'] ?? 'donor');

if (!$bloodGroup || !$contact) jsonResponse(['error' => 'Missing fields'], 400);

$conn = getDB();
$stmt = $conn->prepare(
    "INSERT INTO rare_blood_registry (user_id, blood_group, contact, type) VALUES (?, ?, ?, ?)"
);
$uid = $_SESSION['user_id'];
$stmt->bind_param('isss', $uid, $bloodGroup, $contact, $type);
$stmt->execute();
jsonResponse(['success' => true]);
$conn->close();
