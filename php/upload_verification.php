<?php
require_once 'config.php';
requireAuth('user');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
$maxSize      = 5 * 1024 * 1024;
$uploadDir    = '../uploads/';

@mkdir($uploadDir . 'citizenship',  0755, true);
@mkdir($uploadDir . 'certificates', 0755, true);

function saveFile($fileKey, $subDir, $userId) {
    global $allowedMimes, $maxSize, $uploadDir;
    if (empty($_FILES[$fileKey]['tmp_name'])) return null;

    $tmp  = $_FILES[$fileKey]['tmp_name'];
    $size = $_FILES[$fileKey]['size'];
    if ($size > $maxSize) jsonResponse(['error' => 'File too large (max 5MB)'], 400);

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($tmp);
    if (!in_array($mime, $allowedMimes, true)) jsonResponse(['error' => 'Invalid file type'], 400);

    $ext      = ($mime === 'application/pdf') ? 'pdf' : 'jpg';
    $filename = $subDir . '/' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest     = $uploadDir . $filename;

    if (!move_uploaded_file($tmp, $dest)) jsonResponse(['error' => 'Upload failed'], 500);
    return $filename;
}

$uid   = $_SESSION['user_id'];
$front = saveFile('front', 'citizenship',  $uid);
$back  = saveFile('back',  'citizenship',  $uid);
$cert  = saveFile('cert',  'certificates', $uid);

$conn = getDB();

$stmt = $conn->prepare(
    "INSERT INTO verifications (user_id, front_path, back_path, cert_path, status)
     VALUES (?, ?, ?, ?, 'pending')
     ON DUPLICATE KEY UPDATE
       front_path=VALUES(front_path),
       back_path=VALUES(back_path),
       cert_path=VALUES(cert_path),
       status='pending'"
);
$stmt->bind_param('isss', $uid, $front, $back, $cert);
if (!$stmt->execute()) {
    jsonResponse(['error' => 'DB error: ' . $stmt->error], 500);
}

$upd = $conn->prepare("UPDATE users SET status='pending' WHERE id=?");
$upd->bind_param('i', $uid);
$upd->execute();

jsonResponse(['success' => true, 'redirect' => '/lifelink/user.html']);
$conn->close();
