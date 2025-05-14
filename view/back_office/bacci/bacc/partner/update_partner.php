<?php
/**
 * AJAX endpoint to update partner details
 * 
 * This file handles the partner update form submission using direct database access for debugging
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/Config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method', 'success' => false]);
    exit;
}

// Check if action is updatePartner
if (!isset($_POST['action']) || $_POST['action'] !== 'updatePartner') {
    echo json_encode(['error' => 'Invalid action', 'success' => false]);
    exit;
}

// Check if partner ID is provided
if (!isset($_POST['partnerId']) || empty($_POST['partnerId'])) {
    echo json_encode(['error' => 'Partner ID is required', 'success' => false]);
    exit;
}

// Get form data
$partnerId = intval($_POST['partnerId']);
$name = trim($_POST['name'] ?? '');
$company = trim($_POST['company'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$partnerType = trim($_POST['partnerType'] ?? '');
$partnershipValue = floatval($_POST['partnershipValue'] ?? 0);
$message = trim($_POST['message'] ?? '');
$status = trim($_POST['status'] ?? 'Active');
// contractType field removed as it's redundant with contract_template_id
$contractStart = trim($_POST['contractStart'] ?? '');
$contractEnd = trim($_POST['contractEnd'] ?? '');
$contract_template_id = intval($_POST['contract_template_id'] ?? 0);

// Validate required fields
if (empty($name) || empty($company) || empty($email) || empty($phone) || empty($partnerType)) {
    echo json_encode(['error' => 'Please fill in all required fields', 'success' => false]);
    exit;
}

try {
    // Get database connection
    $db = Config::getConnexion();
    
    // First, check if the contract_template_id column exists
    $checkColumnSql = "SHOW COLUMNS FROM partners LIKE 'contract_template_id'";
    $checkColumnStmt = $db->prepare($checkColumnSql);
    $checkColumnStmt->execute();
    
    if ($checkColumnStmt->rowCount() == 0) {
        // Add contract_template_id column if it doesn't exist
        $addColumnSql = "ALTER TABLE partners ADD COLUMN contract_template_id INT DEFAULT NULL";
        $db->exec($addColumnSql);
    }
    
    // Direct SQL update query - contractType field removed as it's redundant
    $sql = "UPDATE partners SET 
            name = :name, 
            company = :company, 
            email = :email, 
            phone = :phone, 
            partnerType = :partnerType, 
            partnershipValue = :partnershipValue, 
            message = :message, 
            status = :status,
            contractStart = :contractStart, 
            contractEnd = :contractEnd, 
            contract_template_id = :contract_template_id 
            WHERE id = :id";
    
    $query = $db->prepare($sql);
    $result = $query->execute([
        'id' => $partnerId,
        'name' => $name,
        'company' => $company,
        'email' => $email,
        'phone' => $phone,
        'partnerType' => $partnerType,
        'partnershipValue' => $partnershipValue,
        'message' => $message,
        'status' => $status,
        'contractStart' => $contractStart,
        'contractEnd' => $contractEnd,
        'contract_template_id' => $contract_template_id
    ]);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        $errorInfo = $query->errorInfo();
        echo json_encode([
            'error' => 'Database error: ' . ($errorInfo[2] ?? 'Unknown database error'),
            'success' => false
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Exception: ' . $e->getMessage(),
        'success' => false
    ]);
}
