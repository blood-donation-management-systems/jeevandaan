<?php
/**
 * Auth Controller
 */
class Auth extends Controller {
    
    public function index() {
        $this->login();
    }
    
    public function login() {
        if ($this->isLoggedIn()) { $this->redirect('user/dashboard'); }
        if ($this->isOrganization()) { $this->redirect('organization/dashboard'); }
        if ($this->isAdmin()) { $this->redirect('admin/dashboard'); }
        $this->view('auth/login', ['title' => 'Login']);
    }
    
    // ==========================================
    // USER LOGIN (Manual)
    // ==========================================
    public function user_login() {
        if ($this->isLoggedIn()) { $this->redirect('user/dashboard'); }
        
        $error = null;
        $success = null;
        $email = '';
        
        $flash = $this->getFlash();
        if ($flash && $flash['type'] === 'success') { $success = $flash['message']; }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = 'Please enter email and password';
            } else {
                $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                
                if ($user && !empty($user['password']) && password_verify($password, $user['password'])) {
                    $this->setUserSession($user);
                    $this->setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');
                    $this->redirect('user/dashboard');
                } else {
                    $error = 'Invalid email or password. Try Google login if you signed up with Google.';
                }
            }
        }
        
        $this->view('auth/user_login', [
            'title' => 'User Login',
            'error' => $error,
            'success' => $success,
            'email' => $email
        ]);
    }
    
    // ==========================================
    // USER REGISTER (Manual)
    // ==========================================
    public function user_register() {
        if ($this->isLoggedIn()) { $this->redirect('user/dashboard'); }
        
        $error = null;
        $full_name = $email = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            
            if (empty($full_name) || empty($email) || empty($password)) {
                $error = 'All fields are required';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match';
            } else {
                // Check existing email
                $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                
                if ($stmt->get_result()->fetch_assoc()) {
                    $error = 'Email already registered. Please login instead.';
                } else {
                    $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                    $googleId = 'manual_' . uniqid();
                    
                    $stmt = $this->db->prepare("INSERT INTO users (google_id, email, password, full_name, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->bind_param("ssss", $googleId, $email, $hashedPass, $full_name);
                    
                    if ($stmt->execute()) {
                        $this->setFlash('success', 'Account created! Please login.');
                        $this->redirect('auth/user-login');
                    } else {
                        $error = 'Failed to create account. Please try again.';
                    }
                }
            }
        }
        
        $this->view('auth/user_register', [
            'title' => 'Create Account',
            'error' => $error,
            'full_name' => $full_name,
            'email' => $email
        ]);
    }
    
    // ==========================================
    // ORGANIZATION LOGIN (Manual)
    // ==========================================
    public function organization_login() {
        if ($this->isOrganization()) { $this->redirect('organization/dashboard'); }
        
        $error = null;
        $success = null;
        $email = '';
        
        $flash = $this->getFlash();
        if ($flash && $flash['type'] === 'success') { $success = $flash['message']; }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = 'Please enter email and password';
            } else {
                $stmt = $this->db->prepare("SELECT * FROM organization_personnel WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $org = $stmt->get_result()->fetch_assoc();
                
                if ($org && !empty($org['password']) && password_verify($password, $org['password'])) {
                    $this->setOrgSession($org);
                    
                    if (!$org['is_verified']) {
                        $this->setFlash('warning', 'After account verification by admin, you will be able to access the portal. Please complete your profile and visit again later.');
                    } else {
                        $this->setFlash('success', 'Welcome back, ' . $org['full_name'] . '!');
                    }
                    $this->redirect('organization/dashboard');
                } else {
                    $error = 'Invalid email or password. Try Google login if you signed up with Google.';
                }
            }
        }
        
        $this->view('auth/organization_login', [
            'title' => 'Organization Login',
            'error' => $error,
            'success' => $success,
            'email' => $email
        ]);
    }
    
    // ==========================================
    // ORGANIZATION REGISTER (Manual)
    // ==========================================
    public function organization_register() {
        if ($this->isOrganization()) { $this->redirect('organization/dashboard'); }
        
        $error = null;
        $full_name = $email = $organization_name = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $organization_name = trim($_POST['organization_name'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            
            if (empty($full_name) || empty($email) || empty($organization_name) || empty($password)) {
                $error = 'All fields are required';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match';
            } else {
                $stmt = $this->db->prepare("SELECT id FROM organization_personnel WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                
                if ($stmt->get_result()->fetch_assoc()) {
                    $error = 'Email already registered. Please login instead.';
                } else {
                    $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                    $googleId = 'manual_org_' . uniqid();
                    
                    $stmt = $this->db->prepare("INSERT INTO organization_personnel (google_id, email, password, full_name, organization_name, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    $stmt->bind_param("sssss", $googleId, $email, $hashedPass, $full_name, $organization_name);
                    
                    if ($stmt->execute()) {
                        $this->setFlash('success', 'Organization account created! Please login.');
                        $this->redirect('auth/organization-login');
                    } else {
                        $error = 'Failed to create account. Please try again.';
                    }
                }
            }
        }
        
        $this->view('auth/organization_register', [
            'title' => 'Register Organization',
            'error' => $error,
            'full_name' => $full_name,
            'email' => $email,
            'organization_name' => $organization_name
        ]);
    }
    
    // ==========================================
    // GOOGLE OAUTH
    // ==========================================
    public function google_user() {
        $_SESSION['oauth_type'] = 'user';
        $this->redirectToGoogle();
    }
    
    public function google_organization() {
        $_SESSION['oauth_type'] = 'organization';
        $this->redirectToGoogle();
    }
    
    private function redirectToGoogle() {
        $params = http_build_query([
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ]);
        header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
        exit;
    }
    
    public function google_callback() {
        if (!isset($_GET['code'])) {
            $this->setFlash('error', 'Authentication failed');
            $this->redirect('auth/login');
        }
        
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'code' => $_GET['code'],
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'grant_type' => 'authorization_code'
        ]));
        $token = json_decode(curl_exec($ch), true);
        curl_close($ch);
        
        if (!isset($token['access_token'])) {
            $this->setFlash('error', 'Google login failed');
            $this->redirect('auth/login');
        }
        
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $token['access_token']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userInfo = json_decode(curl_exec($ch), true);
        curl_close($ch);
        
        $type = $_SESSION['oauth_type'] ?? 'user';
        
        if ($type === 'user') {
            $this->handleGoogleUser($userInfo);
        } else {
            $this->handleGoogleOrg($userInfo);
        }
    }
    
    private function handleGoogleUser($info) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE google_id = ? OR email = ?");
        $stmt->bind_param("ss", $info['id'], $info['email']);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        // NEW USER - Show terms first
        if (!$user) {
            $_SESSION['pending_google_user'] = $info;
            $_SESSION['pending_user_type'] = 'user';
            $this->redirect('auth/google-terms');
        }
        
        if (!$user) {
            $picture = $info['picture'] ?? null;
            $stmt = $this->db->prepare("INSERT INTO users (google_id, email, full_name, profile_picture, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $info['id'], $info['email'], $info['name'], $picture);
            $stmt->execute();
            $userId = $this->db->insert_id;
            
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
        } else {
            // Update google_id if was manual registration
            if (empty($user['google_id']) || strpos($user['google_id'], 'manual_') === 0) {
                $picture = $info['picture'] ?? null;
                $stmt = $this->db->prepare("UPDATE users SET google_id = ?, profile_picture = ? WHERE id = ?");
                $stmt->bind_param("ssi", $info['id'], $picture, $user['id']);
                $stmt->execute();
            }
        }
        
        $this->setUserSession($user);
        $this->setFlash('success', 'Welcome, ' . $user['full_name'] . '!');
        $this->redirect('user/dashboard');
    }
    
    private function handleGoogleOrg($info) {
        $stmt = $this->db->prepare("SELECT * FROM organization_personnel WHERE google_id = ? OR email = ?");
        $stmt->bind_param("ss", $info['id'], $info['email']);
        $stmt->execute();
        $org = $stmt->get_result()->fetch_assoc();
        
        // NEW ORG - Show terms first
        if (!$org) {
            $_SESSION['pending_google_user'] = $info;
            $_SESSION['pending_user_type'] = 'organization';
            $this->redirect('auth/google-terms');
        }
        
        if (!$org) {
            $picture = $info['picture'] ?? null;
            $stmt = $this->db->prepare("INSERT INTO organization_personnel (google_id, email, full_name, profile_picture, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $info['id'], $info['email'], $info['name'], $picture);
            $stmt->execute();
            $orgId = $this->db->insert_id;
            
            $stmt = $this->db->prepare("SELECT * FROM organization_personnel WHERE id = ?");
            $stmt->bind_param("i", $orgId);
            $stmt->execute();
            $org = $stmt->get_result()->fetch_assoc();
        } else {
            if (empty($org['google_id']) || strpos($org['google_id'], 'manual_') === 0) {
                $picture = $info['picture'] ?? null;
                $stmt = $this->db->prepare("UPDATE organization_personnel SET google_id = ?, profile_picture = ? WHERE id = ?");
                $stmt->bind_param("ssi", $info['id'], $picture, $org['id']);
                $stmt->execute();
            }
        }
        
        $this->setOrgSession($org);
        
        if (!$org['is_verified']) {
            $this->setFlash('warning', 'After account verification by admin, you will be able to access the portal. Please complete your profile and visit again later.');
        } else {
            $this->setFlash('success', 'Welcome, ' . $org['full_name'] . '!');
        }
        $this->redirect('organization/dashboard');
    }
    
    // ==========================================
    // SESSION HELPERS
    // ==========================================
    private function setUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = 'user';
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_verified'] = $user['is_verified'];
        
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
    }
    
    private function setOrgSession($org) {
        $_SESSION['user_id'] = $org['id'];
        $_SESSION['user_type'] = 'organization';
        $_SESSION['user_name'] = $org['full_name'];
        $_SESSION['user_email'] = $org['email'];
        $_SESSION['user_verified'] = $org['is_verified'];
        
        $stmt = $this->db->prepare("UPDATE organization_personnel SET last_login = NOW() WHERE id = ?");
        $stmt->bind_param("i", $org['id']);
        $stmt->execute();
    }
    
    // ==========================================
    // ADMIN LOGIN
    // ==========================================
    public function admin_login() {
        if ($this->isAdmin()) { $this->redirect('admin/dashboard'); }
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = ? AND is_active = 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $admin = $stmt->get_result()->fetch_assoc();
            
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_role'] = $admin['role'];
                
                $stmt = $this->db->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
                $stmt->bind_param("i", $admin['id']);
                $stmt->execute();
                
                $this->setFlash('success', 'Welcome, ' . $admin['full_name'] . '!');
                $this->redirect('admin/dashboard');
            } else {
                $error = 'Invalid username or password';
            }
        }
        
        $this->view('auth/admin_login', [
            'title' => 'Admin Login',
            'error' => $error
        ]);
    }
    
    // ==========================================
    // LOGOUT
    // ==========================================
    public function logout() {
        session_destroy();
        session_start();
        $this->setFlash('success', 'Logged out successfully');
        $this->redirect('');
    }

    
    // ==========================================
    // FORGOT PASSWORD - Step 1: Send OTP
    // ==========================================
    public function forgot_password() {
        $type = $_GET['type'] ?? $_POST['type'] ?? 'user';
        if (!in_array($type, ['user', 'organization'])) $type = 'user';
        
        $error = null;
        $success = null;
        $email = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            
            if (empty($email)) {
                $error = 'Please enter your email';
            } else {
                $table = ($type === 'user') ? 'users' : 'organization_personnel';
                
                $stmt = $this->db->prepare("SELECT id, full_name, email FROM $table WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                
                if ($user) {
                    // Generate 6-digit OTP
                    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                    
                    // Save OTP
                    $stmt = $this->db->prepare("UPDATE $table SET otp_code = ?, otp_expires = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $otp, $expires, $user['id']);
                    $stmt->execute();
                    
                    // Send email
                    require_once APP_ROOT . '/app/helpers/EmailHelper.php';
                    $result = EmailHelper::sendOTP($user['email'], $user['full_name'], $otp);
                    
                    if ($result['success']) {
                        $_SESSION['reset_email'] = $email;
                        $_SESSION['reset_type'] = $type;
                        $this->setFlash('success', 'OTP sent to your email!');
                        $this->redirect('auth/verify-otp');
                    } else {
                        $error = 'Failed to send OTP. Please try again later.';
                    }
                } else {
                    $error = 'No account found with this email address';
                }
            }
        }
        
        $this->view('auth/forgot_password', [
            'title' => 'Forgot Password',
            'error' => $error,
            'success' => $success,
            'email' => $email,
            'type' => $type
        ]);
    }
    
    // ==========================================
    // FORGOT PASSWORD - Step 2: Verify OTP
    // ==========================================
    public function verify_otp() {
        if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_type'])) {
            $this->redirect('auth/forgot-password');
        }
        
        $email = $_SESSION['reset_email'];
        $type = $_SESSION['reset_type'];
        $error = null;
        $success = $this->getFlash();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $otp = trim($_POST['otp'] ?? '');
            
            if (empty($otp) || strlen($otp) !== 6) {
                $error = 'Please enter a valid 6-digit OTP';
            } else {
                $table = ($type === 'user') ? 'users' : 'organization_personnel';
                
                $stmt = $this->db->prepare("SELECT id, otp_expires FROM $table WHERE email = ? AND otp_code = ?");
                $stmt->bind_param("ss", $email, $otp);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                
                if (!$user) {
                    $error = 'Invalid OTP code';
                } elseif (strtotime($user['otp_expires']) < time()) {
                    $error = 'OTP has expired. Please request a new one.';
                } else {
                    $_SESSION['otp_verified'] = true;
                    $_SESSION['reset_user_id'] = $user['id'];
                    $this->redirect('auth/reset-password');
                }
            }
        }
        
        $this->view('auth/verify_otp', [
            'title' => 'Verify OTP',
            'error' => $error,
            'success' => $success ? $success['message'] : null,
            'email' => $email,
            'type' => $type
        ]);
    }
    
    // ==========================================
    // FORGOT PASSWORD - Step 3: Reset Password
    // ==========================================
    public function reset_password() {
        if (!isset($_SESSION['otp_verified']) || !isset($_SESSION['reset_user_id'])) {
            $this->redirect('auth/forgot-password');
        }
        
        $type = $_SESSION['reset_type'] ?? 'user';
        $userId = $_SESSION['reset_user_id'];
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            
            if (empty($password) || strlen($password) < 6) {
                $error = 'Password must be at least 6 characters';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match';
            } else {
                $table = ($type === 'user') ? 'users' : 'organization_personnel';
                $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $this->db->prepare("UPDATE $table SET password = ?, otp_code = NULL, otp_expires = NULL WHERE id = ?");
                $stmt->bind_param("si", $hashedPass, $userId);
                
                if ($stmt->execute()) {
                    // Clear session
                    unset($_SESSION['reset_email']);
                    unset($_SESSION['reset_type']);
                    unset($_SESSION['otp_verified']);
                    unset($_SESSION['reset_user_id']);
                    
                    $success = 'Password reset successfully!';
                    $this->setFlash('success', 'Password reset! Please login with new password.');
                    $this->redirect('auth/' . $type . '-login');
                } else {
                    $error = 'Failed to reset password';
                }
            }
        }
        
        $this->view('auth/reset_password', [
            'title' => 'Reset Password',
            'error' => $error,
            'success' => $success,
            'type' => $type
        ]);
    }


    
    // ==========================================
    // GOOGLE TERMS ACCEPTANCE
    // ==========================================
    public function google_terms() {
        if (!isset($_SESSION['pending_google_user'])) {
            $this->redirect('auth/login');
        }
        
        $this->view('auth/google_terms', [
            'title' => 'Accept Terms',
            'user_name' => $_SESSION['pending_google_user']['name'],
            'user_type' => $_SESSION['pending_user_type']
        ]);
    }
    
    public function accept_google_terms() {
        if (!isset($_SESSION['pending_google_user']) || !isset($_POST['agree_terms'])) {
            $this->redirect('auth/login');
        }
        
        $info = $_SESSION['pending_google_user'];
        $type = $_SESSION['pending_user_type'];
        
        if ($type === 'user') {
            $picture = $info['picture'] ?? null;
            $stmt = $this->db->prepare("INSERT INTO users (google_id, email, full_name, profile_picture, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $info['id'], $info['email'], $info['name'], $picture);
            $stmt->execute();
            $userId = $this->db->insert_id;
            
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            
            $this->setUserSession($user);
            unset($_SESSION['pending_google_user'], $_SESSION['pending_user_type']);
            $this->setFlash('success', 'Welcome to JeevanDaan, ' . $user['full_name'] . '!');
            $this->redirect('user/dashboard');
        } else {
            $picture = $info['picture'] ?? null;
            $stmt = $this->db->prepare("INSERT INTO organization_personnel (google_id, email, full_name, profile_picture, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $info['id'], $info['email'], $info['name'], $picture);
            $stmt->execute();
            $orgId = $this->db->insert_id;
            
            $stmt = $this->db->prepare("SELECT * FROM organization_personnel WHERE id = ?");
            $stmt->bind_param("i", $orgId);
            $stmt->execute();
            $org = $stmt->get_result()->fetch_assoc();
            
            $this->setOrgSession($org);
            unset($_SESSION['pending_google_user'], $_SESSION['pending_user_type']);
            $this->setFlash('warning', 'Welcome! Please complete your profile and wait for admin verification.');
            $this->redirect('organization/dashboard');
        }
    }
    
    public function decline_google_terms() {
        unset($_SESSION['pending_google_user'], $_SESSION['pending_user_type']);
        $this->setFlash('warning', 'You must accept the Terms and Conditions to use JeevanDaan.');
        $this->redirect('auth/login');
    }

}
