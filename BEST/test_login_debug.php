<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';
require_once 'models/User.php';

echo "Testing Login Process...\n";

$userModel = new User();

// 1. Create a test user
$username = 'testuser_' . time();
$password = 'password123';
$email = $username . '@example.com';

$data = [
    'username' => $username,
    'email' => $email,
    'password' => $password,
    'full_name' => 'Test User',
    'phone' => '1234567890',
    'role' => 'donor'
];

echo "Registering user: $username\n";
$userId = $userModel->register($data);

if ($userId) {
    echo "User registered successfully with ID: $userId\n";
    
    // 2. Try to login
    echo "Attempting to login...\n";
    $loggedInUser = $userModel->login($username, $password);
    
    if ($loggedInUser) {
        echo "Login SUCCESSFUL!\n";
        print_r($loggedInUser);
    } else {
        echo "Login FAILED!\n";
        
        // Debug: Fetch user directly to check hash
        $db = Database::getInstance();
        $query = "SELECT * FROM users WHERE user_id = :id";
        $user = $db->fetch($query, [':id' => $userId]);
        echo "User record in DB:\n";
        print_r($user);
        
        echo "Password verification check:\n";
        $check = password_verify($password, $user['password_hash']);
        echo "password_verify('$password', '{$user['password_hash']}') = " . ($check ? 'TRUE' : 'FALSE') . "\n";
    }
    
    // Cleanup
    $userModel->deleteUser($userId);
    echo "Test user deleted.\n";
    
} else {
    echo "Registration FAILED!\n";
}
?>
