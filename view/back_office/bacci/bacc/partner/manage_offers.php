<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/PartnerOfferController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/utils/NetworkUtils.php';

// Initialize controllers
$partnerOfferController = new PartnerOfferController();
$partnerController = new BackOfficePartnerController();

// Get partner ID from URL
$partnerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get partner details
$partner = $partnerController->getPartnerById($partnerId);

// If partner not found, redirect to partners page
if (!$partner) {
    header('Location: partners.php?error=' . urlencode('Partner not found'));
    exit;
}

// Get partner offer
$offer = $partnerOfferController->getPartnerOffer($partnerId);

// Get offer statistics - force a fresh count from the database
$stats = $partnerOfferController->getOfferStatistics($partnerId, true);

// Handle form submission
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $discountAmount = floatval($_POST['discount_amount'] ?? 0);
    $discountType = $_POST['discount_type'] ?? 'percentage';
    $expiryDate = $_POST['expiry_date'] ?? '';
    $redeemable = isset($_POST['redeemable']) ? true : false;
    
    // Validate required fields
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Title is required';
    }
    
    if (empty($description)) {
        $errors[] = 'Description is required';
    }
    
    if (empty($code)) {
        $errors[] = 'Offer code is required';
    }
    
    if ($discountAmount <= 0) {
        $errors[] = 'Discount amount must be greater than zero';
    }
    
    if (empty($expiryDate)) {
        $errors[] = 'Expiry date is required';
    }
    
    // If no errors, save offer
    if (empty($errors)) {
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
        
        if ($partnerOfferController->savePartnerOffer($offerData)) {
            $successMessage = 'Offer saved successfully';
            
            // Refresh offer data
            $offer = $partnerOfferController->getPartnerOffer($partnerId);
        } else {
            $errorMessage = 'Failed to save offer';
        }
    } else {
        $errorMessage = implode('<br>', $errors);
    }
}

// Generate a new offer code if none exists
$offerCode = $offer['code'] ?? substr(str_replace(' ', '', $partner['name']), 0, 3) . strtoupper(substr(md5(time()), 0, 5));

