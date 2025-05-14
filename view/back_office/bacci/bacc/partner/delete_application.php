<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php';

// Initialize controller
$partnerController = new BackOfficePartnerController();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: applications.php?error=' . urlencode('Partner ID is required for deletion'));
    exit;
}

$partnerId = intval($_GET['id']);

// Get partner details for confirmation message
$partner = $partnerController->getPartnerById($partnerId);

// Check if partner exists
if (!$partner) {
    header('Location: applications.php?error=' . urlencode('Partner not found'));
    exit;
}

// Delete partner
if ($partnerController->deletePartner($partnerId)) {
    header('Location: applications.php?success=' . urlencode('Partner application "' . htmlspecialchars($partner['name']) . '" has been deleted successfully'));
} else {
    header('Location: applications.php?error=' . urlencode('Failed to delete partner application'));
}
exit;
?>
