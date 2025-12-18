<?php
/**
 * EMERGENCY LOGIN FIX
 * This will help us understand what's happening
 */

session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/User.php';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $db = Database::getInstance();
    $userModel = new User();
    
    // Try to find user
    $query = "SELECT * FROM users WHERE (username = :username OR email = :username) AND status = 'active'";
    $user = $db->fetch($query, [':username' => $username]);
    
    $debugInfo = [];
    $debugInfo[] = "Username entered: " . $username;
    $debugInfo[] = "Password entered: " . $password;
    
    if (!$user) {
        $debugInfo[] = "❌ User NOT found in database";
        $allUsers = $db->fetchAll("SELECT username, email FROM users");
        $debugInfo[] = "Available users: " . json_encode($allUsers);
    } else {
        $debugInfo[] = "✓ User found: " . $user['username'];
        $debugInfo[] = "User ID: " . $user['user_id'];
        $debugInfo[] = "User email: " . $user['email'];
        $debugInfo[] = "User role: " . $user['role'];
        $debugInfo[] = "Password hash length: " . strlen($user['password_hash']);
        $debugInfo[] = "Hash starts with: " . substr($user['password_hash'], 0, 10);
        
        $verified = password_verify($password, $user['password_hash']);
        $debugInfo[] = "Password verify result: " . ($verified ? "✓ SUCCESS" : "❌ FAILED");
        
        if ($verified) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            
            header('Location: index.php');
            exit;
        }
    }
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['reg_username'] ?? '';
    $email = $_POST['reg_email'] ?? '';
    $password = $_POST['reg_password'] ?? '';
    $fullname = $_POST['reg_fullname'] ?? '';
    
    $db = Database::getInstance();
    $hash = password_hash($password, PASSWORD_BCRYPT);
    
    try {
        $query = "INSERT INTO users (username, email, password_hash, full_name, role, status) 
                  VALUES (:username, :email, :hash, :fullname, 'donor', 'active')";
        
        $db->execute($query, [
            ':username' => $username,
            ':email' => $email,
            ':hash' => $hash,
            ':fullname' => $fullname
        ]);
        
        $regSuccess = "✅ Registration successful! You can now login.";
    } catch (Exception $e) {
        $regError = "❌ Registration failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Login Fix</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #eee;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 { color: #10b981; margin-bottom: 30px; text-align: center; }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .box {
            background: #16213e;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #0f3460;
        }
        h2 { color: #3b82f6; margin-bottom: 15px; font-size: 1.3rem; }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #94a3b8;
        }
        input {
            width: 100%;
            padding: 10px;
            background: #0f1419;
            border: 1px solid #334155;
            border-radius: 5px;
            color: #fff;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin-top: 10px;
        }
        button:hover { background: #059669; }
        .debug {
            background: #0f1419;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 3px solid #ef4444;
        }
        .debug h3 { color: #ef4444; margin-bottom: 10px; font-size: 1.1rem; }
        .debug-line {
            padding: 5px 0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        .success {
            background: #10b981;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error {
            background: #ef4444;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Emergency Login & Registration Fix</h1>
        
        <?php if (isset($regSuccess)): ?>
            <div class="success"><?= $regSuccess ?></div>
        <?php endif; ?>
        
        <?php if (isset($regError)): ?>
            <div class="error"><?= $regError ?></div>
        <?php endif; ?>
        
        <div class="grid">
            <!-- LOGIN FORM -->
            <div class="box">
                <h2>🔐 Login</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Username or Email</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" name="login">Login</button>
                </form>
                
                <?php if (isset($debugInfo)): ?>
                    <div class="debug">
                        <h3>🐛 Debug Information</h3>
                        <?php foreach ($debugInfo as $info): ?>
                            <div class="debug-line"><?= htmlspecialchars($info) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- REGISTRATION FORM -->
            <div class="box">
                <h2>➕ Register New User</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="reg_fullname" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="reg_username" required minlength="3">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="reg_email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="reg_password" required minlength="6">
                    </div>
                    <button type="submit" name="register">Register</button>
                </form>
            </div>
        </div>
        
        <!-- CURRENT USERS -->
        <div class="box">
            <h2>👥 Current Users in Database</h2>
            <?php
            try {
                $db = Database::getInstance();
                $users = $db->fetchAll("SELECT user_id, username, email, role, status, LENGTH(password_hash) as hash_len FROM users");
                
                if (empty($users)) {
                    echo "<p style='color: #f59e0b;'>⚠️ No users found. Register a new user above.</p>";
                } else {
                    echo "<table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>";
                    echo "<tr style='background: #0f3460;'>";
                    echo "<th style='padding: 10px; text-align: left; border: 1px solid #334155;'>ID</th>";
                    echo "<th style='padding: 10px; text-align: left; border: 1px solid #334155;'>Username</th>";
                    echo "<th style='padding: 10px; text-align: left; border: 1px solid #334155;'>Email</th>";
                    echo "<th style='padding: 10px; text-align: left; border: 1px solid #334155;'>Role</th>";
                    echo "<th style='padding: 10px; text-align: left; border: 1px solid #334155;'>Status</th>";
                    echo "<th style='padding: 10px; text-align: left; border: 1px solid #334155;'>Hash OK?</th>";
                    echo "</tr>";
                    
                    foreach ($users as $user) {
                        $hashOk = ($user['hash_len'] == 60);
                        echo "<tr>";
                        echo "<td style='padding: 10px; border: 1px solid #334155;'>{$user['user_id']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #334155;'><strong>{$user['username']}</strong></td>";
                        echo "<td style='padding: 10px; border: 1px solid #334155;'>{$user['email']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #334155;'>{$user['role']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #334155;'>{$user['status']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #334155;'>" . ($hashOk ? "<span style='color: #10b981;'>✓</span>" : "<span style='color: #ef4444;'>✗</span>") . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            } catch (Exception $e) {
                echo "<p style='color: #ef4444;'>Error: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
