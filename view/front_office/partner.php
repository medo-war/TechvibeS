<?php
session_start();
require_once '../../Controller/PartnerController.php';

$partnerController = new PartnerController();
// Ensure tables exist
$partnerController->ensurePartnersTableExists();

$success = '';
$error = '';
$contractTemplates = $partnerController->getContractTemplates();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Clean inputs
    function clean($data) {
        return htmlspecialchars(trim($data));
    }

    $name = clean($_POST['name']);
    $company = clean($_POST['company']);
    $email = clean($_POST['email']);
    $phone = clean($_POST['phone']);
    $partnerType = clean($_POST['partnerType']);
    $partnershipValue = clean($_POST['partnershipValue']);
    $message = clean($_POST['message']);
    // contractType field removed as it's redundant with contract_template_id
    $contractStart = isset($_POST['contractStart']) ? clean($_POST['contractStart']) : null;
    $contractEnd = isset($_POST['contractEnd']) ? clean($_POST['contractEnd']) : null;
    $contractTemplateId = isset($_POST['contract_template_id']) ? clean($_POST['contract_template_id']) : null;

    // Create partner object
    $partner = new Partner(
        $name, $company, $email, $phone, $partnerType, 
        $partnershipValue, $message, 
        $contractStart, $contractEnd, $contractTemplateId
    );

    // Add partner
    if ($partnerController->addPartner($partner)) {
        $success = "Thank you for partnering with us, $name!";
    } else {
        $error = "Error saving your data.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="templatemo">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <title>Partner With Us - LIVE THE MUSIC</title>
    
    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-liberty-market.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/partner-complete.css">
    <link rel="stylesheet" href="assets/css/partner-form-fixes.css">
    
    <!-- Custom styles to override template -->
    <style>
      body, html {
        background-color: #000000 !important;
      }
      
      /* Fix for navbar rounding issue */
      .header-area .main-nav {
        border-radius: 50px !important;
        transition: all 0.3s ease;
      }
      
      .header-area.background-header .main-nav {
        border-radius: 0 !important;
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
                    <nav class="main-nav" id="custom-nav">
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
                            <li><a href="partner.php" class="active">Partner</a></li>
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
    
    <!-- ***** Main Banner Area Start ***** -->
    <div class="page-heading">
        <!-- Blurred music logos in background -->
        <div class="music-logos">
            <div class="music-logo logo-1"><i class="fas fa-headphones"></i></div>
            <div class="music-logo logo-2"><i class="fas fa-music"></i></div>
            <div class="music-logo logo-3"><i class="fas fa-guitar"></i></div>
            <div class="music-logo logo-4"><i class="fas fa-drum"></i></div>
            <div class="music-logo logo-5"><i class="fas fa-microphone-alt"></i></div>
            <div class="music-logo logo-6"><i class="fas fa-record-vinyl"></i></div>
            <div class="music-logo logo-7"><i class="fas fa-volume-up"></i></div>
            <div class="music-logo logo-8"><i class="fas fa-compact-disc"></i></div>
        </div>
        
        <!-- Neon glow circles -->
        <div class="neon-circle neon-circle-1"></div>
        <div class="neon-circle neon-circle-2"></div>
        <div class="neon-circle neon-circle-3"></div>
        
        <!-- Equalizer bars -->
        <div class="equalizer">
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
            <div class="equalizer-bar"></div>
        </div>
        
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="glitch-wrapper">
                        <h6 class="glitch-text" data-text="LIVE THE MUSIC">LIVE THE MUSIC</h6>
                    </div>
                    <div class="line-dec" style="margin: 15px auto;"></div>
                    <h2 class="neon-text">Partner With Us</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** Main Banner Area End ***** -->
    
    <!-- Sound wave transition element -->
    <div class="wave-transition">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120">
            <path class="wave-path" d="M0,64L40,69.3C80,75,160,85,240,80C320,75,400,53,480,48C560,43,640,53,720,69.3C800,85,880,107,960,101.3C1040,96,1120,64,1200,48C1280,32,1360,32,1400,32L1440,32L1440,0L1400,0C1360,0,1280,0,1200,0C1120,0,1040,0,960,0C880,0,800,0,720,0C640,0,560,0,480,0C400,0,320,0,240,0C160,0,80,0,40,0L0,0Z"></path>
        </svg>
        
        <!-- Animated music notes floating up -->
        <div class="floating-notes">
            <i class="fas fa-music note-1"></i>
            <i class="fas fa-music note-2"></i>
            <i class="fas fa-music note-3"></i>
            <i class="fas fa-music note-4"></i>
            <i class="fas fa-music note-5"></i>
        </div>
    </div>
    
    <!-- ***** Partner Area Start ***** -->
    <div class="partner-page">
        <!-- Particles.js Background -->
        <div id="particles-js"></div>
        
        <!-- Floating music logos -->
        <div class="floating-logos">
            <div class="logo-item logo-1"><i class="fas fa-music"></i></div>
            <div class="logo-item logo-2"><i class="fas fa-headphones"></i></div>
            <div class="logo-item logo-3"><i class="fas fa-guitar"></i></div>
            <div class="logo-item logo-4"><i class="fas fa-drum"></i></div>
            <div class="logo-item logo-5"><i class="fas fa-microphone-alt"></i></div>
            <div class="logo-item logo-6"><i class="fas fa-record-vinyl"></i></div>
            <div class="logo-item logo-7"><i class="fas fa-compact-disc"></i></div>
            <div class="logo-item logo-8"><i class="fas fa-volume-up"></i></div>
        </div>
        
        <!-- Neon glow effects -->
        <div class="neon-circle neon-circle-1"></div>
        <div class="neon-circle neon-circle-2"></div>
        <div class="neon-circle neon-circle-3"></div>
        
        <div class="partner-container">
            <div class="container">
                <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <script>
                    // Auto-dismiss success message after 2 seconds
                    setTimeout(() => {
                        const alertElement = document.querySelector('.alert');
                        if (alertElement) {
                            // Create a fade-out effect
                            alertElement.style.transition = 'opacity 0.5s';
                            alertElement.style.opacity = '0';
                            
                            // Remove from DOM after fade completes
                            setTimeout(() => alertElement.remove(), 500);
                        }
                    }, 2000);
                </script>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <script>
                    // Auto-dismiss error message after 2 seconds
                    setTimeout(() => {
                        const alertElement = document.querySelector('.alert');
                        if (alertElement) {
                            // Create a fade-out effect
                            alertElement.style.transition = 'opacity 0.5s';
                            alertElement.style.opacity = '0';
                            
                            // Remove from DOM after fade completes
                            setTimeout(() => alertElement.remove(), 500);
                        }
                    }, 2000);
                </script>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-lg-5">
                        <div class="partner-info">
                            <div class="section-heading">
                                <div class="line-dec"></div>
                                <h2>Join Our <em>Network</em></h2>
                            </div>
                            <p>We're looking for venues, sponsors, and music industry professionals to create extraordinary musical experiences together. Partner with Live The Music and reach a wider audience!</p>
                            
                            <div class="info-item">
                                <i class="fas fa-globe"></i>
                                <div>
                                    <h3>Global Reach</h3>
                                    <p>Connect with music enthusiasts and professionals from around the world.</p>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-handshake"></i>
                                <div>
                                    <h3>Exclusive Partnerships</h3>
                                    <p>Collaborate with top artists, venues, and music industry leaders.</p>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-chart-line"></i>
                                <div>
                                    <h3>Growth Opportunities</h3>
                                    <p>Expand your business and reach new audiences through our platform.</p>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-users"></i>
                                <div>
                                    <h3>Community Support</h3>
                                    <p>Join a supportive community of like-minded music industry professionals.</p>
                                </div>
                            </div>
                            
                            <div class="social-links">
                                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-7">
                        <div class="partner-form" style="background: rgba(11, 28, 62, 0.9); border: 2px solid var(--primary-color); border-radius: 20px; padding: 40px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.5), 0 0 15px rgba(116, 83, 252, 0.4); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); transition: all 0.3s ease;">
                            <div class="section-heading">
                                <div class="line-dec"></div>
                                <h2>Become a <em>Partner</em></h2>
                            </div>
                            
                            <form id="partnerForm" action="" method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Your Name</label>
                                            <input type="text" id="name" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">Company Name</label>
                                            <input type="text" id="company" name="company" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="email" id="email" name="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input type="tel" id="phone" name="phone" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="partnerType">Partnership Type</label>
                                    <select id="partnerType" name="partnerType" required>
                                        <option value="">Select Partnership Type</option>
                                        <option value="venue">Venue Partner</option>
                                        <option value="sponsor">Sponsor</option>
                                        <option value="artist">Artist/Band</option>
                                        <option value="promoter">Event Promoter</option>
                                        <option value="media">Media Partner</option>
                                        <option value="technology">Technology Provider</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="contract_template_id">Select a Contract Template</label>
                                    <select id="contract_template_id" name="contract_template_id">
                                        <option value="">Choose a Contract Template</option>
                                        <?php foreach ($contractTemplates as $template): ?>
                                        <option value="<?php echo $template['id']; ?>" 
                                                data-duration="<?php echo $template['duration']; ?>"
                                                data-price-min="<?php echo $template['price_min']; ?>"
                                                data-price-max="<?php echo $template['price_max']; ?>"
                                                data-description="<?php echo htmlspecialchars($template['description']); ?>"
                                                data-benefits="<?php echo htmlspecialchars($template['benefits']); ?>"
                                                data-terms="<?php echo htmlspecialchars($template['terms']); ?>">
                                            <?php echo $template['name']; ?> (<?php echo $template['duration']; ?> months)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div id="contractDetails" style="display: none; background: rgba(11, 28, 62, 0.7); border-radius: 15px; padding: 25px; margin: 20px 0; border: 3px solid var(--primary-color); box-shadow: 0 0 25px rgba(116, 83, 252, 0.5), inset 0 0 15px rgba(116, 83, 252, 0.3); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                                    <div class="text-center mb-4" style="position: relative;">
                                        <div style="position: absolute; top: -5px; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, var(--accent-color), transparent);"></div>
                                        <i class="fas fa-music" style="color: var(--accent-color); font-size: 1.8rem; margin-bottom: 10px; text-shadow: 0 0 10px var(--accent-color);"></i>
                                        <h4 id="templateName" style="color: var(--accent-color); margin-bottom: 5px; font-size: 1.6rem; text-align: center; text-shadow: 0 0 10px rgba(0, 240, 255, 0.5);"></h4>
                                        
                                        <div class="contract-icon-container" style="position: relative; display: inline-block; margin-top: 15px;">
                                            <a href="#" id="viewFullContractBtn" style="position: relative; display: inline-block;">
                                                <i class="fas fa-file-contract" style="color: var(--accent-color); font-size: 2rem; position: relative; z-index: 2; text-shadow: 0 0 15px var(--accent-color);"></i>
                                                <span class="contract-glow" style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; background: radial-gradient(circle, rgba(0, 240, 255, 0.6) 0%, rgba(0, 240, 255, 0) 70%); border-radius: 50%; filter: blur(5px); z-index: 1; animation: contractPulse 2s infinite;"></span>
                                            </a>
                                            <div class="contract-rings" style="position: absolute; top: -10px; left: -10px; right: -10px; bottom: -10px; border: 1px solid rgba(0, 240, 255, 0.3); border-radius: 50%; z-index: 0; animation: contractRings 3s infinite;"></div>
                                            <div class="contract-rings-2" style="position: absolute; top: -15px; left: -15px; right: -15px; bottom: -15px; border: 1px solid rgba(0, 240, 255, 0.2); border-radius: 50%; z-index: 0; animation: contractRings 3s infinite 0.5s;"></div>
                                        </div>
                                        
                                        <div style="position: absolute; bottom: -5px; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, var(--accent-color), transparent);"></div>
                                    </div>
                                    
                                    <style>
                                        @keyframes contractPulse {
                                            0% { opacity: 0.5; transform: scale(0.9); }
                                            50% { opacity: 1; transform: scale(1.1); }
                                            100% { opacity: 0.5; transform: scale(0.9); }
                                        }
                                        
                                        @keyframes contractRings {
                                            0% { transform: scale(0.8); opacity: 0.1; }
                                            50% { transform: scale(1.1); opacity: 0.3; }
                                            100% { transform: scale(0.8); opacity: 0.1; }
                                        }
                                    </style>
                                    
                                    <div style="background: rgba(30, 30, 58, 0.6); padding: 15px; border-radius: 12px; margin-bottom: 20px; border-left: 3px solid var(--secondary-color);">
                                        <p id="templateDescription" style="margin-bottom: 0; color: #fff; font-size: 1.05rem;"></p>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="benefit-box" style="position: relative; margin-bottom: 15px;">
                                                <h5 style="font-size: 1.2rem; color: var(--secondary-color); margin-bottom: 12px; display: flex; align-items: center;">
                                                    <i class="fas fa-star" style="margin-right: 8px; color: var(--secondary-color); text-shadow: 0 0 5px rgba(255, 0, 85, 0.7);"></i>
                                                    Benefits
                                                </h5>
                                                <div style="background: rgba(30, 30, 58, 0.6); padding: 15px; border-radius: 12px; border-left: 3px solid var(--secondary-color); position: relative; overflow: hidden;">
                                                    <div style="position: absolute; top: 0; right: 0; width: 50px; height: 50px; background: radial-gradient(circle at top right, rgba(255, 0, 85, 0.3), transparent 70%);"></div>
                                                    <pre id="templateBenefits" style="background: transparent; border: none; color: rgba(255, 255, 255, 0.9); white-space: pre-wrap; font-family: inherit; font-size: 0.95rem; margin-bottom: 0; position: relative; z-index: 2;"></pre>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="terms-box" style="position: relative; margin-bottom: 15px;">
                                                <h5 style="font-size: 1.2rem; color: var(--neon-green); margin-bottom: 12px; display: flex; align-items: center;">
                                                    <i class="fas fa-file-contract" style="margin-right: 8px; color: var(--neon-green); text-shadow: 0 0 5px rgba(0, 255, 170, 0.7);"></i>
                                                    Terms
                                                </h5>
                                                <div style="background: rgba(30, 30, 58, 0.6); padding: 15px; border-radius: 12px; border-left: 3px solid var(--neon-green); position: relative; overflow: hidden;">
                                                    <div style="position: absolute; top: 0; right: 0; width: 50px; height: 50px; background: radial-gradient(circle at top right, rgba(0, 255, 170, 0.3), transparent 70%);"></div>
                                                    <pre id="templateTerms" style="background: transparent; border: none; color: rgba(255, 255, 255, 0.9); white-space: pre-wrap; font-family: inherit; font-size: 0.95rem; margin-bottom: 0; position: relative; z-index: 2;"></pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div style="margin-top: 20px; text-align: center; background: rgba(30, 30, 58, 0.6); padding: 15px; border-radius: 12px; position: relative; overflow: hidden;">
                                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color));"></div>
                                        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, var(--accent-color), var(--secondary-color), var(--primary-color));"></div>
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <p style="font-weight: bold; color: #fff; margin-bottom: 0; font-size: 1.1rem;">
                                                    <i class="fas fa-dollar-sign" style="color: var(--neon-green); margin-right: 5px;"></i>
                                                    Price Range: <span style="color: var(--neon-green);">$<span id="templatePriceMin"></span> - $<span id="templatePriceMax"></span></span>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p style="font-weight: bold; color: #fff; margin-bottom: 0; font-size: 1.1rem;">
                                                    <i class="fas fa-calendar-alt" style="color: var(--accent-color); margin-right: 5px;"></i>
                                                    Duration: <span style="color: var(--accent-color);"><span id="templateDuration"></span> months</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div style="margin-top: 25px; text-align: center;">
                                        <button type="button" id="printContractBtn" style="background: linear-gradient(45deg, var(--accent-color), var(--primary-color)); border: none; border-radius: 50px; padding: 10px 25px; position: relative; overflow: hidden; transition: all 0.3s;">
                                            <span style="position: relative; z-index: 2; color: #fff; font-weight: 600; display: flex; align-items: center;">
                                                <i class="fas fa-print" style="margin-right: 8px;"></i> Print Contract Details
                                            </span>
                                            <span style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(45deg, var(--primary-color), var(--accent-color)); opacity: 0; transition: all 0.3s; z-index: 1;"></span>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="partnershipValue">Partnership Value ($) *</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="partnershipValue" name="partnershipValue" required>
                                            <small class="form-text text-muted">Enter the estimated value of your partnership contribution</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="contractStart">Contract Start Date *</label>
                                            <input type="date" class="form-control" id="contractStart" name="contractStart" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="contractEnd">Contract End Date *</label>
                                            <input type="date" class="form-control" id="contractEnd" name="contractEnd" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="message">Additional Information</label>
                                    <textarea id="message" name="message" rows="5" placeholder="Tell us more about your partnership goals..."></textarea>
                                </div>
                                
                                <div class="form-group form-check">
                                    <input type="checkbox" id="termsAgree" name="termsAgree" required>
                                    <label for="termsAgree">I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="terms-link">Terms and Conditions</a></label>
                                </div>
                                
                                <div class="submit-container">
                                    <button type="submit" class="submit-btn">
                                        <span class="btn-content">Submit Partnership Request</span>
                                        <span class="btn-glow"></span>
                                        <i class="fas fa-arrow-right btn-icon"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Wave transition -->
        <div class="wave-transition">
            <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
                <path class="wave-path" d="M0,64L40,69.3C80,75,160,85,240,80C320,75,400,53,480,48C560,43,640,53,720,69.3C800,85,880,107,960,101.3C1040,96,1120,64,1200,48C1280,32,1360,32,1400,32L1440,32L1440,0L1400,0C1360,0,1280,0,1200,0C1120,0,1040,0,960,0C880,0,800,0,720,0C640,0,560,0,480,0C400,0,320,0,240,0C160,0,80,0,40,0L0,0Z"></path>
            </svg>
            
            <div class="floating-notes">
                <i class="fas fa-music note-1"></i>
                <i class="fas fa-music note-2"></i>
                <i class="fas fa-music note-3"></i>
                <i class="fas fa-music note-4"></i>
                <i class="fas fa-music note-5"></i>
            </div>
        </div>
    </div>
    <!-- ***** Partner Area End ***** -->
    
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="copyright">
                        <p>Copyright &copy; 2025 <a href="#">Live The Music</a>. All rights reserved.
                        
                        Designed by <a title="CSS Templates" rel="sponsored" href="https://templatemo.com" target="_blank">TemplateMo</a></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Contract View Modal -->
    <div class="modal fade" id="contractViewModal" tabindex="-1" aria-labelledby="contractViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: rgba(11, 28, 62, 0.9); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid var(--accent-color);">
                <div class="modal-header" style="border-bottom: 1px solid var(--accent-color);">
                    <h5 class="modal-title" id="contractViewModalLabel" style="color: var(--accent-color); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5);">Contract Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="frontContractPrintArea">
                    <div class="contract-full-view">
                        <div class="contract-header text-center mb-4">
                            <h3 id="contract-modal-name" style="color: var(--accent-color); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5);">Contract Template</h3>
                            <div class="d-flex justify-content-between">
                                <div class="contract-info">
                                    <span class="badge bg-primary">Duration: <span id="contract-modal-duration">12 months</span></span>
                                </div>
                                <div class="contract-info">
                                    <span class="badge bg-info">Value: $<span id="contract-modal-value">0.00</span></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="contract-section mb-4">
                            <h5 style="color: var(--accent-color);">Description</h5>
                            <div class="p-3" style="background: rgba(0, 240, 255, 0.1); border-radius: 10px; border: 1px solid rgba(0, 240, 255, 0.2);">
                                <p id="contract-modal-description" class="mb-0">Loading contract details...</p>
                            </div>
                        </div>
                        
                        <div class="contract-section mb-4">
                            <h5 style="color: var(--accent-color);">Benefits</h5>
                            <div class="p-3" style="background: rgba(0, 240, 255, 0.1); border-radius: 10px; border: 1px solid rgba(0, 240, 255, 0.2);">
                                <p id="contract-modal-benefits" class="mb-0">Loading benefits...</p>
                            </div>
                        </div>
                        
                        <div class="contract-section">
                            <h5 style="color: var(--accent-color);">Terms & Conditions</h5>
                            <div class="p-3" style="background: rgba(0, 240, 255, 0.1); border-radius: 10px; border: 1px solid rgba(0, 240, 255, 0.2);">
                                <p id="contract-modal-terms" class="mb-0">Loading terms...</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--accent-color);">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn" id="frontPrintContractBtn" style="background-color: var(--accent-color); color: #000;"><i class="fas fa-print me-2"></i>Print Contract</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: rgba(11, 28, 62, 0.9); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid var(--primary-color);">
                <div class="modal-header" style="border-bottom: 1px solid var(--primary-color);">
                    <h5 class="modal-title" id="termsModalLabel" style="color: var(--primary-color); text-shadow: 0 0 10px rgba(116, 83, 252, 0.5);">Terms and Conditions</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div style="position: relative; overflow: hidden;">
                        <div style="position: absolute; top: -100px; right: -100px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(116, 83, 252, 0.2), transparent 70%); filter: blur(20px);"></div>
                        <div style="position: absolute; bottom: -100px; left: -100px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(255, 0, 85, 0.2), transparent 70%); filter: blur(20px);"></div>
                        
                        <h4 style="color: var(--primary-color); margin-bottom: 20px;">Partnership Agreement</h4>
                        
                        <div class="terms-section mb-4">
                            <h5 style="color: var(--secondary-color); font-size: 1.1rem;">1. General Terms</h5>
                            <p style="color: #fff; line-height: 1.6;">By submitting this partnership request, you agree to enter into a potential business relationship with Live The Music. This agreement outlines the general terms and conditions that will govern our partnership.</p>
                        </div>
                        
                        <div class="terms-section mb-4">
                            <h5 style="color: var(--secondary-color); font-size: 1.1rem;">2. Partnership Obligations</h5>
                            <p style="color: #fff; line-height: 1.6;">Both parties agree to act in good faith and maintain professional standards throughout the partnership. Live The Music will provide the services outlined in the selected contract template, and the partner agrees to fulfill their obligations as specified in the final agreement.</p>
                        </div>
                        
                        <div class="terms-section mb-4">
                            <h5 style="color: var(--secondary-color); font-size: 1.1rem;">3. Intellectual Property</h5>
                            <p style="color: #fff; line-height: 1.6;">Each party retains ownership of their respective intellectual property. Any collaborative creations will be governed by separate agreements to be established upon partnership confirmation.</p>
                        </div>
                        
                        <div class="terms-section mb-4">
                            <h5 style="color: var(--secondary-color); font-size: 1.1rem;">4. Confidentiality</h5>
                            <p style="color: #fff; line-height: 1.6;">Both parties agree to maintain the confidentiality of any sensitive information shared during the partnership. This includes business strategies, customer data, and proprietary information.</p>
                        </div>
                        
                        <div class="terms-section mb-4">
                            <h5 style="color: var(--secondary-color); font-size: 1.1rem;">5. Term and Termination</h5>
                            <p style="color: #fff; line-height: 1.6;">The partnership term will be as specified in the selected contract template. Either party may terminate the agreement with written notice as outlined in the final contract.</p>
                        </div>
                        
                        <div class="terms-section">
                            <h5 style="color: var(--secondary-color); font-size: 1.1rem;">6. Governing Law</h5>
                            <p style="color: #fff; line-height: 1.6;">This agreement shall be governed by the laws of the jurisdiction where Live The Music is registered. Any disputes will be resolved through arbitration before pursuing legal action.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--primary-color);">
                    <button type="button" class="btn" style="background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); color: #fff; border: none;" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    
    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/tabs.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/partner.js"></script>
    <script src="assets/js/partner-form-direct.js"></script>
    
    <!-- Particles.js for background effects -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="assets/js/partner.js"></script>
    <script src="assets/js/partner-validation.js"></script>
    
    <script>
        // Contract template selection and display
        document.addEventListener('DOMContentLoaded', function() {
            const contractTemplateSelect = document.getElementById('contract_template_id');
            const contractDetails = document.getElementById('contractDetails');
            const templateName = document.getElementById('templateName');
            const templateDescription = document.getElementById('templateDescription');
            const templateBenefits = document.getElementById('templateBenefits');
            const templateTerms = document.getElementById('templateTerms');
            const templatePriceMin = document.getElementById('templatePriceMin');
            const templatePriceMax = document.getElementById('templatePriceMax');
            const templateDuration = document.getElementById('templateDuration');
            const printContractBtn = document.getElementById('printContractBtn');
            
            // Contract template selection change
            contractTemplateSelect.addEventListener('change', function() {
                if (this.value !== '') {
                    const selectedOption = this.options[this.selectedIndex];
                    
                    // Display contract details
                    templateName.textContent = selectedOption.textContent;
                    templateDescription.textContent = selectedOption.dataset.description;
                    templateBenefits.textContent = selectedOption.dataset.benefits;
                    templateTerms.textContent = selectedOption.dataset.terms;
                    templatePriceMin.textContent = selectedOption.dataset.priceMin;
                    templatePriceMax.textContent = selectedOption.dataset.priceMax;
                    templateDuration.textContent = selectedOption.dataset.duration;
                    
                    // Show contract details
                    contractDetails.style.display = 'block';
                    
                    // Scroll to contract details
                    setTimeout(() => {
                        contractDetails.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                } else {
                    // Hide contract details if no template is selected
                    contractDetails.style.display = 'none';
                }
            });
            
            // Print contract details functionality
            if (printContractBtn) {
                printContractBtn.addEventListener('click', function() {
                    // Create a new window for printing
                    const printWindow = window.open('', '_blank');
                    
                    // Generate print content
                    const printContent = `
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Contract Details - ${templateName.textContent}</title>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    line-height: 1.6;
                                    color: #333;
                                    max-width: 800px;
                                    margin: 0 auto;
                                    padding: 20px;
                                }
                                .header {
                                    text-align: center;
                                    margin-bottom: 30px;
                                    border-bottom: 2px solid #7453fc;
                                    padding-bottom: 20px;
                                }
                                .logo {
                                    font-size: 24px;
                                    font-weight: bold;
                                    color: #7453fc;
                                    margin-bottom: 10px;
                                }
                                h1 {
                                    color: #7453fc;
                                    margin-bottom: 5px;
                                }
                                .contract-info {
                                    display: flex;
                                    justify-content: space-between;
                                    margin-bottom: 20px;
                                    background: #f9f9f9;
                                    padding: 15px;
                                    border-radius: 5px;
                                }
                                .section {
                                    margin-bottom: 25px;
                                }
                                .section h2 {
                                    color: #FF0055;
                                    border-bottom: 1px solid #eee;
                                    padding-bottom: 5px;
                                    margin-bottom: 15px;
                                }
                                .footer {
                                    margin-top: 50px;
                                    border-top: 1px solid #eee;
                                    padding-top: 20px;
                                    text-align: center;
                                    font-size: 12px;
                                    color: #777;
                                }
                                .signature-area {
                                    margin-top: 50px;
                                    display: flex;
                                    justify-content: space-between;
                                }
                                .signature-box {
                                    width: 45%;
                                    border-top: 1px solid #333;
                                    padding-top: 10px;
                                    margin-top: 70px;
                                }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <div class="logo">LIVE THE MUSIC</div>
                                <h1>${templateName.textContent}</h1>
                                <p>Contract Template Details</p>
                            </div>
                            
                            <div class="contract-info">
                                <div>
                                    <strong>Duration:</strong> ${templateDuration.textContent} months
                                </div>
                                <div>
                                    <strong>Price Range:</strong> $${templatePriceMin.textContent} - $${templatePriceMax.textContent}
                                </div>
                                <div>
                                    <strong>Date:</strong> ${new Date().toLocaleDateString()}
                                </div>
                            </div>
                            
                            <div class="section">
                                <h2>Description</h2>
                                <p>${templateDescription.textContent}</p>
                            </div>
                            
                            <div class="section">
                                <h2>Benefits</h2>
                                <p>${templateBenefits.textContent}</p>
                            </div>
                            
                            <div class="section">
                                <h2>Terms & Conditions</h2>
                                <p>${templateTerms.textContent}</p>
                            </div>
                            
                            <div class="signature-area">
                                <div class="signature-box">
                                    Live The Music Representative
                                </div>
                                <div class="signature-box">
                                    Partner Signature
                                </div>
                            </div>
                            
                            <div class="footer">
                                <p>This is a contract template preview. The final contract will be provided upon partnership approval.</p>
                                <p>© ${new Date().getFullYear()} Live The Music. All rights reserved.</p>
                            </div>
                        </body>
                        </html>
                    `;
                    
                    // Write content to the new window
                    printWindow.document.open();
                    printWindow.document.write(printContent);
                    printWindow.document.close();
                    
                    // Wait for content to load then print
                    printWindow.onload = function() {
                        printWindow.print();
                    };
                });
            }
            
            // Add hover effect to print button
            if (printContractBtn) {
                printContractBtn.addEventListener('mouseenter', function() {
                    this.querySelector('span:last-child').style.opacity = '1';
                });
                
                printContractBtn.addEventListener('mouseleave', function() {
                    this.querySelector('span:last-child').style.opacity = '0';
                });
            }
        });
    </script>
</body>
</html>