<?php
/**
 * Partner Offer Page
 * 
 * This page displays special offers and discounts from partners
 * when users scan the QR code from a partner card
 */
session_start();
require_once '../../Controller/PartnerController.php';
require_once '../../Controller/PartnerOfferController.php';

// Check if partner ID is provided
$partnerId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$offerCode = isset($_GET['code']) ? $_GET['code'] : '';

if (!$partnerId) {
    // Redirect to homepage if no partner ID
    header('Location: index.php');
    exit;
}

// Initialize controllers
$partnerController = new PartnerController();
$offerController = new PartnerOfferController();

// Get partner data
$partner = $partnerController->getPartnerById($partnerId);

// Check if partner exists and is active
if (!$partner || $partner['status'] !== 'Active') {
    // Redirect to homepage if partner doesn't exist or is not active
    header('Location: index.php');
    exit;
}

// Get partner offer
$offer = $offerController->getPartnerOffer($partnerId);

// Track interaction
$interactionId = 0;
if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
    $interactionId = $offerController->trackInteraction($partnerId, $userId, $offerCode);
}

// Check if offer is redeemed
$isRedeemed = false;
$redeemMessage = '';
if (isset($_POST['redeem']) && isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
    $result = $offerController->redeemOffer($partnerId, $userId, $offerCode);
    $isRedeemed = $result['success'];
    $redeemMessage = $result['message'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="LiveTheMusic">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <title>Partner Offer - <?php echo htmlspecialchars($partner['name']); ?></title>

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
        
        .offer-container {
            position: relative;
            padding: 80px 0;
            background-color: var(--dark-color);
            overflow: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        /* Particles.js Background */
        #particles-js-offer {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }
        
        /* Neon glow circles */
        .offer-neon-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            z-index: 0;
        }
        
        .offer-neon-circle-1 {
            top: -200px;
            left: -200px;
            width: 500px;
            height: 500px;
            background: var(--primary-color);
            animation: offer-pulse 8s infinite alternate;
        }
        
        .offer-neon-circle-2 {
            bottom: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: var(--secondary-color);
            animation: offer-pulse 10s infinite alternate-reverse;
        }
        
        @keyframes offer-pulse {
            0% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.2); opacity: 0.5; }
            100% { transform: scale(1); opacity: 0.3; }
        }
        
        .offer-card {
            position: relative;
            z-index: 2;
            background: rgba(11, 28, 62, 0.8);
            border-radius: 20px;
            border: 2px solid var(--primary-color);
            box-shadow: 0 0 30px rgba(116, 83, 252, 0.4);
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .offer-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .offer-header h1 {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 10px;
            text-shadow: 0 0 10px rgba(116, 83, 252, 0.7);
        }
        
        .offer-header h2 {
            font-size: 1.5rem;
            color: var(--accent-color);
            margin-bottom: 20px;
            text-shadow: 0 0 10px rgba(0, 240, 255, 0.7);
        }
        
        .partner-logo {
            width: 120px;
            height: 120px;
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
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .offer-content {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .offer-content h3 {
            font-size: 2rem;
            color: white;
            margin-bottom: 20px;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }
        
        .offer-content p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 30px;
        }
        
        .offer-details {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .offer-code {
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 5px;
            color: var(--neon-green);
            text-shadow: 0 0 10px rgba(0, 255, 170, 0.7);
            margin: 20px 0;
            padding: 10px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            border: 1px dashed rgba(0, 255, 170, 0.5);
        }
        
        .offer-expiry {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 10px;
        }
        
        .offer-button {
            display: inline-block;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(116, 83, 252, 0.4);
        }
        
        .offer-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(116, 83, 252, 0.6);
        }
        
        .offer-button:disabled {
            background: linear-gradient(45deg, #666, #999);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .offer-footer {
            text-align: center;
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }
        
        .offer-message {
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
        
        .offer-message.success {
            background: rgba(0, 255, 170, 0.2);
            border: 1px solid rgba(0, 255, 170, 0.5);
            color: var(--neon-green);
        }
        
        .offer-message.error {
            background: rgba(255, 0, 85, 0.2);
            border: 1px solid rgba(255, 0, 85, 0.5);
            color: var(--secondary-color);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .offer-card {
                padding: 20px;
                margin: 0 15px;
            }
            
            .offer-header h1 {
                font-size: 2rem;
            }
            
            .offer-header h2 {
                font-size: 1.2rem;
            }
            
            .offer-content h3 {
                font-size: 1.5rem;
            }
            
            .offer-code {
                font-size: 1.5rem;
                letter-spacing: 3px;
            }
        }
    </style>
</head>
<body>
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
                            <li><a href="explore.html">Explore</a></li>
                            <li><a href="details.html">Item Details</a></li>
                            <li><a href="author.html">Author</a></li>
                            <li><a href="create.html">Create Yours</a></li>
                            <li><a href="partner.php">Partner</a></li>
                        </ul>   
                        <?php if (isset($_SESSION['user'])): ?>
                        <!-- User profile icon -->
                        <div class="user-profile">
                            <div class="profile-icon" id="profileIcon">
                                <img src="/livethemusic/<?= htmlspecialchars($_SESSION['user']['image']) ?>" alt="Profile Image">
                            </div>
                            <div class="profile-dropdown" id="profileDropdown">
                                <a href="profile.php"><?php echo htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']); ?></a>
                                <a href="#">Settings</a>
                                <a href="logout.php">Logout</a>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="border-button">
                            <a href="login.php">Sign In</a>
                        </div>
                        <?php endif; ?>
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
    
    <!-- ***** Partner Offer Area Start ***** -->
    <div class="offer-container">
        <!-- Particles.js Background -->
        <div id="particles-js-offer"></div>
        
        <!-- Neon glow circles -->
        <div class="offer-neon-circle offer-neon-circle-1"></div>
        <div class="offer-neon-circle offer-neon-circle-2"></div>
        
        <div class="container">
            <div class="offer-card">
                <div class="offer-header">
                    <div class="partner-logo">
                        <?php if (!empty($partner['logo'])): ?>
                        <img src="<?php echo htmlspecialchars($partner['logo']); ?>" alt="<?php echo htmlspecialchars($partner['name']); ?> Logo">
                        <?php else: ?>
                        <div class="initial"><?php echo strtoupper(substr($partner['name'], 0, 1)); ?></div>
                        <?php endif; ?>
                    </div>
                    <h1><?php echo htmlspecialchars($partner['name']); ?></h1>
                    <h2><?php echo htmlspecialchars($partner['company']); ?></h2>
                </div>
                
                <?php if ($isRedeemed): ?>
                <div class="offer-message success">
                    <h3><i class="fas fa-check-circle"></i> Offer Redeemed!</h3>
                    <p><?php echo $redeemMessage; ?></p>
                </div>
                <?php else: ?>
                
                <div class="offer-content">
                    <h3><?php echo htmlspecialchars($offer['title'] ?? 'Special Offer'); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($offer['description'] ?? 'Exclusive offer for LiveTheMusic users! Present this code at our venue to redeem your special discount.')); ?></p>
                    
                    <div class="offer-details">
                        <div class="offer-code">
                            <?php echo htmlspecialchars($offer['code'] ?? $offerCode ?? 'LIVEMUSIC'); ?>
                        </div>
                        
                        <?php if (!empty($offer['expiry_date'])): ?>
                        <div class="offer-expiry">
                            Valid until: <?php echo date('F d, Y', strtotime($offer['expiry_date'])); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                    <form method="post" action="">
                        <input type="hidden" name="redeem" value="1">
                        <button type="submit" class="offer-button" <?php echo ($offer['redeemable'] ?? true) ? '' : 'disabled'; ?>>
                            Redeem Offer
                        </button>
                    </form>
                    <?php else: ?>
                    <a href="login.php" class="offer-button">Sign In to Redeem</a>
                    <?php endif; ?>
                </div>
                
                <?php endif; ?>
                
                <div class="offer-footer">
                    <p>Terms and conditions apply. This offer is provided by <?php echo htmlspecialchars($partner['name']); ?> and is subject to their policies.</p>
                    <p>Scan count: <?php echo $offer['scan_count'] ?? 0; ?></p>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** Partner Offer Area End ***** -->
    
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
            if (document.getElementById('particles-js-offer')) {
                particlesJS('particles-js-offer', {
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
