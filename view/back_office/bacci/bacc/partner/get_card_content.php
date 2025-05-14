<?php
/**
 * AJAX endpoint to get partner card content
 * 
 * This file returns the HTML content of a partner card for display in a modal
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/PartnerCardController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/PartnerOfferController.php';

// Check if partner ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Partner ID is required']);
    exit;
}

$partnerId = intval($_GET['id']);
$cardController = new PartnerCardController();
$offerController = new PartnerOfferController();
$partner = $cardController->getPartnerById($partnerId);

// Return error if partner not found
if (!$partner) {
    echo json_encode(['error' => 'Partner not found']);
    exit;
}

// Generate card content - we need to extract just the card HTML, not the full page
// Get the partner data directly
$partner = $cardController->getPartnerById($partnerId);

// Create a card generator instance
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Model/PartnerCardGenerator.php';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$generator = new PartnerCardGenerator($partner, $baseUrl);

// Generate just the card HTML without the full page layout
$cardContent = $generator->generateCardHTML();

// Get offer statistics
$stats = $offerController->getOfferStatistics($partnerId);

// Return card content and stats as JSON
echo json_encode([
    'cardContent' => $cardContent,
    'stats' => $stats,
    'partnerId' => $partnerId,
    'success' => true
]);
