<?php
/**
 * Admin Controller
 */
class Admin extends Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->isAdmin()) {
            $this->redirect('auth/admin-login');
        }
    }
    
    public function index() {
        $this->redirect('admin/dashboard');
    }
    
    public function dashboard() {
        $stats = [
            'total_users' => $this->db->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'],
            'verified_users' => $this->db->query("SELECT COUNT(*) as c FROM users WHERE is_verified = 1")->fetch_assoc()['c'],
            'pending_user_verifications' => $this->db->query("SELECT COUNT(*) as c FROM users WHERE verification_status = 'pending' AND citizenship_front IS NOT NULL")->fetch_assoc()['c'],
            'total_organizations' => $this->db->query("SELECT COUNT(*) as c FROM organization_personnel")->fetch_assoc()['c'],
            'pending_org_verifications' => $this->db->query("SELECT COUNT(*) as c FROM organization_personnel WHERE verification_status = 'pending'")->fetch_assoc()['c'],
            'active_requests' => $this->db->query("SELECT COUNT(*) as c FROM blood_requests WHERE status = 'active'")->fetch_assoc()['c']
        ];
        
        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function verify_users() {
        $users = $this->db->query("
            SELECT * FROM users 
            WHERE verification_status = 'pending' AND citizenship_front IS NOT NULL AND citizenship_back IS NOT NULL
            ORDER BY created_at ASC
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/verify_users', [
            'title' => 'Verify Users',
            'users' => $users,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function view_user($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            $this->setFlash('error', 'User not found');
            $this->redirect('admin/verify-users');
        }
        
        $this->view('admin/view_user', [
            'title' => 'View User',
            'user' => $user
        ]);
    }
    
    public function approve_user($id) {
        $adminId = $_SESSION['admin_id'];
        
        $stmt = $this->db->prepare("UPDATE users SET is_verified = 1, verification_status = 'approved', verified_by = ?, verified_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $adminId, $id);
        
        if ($stmt->execute()) {
            $stmt = $this->db->prepare("INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) VALUES (?, 'user', 'verification', 'Account Verified!', 'Your account has been verified. You can now donate blood!', '/user/profile')");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            $this->setFlash('success', 'User verified successfully!');
        } else {
            $this->setFlash('error', 'Failed to verify user');
        }
        
        $this->redirect('admin/verify-users');
    }
    
    public function reject_user($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = trim($_POST['reason'] ?? 'No reason provided');
            $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : (int)$id;
        } else {
            if (!$id) {
                $this->redirect('admin/verify-users');
            }
            $userId = (int)$id;
            $reason = 'No reason provided';
        }
        
        $adminId = $_SESSION['admin_id'];
        
        $stmt = $this->db->prepare("UPDATE users SET is_verified = 0, verification_status = 'rejected', verified_by = ?, verified_at = NOW(), rejection_reason = ? WHERE id = ?");
        $stmt->bind_param("isi", $adminId, $reason, $userId);
        
        if ($stmt->execute()) {
            $notifMessage = "Your verification was rejected. Reason: " . $reason . ". Please re-upload correct documents.";
            $notifStmt = $this->db->prepare("INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) VALUES (?, 'user', 'verification', 'Verification Rejected', ?, '/user/profile')");
            $notifStmt->bind_param("is", $userId, $notifMessage);
            $notifStmt->execute();
            $this->setFlash('success', 'User rejected and notified!');
        } else {
            $this->setFlash('error', 'Failed to reject user');
        }
        
        $this->redirect('admin/verify-users');
    }
    
    public function verify_organizations() {
        $orgs = $this->db->query("SELECT * FROM organization_personnel WHERE verification_status = 'pending' ORDER BY created_at ASC")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/verify_organizations', [
            'title' => 'Verify Organizations',
            'orgs' => $orgs,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function approve_organization($id) {
        $adminId = $_SESSION['admin_id'];
        
        $stmt = $this->db->prepare("UPDATE organization_personnel SET is_verified = 1, verification_status = 'approved', verified_by = ?, verified_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $adminId, $id);
        
        if ($stmt->execute()) {
            $notifStmt = $this->db->prepare("INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) VALUES (?, 'organization', 'verification', 'Organization Verified!', 'Your organization has been verified!', '/organization/dashboard')");
            $notifStmt->bind_param("i", $id);
            $notifStmt->execute();
            $this->setFlash('success', 'Organization verified!');
        }
        
        $this->redirect('admin/verify-organizations');
    }
    
    public function users() {
        $users = $this->db->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/users', [
            'title' => 'All Users',
            'users' => $users
        ]);
    }
    
    public function requests() {
        $requests = $this->db->query("SELECT * FROM blood_requests ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/requests', [
            'title' => 'All Requests',
            'requests' => $requests
        ]);
    }
    
    public function organizations() {
        $orgs = $this->db->query("SELECT * FROM organization_personnel ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/organizations', [
            'title' => 'Manage Organizations',
            'orgs' => $orgs
        ]);
    }
    
    public function view_organization($id) {
        $stmt = $this->db->prepare("SELECT * FROM organization_personnel WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $org = $stmt->get_result()->fetch_assoc();
        
        if (!$org) {
            $this->setFlash('error', 'Organization not found');
            $this->redirect('admin/organizations');
        }
        
        $this->view('admin/view_organization', [
            'title' => 'View Organization',
            'org' => $org
        ]);
    }
    
    public function reject_organization($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = trim($_POST['reason'] ?? 'No reason provided');
            $orgId = isset($_POST['org_id']) ? (int)$_POST['org_id'] : (int)$id;
        } else {
            if (!$id) {
                $this->redirect('admin/verify-organizations');
            }
            $orgId = (int)$id;
            $reason = 'No reason provided';
        }
        
        $adminId = $_SESSION['admin_id'];
        
        $stmt = $this->db->prepare("UPDATE organization_personnel SET is_verified = 0, verification_status = 'rejected', verified_by = ?, verified_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $adminId, $orgId);
        
        if ($stmt->execute()) {
            $notifMessage = "Your organization verification was rejected. Reason: " . $reason;
            $notifStmt = $this->db->prepare("INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) VALUES (?, 'organization', 'verification', 'Verification Rejected', ?, '/organization/profile')");
            $notifStmt->bind_param("is", $orgId, $notifMessage);
            $notifStmt->execute();
            $this->setFlash('success', 'Organization rejected and notified!');
        }
        
        $this->redirect('admin/verify-organizations');
    }

    
    public function donations() {
        $donations = $this->db->query("
            SELECT do.*, u.full_name, u.email, u.phone, u.blood_group, u.district,
                   u.has_hiv, u.has_hepatitis_b, u.has_hepatitis_c, 
                   u.weight, u.date_of_birth
            FROM donation_offers do
            JOIN users u ON do.user_id = u.id
            ORDER BY do.created_at DESC
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/donations', [
            'title' => 'Donation Offers',
            'donations' => $donations
        ]);
    }
    
    public function update_donation_status() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $donationId = (int)$_POST['donation_id'];
            $status = $_POST['status'];
            
            if (in_array($status, ['available', 'matched', 'donated', 'cancelled'])) {
                $stmt = $this->db->prepare("UPDATE donation_offers SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $donationId);
                
                if ($stmt->execute()) {
                    // Update user's last_donation_date if marked as donated
                    if ($status === 'donated') {
                        $stmt2 = $this->db->prepare("UPDATE users u JOIN donation_offers do ON u.id = do.user_id SET u.last_donation_date = do.available_date WHERE do.id = ?");
                        $stmt2->bind_param("i", $donationId);
                        $stmt2->execute();
                    }
                    $this->setFlash('success', 'Donation status updated!');
                }
            }
        }
        $this->redirect('admin/donations');
    }

}
