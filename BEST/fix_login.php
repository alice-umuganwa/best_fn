<?php
require_once 'config/config.php';
require_once 'config/Database.php';

header('Content-Type: text/plain');

echo "--- Login Fix Tool ---\n";

$db = Database::getInstance();
$email = 'lesly@gmail.com';
$newPassword = 'password123'; // Setting a known simple password

// 1. Check if user exists
$query = "SELECT * FROM users WHERE email = :email";
$user = $db->fetch($query, [':email' => $email]);

if (!$user) {
    echo "User $email NOT FOUND in database.\n";
    
    // List all users to see what we have
    echo "Listing all users:\n";
    $users = $db->fetchAll("SELECT * FROM users");
    foreach ($users as $u) {
        echo "- ID: {$u['user_id']}, User: {$u['username']}, Email: {$u['email']}\n";
    }
} else {
    echo "User found: {$user['username']} (ID: {$user['user_id']})\n";
    echo "Current Status: {$user['status']}\n";
    
    // 2. Reset Password
    echo "Resetting password to: $newPassword\n";
    
    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
    echo "New Hash: $newHash\n";
    
    $updateQuery = "UPDATE users SET password_hash = :hash WHERE user_id = :id";
    $db->execute($updateQuery, [
        ':hash' => $newHash,
        ':id' => $user['user_id']
    ]);
    
    echo "Password updated in database.\n";
    
    // 3. Verify
    $verifyUser = $db->fetch($query, [':email' => $email]);
    $check = password_verify($newPassword, $verifyUser['password_hash']);
    
    echo "Verification Check: " . ($check ? "SUCCESS" : "FAILED") . "\n";
    
    if ($check) {
        echo "\nFIX COMPLETE: Please login with:\n";
        echo "Email: $email\n";
        echo "Password: $newPassword\n";
    } else {
        echo "\nERROR: Password verification failed even after reset.\n";
    }
}
?>
