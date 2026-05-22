<?php
require_once 'config.php';
requireAuth('user');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'Method not allowed'],405);

$bloodGroup   = trim($_POST['blood_group'] ?? '');
$location     = trim($_POST['location'] ?? '');
$age          = intval($_POST['age'] ?? 0);
$weight       = floatval($_POST['weight'] ?? 0);
$lastDonation = $_POST['last_donation'] ?? null;
$units        = intval($_POST['units_ml'] ?? 0);

if (!$bloodGroup || !$location || $age < 18 || $weight < 45 || $units < 400 || $units > 500) {
    jsonResponse(['error' => 'Invalid form data'], 400);
}

$conn = getDB();
$stmt = $conn->prepare(
    "INSERT INTO blood_donations (user_id, blood_group, location, age, weight, last_donation, units_ml)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$uid = $_SESSION['user_id'];
$stmt->bind_param('issidsi', $uid, $bloodGroup, $location, $age, $weight, $lastDonation, $units);

if ($stmt->execute()) {
    $conn->prepare("UPDATE users SET blood_group=?, donations=donations+1 WHERE id=?")
         ->bind_param('si', $bloodGroup, $uid) && null;
    jsonResponse(['success' => true, 'message' => 'Donation registered successfully!']);
} else {
    jsonResponse(['error' => 'Failed to register donation'], 500);
}
$conn->close();
