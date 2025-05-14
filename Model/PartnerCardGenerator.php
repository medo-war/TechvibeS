<?php
/**
 * PartnerCardGenerator Model
 * 
 * This class handles the generation of partner cards with QR codes
 */

// Use the correct path to Config.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/Config.php';

class PartnerCardGenerator {
    private $partner;
    private $baseUrl;
    
    /**
     * Constructor
     * 
     * @param array $partner Partner data array
     * @param string $baseUrl Base URL for QR code generation
     */
    public function __construct($partner, $baseUrl) {
        $this->partner = $partner;
        $this->baseUrl = $baseUrl;
    }
    
    /**
     * Generate QR code for partner
     * 
     * @return string URL to QR code image
     */
    public function generateQRCode() {
        $partnerId = $this->partner['id'];
        
        // Get the offer code from the partner offer controller if it exists
        // or generate a new one if it doesn't
        require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/PartnerOfferController.php';
        $offerController = new PartnerOfferController();
        $offer = $offerController->getPartnerOffer($partnerId);
        
        if ($offer && isset($offer['code'])) {
            $offerCode = $offer['code'];
        } else {
            // Generate a unique offer code based on partner name and ID
            $offerCode = $this->generateOfferCode($this->partner['name'], $partnerId);
        }
        
        // Use the local IP address for the QR code URL
        // This ensures it works on the same WiFi network
        $localIP = '192.168.137.209';
        
        // Create the partner offer URL with the local IP address
        $offerUrl = "http://{$localIP}/livethemusic/view/front_office/partner-offer.php?id={$partnerId}&code={$offerCode}";
        
        // Use QR Server API to generate a QR code with better compatibility
        // Larger size (300x300) for better visibility and scanning
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($offerUrl);
        
        return $qrCodeUrl;
    }
    
    /**
     * Generate a unique offer code based on partner name and ID
     * 
     * @param string $partnerName The partner name
     * @param int $partnerId The partner ID
     * @return string The generated offer code
     */
    private function generateOfferCode($partnerName, $partnerId) {
        // Extract first letters of each word in partner name
        $words = explode(' ', $partnerName);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        // Add partner ID and random numbers
        $code = $initials . $partnerId . rand(100, 999);
        
        return $code;
    }
    
    /**
     * Try to create a QR code using a local library if available
     * This is a fallback method in case the Google Charts API is not accessible
     * 
     * @param string $data The data to encode in the QR code
     * @return string URL to QR code image
     */
    private function generateLocalQRCode($data) {
        // Check if we can use a local library or method
        // For now, we'll just return a default URL to the Google Charts API
        return 'https://chart.googleapis.com/chart?cht=qr&chl=' . urlencode($data) . '&chs=200x200&chld=L|0';
    }
    
    /**
     * Create a QR code with a custom design (for future implementation)
     * 
     * @param string $url The URL to encode
     * @param string $logoPath Optional path to a logo to place in the center
     * @return string URL to the QR code
     */
    private function createCustomQRCode($url, $logoPath = null) {
        // For now, we'll just use the Google Charts API
        // In the future, this could be enhanced with custom styling and a logo
        return 'https://chart.googleapis.com/chart?cht=qr&chl=' . urlencode($url) . '&chs=200x200&chld=L|0';
    }
    
