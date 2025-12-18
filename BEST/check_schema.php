<?php
require_once 'config/config.php';
require_once 'config/Database.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Schema Diagnostic</title>
    <style>
        body { font-family: sans-serif; padding: 20px; line-height: 1.6; }
        .status { padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .warning { background: #fff3cd; color: #856404; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Database Schema Diagnostic Tool</h1>
    
    <?php
    $db = Database::getInstance();
    $allOk = true;

    try {
        // Check Users Table
        echo "<h2>Checking 'users' Table</h2>";
        try {
            $columns = $db->fetchAll("DESCRIBE users");
            echo "<table><thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Status</th></tr></thead><tbody>";
            
            $expectedColumns = [
                'user_id' => 'int',
                'username' => 'varchar(50)',
                'email' => 'varchar(100)',
                'password_hash' => 'varchar(255)',
                'full_name' => 'varchar(100)',
                'role' => 'enum',
                'status' => 'enum'
            ];

            $foundColumns = [];

            foreach ($columns as $col) {
                $status = "OK";
                $class = "";
                $foundColumns[$col['Field']] = $col;

                // Basic validation
                if ($col['Field'] == 'password_hash') {
                    if (strpos($col['Type'], 'varchar') !== false) {
                        preg_match('/\d+/', $col['Type'], $matches);
                        if (isset($matches[0]) && $matches[0] < 60) {
                            $status = "ERROR: Too short (need 60+)";
                            $class = "error";
                            $allOk = false;
                        }
                    }
                }

                echo "<tr class='$class'>";
                echo "<td>{$col['Field']}</td>";
                echo "<td>{$col['Type']}</td>";
                echo "<td>{$col['Null']}</td>";
                echo "<td>{$col['Key']}</td>";
                echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
                echo "<td>$status</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";

            // Check for missing columns
            $missing = [];
            foreach ($expectedColumns as $key => $val) {
                if (!isset($foundColumns[$key])) {
                    $missing[] = $key;
                }
            }

            if (!empty($missing)) {
                echo "<div class='status error'>Missing Columns: " . implode(', ', $missing) . "</div>";
                $allOk = false;
            } else {
                echo "<div class='status success'>All required columns present.</div>";
            }

        } catch (Exception $e) {
            echo "<div class='status error'>Table 'users' does not exist or error reading it: " . $e->getMessage() . "</div>";
            $allOk = false;
        }

        // Check Test User
        echo "<h2>Checking Test User (lesly@gmail.com)</h2>";
        $user = $db->fetch("SELECT * FROM users WHERE email = 'lesly@gmail.com'");
        if ($user) {
            echo "<div class='status success'>User found!</div>";
            echo "<ul>";
            echo "<li>ID: {$user['user_id']}</li>";
            echo "<li>Username: {$user['username']}</li>";
            echo "<li>Role: {$user['role']}</li>";
            echo "<li>Status: {$user['status']} (Must be 'active')</li>";
            echo "<li>Password Hash Length: " . strlen($user['password_hash']) . "</li>";
            echo "</ul>";

            if ($user['status'] !== 'active') {
                 echo "<div class='status error'>User is NOT active. Login will fail.</div>";
                 $allOk = false;
            }
        } else {
            echo "<div class='status warning'>Test user 'lesly@gmail.com' not found.</div>";
        }

    } catch (Exception $e) {
        echo "<div class='status error'>System Error: " . $e->getMessage() . "</div>";
    }
    ?>

    <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border: 1px solid #ddd;">
        <h3>Actions</h3>
        <p>If there are errors above, you can try to reset the database schema.</p>
        <p><strong>WARNING: This will delete all existing data!</strong></p>
        <form action="setup_database.php" method="post">
            <button type="submit" onclick="return confirm('Are you sure? This will wipe all data!')">Reset Database Schema</button>
        </form>
    </div>

</body>
</html>
