<?php
/**
 * AJAX endpoint to get partner details
 * 
 * This file returns the partner details for display in a modal
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php';

// Check if partner ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Partner ID is required']);
    exit;
}

$partnerId = intval($_GET['id']);
$partnerController = new BackOfficePartnerController();
$partner = $partnerController->getPartnerById($partnerId);

// Return error if partner not found
if (!$partner) {
    echo json_encode(['error' => 'Partner not found']);
    exit;
}

// Get contract template if available
$contractTemplate = null;
try {
    if (!empty($partner['contract_template_id'])) {
        $contractTemplate = $partnerController->getContractTemplateById($partner['contract_template_id']);
    }
} catch (Exception $e) {
    // Log error but continue
    error_log('Error getting contract template: ' . $e->getMessage());
}

// Return partner details as JSON
echo json_encode([
    'partner' => $partner,
    'contractTemplate' => $contractTemplate,
    'success' => true
]);
