<?php
require_once 'Controller/Config.php';

// Set content type to plain text for easier reading
header('Content-Type: text/plain');

echo "Database Diagnostic Tool\n";
echo "=======================\n\n";

try {
    // Connect to database
    $db = Config::getConnexion();
    echo "Database connection successful\n\n";
    
    // Check if offer_interactions table exists
    $stmt = $db->prepare("SHOW TABLES LIKE 'offer_interactions'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        echo "ERROR: offer_interactions table does not exist!\n";
        echo "Creating offer_interactions table...\n";
        
        // Create offer_interactions table
        $sql = "CREATE TABLE offer_interactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            partner_id INT NOT NULL,
            user_id INT,
            offer_code VARCHAR(50),
            interaction_type ENUM('scan', 'redeem') DEFAULT 'scan',
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $db->exec($sql);
        echo "offer_interactions table created successfully\n";
    } else {
        echo "offer_interactions table exists\n";
        
        // Check table structure
        $stmt = $db->prepare("DESCRIBE offer_interactions");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Table structure:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']}: {$column['Type']}\n";
        }
    }
    
    // Count existing interactions
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM offer_interactions");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nTotal interactions in database: {$result['count']}\n";
    
    // Get all partners
    $stmt = $db->prepare("SELECT id, name FROM partners");
    $stmt->execute();
    $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nPartners in database:\n";
    foreach ($partners as $partner) {
        // Count interactions for this partner
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM offer_interactions WHERE partner_id = :partner_id");
        $stmt->bindParam(':partner_id', $partner['id']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "- Partner #{$partner['id']} ({$partner['name']}): {$result['count']} interactions\n";
    }
    
    // Insert a test interaction for the first partner
    if (!empty($partners)) {
        $partnerId = $partners[0]['id'];
        
        $stmt = $db->prepare("INSERT INTO offer_interactions (partner_id, offer_code, interaction_type, ip_address) 
            VALUES (:partner_id, 'TEST123', 'scan', '127.0.0.1')");
        $stmt->bindParam(':partner_id', $partnerId);
        $stmt->execute();
        
        echo "\nTest interaction inserted for partner #{$partnerId}\n";
        
        // Count interactions again
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM offer_interactions WHERE partner_id = :partner_id");
        $stmt->bindParam(':partner_id', $partnerId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Partner #{$partnerId} now has {$result['count']} interactions\n";
    }
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
