<?php
/**
 * Relief Camp Model
 * Handles relief camp management
 */

require_once __DIR__ . '/../config/Database.php';

class ReliefCamp {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new relief camp
     * @param array $data Camp data
     * @return int|false Camp ID or false on failure
     */
    public function create($data) {
        try {
            $query = "INSERT INTO relief_camps 
                      (disaster_id, camp_name, location, latitude, longitude, capacity, 
                       current_occupancy, facilities, status, manager_id, established_date) 
                      VALUES 
                      (:disaster_id, :camp_name, :location, :latitude, :longitude, :capacity, 
                       :current_occupancy, :facilities, :status, :manager_id, :established_date)";
            
            $params = [
                ':disaster_id' => $data['disaster_id'],
                ':camp_name' => $data['camp_name'],
                ':location' => $data['location'],
                ':latitude' => $data['latitude'] ?? null,
                ':longitude' => $data['longitude'] ?? null,
                ':capacity' => $data['capacity'],
                ':current_occupancy' => $data['current_occupancy'] ?? 0,
                ':facilities' => $data['facilities'] ?? null,
                ':status' => $data['status'] ?? 'operational',
                ':manager_id' => $data['manager_id'] ?? null,
                ':established_date' => $data['established_date']
            ];
            
            $this->db->execute($query, $params);
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Create Relief Camp Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get camp by ID
     * @param int $campId Camp ID
     * @return array|false Camp data or false
     */
    public function getById($campId) {
        try {
            $query = "SELECT rc.*, d.disaster_name, u.full_name as manager_name 
                      FROM relief_camps rc 
                      LEFT JOIN disasters d ON rc.disaster_id = d.disaster_id 
                      LEFT JOIN users u ON rc.manager_id = u.user_id 
                      WHERE rc.camp_id = :camp_id";
            
            return $this->db->fetch($query, [':camp_id' => $campId]);
            
        } catch (PDOException $e) {
            error_log("Get Relief Camp Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all camps with optional filters
     * @param array $filters Optional filters
     * @return array Camps list
     */
    public function getAll($filters = []) {
        try {
            $query = "SELECT rc.*, d.disaster_name, u.full_name as manager_name,
                      (SELECT COUNT(*) FROM resources WHERE camp_id = rc.camp_id) as resource_count
                      FROM relief_camps rc 
                      LEFT JOIN disasters d ON rc.disaster_id = d.disaster_id 
                      LEFT JOIN users u ON rc.manager_id = u.user_id 
                      WHERE 1=1";
            
            $params = [];
            
            if (!empty($filters['disaster_id'])) {
                $query .= " AND rc.disaster_id = :disaster_id";
                $params[':disaster_id'] = $filters['disaster_id'];
            }
            
            if (!empty($filters['status'])) {
                $query .= " AND rc.status = :status";
                $params[':status'] = $filters['status'];
            }
            
            $query .= " ORDER BY rc.established_date DESC";
            
            return $this->db->fetchAll($query, $params);
            
        } catch (PDOException $e) {
            error_log("Get All Relief Camps Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update camp information
     * @param int $campId Camp ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update($campId, $data) {
        try {
            $fields = [];
            $params = [':camp_id' => $campId];
            
            $allowedFields = ['camp_name', 'location', 'latitude', 'longitude', 'capacity', 
                            'current_occupancy', 'facilities', 'status', 'manager_id', 'closed_date'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $query = "UPDATE relief_camps SET " . implode(', ', $fields) . " WHERE camp_id = :camp_id";
            $this->db->execute($query, $params);
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Update Relief Camp Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get camp resources
     * @param int $campId Camp ID
     * @return array Resources list
     */
    public function getResources($campId) {
        try {
            $query = "SELECT * FROM resources WHERE camp_id = :camp_id ORDER BY resource_type, resource_name";
            return $this->db->fetchAll($query, [':camp_id' => $campId]);
            
        } catch (PDOException $e) {
            error_log("Get Camp Resources Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Delete camp
     * @param int $campId Camp ID
     * @return bool Success status
     */
    public function delete($campId) {
        try {
            $query = "DELETE FROM relief_camps WHERE camp_id = :camp_id";
            $this->db->execute($query, [':camp_id' => $campId]);
            return true;
            
        } catch (PDOException $e) {
            error_log("Delete Relief Camp Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
