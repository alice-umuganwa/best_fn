<?php
/**
 * Disaster Model
 * Handles disaster event management
 */

require_once __DIR__ . '/../config/Database.php';

class Disaster {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new disaster event
     * @param array $data Disaster data
     * @return int|false Disaster ID or false on failure
     */
    public function create($data) {
        try {
            $query = "INSERT INTO disasters 
                      (disaster_name, disaster_type, location, latitude, longitude, severity, 
                       description, affected_population, casualties, status, start_date, created_by) 
                      VALUES 
                      (:disaster_name, :disaster_type, :location, :latitude, :longitude, :severity, 
                       :description, :affected_population, :casualties, :status, :start_date, :created_by)";
            
            $params = [
                ':disaster_name' => $data['disaster_name'],
                ':disaster_type' => $data['disaster_type'],
                ':location' => $data['location'],
                ':latitude' => $data['latitude'] ?? null,
                ':longitude' => $data['longitude'] ?? null,
                ':severity' => $data['severity'],
                ':description' => $data['description'] ?? null,
                ':affected_population' => $data['affected_population'] ?? 0,
                ':casualties' => $data['casualties'] ?? 0,
                ':status' => $data['status'] ?? 'active',
                ':start_date' => $data['start_date'],
                ':created_by' => $data['created_by']
            ];
            
            $this->db->execute($query, $params);
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Create Disaster Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get disaster by ID
     * @param int $disasterId Disaster ID
     * @return array|false Disaster data or false
     */
    public function getById($disasterId) {
        try {
            $query = "SELECT d.*, u.full_name as created_by_name 
                      FROM disasters d 
                      LEFT JOIN users u ON d.created_by = u.user_id 
                      WHERE d.disaster_id = :disaster_id";
            
            return $this->db->fetch($query, [':disaster_id' => $disasterId]);
            
        } catch (PDOException $e) {
            error_log("Get Disaster Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all disasters with optional filters
     * @param array $filters Optional filters (status, type, severity)
     * @return array Disasters list
     */
    public function getAll($filters = []) {
        try {
            $query = "SELECT d.*, u.full_name as created_by_name,
                      (SELECT COUNT(*) FROM relief_camps WHERE disaster_id = d.disaster_id) as camp_count
                      FROM disasters d 
                      LEFT JOIN users u ON d.created_by = u.user_id 
                      WHERE 1=1";
            
            $params = [];
            
            if (!empty($filters['status'])) {
                $query .= " AND d.status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (!empty($filters['type'])) {
                $query .= " AND d.disaster_type = :type";
                $params[':type'] = $filters['type'];
            }
            
            if (!empty($filters['severity'])) {
                $query .= " AND d.severity = :severity";
                $params[':severity'] = $filters['severity'];
            }
            
            $query .= " ORDER BY d.start_date DESC";
            
            return $this->db->fetchAll($query, $params);
            
        } catch (PDOException $e) {
            error_log("Get All Disasters Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update disaster information
     * @param int $disasterId Disaster ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update($disasterId, $data) {
        try {
            $fields = [];
            $params = [':disaster_id' => $disasterId];
            
            $allowedFields = ['disaster_name', 'disaster_type', 'location', 'latitude', 'longitude', 
                            'severity', 'description', 'affected_population', 'casualties', 'status', 'end_date'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $query = "UPDATE disasters SET " . implode(', ', $fields) . " WHERE disaster_id = :disaster_id";
            $this->db->execute($query, $params);
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Update Disaster Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get active disasters
     * @return array Active disasters
     */
    public function getActive() {
        return $this->getAll(['status' => 'active']);
    }
    
    /**
     * Get disaster statistics
     * @param int $disasterId Disaster ID
     * @return array Statistics
     */
    public function getStatistics($disasterId) {
        try {
            $query = "SELECT 
                      (SELECT COUNT(*) FROM relief_camps WHERE disaster_id = :disaster_id) as total_camps,
                      (SELECT SUM(current_occupancy) FROM relief_camps WHERE disaster_id = :disaster_id) as total_occupancy,
                      (SELECT SUM(capacity) FROM relief_camps WHERE disaster_id = :disaster_id) as total_capacity,
                      (SELECT COUNT(*) FROM donations WHERE disaster_id = :disaster_id AND status = 'completed') as total_donations,
                      (SELECT SUM(amount) FROM donations WHERE disaster_id = :disaster_id AND donation_type = 'monetary' AND status = 'completed') as total_amount";
            
            return $this->db->fetch($query, [':disaster_id' => $disasterId]);
            
        } catch (PDOException $e) {
            error_log("Get Disaster Statistics Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Delete disaster
     * @param int $disasterId Disaster ID
     * @return bool Success status
     */
    public function delete($disasterId) {
        try {
            $query = "DELETE FROM disasters WHERE disaster_id = :disaster_id";
            $this->db->execute($query, [':disaster_id' => $disasterId]);
            return true;
            
        } catch (PDOException $e) {
            error_log("Delete Disaster Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