// Page title
$pageTitle = 'Manage Partner Offer - ' . htmlspecialchars($partner['name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?php echo $pageTitle; ?> - LiveTheMusic Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .partner-type {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .sponsor { background-color: #28a745; color: white; }
        .venue { background-color: #007bff; color: white; }
        .media { background-color: #fd7e14; color: white; }
        .vendor { background-color: #6f42c1; color: white; }
        
        .card-stats {
            border-left: 4px solid #0d6efd;
            transition: transform 0.2s;
        }
        
        .card-stats:hover {
            transform: translateY(-5px);
        }
        
        .qr-code-container {
            text-align: center;
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .qr-code-container img {
            max-width: 100%;
        }
        
        .offer-code {
            font-family: monospace;
            font-size: 24px;
            letter-spacing: 2px;
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
        }
        
        .offer-form label {
            font-weight: 500;
        }
        
        .offer-form .form-control {
            border: 1px solid #ced4da;
        }
        
        .offer-form .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .interaction-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .interaction-item {
            border-left: 3px solid #0d6efd;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f8f9fa;
        }
        
        .interaction-item.redeem {
            border-left-color: #28a745;
        }
        
        .interaction-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="../index.html">LiveTheMusic Admin</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="#!">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="../index.html">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Partners</div>
                        <a class="nav-link" href="partners.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                            Partner Management
                        </a>
                        <a class="nav-link" href="applications.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                            Partner Applications
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Admin User
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4"><?php echo htmlspecialchars($partner['name']); ?> - Offer Management</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="../index.html">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="partners.php">Partners</a></li>
                        <li class="breadcrumb-item active">Manage Offer</li>
                    </ol>
                    
                    <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> <?php echo $successMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> <?php echo $errorMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-id-card me-1"></i>
                                    Partner Information
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Company:</strong> <?php echo htmlspecialchars($partner['company']); ?>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Contact:</strong> <?php echo htmlspecialchars($partner['name']); ?>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Email:</strong> <?php echo htmlspecialchars($partner['email']); ?>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Phone:</strong> <?php echo htmlspecialchars($partner['phone']); ?>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Type:</strong> 
                                        <span class="badge partner-type <?php echo strtolower($partner['partnerType']); ?>">
                                            <?php echo htmlspecialchars($partner['partnerType']); ?>
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Partnership Value:</strong> $<?php echo number_format($partner['partnershipValue'], 2); ?>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Status:</strong> 
                                        <span class="badge bg-<?php echo $partner['status'] === 'Active' ? 'success' : 'danger'; ?>">
                                            <?php echo htmlspecialchars($partner['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            

                            
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-qrcode me-1"></i>
                                    QR Code
                                </div>
                                <div class="card-body">
                                    <div class="qr-code-container">
                                        <?php 
                                        $offerCode = $offer['code'] ?? $offerCode;
                                        
                                        // Get the offer URL using the improved method
                                        $offerUrl = NetworkUtils::getPageUrl('view/front_office/partner-offer.php', [
                                            'id' => $partnerId,
                                            'code' => $offerCode
                                        ]);
                                        
                                        // Generate QR code using QRServer API
                                        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($offerUrl);
                                        ?>
                                        <img src="<?php echo $qrUrl; ?>" alt="Partner Offer QR Code" class="img-fluid">
                                        <div class="offer-code mt-2">
                                            <?php echo $offerCode; ?>
                                        </div>
                                        <div class="small text-muted mt-2">
                                            <i class="fas fa-wifi me-1"></i> This QR code will only work on the same WiFi network
                                        </div>
                                        <div class="small text-muted">
                                            URL: <?php echo htmlspecialchars($offerUrl); ?>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <a href="<?php echo $qrUrl; ?>" download="partner_offer_qr.png" class="btn btn-primary">
                                            <i class="fas fa-download me-2"></i> Download QR Code
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-gift me-1"></i>
                                    Manage Partner Offer
                                </div>
                                <div class="card-body">
                                    <form method="POST" class="offer-form">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="title" class="form-label">Offer Title</label>
                                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($offer['title'] ?? 'Special Discount'); ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="code" class="form-label">Offer Code</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="code" name="code" value="<?php echo htmlspecialchars($offer['code'] ?? $offerCode); ?>" required>
                                                    <button class="btn btn-outline-secondary" type="button" id="generateCodeBtn">
                                                        <i class="fas fa-random"></i> Generate
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Offer Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($offer['description'] ?? 'Exclusive offer for LiveTheMusic users! Present this code at our venue to redeem your special discount.'); ?></textarea>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="discount_amount" class="form-label">Discount Amount</label>
                                                <input type="number" class="form-control" id="discount_amount" name="discount_amount" step="0.01" min="0" value="<?php echo htmlspecialchars($offer['discount_amount'] ?? '15'); ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="discount_type" class="form-label">Discount Type</label>
                                                <select class="form-select" id="discount_type" name="discount_type" required>
                                                    <option value="percentage" <?php echo (($offer['discount_type'] ?? 'percentage') === 'percentage') ? 'selected' : ''; ?>>Percentage (%)</option>
                                                    <option value="fixed" <?php echo (($offer['discount_type'] ?? '') === 'fixed') ? 'selected' : ''; ?>>Fixed Amount ($)</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                                <?php
                                                $defaultExpiry = date('Y-m-d', strtotime('+3 months'));
                                                $expiryDate = $offer['expiry_date'] ?? $defaultExpiry;
                                                ?>
                                                <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?php echo $expiryDate; ?>" min="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mt-4">
                                                    <input class="form-check-input" type="checkbox" id="redeemable" name="redeemable" <?php echo (($offer['redeemable'] ?? 1) == 1) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="redeemable">
                                                        Offer is redeemable
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="partners.php" class="btn btn-secondary me-md-2">
                                                <i class="fas fa-arrow-left me-2"></i> Back to Partners
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Offer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-history me-1"></i>
                                    Recent Interactions
                                </div>
                                <div class="card-body">
                                    <div class="interaction-list">
                                        <?php if (empty($stats['recent_interactions'])): ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i> No interactions recorded yet.
                                        </div>
                                        <?php else: ?>
                                            <?php foreach ($stats['recent_interactions'] as $interaction): ?>
                                            <div class="interaction-item <?php echo $interaction['interaction_type']; ?>">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>
                                                            <?php if ($interaction['interaction_type'] === 'scan'): ?>
                                                            <i class="fas fa-qrcode me-2"></i> QR Code Scan
                                                            <?php else: ?>
                                                            <i class="fas fa-check-circle me-2"></i> Offer Redemption
                                                            <?php endif; ?>
                                                        </strong>
                                                        <?php if (!empty($interaction['first_name'])): ?>
                                                        by <?php echo htmlspecialchars($interaction['first_name'] . ' ' . $interaction['last_name']); ?>
                                                        <?php else: ?>
                                                        by Anonymous User
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="interaction-time">
                                                        <?php echo date('M d, Y h:i A', strtotime($interaction['created_at'])); ?>
                                                    </div>
                                                </div>
                                                <div class="mt-2 small">
                                                    <span class="text-muted">IP:</span> <?php echo htmlspecialchars($interaction['ip_address']); ?>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; LiveTheMusic 2025</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script>

        
        // Simple function to generate a random offer code
        document.addEventListener('DOMContentLoaded', function() {
            const generateCodeBtn = document.getElementById('generateCodeBtn');
            if (generateCodeBtn) {
                generateCodeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Use only easily readable characters (no 0/O or 1/I confusion)
                    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                    let code = '';
                    for (let i = 0; i < 8; i++) {
                        code += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    document.getElementById('code').value = code;
                });
            }
        });
    </script>
</body>
</html>
