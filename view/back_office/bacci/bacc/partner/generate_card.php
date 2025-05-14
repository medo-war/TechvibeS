<?php
/**
 * Partner Card Generator View
 * 
 * This file handles the display of partner cards with QR codes
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/PartnerCardController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/PartnerOfferController.php';

// Check if partner ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$partnerId = intval($_GET['id']);
$cardController = new PartnerCardController();
$offerController = new PartnerOfferController();
$partner = $cardController->getPartnerById($partnerId);

// Redirect if partner not found
if (!$partner) {
    header('Location: index.php');
    exit;
}

// Generate card content
$cardContent = $cardController->generatePartnerCard($partnerId);

// Get offer statistics
$stats = $offerController->getOfferStatistics($partnerId);

// Handle PDF download request
if (isset($_GET['download']) && $_GET['download'] === 'pdf') {
    $pdfPath = $cardController->generatePartnerCardPDF($partnerId);
    
    if ($pdfPath) {
        // Redirect to the generated HTML file (would be PDF in production)
        header('Location: ' . $pdfPath);
        exit;
    }
}

// Handle print request
$printMode = isset($_GET['print']) && $_GET['print'] === 'true';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="LiveTheMusic - Partner Card" />
    <meta name="author" content="" />
    <title>Partner Card - LiveTheMusic</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --primary-color: #FF0055; /* Rouge néon */
            --secondary-color: #FF2A7F; 
            --accent-color: #00F0FF; /* Bleu néon */
            --dark-color: #0F0F1B; /* Fond sombre */
            --light-color: #1E1E3A;
            --neon-green: #00FFAA;
            --neon-purple: #A83AFB;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark-color);
            color: white;
        }
        
        .card-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
        }
        
        .card-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(45deg, var(--accent-color), #00c8ff);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0, 240, 255, 0.4);
            transition: all 0.3s ease;
        }
        
        .card-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 240, 255, 0.5);
            color: white;
        }
        
        .card-action-btn.download {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 5px 15px rgba(255, 0, 85, 0.4);
        }
        
        .card-action-btn.download:hover {
            box-shadow: 0 8px 25px rgba(255, 0, 85, 0.5);
        }
        
        .card-action-btn.print {
            background: linear-gradient(45deg, var(--neon-green), #00cc88);
            box-shadow: 0 5px 15px rgba(0, 255, 170, 0.4);
        }
        
        .card-action-btn.print:hover {
            box-shadow: 0 8px 25px rgba(0, 255, 170, 0.5);
        }
        
        .card-action-btn.back {
            background: linear-gradient(45deg, var(--light-color), #2a2a5a);
            box-shadow: 0 5px 15px rgba(30, 30, 58, 0.4);
        }
        
        .card-action-btn.back:hover {
            box-shadow: 0 8px 25px rgba(30, 30, 58, 0.5);
        }
        
        @media print {
            .sb-nav-fixed .sb-topnav,
            .sb-nav-fixed #layoutSidenav #layoutSidenav_nav,
            .card-actions,
            .breadcrumb {
                display: none !important;
            }
            
            .sb-nav-fixed #layoutSidenav #layoutSidenav_content {
                padding-left: 0;
                top: 0;
            }
            
            body {
                background-color: white !important;
            }
            
            .container-fluid {
                padding: 0 !important;
            }
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
        
        .loyalty-info {
            background-color: rgba(245, 164, 37, 0.1);
            border: 1px solid #f5a425;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .loyalty-info h4 {
            color: #f5a425;
            margin-bottom: 10px;
        }
    </style>
    <?php if ($printMode): ?>
    <script>
        // Automatically trigger print dialog when page loads in print mode
        window.onload = function() {
            window.print();
        };
    </script>
    <?php endif; ?>
</head>
<body class="sb-nav-fixed">
    <?php if (!$printMode): ?>
    <?php include_once '../includes/topnav.php'; ?>
    <div id="layoutSidenav">
        <?php include_once '../includes/sidenav.php'; ?>
        <div id="layoutSidenav_content">
    <?php else: ?>
    <div>
    <?php endif; ?>
        <main>
            <div class="container-fluid px-4">
                <?php if (!$printMode): ?>
                <h1 class="mt-4"><i class="fas fa-id-card me-2"></i>Partner Card</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="../index1.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="index.php">Partners</a></li>
                    <li class="breadcrumb-item active">Partner Card</li>
                </ol>
                
                <div class="card-actions">
                    <a href="generate_card.php?id=<?php echo $partnerId; ?>&download=pdf" class="card-action-btn download">
                        <i class="fas fa-download"></i> Download Card
                    </a>
                    <a href="generate_card.php?id=<?php echo $partnerId; ?>&print=true" class="card-action-btn print">
                        <i class="fas fa-print"></i> Print Card
                    </a>
                    <a href="index.php" class="card-action-btn back">
                        <i class="fas fa-arrow-left"></i> Back to Partners
                    </a>
                </div>
                <?php endif; ?>
                
                <!-- Partner Card Content -->
                <?php echo $cardContent; ?>
                
                <?php if (!$printMode): ?>
                <!-- Loyalty/Reward System Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-gift me-1"></i>
                        Loyalty/Reward System
                    </div>
                    <div class="card-body">
                        <div class="loyalty-info">
                            <h4><i class="fas fa-gift"></i> Partner Loyalty/Reward System</h4>
                            <p>This partner card includes a QR code that links to a special offer page. When customers scan the QR code, they can access exclusive discounts or rewards from this partner.</p>
                            <p>The system tracks interactions and redemptions, helping you measure engagement and loyalty.</p>
                            <div class="text-center mt-3">
                                <a href="manage_offer.php?id=<?php echo $partnerId; ?>" class="btn btn-warning">
                                    <i class="fas fa-cog"></i> Manage Partner Offer
                                </a>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <h3><?php echo $stats['total_scans']; ?></h3>
                                    <p>Total Scans</p>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <h3><?php echo $stats['total_redemptions']; ?></h3>
                                    <p>Redemptions</p>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <h3><?php echo $stats['conversion_rate']; ?>%</h3>
                                    <p>Conversion Rate</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!$printMode): ?>
                <div class="card-actions mt-4">
                    <a href="generate_card.php?id=<?php echo $partnerId; ?>&download=pdf" class="card-action-btn download">
                        <i class="fas fa-download"></i> Download Card
                    </a>
                    <a href="generate_card.php?id=<?php echo $partnerId; ?>&print=true" class="card-action-btn print">
                        <i class="fas fa-print"></i> Print Card
                    </a>
                    <a href="manage_offer.php?id=<?php echo $partnerId; ?>" class="card-action-btn" style="background: linear-gradient(45deg, #f5a425, #f39c12); box-shadow: 0 5px 15px rgba(245, 164, 37, 0.4);">
                        <i class="fas fa-gift"></i> Manage Offer
                    </a>
                    <a href="index.php" class="card-action-btn back">
                        <i class="fas fa-arrow-left"></i> Back to Partners
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </main>
        <?php if (!$printMode): ?>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; LiveTheMusic 2025</div>
                </div>
            </div>
        </footer>
        <?php endif; ?>
    </div>
    <?php if (!$printMode): ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <?php endif; ?>
</body>
</html>
