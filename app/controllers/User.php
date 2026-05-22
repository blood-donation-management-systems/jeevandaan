<?php
class User extends Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }
    }
    
    public function index() {
        $this->redirect('user/dashboard');
    }
    
    public function dashboard() {
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        $matchingRequests = [];
        if ($user['blood_group']) {
            $stmt = $this->db->prepare("SELECT * FROM blood_requests WHERE blood_group = ? AND status = 'active' ORDER BY urgency, created_at DESC LIMIT 5");
            $stmt->bind_param("s", $user['blood_group']);
            $stmt->execute();
            $matchingRequests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        $stmt = $this->db->prepare("SELECT * FROM blood_requests WHERE requested_by = ? AND requester_type = 'user' ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $myRequests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE recipient_id = ? AND recipient_type = 'user' ORDER BY created_at DESC LIMIT 5");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM notifications WHERE recipient_id = ? AND recipient_type = 'user' AND is_read = 0");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $unreadCount = $stmt->get_result()->fetch_assoc()['count'];
        
        $canDonate = true;
        if ($user['last_donation_date']) {
            $lastDonation = new DateTime($user['last_donation_date']);
            $now = new DateTime();
            $diff = $lastDonation->diff($now)->days;
            $canDonate = $diff >= DONATION_INTERVAL_DAYS;
        }
        
        $this->view('user/dashboard', [
            'title' => 'Dashboard',
            'user' => $user,
            'matchingRequests' => $matchingRequests,
            'myRequests' => $myRequests,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'canDonate' => $canDonate,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function profile() {
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        $districts = $this->db->query("SELECT province, district FROM nepal_districts ORDER BY province, district")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('user/profile', [
            'title' => 'My Profile',
            'user' => $user,
            'districts' => $districts,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update_profile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/profile');
        }
        
        $userId = $_SESSION['user_id'];
        
        $citizenshipFront = $this->handleUpload('citizenship_front', 'id_cards');
        $citizenshipBack = $this->handleUpload('citizenship_back', 'id_cards');
        $donationCert = $this->handleUpload('donation_certificate', 'certificates');
        
        $updateFields = [];
        $params = [];
        $types = "";
        
        $fields = [
            'full_name' => ['value' => trim($_POST['full_name']), 'type' => 's'],
            'phone' => ['value' => trim($_POST['phone']), 'type' => 's'],
            'date_of_birth' => ['value' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null, 'type' => 's'],
            'gender' => ['value' => $_POST['gender'], 'type' => 's'],
            'blood_group' => ['value' => $_POST['blood_group'], 'type' => 's'],
            'weight' => ['value' => (float)$_POST['weight'], 'type' => 'd'],
            'province' => ['value' => $_POST['province'], 'type' => 's'],
            'district' => ['value' => $_POST['district'], 'type' => 's'],
            'municipality' => ['value' => trim($_POST['municipality']), 'type' => 's'],
            'ward_no' => ['value' => (int)$_POST['ward_no'], 'type' => 'i'],
            'tole' => ['value' => trim($_POST['tole'] ?? ''), 'type' => 's'],
            'last_donation_date' => ['value' => !empty($_POST['last_donation_date']) ? $_POST['last_donation_date'] : null, 'type' => 's'],
            'has_hiv' => ['value' => isset($_POST['has_hiv']) ? 1 : 0, 'type' => 'i'],
            'has_hepatitis_b' => ['value' => isset($_POST['has_hepatitis_b']) ? 1 : 0, 'type' => 'i'],
            'has_hepatitis_c' => ['value' => isset($_POST['has_hepatitis_c']) ? 1 : 0, 'type' => 'i'],
            'has_diabetes' => ['value' => isset($_POST['has_diabetes']) ? 1 : 0, 'type' => 'i'],
            'has_hypertension' => ['value' => isset($_POST['has_hypertension']) ? 1 : 0, 'type' => 'i'],
            'other_diseases' => ['value' => trim($_POST['other_diseases'] ?? ''), 'type' => 's'],
            'willing_to_donate' => ['value' => isset($_POST['willing_to_donate']) ? 1 : 0, 'type' => 'i'],
            'receive_notifications' => ['value' => isset($_POST['receive_notifications']) ? 1 : 0, 'type' => 'i']
        ];
        
        foreach ($fields as $field => $data) {
            $updateFields[] = "$field = ?";
            $params[] = $data['value'];
            $types .= $data['type'];
        }
        
        if ($citizenshipFront) {
            $updateFields[] = "citizenship_front = ?";
            $params[] = $citizenshipFront;
            $types .= "s";
        }
        if ($citizenshipBack) {
            $updateFields[] = "citizenship_back = ?";
            $params[] = $citizenshipBack;
            $types .= "s";
        }
        if ($donationCert) {
            $updateFields[] = "donation_certificate = ?";
            $params[] = $donationCert;
            $types .= "s";
        }
        
        $params[] = $userId;
        $types .= "i";
        
        $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $_SESSION['user_name'] = trim($_POST['full_name']);
            
            // Check if user has rare blood group and profile is complete
            $bloodGroup = $_POST['blood_group'] ?? '';
            $rareBloodGroups = ['AB-', 'B-', 'A-', 'O-'];
            
            // Check if profile is now complete (has all required fields)
            $profileComplete = !empty($_POST['full_name']) && 
                              !empty($_POST['phone']) && 
                              !empty($_POST['date_of_birth']) && 
                              !empty($_POST['blood_group']) && 
                              !empty($_POST['weight']) && 
                              !empty($_POST['province']) && 
                              !empty($_POST['district']) && 
                              !empty($_POST['municipality']);
            
            if (in_array($bloodGroup, $rareBloodGroups) && $profileComplete) {
                // Check if notification already sent
                $checkStmt = $this->db->prepare("SELECT id FROM notifications WHERE recipient_id = ? AND recipient_type = 'user' AND type = 'rare_blood' LIMIT 1");
                $checkStmt->bind_param("i", $userId);
                $checkStmt->execute();
                $existing = $checkStmt->get_result()->fetch_assoc();
                
                if (!$existing) {
                    // Send rare blood notification
                    $rareMessage = "🎉 Thank you for registering! Your blood group $bloodGroup is RARE and highly valuable. We will contact you when patients urgently need your blood type. You are a lifesaver! ❤️";
                    $notifStmt = $this->db->prepare("INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) VALUES (?, 'user', 'rare_blood', 'You Have Rare Blood! 🩸', ?, '/user/dashboard')");
                    $notifStmt->bind_param("is", $userId, $rareMessage);
                    $notifStmt->execute();
                    
                    // Also notify admin
                    $adminMsg = "New rare blood donor registered: " . $_POST['full_name'] . " (Blood Group: $bloodGroup)";
                    $adminStmt = $this->db->prepare("INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) VALUES (1, 'admin', 'rare_donor', 'New Rare Blood Donor!', ?, '/admin/users')");
                    $adminStmt->bind_param("s", $adminMsg);
                    $adminStmt->execute();
                    
                    $this->setFlash('success', 'Profile updated! 🎉 Your blood group is rare - we sent you a special notification.');
                } else {
                    $this->setFlash('success', 'Profile updated successfully!');
                }
            } else {
                $this->setFlash('success', 'Profile updated successfully!');
            }
        } else {
            $this->setFlash('error', 'Failed to update profile');
        }
        
        $this->redirect('user/profile');
    }
    
    private function handleUpload($fieldName, $folder) {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $file = $_FILES[$fieldName];
        if ($file['size'] > MAX_FILE_SIZE) return null;
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file['type'], $allowedTypes)) return null;
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'jd_' . uniqid() . '_' . time() . '.' . $ext;
        $targetPath = UPLOAD_PATH . $folder . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) return $filename;
        return null;
    }
    
    public function notifications() {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE recipient_id = ? AND recipient_type = 'user' ORDER BY created_at DESC LIMIT 50");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $this->view('user/notifications', ['title' => 'Notifications', 'notifications' => $notifications]);
    }
    
    public function delete_notification($id) {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE id = ? AND recipient_id = ? AND recipient_type = 'user'");
        $stmt->bind_param("ii", $id, $_SESSION['user_id']);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }
    
    public function mark_all_read() {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE recipient_id = ? AND recipient_type = 'user'");
        $stmt->bind_param("i", $_SESSION['user_id']);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }

    
    public function donate_blood() {
        if (!$_SESSION['user_verified']) {
            $this->setFlash('error', 'Your account must be verified to donate blood');
            $this->redirect('user/dashboard');
        }
        
        $userId = $_SESSION['user_id'];
        $error = null;
        $success = null;
        
        // Get user info
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        // Check if can donate
        $canDonate = true;
        if ($user['last_donation_date']) {
            $lastDonation = new DateTime($user['last_donation_date']);
            $now = new DateTime();
            $diff = $lastDonation->diff($now)->days;
            $canDonate = $diff >= DONATION_INTERVAL_DAYS;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$canDonate) {
                $error = 'You need to wait before donating again';
            } else {
                $available_date = $_POST['available_date'] ?? '';
                $location = trim($_POST['location'] ?? '');
                $notes = trim($_POST['notes'] ?? '');
                
                if (empty($available_date) || empty($location)) {
                    $error = 'Please fill all required fields';
                } elseif (preg_match('/^[\d\s\-_.,()]+$/', trim($location))) {
                    $error = 'Location cannot contain only numbers. Please enter a valid location with letters.';
                } elseif (strtotime($available_date) < strtotime(date('Y-m-d'))) {
                    $error = 'Available date cannot be in the past. Please select today or a future date.';
                } else {
                    $stmt = $this->db->prepare("INSERT INTO donation_offers (user_id, available_date, location, notes) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $userId, $available_date, $location, $notes);
                    
                    if ($stmt->execute()) {
                        // Notify all organizations
                        $orgs = $this->db->query("SELECT id FROM organization_personnel WHERE is_verified = 1 AND receive_notifications = 1")->fetch_all(MYSQLI_ASSOC);
                        
                        foreach ($orgs as $org) {
                            $orgId = $org['id'];
                            $msg = $user['full_name'] . " (" . $user['blood_group'] . ") is available to donate blood on " . date('M d, Y', strtotime($available_date)) . " at " . $location;
                            $notifStmt = $this->db->prepare("INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) VALUES (?, 'organization', 'donor_available', 'New Donor Available!', ?, '/organization/donors')");
                            $notifStmt->bind_param("is", $orgId, $msg);
                            $notifStmt->execute();
                        }
                        
                        $this->setFlash('success', 'Donation offer registered! Organizations have been notified.');
                        $this->redirect('user/dashboard');
                    } else {
                        $error = 'Failed to register donation offer';
                    }
                }
            }
        }
        
        // Get user's donation offers
        $stmt = $this->db->prepare("SELECT * FROM donation_offers WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $offers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $this->view('user/donate_blood', [
            'title' => 'Donate Blood',
            'user' => $user,
            'canDonate' => $canDonate,
            'offers' => $offers,
            'error' => $error,
            'success' => $success
        ]);
    }

}
