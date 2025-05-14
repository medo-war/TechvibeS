<?php
/**
 * AJAX endpoint to get partner card content with original styling
 * 
 * This file returns the HTML content of a partner card with original styling for display in a modal
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/PartnerCardController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/PartnerOfferController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Model/PartnerCardGenerator.php';

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

// Get base URL for QR code generation
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$generator = new PartnerCardGenerator($partner, $baseUrl);

// Get offer statistics
$stats = $offerController->getOfferStatistics($partnerId);

// Generate card HTML with styling
ob_start();
?>
<style>
    /* Partner Card Styles */
    .partner-card-container {
        font-family: 'Poppins', sans-serif;
        max-width: 100%;
        margin: 0 auto;
        padding: 0;
        color: white;
    }
    
    .partner-card-preview {
        background: linear-gradient(135deg, #0F0F1B, #1E1E3A);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        border: 2px solid #d4a017; /* Less shiny gold with brown tone */
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 450px; /* Make card more rectangular */
        margin: 0 auto;
        aspect-ratio: 1.6 / 1; /* Credit card-like aspect ratio */
    }
    
    .partner-card-header {
        background: linear-gradient(45deg, #d4a017, #c68e17); /* Less shiny gold with brown tone */
        padding: 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .partner-card-header::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            to bottom right,
            rgba(255, 255, 255, 0.2) 0%,
            rgba(255, 255, 255, 0.1) 25%,
            transparent 50%,
            rgba(255, 255, 255, 0.1) 75%,
            rgba(255, 255, 255, 0.2) 100%
        );
        transform: rotate(30deg);
        animation: shimmer 5s infinite linear;
        z-index: 1;
    }
    
    @keyframes shimmer {
        0% {
            transform: translateX(-100%) rotate(30deg);
        }
        100% {
            transform: translateX(100%) rotate(30deg);
        }
    }
    
    .partner-card-logo {
        width: 100px;
        height: 100px;
        margin: 0 auto 15px;
        background-color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
    
    .partner-card-logo img {
        max-width: 80%;
        max-height: 80%;
    }
    
    .partner-card-logo .initial {
        font-size: 3rem;
        font-weight: 700;
        color: #d4a017; /* Less shiny gold with brown tone */
    }
    
    .partner-card-title {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
        color: white;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 2;
    }
    
    .partner-card-company {
        margin: 5px 0 0;
        font-size: 1.2rem;
        color: rgba(255, 255, 255, 0.8);
        position: relative;
        z-index: 2;
    }
    
    .partner-card-type {
        display: inline-block;
        padding: 5px 15px;
        background-color: rgba(0, 0, 0, 0.3);
        border-radius: 20px;
        margin-top: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        position: relative;
        z-index: 2;
    }
    
    .partner-card-body {
        padding: 20px;
        position: relative;
    }
    
    .partner-card-body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1IiBoZWlnaHQ9IjUiPgo8cmVjdCB3aWR0aD0iNSIgaGVpZ2h0PSI1IiBmaWxsPSIjMWUxZTNhIj48L3JlY3Q+CjxwYXRoIGQ9Ik0wIDVMNSAwWk02IDRMNCA2Wk0tMSAxTDEgLTFaIiBzdHJva2U9IiMzZjNmNWEiIHN0cm9rZS13aWR0aD0iMSI+PC9wYXRoPgo8L3N2Zz4=');
        opacity: 0.3;
        z-index: -1;
    }
    
    .partner-card-info {
        margin-bottom: 20px;
    }
    
    .partner-card-info p {
        margin: 5px 0;
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.7);
    }
    
    .partner-card-info p i {
        width: 20px;
        color: #d4a017; /* Less shiny gold with brown tone */
        margin-right: 10px;
    }
    
    .partner-card-qr {
        text-align: center;
        margin-top: 20px;
        padding: 15px;
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        border: 1px dashed rgba(255, 255, 255, 0.2);
    }
    
    .partner-card-qr img {
        max-width: 200px;
        border: 5px solid #d4a017; /* Less shiny gold with brown tone */
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(212, 160, 23, 0.4);
    }
    
    .partner-card-qr p {
        margin-top: 10px;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
    }
    
    .partner-card-footer {
        background-color: rgba(0, 0, 0, 0.2);
        padding: 15px;
        text-align: center;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.5);
    }
</style>

