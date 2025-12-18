<?php
/**
 * User Model
 * Handles user authentication and management
 */

require_once __DIR__ . '/../config/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Register a new user
     * @param array $data User data
     * @return int|false User ID or false on failure
     */
    public function register($data) {
        try {
            $query = "INSERT INTO users (username, email, password_hash, full_name, phone, role) 
                      VALUES (:username, :email, :password_hash, :full_name, :phone, :role)";
            
            $params = [
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password_hash' => password_hash($data['password'], PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]),
                ':full_name' => $data['full_name'],
                ':phone' => $data['phone'] ?? null,
                ':role' => $data['role'] ?? 'donor'
            ];
            
            $this->db->execute($query, $params);
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("User Registration Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Authenticate user login
     * @param string $username Username or email
     * @param string $password Password
     * @return array|false User data or false on failure
     */
    public function login($username, $password) {
        try {
            $query = "SELECT * FROM users 
                      WHERE (username = :username OR email = :username) 
                      AND status = 'active'";
            
            $user = $this->db->fetch($query, [':username' => $username]);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Update last login
                $this->updateLastLogin($user['user_id']);
                
                // Remove password hash from returned data
                unset($user['password_hash']);
                return $user;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("User Login Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user by ID
     * @param int $userId User ID
     * @return array|false User data or false
     */
    public function getUserById($userId) {
        try {
            $query = "SELECT user_id, username, email, full_name, phone, role, status, created_at, last_login 
                      FROM users WHERE user_id = :user_id";
            
            return $this->db->fetch($query, [':user_id' => $userId]);
            
        } catch (PDOException $e) {
            error_log("Get User Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all users with optional role filter
     * @param string|null $role Filter by role
     * @return array Users list
     */
    public function getAllUsers($role = null) {
        try {
            $query = "SELECT user_id, username, email, full_name, phone, role, status, created_at, last_login 
                      FROM users";
            $params = [];
            
            if ($role) {
                $query .= " WHERE role = :role";
                $params[':role'] = $role;
            }
            
            $query .= " ORDER BY created_at DESC";
            
            return $this->db->fetchAll($query, $params);
            
        } catch (PDOException $e) {
            error_log("Get All Users Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update user information
     * @param int $userId User ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function updateUser($userId, $data) {
        try {
            $fields = [];
            $params = [':user_id' => $userId];
            
            if (isset($data['full_name'])) {
                $fields[] = "full_name = :full_name";
                $params[':full_name'] = $data['full_name'];
            }
            if (isset($data['email'])) {
                $fields[] = "email = :email";
                $params[':email'] = $data['email'];
            }
            if (isset($data['phone'])) {
                $fields[] = "phone = :phone";
                $params[':phone'] = $data['phone'];
            }
            if (isset($data['status'])) {
                $fields[] = "status = :status";
                $params[':status'] = $data['status'];
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = :user_id";
            $this->db->execute($query, $params);
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Update User Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Change user password
     * @param int $userId User ID
     * @param string $newPassword New password
     * @return bool Success status
     */
    public function changePassword($userId, $newPassword) {
        try {
            $query = "UPDATE users SET password_hash = :password_hash WHERE user_id = :user_id";
            
            $params = [
                ':user_id' => $userId,
                ':password_hash' => password_hash($newPassword, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST])
            ];
            
            $this->db->execute($query, $params);
            return true;
            
        } catch (PDOException $e) {
            error_log("Change Password Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if username exists
     * @param string $username Username
     * @return bool
     */
    public function usernameExists($username) {
        $query = "SELECT COUNT(*) as count FROM users WHERE username = :username";
        $result = $this->db->fetch($query, [':username' => $username]);
        return $result['count'] > 0;
    }
    
    /**
     * Check if email exists
     * @param string $email Email
     * @return bool
     */
    public function emailExists($email) {
        $query = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $result = $this->db->fetch($query, [':email' => $email]);
        return $result['count'] > 0;
    }
    
    /**
     * Update last login timestamp
     * @param int $userId User ID
     */
    private function updateLastLogin($userId) {
        $query = "UPDATE users SET last_login = NOW() WHERE user_id = :user_id";
        $this->db->execute($query, [':user_id' => $userId]);
    }
    
    /**
     * Delete user
     * @param int $userId User ID
     * @return bool Success status
     */
    public function deleteUser($userId) {
        try {
            $query = "DELETE FROM users WHERE user_id = :user_id";
            $this->db->execute($query, [':user_id' => $userId]);
            return true;
            
        } catch (PDOException $e) {
            error_log("Delete User Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