    /**
     * Generate HTML for partner card
     * 
     * @return string HTML content of the partner card
     */
    public function generateCardHTML() {
        // Extract partner data
        $partnerId = $this->partner['id'];
        $partnerName = $this->partner['name'];
        $companyName = $this->partner['company'];
        $partnerType = $this->partner['partnerType'];
        $partnerStatus = $this->partner['status'] ?? 'Active';
        $partnerEmail = $this->partner['email'];
        $partnerPhone = $this->partner['phone'];
        $partnerValue = '$' . number_format($this->partner['partnershipValue'], 2);
        
        // Format dates if available
        $contractDates = '';
        if (!empty($this->partner['contractStart']) && !empty($this->partner['contractEnd'])) {
            $startDate = date('M d, Y', strtotime($this->partner['contractStart']));
            $endDate = date('M d, Y', strtotime($this->partner['contractEnd']));
            $contractDates = "$startDate - $endDate";
        }
        
        // Generate QR code URL
        $qrCodeUrl = $this->generateQRCode();
        
        // Return HTML
        return '<div class="partner-card-container">
            <div class="partner-card">
                <div class="card-header">
                    <h1>' . htmlspecialchars($partnerName) . '</h1>
                    <h2>' . htmlspecialchars($companyName) . '</h2>
                    <span class="partner-type ' . strtolower($partnerType) . '">' . htmlspecialchars($partnerType) . '</span>
                    <span class="partner-status ' . strtolower($partnerStatus) . '">' . htmlspecialchars($partnerStatus) . '</span>
                </div>
                <div class="card-body">
                    <div class="card-info">
                        <p><i class="fas fa-envelope"></i> ' . htmlspecialchars($partnerEmail) . '</p>
                        <p><i class="fas fa-phone"></i> ' . htmlspecialchars($partnerPhone) . '</p>
                        ' . (!empty($contractDates) ? '<p><i class="fas fa-calendar-alt"></i> ' . $contractDates . '</p>' : '') . '
                        <p><i class="fas fa-dollar-sign"></i> ' . $partnerValue . '</p>
                        <p><i class="fas fa-id-badge"></i> Partner ID: ' . $partnerId . '</p>
                    </div>
                    <div class="card-qr">
                        <img src="' . $qrCodeUrl . '" alt="QR Code">
                        <p>Scan for exclusive offers!</p>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="logo">
                        <img src="/livethemusic/view/front_office/assets/images/logo.png" alt="LiveTheMusic Logo">
                    </div>
                    <div class="card-id">
                        <p>Generated: ' . date('Y-m-d H:i:s') . '</p>
                    </div>
                </div>
            </div>
        </div>';
    }
    
    /**
     * Generate CSS for partner card
     * 
     * @return string CSS styles for the partner card
     */
    public function generateCardCSS() {
        return '<style>
            :root {
                --primary-color: #C19A6B; /* Softer gold/brown */
                --secondary-color: #DAA520; /* Goldenrod - less bright */
                --accent-color: #996515; /* Darker brown gold */
                --dark-color: #000000; /* Black */
                --light-color: #1A1A1A; /* Slightly lighter black */
                --gold-gradient-light: #DAA520;
                --gold-gradient-dark: #996515;
            }
            
            body {
                font-family: "Poppins", sans-serif;
                margin: 0;
                padding: 0;
                background-color: var(--dark-color);
                color: white;
            }
            
            .partner-card-container {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 20px;
            }
            
            .partner-card {
                width: 100%;
                max-width: 800px;
                background: linear-gradient(135deg, var(--dark-color), var(--light-color));
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 0 20px rgba(193, 154, 107, 0.5), 0 0 40px rgba(193, 154, 107, 0.3);
                position: relative;
                border: 2px solid var(--primary-color);
            }
            
            .partner-card::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(45deg, 
                    rgba(193, 154, 107, 0) 0%, 
                    rgba(193, 154, 107, 0.2) 50%, 
                    rgba(193, 154, 107, 0) 100%);
                z-index: 0;
                animation: shine 3s infinite linear;
            }
            
            .partner-card::after {
                content: "";
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(
                    circle,
                    rgba(218, 165, 32, 0.07) 0%,
                    rgba(153, 101, 21, 0.04) 30%,
                    transparent 70%
                );
                z-index: 0;
                animation: pulse 4s infinite ease-in-out;
            }
            
            @keyframes pulse {
                0% { opacity: 0.3; }
                50% { opacity: 0.7; }
                100% { opacity: 0.3; }
            }
            
            @keyframes shine {
                0% { background-position: -200% 0; }
                100% { background-position: 200% 0; }
            }
            
            .card-header {
                background: linear-gradient(45deg, var(--gold-gradient-dark), var(--gold-gradient-light), var(--gold-gradient-dark));
                background-size: 200% 200%;
                padding: 25px;
                position: relative;
                z-index: 1;
                border-bottom: 2px solid rgba(193, 154, 107, 0.5);
                animation: goldShimmer 5s infinite ease-in-out;
            }
            
