<?php
/**
 * Partners Directory
 * 
 * This page displays all active partners with their QR codes for offers
 */
session_start();
require_once '../../Controller/PartnerController.php';
require_once '../../Controller/PartnerOfferController.php';
require_once '../../Model/PartnerCardGenerator.php';

// Initialize controllers
$partnerController = new PartnerController();
$offerController = new PartnerOfferController();

// Get all active partners
$partners = $partnerController->getActivePartners();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="LiveTheMusic">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <title>Our Partners - LiveTheMusic</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-liberty-market.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    
    <style>
        :root {
            --primary-color: #7453fc; /* Purple */
            --secondary-color: #FF0055; /* Neon pink */
            --accent-color: #00F0FF; /* Neon blue */
            --dark-color: #000000; /* Black background */
            --light-color: #0b1c3e; /* Dark blue background for forms */
            --neon-green: #00FFAA;
        }
        
        .partners-container {
            position: relative;
            padding: 120px 0 80px 0; /* Increased top padding from 80px to 120px */
            background-color: var(--dark-color);
            overflow: hidden;
            min-height: 100vh;
        }
        
        /* Particles.js Background */
        #particles-js-partners {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }
        
        /* Neon glow circles */
        .partners-neon-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            z-index: 0;
        }
        
        .partners-neon-circle-1 {
            top: -200px;
            left: -200px;
            width: 500px;
            height: 500px;
            background: var(--primary-color);
            animation: partners-pulse 8s infinite alternate;
        }
        
        .partners-neon-circle-2 {
            bottom: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: var(--secondary-color);
            animation: partners-pulse 10s infinite alternate-reverse;
        }
        
        @keyframes partners-pulse {
            0% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.2); opacity: 0.5; }
            100% { transform: scale(1); opacity: 0.3; }
        }
        
        .partners-header {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-top: 30px; /* Added top margin */
            margin-bottom: 70px;
        }
        
        .partners-header h1 {
            font-size: 3rem;
            font-weight: 700;
            color: white;
            margin-bottom: 20px;
            text-shadow: 0 0 10px rgba(116, 83, 252, 0.7);
        }
        
        .partners-header p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .partner-card {
            position: relative;
            z-index: 2;
            background: rgba(11, 28, 62, 0.8);
            border-radius: 20px;
            border: 2px solid var(--primary-color);
            box-shadow: 0 0 30px rgba(116, 83, 252, 0.4);
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .partner-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(116, 83, 252, 0.6);
        }
        
        .partner-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 2px solid var(--primary-color);
            box-shadow: 0 0 15px rgba(116, 83, 252, 0.5);
            overflow: hidden;
        }
        
        .partner-logo img {
            max-width: 80%;
            max-height: 80%;
        }
        
        .partner-logo .initial {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .partner-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
            text-align: center;
        }
        
        .partner-company {
            font-size: 1rem;
            color: var(--accent-color);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .partner-type {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }
        
        .partner-type.sponsor {
            background-color: var(--primary-color);
            color: white;
        }
        
        .partner-type.venue {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .partner-type.media {
            background-color: #A83AFB;
            color: white;
        }
        
        .partner-type.technology {
            background-color: var(--neon-green);
            color: var(--dark-color);
        }
        
        .partner-type.other {
            background-color: #6c757d;
            color: white;
        }
        
        .partner-info {
            margin-bottom: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            flex-grow: 1;
        }
        
        .partner-qr {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .partner-qr img {
            max-width: 150px;
            border: 5px solid white;
            border-radius: 10px;
        }
        
        .partner-qr p {
            margin-top: 10px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .partner-action {
            text-align: center;
        }
        
        .partner-action .btn {
            display: inline-block;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(116, 83, 252, 0.4);
        }
        
        .partner-action .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(116, 83, 252, 0.6);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .partners-header h1 {
                font-size: 2.5rem;
            }
            
            .partners-header p {
                font-size: 1rem;
            }
            
            .partner-card {
                padding: 20px;
            }
            
            .partner-logo {
                width: 80px;
                height: 80px;
            }
            
            .partner-name {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <!-- ***** Header Area Start ***** -->
    <?php
    // Set current page for navbar highlighting
    $currentPage = basename($_SERVER['PHP_SELF']);
    include_once 'includes/navbar.php';
    ?>
    <!-- ***** Header Area End ***** -->
    
    <!-- ***** Partners Area Start ***** -->
    <div class="partners-container">
        <!-- Particles.js Background -->
        <div id="particles-js-partners"></div>
        
        <!-- Neon glow circles -->
        <div class="partners-neon-circle partners-neon-circle-1"></div>
        <div class="partners-neon-circle partners-neon-circle-2"></div>
        
        <div class="container">
            <div class="partners-header">
                <h1>Our Partners</h1>
                <p>Discover our amazing partners and exclusive offers. Scan the QR codes to access special discounts and rewards available only to LiveTheMusic community members.</p>
            </div>
            
            <div class="row">
                <?php if (empty($partners)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        No partners found. Check back soon as we're constantly adding new partners!
                    </div>
                </div>
                <?php else: ?>
                    <?php foreach ($partners as $partner): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="partner-card">
                            <div class="partner-logo">
                                <?php if (!empty($partner['logo'])): ?>
                                <img src="<?php echo htmlspecialchars($partner['logo']); ?>" alt="<?php echo htmlspecialchars($partner['name']); ?> Logo">
                                <?php else: ?>
                                <div class="initial"><?php echo strtoupper(substr($partner['name'], 0, 1)); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="partner-name"><?php echo htmlspecialchars($partner['name']); ?></h3>
                            <p class="partner-company"><?php echo htmlspecialchars($partner['company']); ?></p>
                            
                            <div class="partner-type <?php echo strtolower($partner['partnerType']); ?>">
                                <?php echo htmlspecialchars($partner['partnerType']); ?>
                            </div>
                            
                            <div class="partner-info">
                                <p><?php echo nl2br(htmlspecialchars(substr($partner['message'] ?? 'Join us in celebrating music and culture with our amazing partner.', 0, 100))); ?>...</p>
                            </div>
                            
                            <div class="partner-qr">
                                <?php
                                // Generate QR code
                                $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                                $generator = new PartnerCardGenerator($partner, $baseUrl);
                                $qrCode = $generator->generateQRCode();
                                ?>
                                <img src="<?php echo $qrCode; ?>" alt="Partner Offer QR Code">
                                <p>Scan for exclusive offers!</p>
                            </div>
                            
                            <div class="partner-action">
                                <a href="partner_offer.php?id=<?php echo $partner['id']; ?>" class="btn">View Offer</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="partner.php" class="btn btn-lg" style="background: linear-gradient(45deg, #7453fc, #A83AFB); color: white; font-weight: 700; padding: 15px 40px; border-radius: 50px; box-shadow: 0 5px 15px rgba(116, 83, 252, 0.4);">
                    Become a Partner
                </a>
            </div>
        </div>
    </div>
    <!-- ***** Partners Area End ***** -->
    
    <!-- ***** Footer Start ***** -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright © 2025 <a href="#">LiveTheMusic</a> All rights reserved.
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/animation.js"></script>
    <script src="assets/js/imagesloaded.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Initialize particles.js
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('particles-js-partners')) {
                particlesJS('particles-js-partners', {
                    "particles": {
                        "number": {
                            "value": 80,
                            "density": {
                                "enable": true,
                                "value_area": 800
                            }
                        },
                        "color": {
                            "value": "#ffffff"
                        },
                        "shape": {
                            "type": "circle",
                            "stroke": {
                                "width": 0,
                                "color": "#000000"
                            },
                            "polygon": {
                                "nb_sides": 5
                            }
                        },
                        "opacity": {
                            "value": 0.5,
                            "random": true,
                            "anim": {
                                "enable": true,
                                "speed": 1,
                                "opacity_min": 0.1,
                                "sync": false
                            }
                        },
                        "size": {
                            "value": 3,
                            "random": true,
                            "anim": {
                                "enable": true,
                                "speed": 2,
                                "size_min": 0.1,
                                "sync": false
                            }
                        },
                        "line_linked": {
                            "enable": true,
                            "distance": 150,
                            "color": "#7453fc",
                            "opacity": 0.2,
                            "width": 1
                        },
                        "move": {
                            "enable": true,
                            "speed": 1,
                            "direction": "none",
                            "random": true,
                            "straight": false,
                            "out_mode": "out",
                            "bounce": false,
                            "attract": {
                                "enable": false,
                                "rotateX": 600,
                                "rotateY": 600
                            }
                        }
                    },
                    "interactivity": {
                        "detect_on": "canvas",
                        "events": {
                            "onhover": {
                                "enable": true,
                                "mode": "bubble"
                            },
                            "onclick": {
                                "enable": true,
                                "mode": "push"
                            },
                            "resize": true
                        },
                        "modes": {
                            "grab": {
                                "distance": 400,
                                "line_linked": {
                                    "opacity": 1
                                }
                            },
                            "bubble": {
                                "distance": 200,
                                "size": 4,
                                "duration": 2,
                                "opacity": 0.8,
                                "speed": 3
                            },
                            "repulse": {
                                "distance": 200,
                                "duration": 0.4
                            },
                            "push": {
                                "particles_nb": 4
                            },
                            "remove": {
                                "particles_nb": 2
                            }
                        }
                    },
                    "retina_detect": true
                });
            }
        });
    </script>
</body>
</html>
