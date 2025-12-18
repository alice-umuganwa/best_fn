<?php
/**
 * Database Connection Test Script
 * Tests the connection to the disaster_relief database
 */

require_once __DIR__ . '/config/Database.php';

echo "<!DOCTYPE html>\n";
echo "<html lang='en'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>Database Connection Test</title>\n";
echo "    <style>\n";
echo "        * { margin: 0; padding: 0; box-sizing: border-box; }\n";
echo "        body {\n";
echo "            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;\n";
echo "            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);\n";
echo "            min-height: 100vh;\n";
echo "            display: flex;\n";
echo "            justify-content: center;\n";
echo "            align-items: center;\n";
echo "            padding: 20px;\n";
echo "        }\n";
echo "        .container {\n";
echo "            background: white;\n";
echo "            border-radius: 20px;\n";
echo "            padding: 40px;\n";
echo "            box-shadow: 0 20px 60px rgba(0,0,0,0.3);\n";
echo "            max-width: 800px;\n";
echo "            width: 100%;\n";
echo "        }\n";
echo "        h1 {\n";
echo "            color: #333;\n";
echo "            margin-bottom: 30px;\n";
echo "            text-align: center;\n";
echo "            font-size: 2em;\n";
echo "        }\n";
echo "        .status {\n";
echo "            padding: 20px;\n";
echo "            border-radius: 10px;\n";
echo "            margin-bottom: 20px;\n";
echo "            font-weight: 600;\n";
echo "            text-align: center;\n";
echo "            font-size: 1.2em;\n";
echo "        }\n";
echo "        .success {\n";
echo "            background: #d4edda;\n";
echo "            color: #155724;\n";
echo "            border: 2px solid #c3e6cb;\n";
echo "        }\n";
echo "        .error {\n";
echo "            background: #f8d7da;\n";
echo "            color: #721c24;\n";
echo "            border: 2px solid #f5c6cb;\n";
echo "        }\n";
echo "        .info-box {\n";
echo "            background: #f8f9fa;\n";
echo "            padding: 20px;\n";
echo "            border-radius: 10px;\n";
echo "            margin-bottom: 15px;\n";
echo "        }\n";
echo "        .info-box h3 {\n";
echo "            color: #667eea;\n";
echo "            margin-bottom: 15px;\n";
echo "            font-size: 1.3em;\n";
echo "        }\n";
echo "        .info-item {\n";
echo "            display: flex;\n";
echo "            justify-content: space-between;\n";
echo "            padding: 10px 0;\n";
echo "            border-bottom: 1px solid #dee2e6;\n";
echo "        }\n";
echo "        .info-item:last-child { border-bottom: none; }\n";
echo "        .label {\n";
echo "            font-weight: 600;\n";
echo "            color: #555;\n";
echo "        }\n";
echo "        .value {\n";
echo "            color: #333;\n";
echo "            font-family: 'Courier New', monospace;\n";
echo "        }\n";
echo "        .table-list {\n";
echo "            display: grid;\n";
echo "            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));\n";
echo "            gap: 10px;\n";
echo "            margin-top: 10px;\n";
echo "        }\n";
echo "        .table-item {\n";
echo "            background: white;\n";
echo "            padding: 12px;\n";
echo "            border-radius: 8px;\n";
echo "            border: 2px solid #667eea;\n";
echo "            color: #667eea;\n";
echo "            font-weight: 600;\n";
echo "            text-align: center;\n";
echo "        }\n";
echo "        .icon {\n";
echo "            font-size: 3em;\n";
echo "            margin-bottom: 20px;\n";
echo "            text-align: center;\n";
echo "        }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";
echo "    <div class='container'>\n";
echo "        <h1>🗄️ Database Connection Test</h1>\n";

try {
    // Test database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "<div class='status success'>\n";
    echo "    <div class='icon'>✅</div>\n";
    echo "    Database Connection Successful!\n";
    echo "</div>\n";
    
    // Get database info
    echo "<div class='info-box'>\n";
    echo "    <h3>📊 Database Information</h3>\n";
    
    echo "    <div class='info-item'>\n";
    echo "        <span class='label'>Database Name:</span>\n";
    echo "        <span class='value'>" . DB_NAME . "</span>\n";
    echo "    </div>\n";
    
    echo "    <div class='info-item'>\n";
    echo "        <span class='label'>Host:</span>\n";
    echo "        <span class='value'>" . DB_HOST . "</span>\n";
    echo "    </div>\n";
    
    echo "    <div class='info-item'>\n";
    echo "        <span class='label'>User:</span>\n";
    echo "        <span class='value'>" . DB_USER . "</span>\n";
    echo "    </div>\n";
    
    echo "    <div class='info-item'>\n";
    echo "        <span class='label'>Charset:</span>\n";
    echo "        <span class='value'>" . DB_CHARSET . "</span>\n";
    echo "    </div>\n";
    echo "</div>\n";
    
    // Get table count
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $tableCount = count($tables);
    
    echo "<div class='info-box'>\n";
    echo "    <h3>📋 Database Tables ({$tableCount})</h3>\n";
    echo "    <div class='table-list'>\n";
    foreach ($tables as $table) {
        echo "        <div class='table-item'>{$table}</div>\n";
    }
    echo "    </div>\n";
    echo "</div>\n";
    
    // Get user count
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get disaster count
    $stmt = $conn->query("SELECT COUNT(*) as count FROM disasters");
    $disasterCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get donation count
    $stmt = $conn->query("SELECT COUNT(*) as count FROM donations");
    $donationCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get camp count
    $stmt = $conn->query("SELECT COUNT(*) as count FROM relief_camps");
    $campCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<div class='info-box'>\n";
    echo "    <h3>📈 Sample Data Statistics</h3>\n";
    
    echo "    <div class='info-item'>\n";
    echo "        <span class='label'>Total Users:</span>\n";
    echo "        <span class='value'>{$userCount}</span>\n";
    echo "    </div>\n";
    
    echo "    <div class='info-item'>\n";
    echo "        <span class='label'>Total Disasters:</span>\n";
    echo "        <span class='value'>{$disasterCount}</span>\n";
    echo "    </div>\n";
    
    echo "    <div class='info-item'>\n";
    echo "        <span class='label'>Total Relief Camps:</span>\n";
    echo "        <span class='value'>{$campCount}</span>\n";
    echo "    </div>\n";
    
    echo "    <div class='info-item'>\n";
    echo "        <span class='label'>Total Donations:</span>\n";
    echo "        <span class='value'>{$donationCount}</span>\n";
    echo "    </div>\n";
    echo "</div>\n";
    
    echo "<div class='status success'>\n";
    echo "    ✨ All systems operational! You can now use the application.\n";
    echo "</div>\n";
    
} catch (Exception $e) {
    echo "<div class='status error'>\n";
    echo "    <div class='icon'>❌</div>\n";
    echo "    Database Connection Failed!\n";
    echo "</div>\n";
    
    echo "<div class='info-box'>\n";
    echo "    <h3>Error Details</h3>\n";
    echo "    <p style='color: #721c24; padding: 10px; background: white; border-radius: 5px; font-family: monospace;'>\n";
    echo "        " . htmlspecialchars($e->getMessage()) . "\n";
    echo "    </p>\n";
    echo "</div>\n";
}

echo "    </div>\n";
echo "</body>\n";
echo "</html>\n";
?>
