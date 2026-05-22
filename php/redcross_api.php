<?php
require_once 'config.php';
startSession();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'redcross') {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$action = $_GET['action'] ?? '';
$conn   = getDB();

switch ($action) {

    case 'stats':
        $donors   = $conn->query("SELECT COUNT(DISTINCT user_id) c FROM blood_donations")->fetch_assoc()['c'];
        $units    = $conn->query("SELECT COALESCE(SUM(units_available),0) c FROM blood_inventory")->fetch_assoc()['c'];
        $requests = $conn->query("SELECT COUNT(*) c FROM blood_requests")->fetch_assoc()['c'];
        $expiring = $conn->query("SELECT COUNT(*) c FROM blood_inventory WHERE expire_date IS NOT NULL AND DATEDIFF(expire_date, CURDATE()) <= 30 AND DATEDIFF(expire_date, CURDATE()) >= 0")->fetch_assoc()['c'];
        jsonResponse(['donors'=>$donors,'units'=>$units,'requests'=>$requests,'expiring'=>$expiring]);

    case 'inventory':
        $rows = $conn->query("SELECT id, blood_group, units_available, storage_date, expire_date FROM blood_inventory ORDER BY blood_group");
        $data = [];
        while ($r = $rows->fetch_assoc()) $data[] = $r;
        jsonResponse($data);

    case 'add_units':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id    = intval($_POST['id'] ?? 0);
        $units = intval($_POST['units'] ?? 0);
        if (!$id || $units < 1) jsonResponse(['error'=>'Invalid data'],400);
        $stmt = $conn->prepare("UPDATE blood_inventory SET units_available = units_available + ? WHERE id=?");
        $stmt->bind_param('ii', $units, $id);
        $stmt->execute();
        jsonResponse(['success'=>true]);

    case 'update_inventory':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id      = intval($_POST['id'] ?? 0);
        $units   = intval($_POST['units_available'] ?? 0);
        $storage = $_POST['storage_date'] ?? '';
        $expire  = $_POST['expire_date'] ?? '';
        $stmt = $conn->prepare("UPDATE blood_inventory SET units_available=?, storage_date=?, expire_date=? WHERE id=?");
        $stmt->bind_param('issi', $units, $storage, $expire, $id);
        $stmt->execute();
        jsonResponse(['success'=>true]);

    case 'appointments':
        $rows = $conn->query("
            SELECT a.id, u.full_name, a.blood_group, a.requested_date,
                   a.last_donation, a.status
            FROM appointments a
            JOIN users u ON u.id = a.user_id
            ORDER BY a.requested_date DESC
        ");
        $data = [];
        while ($r = $rows->fetch_assoc()) $data[] = $r;
        jsonResponse($data);

    case 'update_appointment':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id     = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (!$id || !in_array($status,['scheduled','pending','completed','cancelled'])) jsonResponse(['error'=>'Invalid'],400);
        $stmt = $conn->prepare("UPDATE appointments SET status=? WHERE id=?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        jsonResponse(['success'=>true]);

    case 'delete_appointment':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM appointments WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        jsonResponse(['success'=>true]);

    case 'blood_testing':
        $rows = $conn->query("
            SELECT bt.id, u.full_name, bt.claimed_group, bt.tested_group,
                   bt.hiv, bt.hepatitis_b, bt.hepatitis_c, bt.syphilis,
                   bt.malaria, bt.covid, bt.hemoglobin, bt.blood_pressure,
                   bt.pulse_rate, bt.temperature, bt.weight,
                   bt.health_declaration, bt.status
            FROM blood_testing bt
            JOIN users u ON u.id = bt.user_id
            ORDER BY bt.created_at DESC
        ");
        $data = [];
        while ($r = $rows->fetch_assoc()) $data[] = $r;
        jsonResponse($data);

    case 'update_testing':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id     = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (!$id || !in_array($status,['pending','accepted','rejected'])) jsonResponse(['error'=>'Invalid'],400);
        $stmt = $conn->prepare("UPDATE blood_testing SET status=? WHERE id=?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        jsonResponse(['success'=>true]);

    case 'blood_requests':
        $rows = $conn->query("
            SELECT br.id, u.full_name, br.blood_group, br.units_ml,
                   br.urgency, br.status, br.date_needed, br.location
            FROM blood_requests br
            JOIN users u ON u.id = br.user_id
            ORDER BY br.created_at DESC
        ");
        $data = [];
        while ($r = $rows->fetch_assoc()) $data[] = $r;
        jsonResponse($data);

    case 'update_request':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id     = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (!$id || !in_array($status,['pending','approved','rejected'])) jsonResponse(['error'=>'Invalid'],400);
        $stmt = $conn->prepare("UPDATE blood_requests SET status=? WHERE id=?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        jsonResponse(['success'=>true]);

    case 'rare_blood':
        $rows = $conn->query("
            SELECT rb.id, COALESCE(u.full_name,'Unknown') as full_name,
                   rb.blood_group, rb.contact, rb.type
            FROM rare_blood_registry rb
            LEFT JOIN users u ON u.id = rb.user_id
            ORDER BY rb.created_at DESC
        ");
        $data = [];
        while ($r = $rows->fetch_assoc()) $data[] = $r;
        jsonResponse($data);

    case 'donors':
        $rows = $conn->query("
            SELECT u.id, u.full_name, u.blood_group, u.donations, u.status,
                   MAX(bd.last_donation) as last_donation
            FROM users u
            LEFT JOIN blood_donations bd ON bd.user_id = u.id
            WHERE u.role = 'user' AND u.status = 'active'
            GROUP BY u.id
            ORDER BY u.donations DESC
        ");
        $data = [];
        while ($r = $rows->fetch_assoc()) $data[] = $r;
        jsonResponse($data);

    case 'expiring':
        $rows = $conn->query("
            SELECT id, blood_group, units_available, expire_date,
                   DATEDIFF(expire_date, CURDATE()) as days_left
            FROM blood_inventory
            WHERE expire_date IS NOT NULL
              AND DATEDIFF(expire_date, CURDATE()) <= 30
              AND DATEDIFF(expire_date, CURDATE()) >= 0
            ORDER BY days_left ASC
        ");
        $data = [];
        while ($r = $rows->fetch_assoc()) $data[] = $r;
        jsonResponse($data);

    case 'remove_expiry':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("UPDATE blood_inventory SET units_available=0, expire_date=NULL WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        jsonResponse(['success'=>true]);

    default:
        jsonResponse(['error'=>'Unknown action'],400);
}
$conn->close();
