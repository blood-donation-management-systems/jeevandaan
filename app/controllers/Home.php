<?php
/**
 * Home Controller
 */
class Home extends Controller {
    
    public function index() {
        // Get active blood requests
        $result = $this->db->query("
            SELECT * FROM blood_requests 
            WHERE status = 'active' 
            ORDER BY 
                CASE urgency WHEN 'critical' THEN 1 WHEN 'urgent' THEN 2 ELSE 3 END,
                created_at DESC 
            LIMIT 6
        ");
        $requests = $result->fetch_all(MYSQLI_ASSOC);
        
        $this->view('home/index', [
            'title' => 'Welcome',
            'requests' => $requests
        ]);
    }
    
    public function about() {
        $this->view('home/about', ['title' => 'About Us']);
    }
    
    public function learn() {
        $this->view('home/learn', ['title' => 'Learn About Blood Donation']);
    }
    
    public function contact() {
        $this->view('home/contact', ['title' => 'Contact Us']);
    }
}
