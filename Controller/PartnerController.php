<?php
require_once('Config.php');
require_once __DIR__ . '/../Model/partner.php';

class PartnerController {
    
    // Get all partners
    public function getPartners() {
        $sql = "SELECT * FROM partners";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            die('error:' . $e->getMessage());
        }
    }
    
    // Add a new partner
    public function addPartner($partner) {
        $db = Config::getConnexion();
        $sql = "INSERT INTO partners (name, company, email, phone, partnerType, partnershipValue, message, contractStart, contractEnd, contract_template_id) 
                VALUES (:name, :company, :email, :phone, :partnerType, :partnershipValue, :message, :contractStart, :contractEnd, :contract_template_id)";
        try {
            $query = $db->prepare($sql);
            $query->execute([
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
            error_log('Error adding partner: ' . $e->getMessage());
            return false;
        }
    }
    
    // Get partner by ID
    public function getPartnerById($id) {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM partners WHERE id = :id";
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            error_log("Error retrieving partner by ID: " . $e->getMessage());
            return false;
        }
    }
    
    // Get all active partners for public directory
    public function getActivePartners() {
        $db = Config::getConnexion();
        
        // First, ensure the status column exists
        $this->ensurePartnerStatusColumn();
        
        // Get partners that are either explicitly marked as Active or have status=1 (from older records)
        $sql = "SELECT * FROM partners WHERE status = 'Active' OR status = '1' ORDER BY name ASC";
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $partners = $query->fetchAll();
            
            // If no partners found, try getting all partners as a fallback
            if (empty($partners)) {
                $sql = "SELECT * FROM partners ORDER BY name ASC LIMIT 10";
                $query = $db->prepare($sql);
                $query->execute();
                $partners = $query->fetchAll();
            }
            
            return $partners;
        } catch (Exception $e) {
            error_log("Error retrieving active partners: " . $e->getMessage());
            return [];
        }
    }
    
    // Ensure partners table has status column (simplified version of BackOfficePartnerController method)
    public function ensurePartnerStatusColumn() {
        $db = Config::getConnexion();
        
        try {
            // Check if status column exists
            $stmt = $db->query("SHOW COLUMNS FROM partners LIKE 'status'");
            if ($stmt && $stmt->rowCount() == 0) {
                // Add status column if it doesn't exist
                $sql = "ALTER TABLE partners ADD COLUMN status VARCHAR(50) DEFAULT 'Active'";
                $db->exec($sql);
                
                // Update existing records to 'Active'
                $sql = "UPDATE partners SET status = 'Active' WHERE status IS NULL";
                $db->exec($sql);
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Error ensuring partner status column: ' . $e->getMessage());
            return false;
        }
    }
    
    // Get contract templates
    public function getContractTemplates() {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM contract_templates WHERE is_active = 1 ORDER BY name";
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log("Error retrieving contract templates: " . $e->getMessage());
            return [];
        }
    }
    
    // Check if partners table exists, create if not
    public function ensurePartnersTableExists() {
        $db = Config::getConnexion();
        try {
            // Check if partners table exists
            $tableExists = false;
            $stmt = $db->query("SHOW TABLES LIKE 'partners'");
            if ($stmt && $stmt->rowCount() > 0) {
                $tableExists = true;
            }
            
            // Create table if it doesn't exist
            if (!$tableExists) {
                $sql = "CREATE TABLE partners (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    company VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    phone VARCHAR(50) NOT NULL,
                    partnerType VARCHAR(100) NOT NULL,
                    partnershipValue DECIMAL(10,2) NOT NULL,
                    message TEXT,
                    contractType VARCHAR(100),
                    contractStart DATE,
                    contractEnd DATE,
                    contract_template_id INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                $db->exec($sql);
                
                // Create contract_templates table if it doesn't exist
                $stmt = $db->query("SHOW TABLES LIKE 'contract_templates'");
                if ($stmt && $stmt->rowCount() == 0) {
                    $sql = "CREATE TABLE contract_templates (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        description TEXT,
                        benefits TEXT,
                        terms TEXT,
                        duration INT NOT NULL,
                        price_min DECIMAL(10,2) NOT NULL,
                        price_max DECIMAL(10,2) NOT NULL,
                        is_active BOOLEAN DEFAULT 1,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )";
                    $db->exec($sql);
                    
                    // Insert sample contract templates
                    $sql = "INSERT INTO contract_templates (name, description, benefits, terms, duration, price_min, price_max) VALUES
                        ('Bronze Sponsorship', 'Basic sponsorship package for small businesses and startups.', 'Logo on website, Mention in event programs, Social media shoutout', 'Payment due within 30 days of signing. Cancellation requires 30 days notice.', 6, 500.00, 1000.00),
                        ('Silver Partnership', 'Medium-tier partnership for growing businesses.', 'All Bronze benefits plus: Banner at events, Dedicated social media post, Newsletter feature', 'Payment due within 30 days of signing. Cancellation requires 60 days notice.', 12, 1000.00, 3000.00),
                        ('Gold Alliance', 'Premium partnership for established businesses.', 'All Silver benefits plus: VIP event access, Speaking opportunity at one event, Featured interview on website', 'Payment due within 30 days of signing. Cancellation requires 90 days notice.', 12, 3000.00, 7500.00),
                        ('Platinum Exclusive', 'Top-tier exclusive partnership with maximum visibility.', 'All Gold benefits plus: Main stage branding, Co-branded merchandise, Exclusive event sponsorship, Artist meet & greet opportunities', 'Payment due within 30 days of signing. Cancellation requires 120 days notice.', 24, 7500.00, 15000.00)";
                    $db->exec($sql);
                }
                
                return true;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error creating partners table: " . $e->getMessage());
            return false;
        }
    }
}
?>
