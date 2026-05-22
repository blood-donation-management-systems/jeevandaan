<?php
require_once 'config.php';
startSession();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$action = $_GET['action'] ?? '';
$conn   = getDB();

switch ($action) {

    case 'stats':
        $users    = $conn->query("SELECT COUNT(*) c FROM users WHERE role='user'")->fetch_assoc()['c'];
        $centers  = $conn->query("SELECT COUNT(*) c FROM redcross_centers WHERE status='active'")->fetch_assoc()['c'];
        $donors   = $conn->query("SELECT COUNT(DISTINCT user_id) c FROM blood_donations")->fetch_assoc()['c'];
        $requests = $conn->query("SELECT COUNT(*) c FROM blood_requests WHERE MONTH(created_at)=MONTH(NOW())")->fetch_assoc()['c'];
        $pending  = $conn->query("SELECT COUNT(*) c FROM verifications WHERE status='pending'")->fetch_assoc()['c'];
        $newToday = $conn->query("SELECT COUNT(*) c FROM users WHERE DATE(created_at)=CURDATE()")->fetch_assoc()['c'];
        $activeReq= $conn->query("SELECT COUNT(*) c FROM blood_requests WHERE status='pending'")->fetch_assoc()['c'];
        jsonResponse([
            'users'=>$users,'centers'=>$centers,'donors'=>$donors,
            'requests'=>$requests,'pending'=>$pending,
            'new_today'=>$newToday,'active_requests'=>$activeReq
        ]);

    case 'users':
        $rows = $conn->query("SELECT id, full_name, email, role, status, created_at FROM users ORDER BY created_at DESC");
        $data = [];
        while ($r = $rows->fetch_assoc()) $data[] = $r;
        jsonResponse($data);

    case 'update_user':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id     = intval($_POST['id'] ?? 0);
        $role   = $_POST['role'] ?? '';
        $status = $_POST['status'] ?? '';
        if (!$id || !in_array($role,['user','redcross','admin']) || !in_array($status,['active','inactive','pending'])) jsonResponse(['error'=>'Invalid'],400);
        $stmt = $conn->prepare("UPDATE users SET role=?, status=? WHERE id=?");
        $stmt->bind_param('ssi', $role, $status, $id);
        $stmt->execute();
        jsonResponse(['success'=>true]);

    case 'delete_user':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        jsonResponse(['success'=>true]);

    case 'centers':
        $rows = $conn->query("SELECT id, name, location, contact, status, created_at FROM redcross_centers ORDER BY created_at DESC");
        $data = [];
        while ($r = $rows->fetch_assoc()) $data[] = $r;
        jsonResponse($data);

    case 'add_center':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $name     = trim($_POST['name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $contact  = trim($_POST['contact'] ?? '');
        if (!$name) jsonResponse(['error'=>'Name required'],400);
        $stmt = $conn->prepare("INSERT INTO redcross_centers (name, location, contact) VALUES (?,?,?)");
        $stmt->bind_param('sss', $name, $location, $contact);
        $stmt->execute();
        jsonResponse(['success'=>true, 'id'=>$conn->insert_id]);

    case 'update_center':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id       = intval($_POST['id'] ?? 0);
        $name     = trim($_POST['name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $contact  = trim($_POST['contact'] ?? '');
        $status   = $_POST['status'] ?? 'active';
        if (!$id || !$name) jsonResponse(['error'=>'Invalid'],400);
        $stmt = $conn->prepare("UPDATE redcross_centers SET name=?, location=?, contact=?, status=? WHERE id=?");
        $stmt->bind_param('ssssi', $name, $location, $contact, $status, $id);
        $stmt->execute();
        jsonResponse(['success'=>true]);

    case 'delete_center':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM redcross_centers WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        jsonResponse(['success'=>true]);

    case 'requests':
        $rows = $conn->query("
            SELECT br.id, u.full_name, br.blood_group, br.units_ml,
                   br.urgency, br.status, br.date_needed, br.created_at
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

    case 'delete_request':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM blood_requests WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        jsonResponse(['success'=>true]);

    case 'donors':
        $rows = $conn->query("
            SELECT u.id, u.full_name, u.blood_group, u.donations, u.status,
                   MAX(bd.last_donation) as last_donation
            FROM users u
            LEFT JOIN blood_donations bd ON bd.user_id = u.id
            WHERE u.role = 'user'
            GROUP BY u.id
            ORDER BY u.donations DESC
        ");
        $data = [];
        while ($r = $rows->fetch_assoc()) $data[] = $r;
        jsonResponse($data);

    case 'verifications':
        $rows = $conn->query("
            SELECT v.id, u.full_name, v.front_path, v.back_path,
                   v.cert_path, v.status, v.submitted_at, u.id as user_id
            FROM verifications v
            JOIN users u ON u.id = v.user_id
            ORDER BY v.submitted_at DESC
        ");
        $data = [];
        while ($r = $rows->fetch_assoc()) $data[] = $r;
        jsonResponse($data);

    case 'verify_action':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error'=>'POST required'],405);
        $id      = intval($_POST['id'] ?? 0);
        $user_id = intval($_POST['user_id'] ?? 0);
        $status  = $_POST['status'] ?? '';
        if (!$id || !in_array($status,['approved','rejected'])) jsonResponse(['error'=>'Invalid'],400);
        $stmt = $conn->prepare("UPDATE verifications SET status=? WHERE id=?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        $userStatus = $status === 'approved' ? 'active' : 'inactive';
        $stmt2 = $conn->prepare("UPDATE users SET status=? WHERE id=?");
        $stmt2->bind_param('si', $userStatus, $user_id);
        $stmt2->execute();
        jsonResponse(['success'=>true]);

    default:
        jsonResponse(['error'=>'Unknown action'],400);
}
$conn->close();
