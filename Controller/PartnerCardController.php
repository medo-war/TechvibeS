<?php
/**
 * PartnerCardController
 * 
 * This controller handles the generation of partner cards with QR codes
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Model/PartnerCardGenerator.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php';
require_once('Config.php');

class PartnerCardController {
    private $partnerController;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->partnerController = new BackOfficePartnerController();
    }
    
    /**
     * Get partner data by ID
     * 
     * @param int $partnerId Partner ID
     * @return array|null Partner data or null if not found
     */
    public function getPartnerById($partnerId) {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM partners WHERE id = :id";
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $partnerId]);
            $partner = $query->fetch(PDO::FETCH_ASSOC);
            
            return $partner ?: null;
        } catch (Exception $e) {
            error_log('Error getting partner by ID: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate partner card HTML
     * 
     * @param int $partnerId Partner ID
     * @return string|null HTML content of the partner card or null if partner not found
     */
    public function generatePartnerCard($partnerId) {
        // Get partner data
        $partner = $this->getPartnerById($partnerId);
        
        if (!$partner) {
            return null;
        }
        
        // Get base URL
        $baseUrl = $this->getBaseUrl();
        
        // Create card generator
        $cardGenerator = new PartnerCardGenerator($partner, $baseUrl);
        
        // Generate card HTML and CSS
        $cardCSS = $cardGenerator->generateCardCSS();
        $cardHTML = $cardGenerator->generateCardHTML();
        
        return $cardCSS . $cardHTML;
    }
    
    /**
     * Get base URL for QR code generation
     * 
     * @return string Base URL
     */
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . $host;
    }
    
    /**
     * Generate partner card as downloadable PDF
     * 
     * @param int $partnerId Partner ID
     * @return bool True if PDF was generated successfully, false otherwise
     */
    public function generatePartnerCardPDF($partnerId) {
        // Get partner data
        $partner = $this->getPartnerById($partnerId);
        
        if (!$partner) {
            return false;
        }
        
        // Get base URL
        $baseUrl = $this->getBaseUrl();
        
        // Create card generator
        $cardGenerator = new PartnerCardGenerator($partner, $baseUrl);
        
        // Generate QR code URL
        $qrCodeUrl = $cardGenerator->generateQRCode();
        
        // Format partner data for PDF
        $partnerName = $partner['name'];
        $companyName = $partner['company'];
        $partnerType = $partner['partnerType'];
        $partnerStatus = $partner['status'];
        $partnerEmail = $partner['email'];
        $partnerPhone = $partner['phone'];
        $partnerValue = '$' . number_format($partner['partnershipValue'], 2);
        
        // Format dates if available
        $contractDates = '';
        if (!empty($partner['contractStart']) && !empty($partner['contractEnd'])) {
            $startDate = date('M d, Y', strtotime($partner['contractStart']));
            $endDate = date('M d, Y', strtotime($partner['contractEnd']));
            $contractDates = "$startDate - $endDate";
        }
        
        // Create PDF using basic PHP functionality (no external libraries)
        $filename = 'partner_card_' . $partnerId . '.html';
        
        // Use a directory that's accessible via the web server
        $tempDir = $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/view/back_office/bacci/bacc/partner/temp/';
        $filepath = $tempDir . $filename;
        $webPath = '/livethemusic/view/back_office/bacci/bacc/partner/temp/' . $filename;
        
        // Create temp directory if it doesn't exist
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        
        // Generate HTML for PDF
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Partner Card - ' . htmlspecialchars($partnerName) . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #0F0F1B;
                    color: white;
                }
                .partner-card {
                    width: 800px;
                    margin: 20px auto;
                    border: 2px solid #FF0055;
                    border-radius: 20px;
                    overflow: hidden;
                    background-color: #1E1E3A;
                }
                .card-header {
                    background-color: #0F0F1B;
                    padding: 20px;
                    border-bottom: 1px solid #FF0055;
                }
                .card-header h1 {
                    margin: 0;
                    font-size: 24px;
                    color: white;
                }
                .card-header h2 {
                    margin: 5px 0;
                    font-size: 18px;
                    color: #ccc;
                }
                .card-body {
                    display: flex;
                    padding: 20px;
                }
                .card-info {
                    flex: 1;
                }
                .card-info p {
                    margin: 10px 0;
                }
                .card-qr {
                    text-align: center;
                    padding: 10px;
                }
                .card-qr img {
                    width: 150px;
                    height: 150px;
                    background: white;
                    padding: 10px;
                }
                .card-footer {
                    background-color: #0F0F1B;
                    padding: 15px 20px;
                    border-top: 1px solid #FF0055;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .partner-type, .partner-status {
                    display: inline-block;
                    padding: 5px 10px;
                    border-radius: 50px;
                    font-size: 12px;
                    margin-right: 10px;
                }
                .partner-type {
                    background-color: #7453fc;
                    color: white;
                }
                .partner-status {
                    background-color: #00F0FF;
                    color: black;
                }
                .partner-status.active {
                    background-color: #00FFAA;
                }
                .partner-status.pending {
                    background-color: #FFD700;
                }
                .partner-status.inactive {
                    background-color: #FF0055;
                }
            </style>
        </head>
        <body>
            <div class="partner-card">
                <div class="card-header">
                    <h1>' . htmlspecialchars($partnerName) . '</h1>
                    <h2>' . htmlspecialchars($companyName) . '</h2>
                    <span class="partner-type">' . htmlspecialchars($partnerType) . '</span>
                    <span class="partner-status ' . strtolower($partnerStatus) . '">' . htmlspecialchars($partnerStatus) . '</span>
                </div>
                <div class="card-body">
                    <div class="card-info">
                        <p><strong>Email:</strong> ' . htmlspecialchars($partnerEmail) . '</p>
                        <p><strong>Phone:</strong> ' . htmlspecialchars($partnerPhone) . '</p>
                        ' . (!empty($contractDates) ? '<p><strong>Contract Period:</strong> ' . $contractDates . '</p>' : '') . '
                        <p><strong>Partnership Value:</strong> ' . $partnerValue . '</p>
                        <p><strong>Partner ID:</strong> ' . $partner['id'] . '</p>
                    </div>
                    <div class="card-qr">
                        <img src="' . $qrCodeUrl . '" alt="QR Code">
                        <p>Scan for exclusive offers!</p>
                    </div>
                </div>
                <div class="card-footer">
                    <div>Live The Music Partnership</div>
                    <div>Generated on: ' . date('Y-m-d H:i:s') . '</div>
                </div>
            </div>
        </body>
        </html>';
        
        // Save HTML to file
        file_put_contents($filepath, $html);
        
        // Return the web-accessible path to the HTML file
        return $webPath;
    }
}
?>