            @keyframes goldShimmer {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            .card-header h1 {
                margin: 0;
                font-size: 28px;
                font-weight: 700;
                color: #000000;
                text-shadow: 0 0 8px rgba(193, 154, 107, 0.6);
                letter-spacing: 1px;
                text-transform: uppercase;
            }
            
            .card-header h2 {
                margin: 5px 0 15px;
                font-size: 18px;
                font-weight: 400;
                color: #000000;
                letter-spacing: 0.5px;
            }
            
            .partner-type, .partner-status {
                display: inline-block;
                padding: 5px 15px;
                border-radius: 50px;
                font-size: 12px;
                font-weight: 600;
                margin-right: 10px;
            }
            
            .partner-type {
                background: linear-gradient(45deg, #000000, #333333);
                color: #DAA520;
                box-shadow: 0 0 8px rgba(193, 154, 107, 0.4);
                border: 1px solid #C19A6B;
            }
            
            .partner-type.venue { background: linear-gradient(45deg, #000000, #333333); }
            .partner-type.sponsor { background: linear-gradient(45deg, #000000, #333333); }
            .partner-type.artist { background: linear-gradient(45deg, #000000, #333333); }
            .partner-type.promoter { background: linear-gradient(45deg, #000000, #333333); }
            .partner-type.media { background: linear-gradient(45deg, #000000, #333333); }
            .partner-type.technology { background: linear-gradient(45deg, #000000, #333333); }
            
            .partner-status {
                background: linear-gradient(45deg, #000000, #333333);
                color: #DAA520;
                box-shadow: 0 0 8px rgba(193, 154, 107, 0.4);
                border: 1px solid #C19A6B;
            }
            
            .partner-status.active { 
                background: linear-gradient(45deg, #000000, #333333); 
                border: 1px solid #C19A6B;
                color: #DAA520;
            }
            .partner-status.pending { 
                background: linear-gradient(45deg, #000000, #333333); 
                border: 1px solid #C19A6B;
                color: #DAA520;
            }
            .partner-status.inactive { 
                background: linear-gradient(45deg, #000000, #333333); 
                border: 1px solid #C19A6B;
                color: #DAA520;
            }
            
            .card-body {
                display: flex;
                padding: 20px;
                position: relative;
                z-index: 1;
            }
            
            .card-info {
                flex: 1;
                padding-right: 20px;
            }
            
            .card-info p {
                margin: 10px 0;
                font-size: 16px;
                color: white;
            }
            
            .card-info i {
                width: 25px;
                color: #DAA520;
                text-shadow: 0 0 8px rgba(193, 154, 107, 0.6);
                margin-right: 10px;
            }
            
            .card-qr {
                text-align: center;
                padding: 10px;
                background: rgba(0, 0, 0, 0.7);
                border-radius: 10px;
                border: 2px solid #C19A6B;
                box-shadow: 0 0 12px rgba(193, 154, 107, 0.4);
            }
            
            .card-qr img {
                max-width: 150px;
                border-radius: 5px;
                background: white;
                padding: 10px;
                margin-bottom: 10px;
            }
            
            .card-qr p {
                font-size: 12px;
                color: rgba(255, 255, 255, 0.8);
                margin: 0;
            }
            
            .card-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 15px 20px;
                background: linear-gradient(45deg, #000000, #1A1A1A);
                border-top: 2px solid rgba(193, 154, 107, 0.5);
                position: relative;
                z-index: 1;
            }
            
            .logo img {
                height: 40px;
            }
            
            .card-id p {
                margin: 0;
                font-size: 12px;
                color: rgba(255, 255, 255, 0.6);
            }
            
            @media print {
                body * {
                    visibility: hidden;
                }
                .partner-card-container, .partner-card-container * {
                    visibility: visible;
                }
                .partner-card-container {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .partner-card {
                    width: 100%;
                    max-width: 800px;
                    box-shadow: none;
                    border: 1px solid #ccc;
                }
            }
        </style>';
    }
}
?>