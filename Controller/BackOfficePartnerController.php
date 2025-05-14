<?php
require_once('Config.php');
require_once __DIR__ . '/../Model/partner.php';
require_once __DIR__ . '/PartnerController.php';

class BackOfficePartnerController extends PartnerController {
    
    // Get all partners with pagination and filtering
    public function getPartnersPaginated($page = 1, $limit = 10, $filters = []) {
        $db = Config::getConnexion();
        
        // Build the base query
        $sql = "SELECT * FROM partners WHERE 1=1";
        $countSql = "SELECT COUNT(*) as total FROM partners WHERE 1=1";
        $params = [];
        
        // Apply filters if provided
        if (!empty($filters)) {
            if (isset($filters['partnerType']) && $filters['partnerType'] !== 'all') {
                $sql .= " AND partnerType = :partnerType";
                $countSql .= " AND partnerType = :partnerType";
                $params['partnerType'] = $filters['partnerType'];
            }
            
            if (isset($filters['status']) && $filters['status'] !== 'all') {
                $sql .= " AND status = :status";
                $countSql .= " AND status = :status";
                $params['status'] = $filters['status'];
            }
            
            // Exclude specific status (e.g., Pending)
            if (isset($filters['excludeStatus'])) {
                $sql .= " AND status != :excludeStatus";
                $countSql .= " AND status != :excludeStatus";
                $params['excludeStatus'] = $filters['excludeStatus'];
            }
            
            if (isset($filters['search']) && !empty($filters['search'])) {
                $sql .= " AND (name LIKE :search OR company LIKE :search OR email LIKE :search)";
                $countSql .= " AND (name LIKE :search OR company LIKE :search OR email LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }
        }
        
        // Apply sorting
        if (isset($filters['sort'])) {
            $sortField = 'name'; // Default sort field
            $sortDirection = 'ASC'; // Default sort direction
            
            switch ($filters['sort']) {
                case 'name':
                    $sortField = 'name';
                    break;
                case 'date':
                    $sortField = 'created_at';
                    $sortDirection = 'DESC';
                    break;
                case 'price':
                    $sortField = 'partnershipValue';
                    $sortDirection = 'DESC';
                    break;
            }
            
            $sql .= " ORDER BY $sortField $sortDirection";
        } else {
            $sql .= " ORDER BY created_at DESC";
        }
        
        // Calculate offset for pagination
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT :limit OFFSET :offset";
        
        try {
            // Get total count
            $countQuery = $db->prepare($countSql);
            foreach ($params as $key => $value) {
                $countQuery->bindValue(':' . $key, $value);
            }
            $countQuery->execute();
            $totalCount = $countQuery->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Get paginated results
            $query = $db->prepare($sql);
            foreach ($params as $key => $value) {
                $query->bindValue(':' . $key, $value);
            }
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->bindValue(':offset', $offset, PDO::PARAM_INT);
            $query->execute();
            $partners = $query->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'partners' => $partners,
                'total' => $totalCount,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => ceil($totalCount / $limit)
            ];
        } catch (Exception $e) {
            error_log('Error getting paginated partners: ' . $e->getMessage());
            return [
                'partners' => [],
                'total' => 0,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => 0
            ];
        }
    }
    
