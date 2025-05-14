<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/PartnerOfferController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/utils/NetworkUtils.php';

// Initialize controllers
$partnerOfferController = new PartnerOfferController();
$partnerController = new BackOfficePartnerController();

// Get a test partner ID (using the first partner in the database)
$partners = $partnerController->getAllPartners();
$testPartnerId = !empty($partners) ? $partners[0]['id'] : 1;

// Get partner details
$partner = $partnerController->getPartnerById($testPartnerId);

// Get or generate offer code
$offer = $partnerOfferController->getPartnerOffer($testPartnerId);
$offerCode = isset($offer['code']) ? $offer['code'] : 
    substr(str_replace(' ', '', $partner['name']), 0, 3) . $testPartnerId . rand(100, 999);

// Get the local IP address
$localIP = '192.168.137.209'; // Hardcoded IP as per previous changes

// Create the offer URL
$offerUrl = "http://{$localIP}/livethemusic/view/front_office/partner-offer.php?id={$testPartnerId}&code={$offerCode}";

// Generate QR code using QR Server API
$qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($offerUrl);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="LiveTheMusic - QR Code Test">
    <meta name="author" content="LiveTheMusic">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title>QR Code Test | LiveTheMusic</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">
    
    <style>
        .test-container {
            background: linear-gradient(135deg, #1f2122 0%, #2a2e31 100%);
            border-radius: 25px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .qr-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
            margin: 20px auto;
            border: 5px solid #ec6090;
            box-shadow: 0 5px 15px rgba(236, 96, 144, 0.4);
        }
        
        .url-display {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            word-break: break-all;
            font-family: monospace;
            color: #fff;
            border: 1px solid rgba(236, 96, 144, 0.3);
        }
        
        .test-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ec6090;
            color: #fff;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            margin: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .test-btn:hover {
            background-color: #e75e8d;
            transform: translateY(-2px);
        }
        
        .section-heading {
            text-align: center;
            margin-bottom: 30px;
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
                    <!-- ***** QR Code Test Start ***** -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="heading-section">
                                <h4><em>QR Code</em> Test Page</h4>
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div class="test-container">
                                <div class="section-heading">
                                    <h2>Partner Offer QR Code</h2>
                                    <p>Test the QR code functionality for partner <?php echo htmlspecialchars($partner['name']); ?></p>
                                </div>
                                
                                <div class="text-center">
                                    <div class="qr-container">
                                        <img src="<?php echo $qrCodeUrl; ?>" alt="Partner Offer QR Code" style="width: 250px; height: 250px;">
                                    </div>
                                    
                                    <div class="url-display">
                                        <?php echo htmlspecialchars($offerUrl); ?>
                                    </div>
                                    
                                    <div class="button-group">
                                        <a href="<?php echo htmlspecialchars($offerUrl); ?>" class="test-btn" target="_blank">Test Direct Link</a>
                                        <a href="partners_directory.php" class="test-btn">Back to Partners</a>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <h3>Debug Information</h3>
                                    <div class="url-display">
                                        <p><strong>Partner ID:</strong> <?php echo $testPartnerId; ?></p>
                                        <p><strong>Offer Code:</strong> <?php echo htmlspecialchars($offerCode); ?></p>
                                        <p><strong>Local IP:</strong> <?php echo htmlspecialchars($localIP); ?></p>
                                        <p><strong>Partner Found:</strong> <?php echo !empty($partner) ? 'Yes' : 'No'; ?></p>
                                        <p><strong>Offer Found:</strong> <?php echo !empty($offer) ? 'Yes' : 'No'; ?></p>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <h3>Test Different Partners</h3>
                                    <div class="row">
                                        <?php foreach (array_slice($partners, 0, 4) as $testPartner): ?>
                                        <div class="col-md-6 mb-3">
                                            <div style="background-color: rgba(0, 0, 0, 0.2); border-radius: 10px; padding: 15px; height: 100%;">
                                                <h4><?php echo htmlspecialchars($testPartner['name']); ?></h4>
                                                <p><strong>Company:</strong> <?php echo htmlspecialchars($testPartner['company']); ?></p>
                                                <p><strong>Type:</strong> <?php echo htmlspecialchars($testPartner['partnerType']); ?></p>
                                                <?php 
                                                $testOffer = $partnerOfferController->getPartnerOffer($testPartner['id']);
                                                $testOfferCode = isset($testOffer['code']) ? $testOffer['code'] : 
                                                    substr(str_replace(' ', '', $testPartner['name']), 0, 3) . $testPartner['id'] . rand(100, 999);
                                                $testOfferUrl = "http://{$localIP}/livethemusic/view/front_office/partner-offer.php?id={$testPartner['id']}&code={$testOfferCode}";
                                                ?>
                                                <a href="<?php echo htmlspecialchars($testOfferUrl); ?>" class="test-btn" target="_blank">Test Partner #<?php echo $testPartner['id']; ?></a>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ***** QR Code Test End ***** -->
                </div>
            </div>
        </div>
    </div>
  
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright © 2025 <a href="#">LiveTheMusic</a>. All rights reserved.</p>
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
    // Hide preloader when page is loaded
    window.addEventListener('load', function() {
        const preloader = document.getElementById('js-preloader');
        if (preloader) {
            setTimeout(function() {
                preloader.style.display = 'none';
            }, 500);
        }
    });
    </script>
</body>
</html>
