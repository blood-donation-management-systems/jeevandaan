<?php
/**
 * Base Controller
 */
class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    protected function view($view, $data = []) {
        extract($data);
        require_once APP_ROOT . '/app/views/' . $view . '.php';
    }
    
    protected function redirect($url) {
        header('Location: ' . APP_URL . '/' . ltrim($url, '/'));
        exit;
    }
    
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'user';
    }
    
    protected function isOrganization() {
        return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'organization';
    }
    
    protected function isAdmin() {
        return isset($_SESSION['admin_id']);
    }
    
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
    
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
