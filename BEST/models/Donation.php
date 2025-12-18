<?php
/**
 * Donation Model
 * Handles donation management
 */

require_once __DIR__ . '/../config/Database.php';

class Donation {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new donation
     * @param array $data Donation data
     * @return int|false Donation ID or false on failure
     */
    public function create($data) {
        try {
            $query = "INSERT INTO donations 
                      (donor_id, disaster_id, donation_type, amount, currency, material_description, 
                       material_quantity, material_unit, payment_method, transaction_id, status, notes) 
                      VALUES 
                      (:donor_id, :disaster_id, :donation_type, :amount, :currency, :material_description, 
                       :material_quantity, :material_unit, :payment_method, :transaction_id, :status, :notes)";
            
            $params = [
                ':donor_id' => $data['donor_id'] ?? null,
                ':disaster_id' => $data['disaster_id'] ?? null,
                ':donation_type' => $data['donation_type'],
                ':amount' => $data['amount'] ?? null,
                ':currency' => $data['currency'] ?? 'USD',
                ':material_description' => $data['material_description'] ?? null,
                ':material_quantity' => $data['material_quantity'] ?? null,
                ':material_unit' => $data['material_unit'] ?? null,
                ':payment_method' => $data['payment_method'] ?? null,
                ':transaction_id' => $data['transaction_id'] ?? null,
                ':status' => $data['status'] ?? 'pending',
                ':notes' => $data['notes'] ?? null
            ];
            
            $this->db->execute($query, $params);
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Create Donation Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get donation by ID
     * @param int $donationId Donation ID
     * @return array|false Donation data or false
     */
    public function getById($donationId) {
        try {
            $query = "SELECT d.*, u.full_name as donor_name, dis.disaster_name 
                      FROM donations d 
                      LEFT JOIN users u ON d.donor_id = u.user_id 
                      LEFT JOIN disasters dis ON d.disaster_id = dis.disaster_id 
                      WHERE d.donation_id = :donation_id";
            
            return $this->db->fetch($query, [':donation_id' => $donationId]);
            
        } catch (PDOException $e) {
            error_log("Get Donation Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all donations with optional filters
     * @param array $filters Optional filters
     * @return array Donations list
     */
    public function getAll($filters = []) {
        try {
            $query = "SELECT d.*, u.full_name as donor_name, dis.disaster_name 
                      FROM donations d 
                      LEFT JOIN users u ON d.donor_id = u.user_id 
                      LEFT JOIN disasters dis ON d.disaster_id = dis.disaster_id 
                      WHERE 1=1";
            
            $params = [];
            
            if (!empty($filters['donor_id'])) {
                $query .= " AND d.donor_id = :donor_id";
                $params[':donor_id'] = $filters['donor_id'];
            }
            
            if (!empty($filters['disaster_id'])) {
                $query .= " AND d.disaster_id = :disaster_id";
                $params[':disaster_id'] = $filters['disaster_id'];
            }
            
            if (!empty($filters['donation_type'])) {
                $query .= " AND d.donation_type = :donation_type";
                $params[':donation_type'] = $filters['donation_type'];
            }
            
            if (!empty($filters['status'])) {
                $query .= " AND d.status = :status";
                $params[':status'] = $filters['status'];
            }
            
            $query .= " ORDER BY d.donation_date DESC";
            
            return $this->db->fetchAll($query, $params);
            
        } catch (PDOException $e) {
            error_log("Get All Donations Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update donation status
     * @param int $donationId Donation ID
     * @param string $status New status
     * @return bool Success status
     */
    public function updateStatus($donationId, $status) {
        try {
            $query = "UPDATE donations SET status = :status WHERE donation_id = :donation_id";
            $this->db->execute($query, [':donation_id' => $donationId, ':status' => $status]);
            return true;
            
        } catch (PDOException $e) {
            error_log("Update Donation Status Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get donation statistics
     * @param int|null $disasterId Optional disaster filter
     * @return array Statistics
     */
    public function getStatistics($disasterId = null) {
        try {
            $query = "SELECT 
                      COUNT(*) as total_donations,
                      COUNT(CASE WHEN donation_type = 'monetary' THEN 1 END) as monetary_count,
                      COUNT(CASE WHEN donation_type = 'material' THEN 1 END) as material_count,
                      SUM(CASE WHEN donation_type = 'monetary' AND status = 'completed' THEN amount ELSE 0 END) as total_amount
                      FROM donations WHERE status = 'completed'";
            
            $params = [];
            
            if ($disasterId) {
                $query .= " AND disaster_id = :disaster_id";
                $params[':disaster_id'] = $disasterId;
            }
            
            return $this->db->fetch($query, $params);
            
        } catch (PDOException $e) {
            error_log("Get Donation Statistics Error: " . $e->getMessage());
            return [];
        }
    }
}
?>
