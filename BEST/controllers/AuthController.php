<?php
/**
 * Authentication Controller
 * Handles user login, logout, and registration
 */

ob_start();
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Handle user login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            
            if (empty($username) || empty($password)) {
                return $this->jsonResponse(false, 'Username and password are required');
            }
            
            $user = $this->userModel->login($username, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                
                return $this->jsonResponse(true, 'Login successful', [
                    'redirect' => $this->getRedirectUrl($user['role'])
                ]);
            } else {
                return $this->jsonResponse(false, 'Invalid username or password');
            }
        }
    }
    
    /**
     * Handle user registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => isset($_POST['username']) ? trim($_POST['username']) : '',
                'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
                'password' => isset($_POST['password']) ? $_POST['password'] : '', // Don't trim password? Usually we don't, but for typical users it's safer. Let's NOT trim password to allow spaces if intended, but typically leading/trailing spaces are mistakes.
                'full_name' => isset($_POST['full_name']) ? trim($_POST['full_name']) : '',
                'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
                'role' => $_POST['role'] ?? 'donor'
            ];
            
            // Validation
            $errors = $this->validateRegistration($data);
            
            if (!empty($errors)) {
                return $this->jsonResponse(false, 'Validation failed', ['errors' => $errors]);
            }
            
            // Check if username or email already exists
            if ($this->userModel->usernameExists($data['username'])) {
                return $this->jsonResponse(false, 'Username already exists');
            }
            
            if ($this->userModel->emailExists($data['email'])) {
                return $this->jsonResponse(false, 'Email already exists');
            }
            
            $userId = $this->userModel->register($data);
            
            if ($userId) {
                return $this->jsonResponse(true, 'Registration successful', [
                    'redirect' => 'login.php'
                ]);
            } else {
                return $this->jsonResponse(false, 'Registration failed. Please try again.');
            }
        }
    }
    
    /**
     * Handle user logout
     */
    public function logout() {
        session_destroy();
        header('Location: ../index.php');
        exit;
    }
    
    /**
     * Check if user is logged in
     * @return bool
     */
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Check if user has specific role
     * @param string $role Required role
     * @return bool
     */
    public static function hasRole($role) {
        return self::isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Require login (redirect if not logged in)
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ../views/auth/login.php');
            exit;
        }
    }
    
    /**
     * Require specific role (redirect if not authorized)
     * @param string $role Required role
     */
    public static function requireRole($role) {
        self::requireLogin();
        
        if (!self::hasRole($role)) {
            header('Location: ../index.php');
            exit;
        }
    }
    
    /**
     * Validate registration data
     * @param array $data Registration data
     * @return array Validation errors
     */
    private function validateRegistration($data) {
        $errors = [];
        
        if (empty($data['username']) || strlen($data['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }
        
        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Full name is required';
        }
        
        return $errors;
    }
    
    /**
     * Get redirect URL based on user role
     * @param string $role User role
     * @return string Redirect URL
     */
    private function getRedirectUrl($role) {
        switch ($role) {
            case 'admin':
            case 'staff':
                return 'views/admin/dashboard.php';
            case 'volunteer':
                return 'views/volunteer/dashboard.php';
            default:
                return 'index.php';
        }
    }
    
    /**
     * Send JSON response
     * @param bool $success Success status
     * @param string $message Response message
     * @param array $data Additional data
     */
    private function jsonResponse($success, $message, $data = []) {
        // Clear any previous output to ensure clean JSON response
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json');
        echo json_encode(array_merge([
            'success' => $success,
            'message' => $message
        ], $data));
        exit;
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    $controller = new AuthController();
    
    switch ($_GET['action']) {
        case 'login':
            $controller->login();
            break;
        case 'register':
            $controller->register();
            break;
        case 'logout':
            $controller->logout();
            break;
    }
}