<div class="partner-card-container">
    <!-- Simple card design with plenty of space for QR code -->
    <table style="width: 100%; max-width: 600px; margin: 0 auto; background: linear-gradient(135deg, #0F0F1B, #1E1E3A); border-radius: 12px; border: 2px solid #d4a017; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);">
        <tr>
            <td colspan="2" style="background: linear-gradient(45deg, #d4a017, #c68e17); padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
                <div style="width: 80px; height: 80px; margin: 0 auto 15px; background-color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);">
                    <?php if (!empty($partner['logo'])): ?>
                    <img src="<?php echo htmlspecialchars($partner['logo']); ?>" alt="Logo" style="max-width: 60px; max-height: 60px;">
                    <?php else: ?>
                    <div style="font-size: 2.5rem; font-weight: 700; color: #d4a017;"><?php echo strtoupper(substr($partner['name'], 0, 1)); ?></div>
                    <?php endif; ?>
                </div>
                <h2 style="margin: 0; font-size: 1.8rem; font-weight: 700; color: white; text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);"><?php echo htmlspecialchars($partner['name']); ?></h2>
                <p style="margin: 5px 0 0; font-size: 1.2rem; color: rgba(255, 255, 255, 0.8);"><?php echo htmlspecialchars($partner['company']); ?></p>
                <div style="display: inline-block; padding: 5px 15px; margin-top: 10px; background: rgba(0, 0, 0, 0.3); border-radius: 20px; font-size: 0.9rem; color: white;">
                    <?php echo htmlspecialchars($partner['partnerType']); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; vertical-align: top; width: 50%;">
                <p style="margin: 5px 0; font-size: 0.95rem; color: rgba(255, 255, 255, 0.7);"><i class="fas fa-envelope" style="width: 20px; color: #d4a017; margin-right: 10px;"></i> <?php echo htmlspecialchars($partner['email']); ?></p>
                <p style="margin: 5px 0; font-size: 0.95rem; color: rgba(255, 255, 255, 0.7);"><i class="fas fa-phone" style="width: 20px; color: #d4a017; margin-right: 10px;"></i> <?php echo htmlspecialchars($partner['phone']); ?></p>
                <?php if (!empty($partner['contractStart']) && !empty($partner['contractEnd'])): ?>
                <p style="margin: 5px 0; font-size: 0.95rem; color: rgba(255, 255, 255, 0.7);"><i class="fas fa-calendar-alt" style="width: 20px; color: #d4a017; margin-right: 10px;"></i> <?php echo date('M d, Y', strtotime($partner['contractStart'])); ?> - <?php echo date('M d, Y', strtotime($partner['contractEnd'])); ?></p>
                <?php endif; ?>
                <p style="margin: 5px 0; font-size: 0.95rem; color: rgba(255, 255, 255, 0.7);"><i class="fas fa-comment" style="width: 20px; color: #d4a017; margin-right: 10px;"></i> <?php echo nl2br(htmlspecialchars(substr($partner['message'] ?? 'Join us in celebrating music and culture with our amazing partner.', 0, 100))); ?>...</p>
            </td>
            <td style="padding: 20px; vertical-align: top; width: 50%; text-align: center;">
                <?php 
                // Get the offer code from the partner offer controller if it exists
                $offer = $offerController->getPartnerOffer($partnerId);
                
                if ($offer && isset($offer['code'])) {
                    $offerCode = $offer['code'];
                } else {
                    // Generate a unique offer code based on partner name and ID
                    $offerCode = substr(str_replace(' ', '', $partner['name']), 0, 3) . $partnerId . rand(100, 999);
                }
                
                // Use the local IP address for the QR code URL
                $localIP = '192.168.137.209';
                
                // Create the partner offer URL with the local IP address
                $offerUrl = "http://{$localIP}/livethemusic/view/front_office/partner-offer.php?id={$partnerId}&code={$offerCode}";
                
                // Generate QR code using QR Server API
                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($offerUrl);
                ?>
                <div style="background-color: white; padding: 10px; border-radius: 10px; display: inline-block; border: 5px solid #d4a017; box-shadow: 0 5px 15px rgba(212, 160, 23, 0.4);">
                    <img src="<?php echo $qrCodeUrl; ?>" alt="Partner Offer QR Code" style="width: 150px; height: 150px;">
                </div>
                <p style="margin-top: 10px; font-size: 1rem; color: rgba(255, 255, 255, 0.9); font-weight: bold;">Scan for exclusive offers!</p>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: rgba(0, 0, 0, 0.2); padding: 15px; text-align: center; font-size: 0.8rem; color: rgba(255, 255, 255, 0.5); border-radius: 0 0 10px 10px;">
                LiveTheMusic Partner Card &copy; <?php echo date('Y'); ?>
            </td>
        </tr>
    </table>
</div>
<?php
$cardHTML = ob_get_clean();

// Return card content and stats as JSON
echo json_encode([
    'cardContent' => $cardHTML,
    'stats' => $stats,
    'partnerId' => $partnerId,
    'success' => true
]);
