<?php
/**
 * PartnerOfferController
 * 
 * This controller handles the partner offers and loyalty/reward system
 */
require_once('Config.php');
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Model/partner.php';

class PartnerOfferController {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Config::getConnexion();
        
        // Ensure the necessary tables exist
        $this->ensureTablesExist();
    }
    
    /**
     * Ensure necessary tables exist
     */
    private function ensureTablesExist() {
        try {
            // Check if partner_offers table exists
            $stmt = $this->db->prepare("SHOW TABLES LIKE 'partner_offers'");
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                // Create partner_offers table
                $sql = "CREATE TABLE partner_offers (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    partner_id INT NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    code VARCHAR(50) NOT NULL,
                    discount_amount DECIMAL(10,2),
                    discount_type ENUM('percentage', 'fixed') DEFAULT 'percentage',
                    expiry_date DATE,
                    redeemable BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE
                )";
                $this->db->exec($sql);
                
                // Insert some default offers for existing partners
                $this->createDefaultOffers();
            }
            
            // Check if offer_interactions table exists
            $stmt = $this->db->prepare("SHOW TABLES LIKE 'offer_interactions'");
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                // Create offer_interactions table
                $sql = "CREATE TABLE offer_interactions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    partner_id INT NOT NULL,
                    user_id INT,
                    offer_code VARCHAR(50),
                    interaction_type ENUM('scan', 'redeem') DEFAULT 'scan',
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE
                )";
                $this->db->exec($sql);
            }
        } catch (PDOException $e) {
            // Log error
            error_log("Database Error: " . $e->getMessage());
        }
    }
    
    /**
     * Create default offers for existing partners
     */
    private function createDefaultOffers() {
        try {
            // Get all active partners
            $stmt = $this->db->prepare("SELECT id, name FROM partners WHERE status = 'Active'");
            $stmt->execute();
            $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Create default offers
            foreach ($partners as $partner) {
                $code = $this->generateOfferCode($partner['name']);
                $title = "Special Discount";
                $description = "Exclusive offer for LiveTheMusic users! Present this code at our venue to redeem your special discount.";
                
                $stmt = $this->db->prepare("INSERT INTO partner_offers (partner_id, title, description, code, discount_amount, discount_type, expiry_date) 
                    VALUES (:partner_id, :title, :description, :code, :discount_amount, :discount_type, DATE_ADD(CURRENT_DATE, INTERVAL 3 MONTH))");
                
                $stmt->bindParam(':partner_id', $partner['id']);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':code', $code);
                
                // Random discount between 10% and 25%
                $discount = rand(10, 25);
                $stmt->bindParam(':discount_amount', $discount);
                
                $discountType = 'percentage';
                $stmt->bindParam(':discount_type', $discountType);
                
                $stmt->execute();
            }
        } catch (PDOException $e) {
            // Log error
            error_log("Database Error: " . $e->getMessage());
        }
    }
    
    /**
     * Generate a unique offer code based on partner name
     * 
     * @param string $partnerName The partner name
     * @return string The generated offer code
     */
    private function generateOfferCode($partnerName) {
        // Extract first letters of each word in partner name
        $words = explode(' ', $partnerName);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        // Add random numbers
        $code = $initials . rand(1000, 9999);
        
        return $code;
    }
    
    /**
     * Get partner offer
     * 
     * @param int $partnerId The partner ID
     * @return array|null The partner offer or null if not found
     */
    public function getPartnerOffer($partnerId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM partner_offers WHERE partner_id = :partner_id");
            $stmt->bindParam(':partner_id', $partnerId);
            $stmt->execute();
            
            $offer = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($offer) {
                // Get scan count
                $stmt = $this->db->prepare("SELECT COUNT(*) as scan_count FROM offer_interactions 
                    WHERE partner_id = :partner_id AND interaction_type = 'scan'");
                $stmt->bindParam(':partner_id', $partnerId);
                $stmt->execute();
                $scanCount = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $offer['scan_count'] = $scanCount['scan_count'];
                
                return $offer;
            }
            
            return null;
        } catch (PDOException $e) {
            // Log error
            error_log("Database Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Track interaction with partner offer
     * 
     * @param int $partnerId The partner ID
     * @param int|null $userId The user ID (optional)
     * @param string $offerCode The offer code
     * @param string $interactionType The type of interaction ('scan' or 'redeem')
     * @param string $ipAddress IP address of the user
     * @param string $userAgent User agent of the browser
     * @return int The interaction ID or 0 on failure
     */
    public function trackInteraction($partnerId, $userId = null, $offerCode = '', $interactionType = 'scan', $ipAddress = '', $userAgent = '') {
        // Validate parameters
        $partnerId = intval($partnerId);
        if ($partnerId <= 0) {
            error_log("Invalid partner ID in trackInteraction: {$partnerId}");
            return 0;
        }
        
        // Use provided IP and user agent or get from server if not provided
        $ipAddress = $ipAddress ?: ($_SERVER['REMOTE_ADDR'] ?? '');
        $userAgent = $userAgent ?: ($_SERVER['HTTP_USER_AGENT'] ?? '');
        
        // Validate interaction type
        $interactionType = in_array($interactionType, ['scan', 'redeem']) ? $interactionType : 'scan';
        
        try {
            $stmt = $this->db->prepare("INSERT INTO offer_interactions 
                (partner_id, user_id, offer_code, interaction_type, ip_address, user_agent) 
                VALUES (:partner_id, :user_id, :offer_code, :interaction_type, :ip_address, :user_agent)");
            
            $stmt->bindParam(':partner_id', $partnerId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':offer_code', $offerCode);
            $stmt->bindParam(':interaction_type', $interactionType);
            $stmt->bindParam(':ip_address', $ipAddress);
            $stmt->bindParam(':user_agent', $userAgent);
            
            $stmt->execute();
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // Log error
            error_log("Database Error in trackInteraction: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Redeem offer
     * 
     * @param int $partnerId The partner ID
     * @param int $userId The user ID
     * @param string $offerCode The offer code
     * @return array Result with success status and message
     */
    public function redeemOffer($partnerId, $userId, $offerCode) {
        try {
            // Check if offer exists and is redeemable
            $stmt = $this->db->prepare("SELECT * FROM partner_offers 
                WHERE partner_id = :partner_id AND code = :code AND redeemable = TRUE");
            $stmt->bindParam(':partner_id', $partnerId);
            $stmt->bindParam(':code', $offerCode);
            $stmt->execute();
            
            $offer = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$offer) {
                return [
                    'success' => false,
                    'message' => 'This offer is not available or has expired.'
                ];
            }
            
            // Check if user has already redeemed this offer
            $stmt = $this->db->prepare("SELECT * FROM offer_interactions 
                WHERE partner_id = :partner_id AND user_id = :user_id AND offer_code = :offer_code AND interaction_type = 'redeem'");
            $stmt->bindParam(':partner_id', $partnerId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':offer_code', $offerCode);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return [
                    'success' => false,
                    'message' => 'You have already redeemed this offer.'
                ];
            }
            
            // Record redemption
            $stmt = $this->db->prepare("INSERT INTO offer_interactions (partner_id, user_id, offer_code, interaction_type, ip_address, user_agent) 
                VALUES (:partner_id, :user_id, :offer_code, 'redeem', :ip_address, :user_agent)");
            
            $stmt->bindParam(':partner_id', $partnerId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':offer_code', $offerCode);
            
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
            $stmt->bindParam(':ip_address', $ipAddress);
            
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $stmt->bindParam(':user_agent', $userAgent);
            
            $stmt->execute();
            
            // Prepare success message
            $message = 'Congratulations! You have successfully redeemed this offer.';
            if ($offer['discount_type'] == 'percentage') {
                $message .= ' Enjoy your ' . $offer['discount_amount'] . '% discount!';
            } else {
                $message .= ' Enjoy your $' . $offer['discount_amount'] . ' discount!';
            }
            
            return [
                'success' => true,
                'message' => $message
            ];
        } catch (PDOException $e) {
            // Log error
            error_log("Database Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while redeeming the offer. Please try again later.'
            ];
        }
    }
    
    /**
     * Create or update partner offer
     * 
     * @param array $offerData The offer data
     * @return bool True if successful, false otherwise
     */
    public function savePartnerOffer($offerData) {
        try {
            // Check if offer exists
            $stmt = $this->db->prepare("SELECT id FROM partner_offers WHERE partner_id = :partner_id");
            $stmt->bindParam(':partner_id', $offerData['partner_id']);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                // Update existing offer
                $offer = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $stmt = $this->db->prepare("UPDATE partner_offers SET 
                    title = :title,
                    description = :description,
                    code = :code,
                    discount_amount = :discount_amount,
                    discount_type = :discount_type,
                    expiry_date = :expiry_date,
                    redeemable = :redeemable
                    WHERE id = :id");
                
                $stmt->bindParam(':id', $offer['id']);
            } else {
                // Create new offer
                $stmt = $this->db->prepare("INSERT INTO partner_offers (
                    partner_id, title, description, code, discount_amount, discount_type, expiry_date, redeemable
                ) VALUES (
                    :partner_id, :title, :description, :code, :discount_amount, :discount_type, :expiry_date, :redeemable
                )");
            }
            
            $stmt->bindParam(':partner_id', $offerData['partner_id']);
            $stmt->bindParam(':title', $offerData['title']);
            $stmt->bindParam(':description', $offerData['description']);
            $stmt->bindParam(':code', $offerData['code']);
            $stmt->bindParam(':discount_amount', $offerData['discount_amount']);
            $stmt->bindParam(':discount_type', $offerData['discount_type']);
            $stmt->bindParam(':expiry_date', $offerData['expiry_date']);
            $stmt->bindParam(':redeemable', $offerData['redeemable'], PDO::PARAM_BOOL);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get offer statistics
     * 
     * @param int $partnerId The partner ID
     * @param bool $forceRefresh Whether to force a fresh count from the database
     * @return array The offer statistics
     */
    public function getOfferStatistics($partnerId, $forceRefresh = false) {
        // Validate partner ID
        $partnerId = intval($partnerId);
        if ($partnerId <= 0) {
            error_log("Invalid partner ID in getOfferStatistics: {$partnerId}");
            return [
                'total_scans' => 0,
                'total_redemptions' => 0,
                'conversion_rate' => 0,
                'recent_interactions' => []
            ];
        }
        
        try {
            // Initialize stats array
            $stats = [
                'total_scans' => 0,
                'total_redemptions' => 0,
                'conversion_rate' => 0,
                'recent_interactions' => []
            ];
            
            // Get counts in a single query for better performance
            $stmt = $this->db->prepare("SELECT 
                COUNT(*) as total_scans,
                SUM(CASE WHEN interaction_type = 'redeem' THEN 1 ELSE 0 END) as total_redemptions
                FROM offer_interactions 
                WHERE partner_id = :partner_id");
            $stmt->bindParam(':partner_id', $partnerId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Update stats with query results
            $stats['total_scans'] = intval($result['total_scans']);
            $stats['total_redemptions'] = intval($result['total_redemptions']);
            
            // Calculate conversion rate
            if ($stats['total_scans'] > 0) {
                $stats['conversion_rate'] = round(($stats['total_redemptions'] / $stats['total_scans']) * 100, 2);
            }
            
            // Get recent interactions
            $stmt = $this->db->prepare("SELECT oi.*, u.first_name, u.last_name 
                FROM offer_interactions oi
                LEFT JOIN users u ON oi.user_id = u.id
                WHERE oi.partner_id = :partner_id
                ORDER BY oi.created_at DESC
                LIMIT 10");
            $stmt->bindParam(':partner_id', $partnerId);
            $stmt->execute();
            $stats['recent_interactions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (PDOException $e) {
            // Log error with more context
            error_log("Database Error in getOfferStatistics for partner {$partnerId}: " . $e->getMessage());
            return [
                'total_scans' => 0,
                'total_redemptions' => 0,
                'conversion_rate' => 0,
                'recent_interactions' => []
            ];
        }
    }
}
?>
