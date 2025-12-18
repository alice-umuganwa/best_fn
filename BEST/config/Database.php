<?php
/**
 * Database Connection Class
 * Uses PDO for secure database operations
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            // Try to create the database if it doesn't exist
            try {
                $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS, $options);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
                $pdo = null;
                
                // Now connect to the database
                $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
                
                // Import the schema
                $this->importSchema();
                
            } catch (PDOException $e2) {
                error_log("Database Connection Error: " . $e2->getMessage());
                die("Database connection failed. Please check your configuration.");
            }
        }
    }
    
    /**
     * Get singleton instance of Database
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get PDO connection
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserializing of the instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Execute a prepared statement with parameters
     * @param string $query SQL query
     * @param array $params Parameters for prepared statement
     * @return PDOStatement
     */
    public function execute($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query Execution Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Fetch all rows from a query
     * @param string $query SQL query
     * @param array $params Parameters for prepared statement
     * @return array
     */
    public function fetchAll($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Fetch single row from a query
     * @param string $query SQL query
     * @param array $params Parameters for prepared statement
     * @return array|false
     */
    public function fetch($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt->fetch();
    }
    
    /**
     * Get last inserted ID
     * @return string
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        $this->connection->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        $this->connection->rollBack();
    }
    
    /**
     * Import database schema
     */
    private function importSchema() {
        try {
            $sql = file_get_contents(__DIR__ . '/../database/database_schema.sql');
            
            // Split into lines
            $lines = explode("\n", $sql);
            $statements = [];
            $currentStatement = '';
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '--') === 0) {
                    continue;
                }
                $currentStatement .= $line . ' ';
                if (strpos($line, ';') !== false) {
                    $statement = trim(str_replace(';', '', $currentStatement));
                    if (!empty($statement) && !preg_match('/^CREATE DATABASE/i', $statement) && !preg_match('/^USE/i', $statement)) {
                        $statements[] = $statement;
                    }
                    $currentStatement = '';
                }
            }
            
            foreach ($statements as $statement) {
                $this->connection->exec($statement);
            }
            
        } catch (Exception $e) {
            error_log("Schema Import Error: " . $e->getMessage());
        }
    }
}
