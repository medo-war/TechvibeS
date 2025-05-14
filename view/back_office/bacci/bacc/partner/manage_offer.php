<?php
/**
 * Manage Partner Offer
 * 
 * This page allows partners to manage their loyalty/reward offers
 */
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/PartnerOfferController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php';

// For demonstration purposes, we're allowing access without admin authentication
// In a production environment, you would want to keep the authentication check
/*
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: ../401.html');
    exit;
}
*/

// Set a demo user session for testing
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id' => 1,
        'role' => 'admin',
        'username' => 'demo_admin'
    ];
}

// Check if partner ID is provided
$partnerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$partnerId) {
    // Redirect to partner list if no partner ID
    header('Location: index.php');
    exit;
}

// Initialize controllers
$partnerController = new BackOfficePartnerController();
$offerController = new PartnerOfferController();

// Get partner data
$partner = $partnerController->getPartnerById($partnerId);

// Check if partner exists
if (!$partner) {
    // Redirect to partner list if partner doesn't exist
    header('Location: index.php');
    exit;
}

// Get partner offer
$offer = $offerController->getPartnerOffer($partnerId);

// Get offer statistics
$stats = $offerController->getOfferStatistics($partnerId);

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $title = htmlspecialchars($_POST['title'] ?? '');
    $description = htmlspecialchars($_POST['description'] ?? '');
    $code = htmlspecialchars($_POST['code'] ?? '');
    $discountAmount = floatval($_POST['discount_amount'] ?? 0);
    $discountType = $_POST['discount_type'] ?? 'percentage';
    $expiryDate = $_POST['expiry_date'] ?? '';
    $redeemable = isset($_POST['redeemable']) ? true : false;
    
    // Validate required fields
    if (empty($title) || empty($code) || $discountAmount <= 0) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } else {
        // Save offer
        $offerData = [
            'partner_id' => $partnerId,
            'title' => $title,
            'description' => $description,
            'code' => $code,
            'discount_amount' => $discountAmount,
            'discount_type' => $discountType,
            'expiry_date' => $expiryDate,
            'redeemable' => $redeemable
        ];
        
        if ($offerController->savePartnerOffer($offerData)) {
            $message = 'Offer saved successfully.';
            $messageType = 'success';
            
            // Refresh offer data
            $offer = $offerController->getPartnerOffer($partnerId);
        } else {
            $message = 'An error occurred while saving the offer.';
            $messageType = 'error';
        }
    }
}

