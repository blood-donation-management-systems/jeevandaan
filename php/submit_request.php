<?php
require_once 'config.php';
requireAuth('user');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'Method not allowed'],405);

$bloodGroup = trim($_POST['blood_group'] ?? '');
$location   = trim($_POST['location'] ?? '');
$dateNeeded = $_POST['date_needed'] ?? '';
$units      = intval($_POST['units_ml'] ?? 0);
$urgency    = trim($_POST['urgency'] ?? '');

if (!$bloodGroup || !$location || !$dateNeeded || $units < 400 || $units > 2000 || !$urgency) {
    jsonResponse(['error' => 'Invalid form data'], 400);
}

$conn = getDB();
$stmt = $conn->prepare(
    "INSERT INTO blood_requests (user_id, blood_group, location, date_needed, units_ml, urgency)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$uid = $_SESSION['user_id'];
$stmt->bind_param('isssss', $uid, $bloodGroup, $location, $dateNeeded, $units, $urgency);

if ($stmt->execute()) {
    jsonResponse(['success' => true, 'message' => 'Blood request submitted!']);
} else {
    jsonResponse(['error' => 'Failed to submit request'], 500);
}
$conn->close();
