<?php
require_once __DIR__ . '/config/config.php';

try {
    // Connect without database first
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $pdo->exec("USE " . DB_NAME);
    
    // Read and execute the schema
    $sql = file_get_contents(__DIR__ . '/database/database_schema.sql');
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "Database and tables created successfully!";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>