    // Update partner status
    public function updatePartnerStatus($id, $status) {
        $db = Config::getConnexion();
        $sql = "UPDATE partners SET status = :status WHERE id = :id";
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $id,
                'status' => $status
            ]);
            return true;
        } catch (Exception $e) {
            error_log('Error updating partner status: ' . $e->getMessage());
            return false;
        }
    }
    
    // Delete partner
    public function deletePartner($id) {
        $db = Config::getConnexion();
        $sql = "DELETE FROM partners WHERE id = :id";
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log('Error deleting partner: ' . $e->getMessage());
            return false;
        }
    }
    
    // Update partner
    public function updatePartner($partner) {
        $db = Config::getConnexion();
        $sql = "UPDATE partners SET 
                name = :name, 
                company = :company, 
                email = :email, 
                phone = :phone, 
                partnerType = :partnerType, 
                partnershipValue = :partnershipValue, 
                message = :message, 
                contractStart = :contractStart, 
                contractEnd = :contractEnd, 
                contract_template_id = :contract_template_id 
                WHERE id = :id";
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $partner->getId(),
                'name' => $partner->getName(),
                'company' => $partner->getCompany(),
                'email' => $partner->getEmail(),
                'phone' => $partner->getPhone(),
                'partnerType' => $partner->getPartnerType(),
                'partnershipValue' => $partner->getPartnershipValue(),
                'message' => $partner->getMessage(),
                'contractStart' => $partner->getContractStart(),
                'contractEnd' => $partner->getContractEnd(),
                'contract_template_id' => $partner->getContractTemplateId()
            ]);
            return true;
        } catch (Exception $e) {
            error_log('Error updating partner: ' . $e->getMessage());
            return false;
        }
    }
    
    // Get partner applications (pending partners)
    public function getPartnerApplications($forceRefresh = true) {
        try {
            // Always use a new connection for fresh data
            $db = new Config();
            $db = $db->getConnexion();
            
            $sql = "SELECT * FROM partners WHERE status = 'Pending' ORDER BY created_at DESC";
            
            $query = $db->prepare($sql);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Close the connection to ensure fresh data next time
            $db = null;
            
            return $results;
        } catch (Exception $e) {
            error_log('Error getting partner applications: ' . $e->getMessage());
            return [];
        }
    }
    
    // Get approved partners
    public function getApprovedPartners() {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM partners WHERE status = 'Active' ORDER BY name ASC";
        
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error getting approved partners: ' . $e->getMessage());
            return [];
        }
    }
    
    // Get partner statistics with option to force fresh data
    public function getPartnerStatistics($forceRefresh = true) {
        try {
            // Use a new connection for each query to ensure fresh data
            $db = new Config();
            $db = $db->getConnexion();
            
            // Total partners - use prepared statements for consistency
            $totalStmt = $db->prepare("SELECT COUNT(*) as total FROM partners");
            $totalStmt->execute();
            $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Partners by type
            $typeStmt = $db->prepare("SELECT partnerType, COUNT(*) as count FROM partners GROUP BY partnerType");
            $typeStmt->execute();
            $byType = $typeStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Partners by status
            $statusStmt = $db->prepare("SELECT status, COUNT(*) as count FROM partners GROUP BY status");
            $statusStmt->execute();
            $byStatus = $statusStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Total partnership value
            $valueStmt = $db->prepare("SELECT SUM(partnershipValue) as total FROM partners WHERE status = 'Active'");
            $valueStmt->execute();
            $totalValue = $valueStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Recent partners
            $recentStmt = $db->prepare("SELECT * FROM partners ORDER BY created_at DESC LIMIT 5");
            $recentStmt->execute();
            $recent = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Close the connection to ensure fresh data next time
            $db = null;
            
            return [
                'total' => $total,
                'byType' => $byType,
                'byStatus' => $byStatus,
                'totalValue' => $totalValue,
                'recent' => $recent
            ];
        } catch (Exception $e) {
            error_log('Error getting partner statistics: ' . $e->getMessage());
            return [
                'total' => 0,
                'byType' => [],
                'byStatus' => [],
                'totalValue' => 0,
                'recent' => []
            ];
        }
    }
    
    // Ensure partners table has status column
    public function ensurePartnerStatusColumn() {
        $db = Config::getConnexion();
        
        try {
            // Check if status column exists
            $stmt = $db->prepare("SHOW COLUMNS FROM partners LIKE 'status'");
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                // Add status column if it doesn't exist
                $sql = "ALTER TABLE partners ADD COLUMN status VARCHAR(20) DEFAULT 'Pending'"; 
                $db->exec($sql);
                
                // Update existing partners to 'Active' status
                $sql = "UPDATE partners SET status = 'Active' WHERE status IS NULL";
                $db->exec($sql);
            }
            
            // Check if contract_template_id column exists
            $stmt = $db->prepare("SHOW COLUMNS FROM partners LIKE 'contract_template_id'");
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                // Add contract_template_id column if it doesn't exist
                $sql = "ALTER TABLE partners ADD COLUMN contract_template_id INT DEFAULT NULL"; 
                $db->exec($sql);
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Error ensuring partner columns: ' . $e->getMessage());
            return false;
        }
    }
    
    // Get a partner by ID
    public function getPartnerById($id) {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM partners WHERE id = :id";
        
        try {
            $query = $db->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            
            $partner = $query->fetch(PDO::FETCH_ASSOC);
            return $partner ?: false;
        } catch (Exception $e) {
            error_log('Error getting partner by ID: ' . $e->getMessage());
            return false;
        }
    }
    
    // Get contract templates
    public function getContractTemplates() {
        $db = Config::getConnexion();
        
        try {
            // Check if contract_templates table exists
            $stmt = $db->prepare("SHOW TABLES LIKE 'contract_templates'");
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                // Create contract_templates table
                $sql = "CREATE TABLE contract_templates (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    description TEXT,
                    benefits TEXT,
                    terms TEXT,
                    duration INT DEFAULT 12,
                    price_min DECIMAL(10,2) DEFAULT 0,
                    price_max DECIMAL(10,2) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                $db->exec($sql);
                
                // Insert default templates
                $this->insertDefaultContractTemplates();
            }
            
            // Get all templates
            $sql = "SELECT * FROM contract_templates ORDER BY price_min ASC";
            $query = $db->query($sql);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error getting contract templates: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get a contract template by ID
     * 
     * @param int $id The template ID
     * @return array|null The contract template or null if not found
     */
    public function getContractTemplateById($id) {
        if (!$id) return null;
        
        $db = Config::getConnexion();
        
        try {
            $sql = "SELECT * FROM contract_templates WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            return null;
        } catch (Exception $e) {
            error_log('Error getting contract template by ID: ' . $e->getMessage());
            return null;
        }
    }
    
    // Insert default contract templates
    private function insertDefaultContractTemplates() {
        $db = Config::getConnexion();
        
        $templates = [
            [
                'name' => 'Basic Sponsorship',
                'description' => 'Entry-level sponsorship package for small businesses and startups looking to gain visibility at our events.',
                'benefits' => "- Logo on event website\n- Mention in event program\n- 2 complimentary tickets to sponsored events\n- Social media mention",
                'terms' => "- Payment due 30 days before event\n- Cancellation policy: 50% refund if cancelled 60+ days before event\n- No refund for cancellations within 60 days of event",
                'duration' => 6,
                'price_min' => 1000,
                'price_max' => 5000
            ],
            [
                'name' => 'Premium Partnership',
                'description' => 'Comprehensive partnership package for established businesses looking for significant brand exposure and engagement opportunities.',
                'benefits' => "- Premium logo placement on all event materials\n- Dedicated booth space at events\n- Speaking opportunity at one event\n- 10 complimentary VIP tickets\n- Featured in email newsletters\n- Dedicated social media posts",
                'terms' => "- 50% payment due upon signing\n- Remaining balance due 60 days before event\n- Cancellation policy: 75% refund if cancelled 90+ days before event\n- 25% refund for cancellations 60-90 days before event\n- No refund for cancellations within 60 days",
                'duration' => 12,
                'price_min' => 10000,
                'price_max' => 25000
            ],
            [
                'name' => 'Elite Sponsorship',
                'description' => 'Exclusive top-tier sponsorship package with maximum visibility and brand integration throughout the year.',
                'benefits' => "- Title sponsorship of main event\n- Logo prominently displayed on all materials\n- Premium booth location\n- Multiple speaking opportunities\n- 20 VIP tickets with backstage access\n- Exclusive meet & greet with artists\n- Year-round brand presence on website and materials\n- Custom activation opportunities",
                'terms' => "- Payment schedule: 40% upon signing, 30% at 90 days, 30% at 30 days before event\n- First right of refusal for next year's event\n- Exclusivity in sponsor's industry category\n- Cancellation policy: Custom terms negotiated per contract",
                'duration' => 24,
                'price_min' => 50000,
                'price_max' => 100000
            ]
        ];
        
        try {
            $sql = "INSERT INTO contract_templates (name, description, benefits, terms, duration, price_min, price_max) 
                    VALUES (:name, :description, :benefits, :terms, :duration, :price_min, :price_max)";
            $stmt = $db->prepare($sql);
            
            foreach ($templates as $template) {
                $stmt->execute([
                    ':name' => $template['name'],
                    ':description' => $template['description'],
                    ':benefits' => $template['benefits'],
                    ':terms' => $template['terms'],
                    ':duration' => $template['duration'],
                    ':price_min' => $template['price_min'],
                    ':price_max' => $template['price_max']
                ]);
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Error inserting default contract templates: ' . $e->getMessage());
            return false;
        }
    }
}
?>
