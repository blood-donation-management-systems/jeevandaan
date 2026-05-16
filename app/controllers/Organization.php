<?php
/**
 * Organization Controller
 */
class Organization extends Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->isOrganization()) {
            $this->redirect('auth/login');
        }
    }
    
    public function index() {
        $this->redirect('organization/dashboard');
    }
    
    public function dashboard() {
        $orgId = $_SESSION['user_id'];
        
        // Get org
        $stmt = $this->db->prepare("SELECT * FROM organization_personnel WHERE id = ?");
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $org = $stmt->get_result()->fetch_assoc();
        
        // Get active requests
        $requests = $this->db->query("SELECT * FROM blood_requests WHERE status = 'active' ORDER BY urgency, created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
        
        // Get notifications
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE recipient_id = ? AND recipient_type = 'organization' ORDER BY created_at DESC LIMIT 5");
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $this->view('organization/dashboard', [
            'title' => 'Organization Dashboard',
            'org' => $org,
            'requests' => $requests,
            'notifications' => $notifications,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function donors() {
        if (!$_SESSION['user_verified']) {
            $this->setFlash('error', 'Your account must be verified to view donors. Please wait for admin approval.');
            $this->redirect('organization/dashboard');
        }
        
        $donors = $this->db->query("
            SELECT id, full_name, email, phone, blood_group, district, municipality, last_donation_date, is_verified 
            FROM users 
            WHERE is_verified = 1 AND willing_to_donate = 1 
            ORDER BY blood_group, district
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('organization/donors', [
            'title' => 'All Donors',
            'donors' => $donors
        ]);
    }
    
    public function requests() {
        $requests = $this->db->query("SELECT * FROM blood_requests WHERE status = 'active' ORDER BY urgency, created_at DESC")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('organization/requests', [
            'title' => 'Blood Requests',
            'requests' => $requests
        ]);
    }
    
    public function profile() {
        $orgId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("SELECT * FROM organization_personnel WHERE id = ?");
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $org = $stmt->get_result()->fetch_assoc();
        
        $districts = $this->db->query("SELECT province, district FROM nepal_districts ORDER BY province, district")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('organization/profile', [
            'title' => 'Organization Profile',
            'org' => $org,
            'districts' => $districts,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update_profile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('organization/profile');
        }
        
        $orgId = $_SESSION['user_id'];
        
        // Handle file upload
        $orgIdDoc = null;
        if (isset($_FILES['organization_id_document']) && $_FILES['organization_id_document']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['organization_id_document'];
            if ($file['size'] <= MAX_FILE_SIZE && in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'org_' . uniqid() . '_' . time() . '.' . $ext;
                $targetPath = UPLOAD_PATH . 'id_cards/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $orgIdDoc = $filename;
                }
            }
        }
        
        $sql = "UPDATE organization_personnel SET 
            full_name = ?, phone = ?, organization_name = ?, organization_id = ?,
            organization_type = ?, position = ?, province = ?, district = ?,
            municipality = ?, address = ?, receive_notifications = ?";
        
        $params = [
            $_POST['full_name'],
            $_POST['phone'],
            $_POST['organization_name'],
            $_POST['organization_id'],
            $_POST['organization_type'],
            $_POST['position'],
            $_POST['province'],
            $_POST['district'],
            $_POST['municipality'],
            $_POST['address'],
            isset($_POST['receive_notifications']) ? 1 : 0
        ];
        $types = "ssssssssssi";
        
        if ($orgIdDoc) {
            $sql .= ", organization_id_document = ?";
            $params[] = $orgIdDoc;
            $types .= "s";
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $orgId;
        $types .= "i";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $_POST['full_name'];
            $this->setFlash('success', 'Profile updated successfully!');
        } else {
            $this->setFlash('error', 'Failed to update profile');
        }
        
        $this->redirect('organization/profile');
    }
}
