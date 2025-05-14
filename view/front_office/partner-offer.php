<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/PartnerOfferController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php';

// Initialize controllers
$partnerOfferController = new PartnerOfferController();
$partnerController = new BackOfficePartnerController();

// Get partner ID and offer code from URL
$partnerId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$offerCode = isset($_GET['code']) ? trim($_GET['code']) : '';

// If no parameters are provided, set a helpful message
if (!isset($_GET['partner_id']) && !isset($_GET['code'])) {
    $errorMessage = 'This page displays partner offers when accessed through a QR code. Please scan a partner QR code or visit the <a href="partners_directory.php" style="color: #ec6090;">Partners Directory</a> to browse all partners.';
}

// Check if partner ID and offer code are provided
if (!isset($_GET['partner_id']) || empty($_GET['partner_id'])) {
    $errorMessage = 'Missing partner information. Please scan the QR code again or contact the partner directly.';
} elseif (!isset($_GET['code']) || empty($_GET['code'])) {
    $errorMessage = 'Missing offer code. Please scan the QR code again to access this exclusive offer.';
} else {
    // Get partner details
    $partner = $partnerController->getPartnerById($partnerId);
    
    // Get partner offer
    $offer = $partnerOfferController->getPartnerOffer($partnerId);
    
    // Track interaction
    if ($partner && $offer) {
        try {
            // Use the controller method to track the interaction (cleaner MVC approach)
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            // Track the interaction with proper parameters
            $result = $partnerOfferController->trackInteraction(
                $partnerId, 
                null, // user_id is null for anonymous users
                $offerCode,
                'scan', // interaction type
                $ipAddress,
                $userAgent
            );
            
            // For debugging (only in development)
            $_SESSION['debug_info'] = [
                'partner_id' => $partnerId,
                'offer_code' => $offerCode,
                'partner_found' => !empty($partner),
                'offer_found' => !empty($offer),
                'interaction_tracked' => true,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            // Log error
            error_log("Error tracking interaction: " . $e->getMessage());
            
            $_SESSION['debug_info'] = [
                'partner_id' => $partnerId,
                'offer_code' => $offerCode,
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
    } else if (!$partner) {
        $errorMessage = 'Partner not found. This partner may no longer be active in our system.';
    } else if (!$offer) {
        $errorMessage = 'Offer not found. This offer may have expired or been removed by the partner.';
    }
}

// Format discount for display
$formattedDiscount = '';
if (isset($offer['discount_type']) && isset($offer['discount_amount'])) {
    if ($offer['discount_type'] === 'percentage') {
        $formattedDiscount = $offer['discount_amount'] . '%';
    } else {
        $formattedDiscount = '$' . number_format($offer['discount_amount'], 2);
    }
}

// Check if offer is expired
$isExpired = false;
if (isset($offer['expiry_date'])) {
    $expiryDate = new DateTime($offer['expiry_date']);
    $today = new DateTime();
    $isExpired = $today > $expiryDate;
}

// Check if offer is redeemable
$isRedeemable = isset($offer['redeemable']) && $offer['redeemable'] && !$isExpired;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="LiveTheMusic - Partner Offer">
    <meta name="author" content="LiveTheMusic">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title><?php echo isset($partner['name']) ? htmlspecialchars($partner['name']) . ' - Special Offer' : 'Partner Offer'; ?> | LiveTheMusic</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Partner Icons CSS -->
    <link rel="stylesheet" href="assets/css/partner-icons.css">
    
    <!-- Error Styles CSS -->
    <link rel="stylesheet" href="assets/css/error-styles.css">
    
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">
    
    <style>
        /* Improved preloader with transitions */
        .js-preloader {
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        .offer-card {
            background: linear-gradient(135deg, #1f2122 0%, #2a2e31 100%);
            border-radius: 25px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .offer-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #ec6090, #e75e8d);
        }
        
        .offer-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .offer-title {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 10px;
            text-shadow: 0 0 10px rgba(236, 96, 144, 0.5);
        }
        
        .partner-name {
            font-size: 18px;
            color: #ec6090;
            margin-bottom: 5px;
        }
        
        .partner-type {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .sponsor { background-color: #27ae60; color: white; }
        .venue { background-color: #3498db; color: white; }
        .media { background-color: #e67e22; color: white; }
        .vendor { background-color: #9b59b6; color: white; }
        
        .discount-badge {
            position: absolute;
            top: -15px;
            right: -15px;
            background: linear-gradient(135deg, #ec6090, #e75e8d);
            color: white;
            font-size: 24px;
            font-weight: 700;
            padding: 15px;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(236, 96, 144, 0.5);
            transform: rotate(15deg);
            z-index: 10;
        }
        
        /* Mobile responsiveness for discount badge */
        @media (max-width: 767px) {
            .discount-badge {
                width: 60px;
                height: 60px;
                font-size: 18px;
                top: -10px;
                right: -10px;
            }
        }
        
        .offer-description {
            font-size: 16px;
            color: #ccc;
            line-height: 1.6;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .offer-code-container {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
            border: 1px dashed rgba(236, 96, 144, 0.5);
        }
        
        .offer-code-label {
            font-size: 14px;
            color: #ccc;
            margin-bottom: 10px;
        }
        
        .offer-code {
            font-family: monospace;
            font-size: 28px;
            letter-spacing: 3px;
            color: #fff;
            font-weight: 700;
            background-color: rgba(0, 0, 0, 0.3);
            padding: 10px 20px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 10px;
            position: relative;
            overflow: hidden;
            word-break: break-all; /* Ensure code wraps on mobile */
        }
        
        /* Mobile responsiveness for offer code */
        @media (max-width: 767px) {
            .offer-code {
                font-size: 20px;
                letter-spacing: 2px;
                padding: 8px 12px;
            }
        }
        
        .offer-code::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                rgba(255, 255, 255, 0.1),
                rgba(255, 255, 255, 0.05),
                rgba(255, 255, 255, 0.025),
                transparent
            );
            transform: rotate(30deg);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(30deg); }
            100% { transform: translateX(100%) rotate(30deg); }
        }
        
        .copy-btn {
            background-color: #ec6090;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }
        
        .copy-btn:hover {
            background-color: #e75e8d;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(236, 96, 144, 0.3);
        }
        
        .copy-btn i {
            margin-right: 8px;
        }
        
        .expiry-info {
            font-size: 14px;
            color: #ccc;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .expired-badge {
            background-color: #e74c3c;
            color: white;
            font-size: 16px;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .partner-info {
            display: flex;
            align-items: flex-start; /* Changed from center for better mobile layout */
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            flex-wrap: wrap; /* Allow wrapping on mobile */
        }
        
        .partner-logo {
            width: 80px;
            height: 80px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 30px;
            color: #ec6090;
            flex-shrink: 0; /* Prevent logo from shrinking */
        }
        
        /* Mobile responsiveness for partner info */
        @media (max-width: 480px) {
            .partner-info {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .partner-logo {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .partner-details {
                width: 100%;
                text-align: center;
            }
        }
        
        .partner-details {
            flex: 1;
        }
        
        .partner-company {
            font-size: 18px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 5px;
        }
        
        .partner-contact {
            font-size: 14px;
            color: #ccc;
            margin-bottom: 5px;
        }
        
        .redeem-btn {
            background: linear-gradient(135deg, #ec6090, #e75e8d);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
        }
        
        .redeem-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(236, 96, 144, 0.3);
            color: white;
        }
        
        .redeem-btn:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .redeem-btn i {
            margin-right: 10px;
        }
        
        .back-to-partners {
            display: inline-flex;
            align-items: center;
            color: #ec6090;
            font-size: 16px;
            font-weight: 500;
            margin-top: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .back-to-partners:hover {
            color: #e75e8d;
            transform: translateX(-5px);
        }
        
        .back-to-partners i {
            margin-right: 10px;
        }
        
        .error-container {
            text-align: center;
            padding: 50px 20px;
        }
        
        .error-icon {
            font-size: 60px;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        
        .error-message {
            font-size: 24px;
            color: #fff;
            margin-bottom: 30px;
        }
        
        /* Fix for the header on mobile */
        @media (max-width: 767px) {
            .header-area .main-nav .nav li {
                padding-left: 5px;
                padding-right: 5px;
            }
            
            .header-area .main-nav .nav li a {
                font-size: 12px;
            }
        }
        
        /* Fix for the preloader */
        #js-preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #1f2122;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        #js-preloader.loaded {
            opacity: 0;
            visibility: hidden;
        }
        
        .preloader-inner {
            text-align: center;
        }
        
        .preloader-inner .dot {
            display: inline-block;
            width: 15px;
            height: 15px;
            background-color: #ec6090;
            border-radius: 50%;
            margin: 0 5px;
            animation: dot-pulse 1s infinite ease-in-out;
        }
        
        .preloader-inner .dots span {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: #ec6090;
            border-radius: 50%;
            margin: 0 5px;
            animation: dots-pulse 1.5s infinite ease-in-out;
        }
        
        .preloader-inner .dots span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .preloader-inner .dots span:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes dot-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.5); }
            100% { transform: scale(1); }
        }
        
        @keyframes dots-pulse {
            0% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 0.5; }
        }
    </style>
</head>
<body>
    <!-- ***** Preloader Start ***** -->
    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!-- ***** Preloader End ***** -->

    <script>
    // Hide preloader immediately and when page is loaded
    document.addEventListener('DOMContentLoaded', function() {
        const preloader = document.getElementById('js-preloader');
        if (preloader) {
            // Hide preloader immediately
            preloader.style.opacity = '0';
            setTimeout(function() {
                preloader.style.display = 'none';
            }, 100);
        }
    });
    
    // Backup in case DOMContentLoaded doesn't trigger
    window.onload = function() {
        const preloader = document.getElementById('js-preloader');
        if (preloader) {
            preloader.style.display = 'none';
        }
    };
    </script>

    <!-- ***** Header Area Start ***** -->
    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <!-- ***** Logo Start ***** -->
                        <a href="index.php" class="logo">
                            <img src="assets/images/logo.png" alt="">
                        </a>
                        <!-- ***** Logo End ***** -->
                        <!-- ***** Menu Start ***** -->
                        <ul class="nav">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="browse.php">Browse</a></li>
                            <li><a href="details.php">Details</a></li>
                            <li><a href="streams.php">Streams</a></li>
                            <li><a href="partners_directory.php" class="active">Partners</a></li>
                            <li><a href="profile.php">Profile <img src="assets/images/profile-header.jpg" alt=""></a></li>
                        </ul>   
                        <a class='menu-trigger'>
                            <span>Menu</span>
                        </a>
                        <!-- ***** Menu End ***** -->
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- ***** Header Area End ***** -->

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-content">
                    <!-- ***** Partner Offer Start ***** -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="heading-section">
                                <h4><em>LiveTheMusic</em> Partner Offers</h4>
                            </div>
                        </div>
                        
                        <?php if (isset($errorMessage)): ?>
                        <!-- Simple Error Message -->
                        <div class="col-lg-12">
                            <div style="background-color: #1f2122; border: 2px solid #ec6090; border-radius: 15px; padding: 30px; text-align: center; margin: 50px auto; max-width: 500px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);">
                                <!-- Direct SVG Error Icon -->
                                <div style="margin-bottom: 20px;">
                                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSIjZTc0YzNjIiBkPSJNNTA0IDI1NmMwIDEzNi45OTctMTExLjA0MyAyNDgtMjQ4IDI0OFM4IDM5Mi45OTcgOCAyNTZDOCAxMTkuMDgzIDExOS4wNDMgOCAyNTYgOHMyNDggMTExLjA4MyAyNDggMjQ4em0tMjQ4IDUwYy0yNS40MDUgMC00NiAyMC41OTUtNDYgNDZzMjAuNTk1IDQ2IDQ2IDQ2IDQ2LTIwLjU5NSA0Ni00Ni0yMC41OTUtNDYtNDYtNDZ6bS00My42NzMtMTY1LjM0Nmw3LjQxOCAxMzZjLjM0NyA2LjM2NCA1LjYwOSAxMS4zNDYgMTEuOTgyIDExLjM0Nmg0OC41NDZjNi4zNzMgMCAxMS42MzUtNC45ODIgMTEuOTgyLTExLjM0Nmw3LjQxOC0xMzZjLjM3NS02Ljg3NC01LjA5OC0xMi42NTQtMTEuOTgyLTEyLjY1NGgtNjMuMzgzYy02Ljg4NCAwLTEyLjM1NiA1Ljc4LTExLjk4MSAxMi42NTR6Ij48L3BhdGg+PC9zdmc+" 
                                         alt="Error" style="width: 100px; height: 100px;">
                                </div>
                                <!-- Error Message -->
                                <div style="font-size: 18px; color: #fff; margin-bottom: 20px; line-height: 1.5;">
                                    <?php echo htmlspecialchars($errorMessage); ?>
                                </div>
                                <!-- Back to Partners Link -->
                                <a href="partners_directory.php" style="display: inline-block; padding: 10px 20px; background-color: #ec6090; color: #fff; border-radius: 25px; text-decoration: none; font-weight: 500; transition: all 0.3s;">
                                    Back to Partners Directory
                                </a>
                            </div>
                        </div>
                        <?php elseif (isset($partner) && isset($offer)): ?>
                        <!-- Partner Offer Card -->
                        <div class="col-lg-8 offset-lg-2">
                            <div class="offer-card">
                                <?php if (!$isExpired && isset($formattedDiscount)): ?>
                                <div class="discount-badge">
                                    <?php echo htmlspecialchars($formattedDiscount); ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="offer-header">
                                    <div class="partner-name"><?php echo htmlspecialchars($partner['company']); ?></div>
                                    <div class="partner-type <?php echo strtolower($partner['partnerType']); ?>">
                                        <?php echo htmlspecialchars($partner['partnerType']); ?> Partner
                                    </div>
                                    <h1 class="offer-title"><?php echo htmlspecialchars($offer['title'] ?? 'Special Offer'); ?></h1>
                                </div>
                                
                                <?php if ($isExpired): ?>
                                <div class="text-center">
                                    <div class="expired-badge">
                                        <i class="fa fa-calendar-times-o"></i> This offer has expired
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="offer-description">
                                    <?php echo htmlspecialchars($offer['description'] ?? 'Exclusive offer for LiveTheMusic users! Present this code at our venue to redeem your special discount.'); ?>
                                </div>
                                
                                <div class="offer-code-container">
                                    <div class="offer-code-label">Use this code to redeem your offer:</div>
                                    <div class="offer-code" id="offerCode"><?php echo htmlspecialchars($offer['code'] ?? $offerCode); ?></div>
                                    <button class="copy-btn" id="copyBtn">
                                        <span class="custom-icon icon-copy"></span> Copy Code
                                    </button>
                                </div>
                                
                                <div class="expiry-info">
                                    <?php if (isset($offer['expiry_date'])): ?>
                                    <span class="custom-icon icon-calendar"></span> Valid until: <?php echo date('F d, Y', strtotime($offer['expiry_date'])); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($isRedeemable): ?>
                                <a href="#" class="redeem-btn" id="redeemBtn">
                                    <span class="custom-icon icon-check-circle"></span> Redeem Now
                                </a>
                                <?php else: ?>
                                <button class="redeem-btn" disabled>
                                    <span class="custom-icon icon-ban"></span> Not Redeemable
                                </button>
                                <?php endif; ?>
                                
                                <div class="partner-info">
                                    <div class="partner-logo">
                                        <span class="custom-icon icon-building"></span>
                                    </div>
                                    <div class="partner-details">
                                        <div class="partner-company"><?php echo htmlspecialchars($partner['company']); ?></div>
                                        <div class="partner-contact">
                                            <span class="custom-icon icon-user"></span> <?php echo htmlspecialchars($partner['name']); ?>
                                        </div>
                                        <div class="partner-contact">
                                            <span class="custom-icon icon-envelope"></span> <?php echo htmlspecialchars($partner['email']); ?>
                                        </div>
                                        <div class="partner-contact">
                                            <span class="custom-icon icon-phone"></span> <?php echo htmlspecialchars($partner['phone']); ?>
                                        </div>
                                        <?php if (!empty($partner['contractStart']) && !empty($partner['contractEnd'])): ?>
                                        <div class="partner-contact">
                                            <span class="custom-icon icon-calendar"></span> Valid: <?php echo date('M d, Y', strtotime($partner['contractStart'])); ?> - <?php echo date('M d, Y', strtotime($partner['contractEnd'])); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <a href="partners_directory.php" class="back-to-partners">
                                    <span class="custom-icon icon-arrow-left"></span> Back to Partners Directory
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- ***** Partner Offer End ***** -->
                </div>
            </div>
        </div>
    </div>
  
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright © 2025 <a href="#">LiveTheMusic</a> Company. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/tabs.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/custom.js"></script>
    
    <script>
        // Handle preloader
        window.addEventListener('load', function() {
            const preloader = document.getElementById('js-preloader');
            if (preloader) {
                preloader.classList.add('loaded');
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 500);
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            // Copy offer code to clipboard
            const copyBtn = document.getElementById('copyBtn');
            const offerCode = document.getElementById('offerCode');
            
            if (copyBtn && offerCode) {
                copyBtn.addEventListener('click', function() {
                    // Modern clipboard API
                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(offerCode.textContent.trim())
                            .then(() => {
                                showCopiedMessage();
                            })
                            .catch(() => {
                                // Fallback for older browsers
                                fallbackCopyTextToClipboard();
                            });
                    } else {
                        // Fallback for older browsers
                        fallbackCopyTextToClipboard();
                    }
                    
                    function fallbackCopyTextToClipboard() {
                        const tempInput = document.createElement('input');
                        tempInput.value = offerCode.textContent.trim();
                        document.body.appendChild(tempInput);
                        tempInput.select();
                        document.execCommand('copy');
                        document.body.removeChild(tempInput);
                        showCopiedMessage();
                    }
                    
                    function showCopiedMessage() {
                        // Change button text temporarily
                        const originalText = copyBtn.innerHTML;
                        copyBtn.innerHTML = '<i class="fa fa-check"></i> Copied!';
                        copyBtn.style.backgroundColor = '#27ae60';
                        
                        setTimeout(function() {
                            copyBtn.innerHTML = originalText;
                            copyBtn.style.backgroundColor = '#ec6090';
                        }, 2000);
                    }
                });
            }
            
            // Redeem offer
            const redeemBtn = document.getElementById('redeemBtn');
            
            if (redeemBtn) {
                redeemBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Show confirmation dialog
                    if (confirm('Are you sure you want to redeem this offer? This action cannot be undone.')) {
                        // In a real application, this would send an AJAX request to redeem the offer
                        alert('Offer redeemed successfully! Show this screen to the partner to receive your discount.');
                        
                        // Disable the button
                        redeemBtn.disabled = true;
                        redeemBtn.innerHTML = '<i class="fa fa-check-circle"></i> Redeemed';
                        redeemBtn.style.backgroundColor = '#27ae60';
                    }
                });
            }
            
            // Fix any missing icons by ensuring Font Awesome is loaded
            if (typeof FontAwesome === 'undefined') {
                const fontAwesomeLink = document.createElement('link');
                fontAwesomeLink.rel = 'stylesheet';
                fontAwesomeLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
                document.head.appendChild(fontAwesomeLink);
            }
        });
    </script>
</body>
</html>