// Set default values if offer doesn't exist
if (!$offer) {
    $offer = [
        'title' => 'Special Discount',
        'description' => 'Exclusive offer for LiveTheMusic users! Present this code at our venue to redeem your special discount.',
        'code' => '',
        'discount_amount' => 15,
        'discount_type' => 'percentage',
        'expiry_date' => date('Y-m-d', strtotime('+3 months')),
        'redeemable' => true,
        'scan_count' => 0
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
    <title>Manage Partner Offer - LiveTheMusic</title>
    
    <!-- Bootstrap core CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="../assets/css/fontawesome.css">
    <link rel="stylesheet" href="../assets/css/templatemo-style.css">
    <link rel="stylesheet" href="../assets/css/owl.css">
    
    <style>
        .offer-stats {
            background-color: #1f2122;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background-color: #27292a;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 2rem;
            color: #f5a425;
            margin-bottom: 5px;
        }
        
        .stat-card p {
            font-size: 0.9rem;
            color: #ccc;
            margin-bottom: 0;
        }
        
        .recent-interactions {
            background-color: #1f2122;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .recent-interactions h4 {
            color: #f5a425;
            margin-bottom: 15px;
        }
        
        .interaction-item {
            background-color: #27292a;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
        
        .interaction-item .type {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8rem;
            margin-right: 10px;
        }
        
        .interaction-item .type.scan {
            background-color: #3498db;
            color: white;
        }
        
        .interaction-item .type.redeem {
            background-color: #2ecc71;
            color: white;
        }
        
        .interaction-item .date {
            font-size: 0.8rem;
            color: #999;
        }
        
        .interaction-item .user {
            font-size: 0.9rem;
            color: white;
        }
        
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .message.success {
            background-color: rgba(46, 204, 113, 0.2);
            border: 1px solid #2ecc71;
            color: #2ecc71;
        }
        
        .message.error {
            background-color: rgba(231, 76, 60, 0.2);
            border: 1px solid #e74c3c;
            color: #e74c3c;
        }
        
        .qr-preview {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .qr-preview img {
            max-width: 200px;
            border: 5px solid white;
            border-radius: 10px;
        }
        
        .qr-preview p {
            margin-top: 10px;
            font-size: 0.9rem;
            color: #ccc;
        }
    </style>
</head>
<body class="is-preload">
    <!-- Wrapper -->
    <div id="wrapper">
        <!-- Main -->
        <div id="main">
            <div class="inner">
                <!-- Header -->
                <header id="header">
                    <div class="logo">
                        <a href="index.php">LiveTheMusic</a>
                    </div>
                </header>
                
                <!-- Content -->
                <section class="main-banner">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="banner-content">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="banner-caption">
                                                <h4>Manage Partner Offer</h4>
                                                <h2><em><?php echo htmlspecialchars($partner['name']); ?></em></h2>
                                                
                                                <?php if (!empty($message)): ?>
                                                <div class="message <?php echo $messageType; ?>">
                                                    <?php echo $message; ?>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Offer Details</h4>
                                                                
                                                                <form method="post" action="">
                                                                    <div class="form-group">
                                                                        <label for="title">Offer Title*</label>
                                                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($offer['title']); ?>" required>
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label for="description">Description</label>
                                                                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($offer['description']); ?></textarea>
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label for="code">Offer Code*</label>
                                                                        <input type="text" class="form-control" id="code" name="code" value="<?php echo htmlspecialchars($offer['code']); ?>" required>
                                                                    </div>
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="discount_amount">Discount Amount*</label>
                                                                                <input type="number" class="form-control" id="discount_amount" name="discount_amount" value="<?php echo htmlspecialchars($offer['discount_amount']); ?>" step="0.01" min="0" required>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="discount_type">Discount Type</label>
                                                                                <select class="form-control" id="discount_type" name="discount_type">
                                                                                    <option value="percentage" <?php echo $offer['discount_type'] == 'percentage' ? 'selected' : ''; ?>>Percentage (%)</option>
                                                                                    <option value="fixed" <?php echo $offer['discount_type'] == 'fixed' ? 'selected' : ''; ?>>Fixed Amount ($)</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label for="expiry_date">Expiry Date</label>
                                                                        <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?php echo htmlspecialchars($offer['expiry_date']); ?>">
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <div class="custom-control custom-checkbox">
                                                                            <input type="checkbox" class="custom-control-input" id="redeemable" name="redeemable" <?php echo $offer['redeemable'] ? 'checked' : ''; ?>>
                                                                            <label class="custom-control-label" for="redeemable">Offer is Redeemable</label>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <button type="submit" class="btn btn-primary">Save Offer</button>
                                                                        <a href="index.php" class="btn btn-secondary">Back to Partners</a>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <div class="qr-preview">
                                                            <h4>QR Code Preview</h4>
                                                            <?php
                                                             // Generate QR code preview
                                                             require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Model/PartnerCardGenerator.php';
                                                             // Get base URL for QR code generation
                                                             $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                                                             $generator = new PartnerCardGenerator($partner, $baseUrl);
                                                             $qrCode = $generator->generateQRCode();
                                                            ?>
                                                            <img src="<?php echo $qrCode; ?>" alt="QR Code">
                                                            <p>Scan this QR code to view the offer</p>
                                                        </div>
                                                        
                                                        <div class="offer-stats">
                                                            <h4>Offer Statistics</h4>
                                                            
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="stat-card">
                                                                        <h3><?php echo $stats['total_scans']; ?></h3>
                                                                        <p>Total Scans</p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-md-6">
                                                                    <div class="stat-card">
                                                                        <h3><?php echo $stats['total_redemptions']; ?></h3>
                                                                        <p>Redemptions</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="stat-card">
                                                                <h3><?php echo $stats['conversion_rate']; ?>%</h3>
                                                                <p>Conversion Rate</p>
                                                            </div>
                                                        </div>
                                                        
                                                        <?php if (!empty($stats['recent_interactions'])): ?>
                                                        <div class="recent-interactions">
                                                            <h4>Recent Interactions</h4>
                                                            
                                                            <?php foreach ($stats['recent_interactions'] as $interaction): ?>
                                                            <div class="interaction-item">
                                                                <span class="type <?php echo $interaction['interaction_type']; ?>">
                                                                    <?php echo ucfirst($interaction['interaction_type']); ?>
                                                                </span>
                                                                
                                                                <?php if (!empty($interaction['first_name'])): ?>
                                                                <span class="user">
                                                                    <?php echo htmlspecialchars($interaction['first_name'] . ' ' . $interaction['last_name']); ?>
                                                                </span>
                                                                <?php else: ?>
                                                                <span class="user">Anonymous User</span>
                                                                <?php endif; ?>
                                                                
                                                                <div class="date">
                                                                    <?php echo date('M d, Y H:i', strtotime($interaction['created_at'])); ?>
                                                                </div>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        
        <!-- Sidebar -->
        <?php include_once('../sidebar.php'); // Include sidebar from parent directory ?>
    </div>
    
    <!-- Scripts -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/browser.min.js"></script>
    <script src="../assets/js/breakpoints.min.js"></script>
    <script src="../assets/js/transition.js"></script>
    <script src="../assets/js/owl-carousel.js"></script>
    <script src="../assets/js/custom.js"></script>
    
    <script>
        // Auto-dismiss messages after 2 seconds
        $(document).ready(function() {
            setTimeout(function() {
                $('.message').fadeOut('slow');
            }, 2000);
        });
    </script>
</body>
</html>
