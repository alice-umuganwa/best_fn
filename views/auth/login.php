<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: ../../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Disaster Relief Management System</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
        }
        
        .auth-card {
            background: var(--dark-surface);
            border-radius: var(--radius-2xl);
            padding: var(--spacing-2xl);
            max-width: 450px;
            width: 100%;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .auth-header h1 {
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
        }
        
        .auth-logo {
            font-size: 3rem;
            margin-bottom: var(--spacing-md);
        }
        
        .alert {
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-md);
            display: none;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid var(--error);
            color: var(--error);
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid var(--success);
            color: var(--success);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: var(--spacing-lg);
            padding-top: var(--spacing-lg);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: var(--spacing-md);
            color: var(--text-secondary);
            transition: color var(--transition-base);
        }
        
        .back-link:hover {
            color: var(--primary-light);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card fade-in">
            <a href="../../index.php" class="back-link">← Back to Home</a>
            
            <div class="auth-header">
                <div class="auth-logo">🆘</div>
                <h1>Welcome Back</h1>
                <p>Login to access the Disaster Relief Management System</p>
            </div>
            
            <div id="alert" class="alert"></div>
            
            <form id="loginForm">
                <div class="form-group">
                    <label for="username" class="form-label">Username or Email</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;" id="loginBtn">
                    Login
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
    
    <script>
        const loginForm = document.getElementById('loginForm');
        const alert = document.getElementById('alert');
        const loginBtn = document.getElementById('loginBtn');
        
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Disable button
            loginBtn.disabled = true;
            loginBtn.textContent = 'Logging in...';
            
            const formData = new FormData(loginForm);
            
            try {
                const response = await fetch('../../controllers/AuthController.php?action=login', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.href = '../../' + data.redirect;
                    }, 1000);
                } else {
                    showAlert('error', data.message);
                    loginBtn.disabled = false;
                    loginBtn.textContent = 'Login';
                }
            } catch (error) {
                showAlert('error', 'An error occurred. Please try again.');
                loginBtn.disabled = false;
                loginBtn.textContent = 'Login';
            }
        });
        
        function showAlert(type, message) {
            alert.className = 'alert alert-' + type;
            alert.textContent = message;
            alert.style.display = 'block';
        }
    </script>
</body>
</html>
