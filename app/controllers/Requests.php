<?php
class Requests extends Controller {
    
    public function index() {
        $requests = $this->db->query("
            SELECT * FROM blood_requests 
            WHERE status = 'active' 
            ORDER BY 
                CASE urgency WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END,
                created_at DESC
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('requests/index', [
            'title' => 'Blood Requests',
            'requests' => $requests
        ]);
    }
    
    public function details($id = null) {
        if (!$id) { $this->redirect('requests'); }
        
        $stmt = $this->db->prepare("SELECT * FROM blood_requests WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        
        if (!$request) {
            $this->setFlash('error', 'Request not found');
            $this->redirect('requests');
        }
        
        $this->view('requests/view', [
            'title' => 'Blood Request',
            'request' => $request
        ]);
    }
    
    public function create() {
        if (!$this->isLoggedIn() && !$this->isOrganization()) {
            $this->redirect('auth/login');
        }
        
        $districts = $this->db->query("SELECT DISTINCT district FROM nepal_districts ORDER BY district")->fetch_all(MYSQLI_ASSOC);
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation
            $patient_name = trim($_POST['patient_name'] ?? '');
            $hospital_name = trim($_POST['hospital_name'] ?? '');
            $hospital_address = trim($_POST['hospital_address'] ?? '');
            $contact_name = trim($_POST['contact_name'] ?? '');
            $contact_phone = trim($_POST['contact_phone'] ?? '');
            $required_by = $_POST['required_by'] ?? '';
            
            // Check if names contain only numbers
            $numericPattern = '/^[\d\s\-_.,()]+$/';
            
            if (preg_match($numericPattern, $patient_name)) {
                $error = 'Patient name cannot contain only numbers';
            } elseif (preg_match($numericPattern, $hospital_name)) {
                $error = 'Hospital name cannot contain only numbers';
            } elseif (!empty($hospital_address) && preg_match($numericPattern, $hospital_address)) {
                $error = 'Hospital address cannot contain only numbers';
            } elseif (preg_match($numericPattern, $contact_name)) {
                $error = 'Contact name cannot contain only numbers';
            } elseif (!preg_match('/^9[0-9]{9}$/', $contact_phone)) {
                $error = 'Phone number must be 10 digits starting with 9';
            } elseif (!empty($required_by) && strtotime($required_by) < time()) {
                $error = 'Required by date cannot be in the past';
            }
            
            if (!empty($error)) {
                $this->view('requests/create', [
                    'title' => 'Create Blood Request',
                    'districts' => $districts,
                    'error' => $error
                ]);
                return;
            }
            
            $requiredBy = !empty($_POST['required_by']) ? $_POST['required_by'] : null;
            
            $stmt = $this->db->prepare("
                INSERT INTO blood_requests 
                (requested_by, requester_type, patient_name, patient_age, blood_group, 
                units_required, hospital_name, hospital_district, hospital_address, 
                urgency, contact_name, contact_phone, contact_email, reason, 
                required_by, additional_notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param("ississssssssssss",
                $_SESSION['user_id'], $_SESSION['user_type'],
                $_POST['patient_name'], $_POST['patient_age'], $_POST['blood_group'],
                $_POST['units_required'], $_POST['hospital_name'], $_POST['hospital_district'],
                $_POST['hospital_address'], $_POST['urgency'], $_POST['contact_name'],
                $_POST['contact_phone'], $_POST['contact_email'], $_POST['reason'],
                $requiredBy, $_POST['additional_notes']
            );
            
            if ($stmt->execute()) {
                $requestId = $this->db->insert_id;
                
                // Notify matching donors
                $bloodGroup = $this->db->real_escape_string($_POST['blood_group']);
                $donors = $this->db->query("
                    SELECT id FROM users 
                    WHERE blood_group = '$bloodGroup' AND is_verified = 1 
                    AND willing_to_donate = 1 AND receive_notifications = 1
                ")->fetch_all(MYSQLI_ASSOC);
                
                $urgencyLabel = ucfirst($_POST['urgency']);
                
                foreach ($donors as $donor) {
                    $donorId = $donor['id'];
                    $msg = "$urgencyLabel priority: Someone needs $bloodGroup blood at " . $_POST['hospital_name'];
                    $link = "/requests/details/$requestId";
                    
                    $notifStmt = $this->db->prepare("
                        INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) 
                        VALUES (?, 'user', 'blood_request', 'Blood Request - $urgencyLabel Priority', ?, ?)
                    ");
                    $notifStmt->bind_param("iss", $donorId, $msg, $link);
                    $notifStmt->execute();
                }
                
                $this->setFlash('success', 'Blood request created! ' . count($donors) . ' matching donors notified.');
                $this->redirect('requests/details/' . $requestId);
            } else {
                $error = 'Failed to create request';
            }
        }
        
        $this->view('requests/create', [
            'title' => 'Create Blood Request',
            'districts' => $districts,
            'error' => $error
        ]);
    }
}
