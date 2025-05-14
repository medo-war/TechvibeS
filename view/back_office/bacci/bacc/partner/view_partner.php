<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php';

// Initialize controller
$partnerController = new BackOfficePartnerController();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?error=' . urlencode('Partner ID is required'));
    exit;
}

$partnerId = intval($_GET['id']);

// Get partner details
$partner = $partnerController->getPartnerById($partnerId);

// Check if partner exists
if (!$partner) {
    header('Location: index.php?error=' . urlencode('Partner not found'));
    exit;
}

// Get contract template details if available
$contractTemplate = null;
if (!empty($partner['contract_template_id'])) {
    $templates = $partnerController->getContractTemplates();
    foreach ($templates as $template) {
        if ($template['id'] == $partner['contract_template_id']) {
            $contractTemplate = $template;
            break;
        }
    }
}

// Handle status change
if (isset($_POST['action']) && $_POST['action'] === 'updateStatus') {
    $newStatus = $_POST['status'];
    if ($partnerController->updatePartnerStatus($partnerId, $newStatus)) {
        $partner['status'] = $newStatus;
        $statusMessage = 'Partner status updated successfully!';
    } else {
        $statusError = 'Failed to update partner status.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="LiveTheMusic - View Partner" />
    <meta name="author" content="" />
    <title>Partner Details - LiveTheMusic</title>
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
        
        .partner-profile-card {
            background-color: var(--light-color);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .partner-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            padding: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .partner-header::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                transparent,
                transparent,
                transparent,
                rgba(255, 255, 255, 0.1)
            );
            transform: rotate(30deg);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: rotate(30deg) translate(-10%, -10%); }
            100% { transform: rotate(30deg) translate(10%, 10%); }
        }
        
        .partner-name {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
            position: relative;
            z-index: 2;
        }
        
        .partner-company {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
        }
        
        .partner-type-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-right: 10px;
            position: relative;
            z-index: 2;
        }
        
        .partner-type-badge.sponsor {
            background-color: var(--primary-color);
            color: white;
        }
        
        .partner-type-badge.venue {
            background-color: var(--accent-color);
            color: var(--dark-color);
        }
        
        .partner-type-badge.media {
            background-color: var(--neon-purple);
            color: white;
        }
        
        .partner-type-badge.technology {
            background-color: var(--neon-green);
            color: var(--dark-color);
        }
        
        .partner-type-badge.other {
            background-color: #6c757d;
            color: white;
        }
        
        .partner-status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            position: relative;
            z-index: 2;
        }
        
        .partner-status-badge.active {
            background-color: var(--neon-green);
            color: var(--dark-color);
        }
        
        .partner-status-badge.pending {
            background-color: var(--accent-color);
            color: var(--dark-color);
        }
        
        .partner-status-badge.inactive {
            background-color: #6c757d;
            color: white;
        }
        
        .partner-body {
            padding: 30px;
        }
        
        .partner-info-section {
            margin-bottom: 30px;
        }
        
        .partner-info-section h4 {
            color: var(--accent-color);
            font-size: 1.3rem;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .partner-info-item {
            display: flex;
            margin-bottom: 15px;
        }
        
        .partner-info-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--accent-color);
        }
        
        .partner-info-content {
            flex: 1;
        }
        
        .partner-info-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 3px;
        }
        
        .partner-info-value {
            font-size: 1.1rem;
            color: white;
        }
        
        .partner-value-box {
            background: linear-gradient(135deg, rgba(0, 255, 170, 0.1), rgba(0, 240, 255, 0.1));
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid rgba(0, 255, 170, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .partner-value-box::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(0, 255, 170, 0.3), transparent 70%);
            border-radius: 50%;
            opacity: 0.5;
        }
        
        .partner-value-label {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }
        
        .partner-value-amount {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--neon-green);
            text-shadow: 0 0 10px rgba(0, 255, 170, 0.5);
        }
        
        .partner-contract-box {
            background: linear-gradient(135deg, rgba(255, 0, 85, 0.1), rgba(168, 58, 251, 0.1));
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 0, 85, 0.2);
        }
        
        .partner-contract-dates {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        
        .partner-contract-date {
            text-align: center;
            flex: 1;
        }
        
        .partner-contract-date-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 5px;
        }
        
        .partner-contract-date-value {
            font-size: 1.1rem;
            color: white;
            padding: 5px 10px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
            display: inline-block;
        }
        
        .partner-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .contract-template-details {
            background-color: rgba(30, 30, 58, 0.5);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid rgba(0, 240, 255, 0.2);
        }
        
        .contract-template-details h5 {
            color: var(--accent-color);
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .contract-template-section {
            margin-bottom: 15px;
        }
        
        .contract-template-section-title {
            font-size: 1rem;
            color: var(--primary-color);
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .contract-template-section-content {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.8);
            background-color: rgba(255, 255, 255, 0.05);
            padding: 10px;
            border-radius: 5px;
        }
        
        .partner-notes {
            background-color: rgba(30, 30, 58, 0.5);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid rgba(255, 0, 85, 0.2);
        }
        
        .partner-notes h5 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .partner-notes-content {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.8);
            background-color: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 5px;
            white-space: pre-line;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="../index1.php">LIVE<span>THE</span>MUSIC</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for artists, events..." aria-label="Search" aria-describedby="btnNavbarSearch" style="background-color: rgba(255, 255, 255, 0.1); color: white; border: 1px solid rgba(255, 0, 85, 0.3);" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" style="background-color: var(--light-color); border: 1px solid rgba(255, 0, 85, 0.3);">
                    <li><a class="dropdown-item" href="#!" style="color: white;">Profile</a></li>
                    <li><a class="dropdown-item" href="#!" style="color: white;">Settings</a></li>
                    <li><hr class="dropdown-divider" style="border-color: rgba(255, 0, 85, 0.3);" /></li>
                    <li><a class="dropdown-item" href="#!" style="color: white;">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <?php include_once '../includes/sidenav.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4"><i class="fas fa-handshake me-2"></i>Partner Details</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="../index1.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Partners</a></li>
                        <li class="breadcrumb-item active">View Partner</li>
                    </ol>
                    
                    <?php if (isset($statusMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($statusMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($statusError)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($statusError); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <div class="partner-profile-card mb-4">
                        <div class="partner-header">
                            <div class="partner-name"><?php echo htmlspecialchars($partner['name']); ?></div>
                            <div class="partner-company"><?php echo htmlspecialchars($partner['company']); ?></div>
                            <div class="d-flex align-items-center">
                                <span class="partner-type-badge <?php echo strtolower($partner['partnerType']); ?>">
                                    <?php echo htmlspecialchars($partner['partnerType']); ?>
                                </span>
                                <span class="partner-status-badge <?php echo strtolower($partner['status'] ?? 'pending'); ?>">
                                    <?php echo htmlspecialchars($partner['status'] ?? 'Pending'); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="partner-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="partner-value-box">
                                        <div class="partner-value-label">Partnership Value</div>
                                        <div class="partner-value-amount">$<?php echo number_format($partner['partnershipValue'], 2); ?></div>
                                    </div>
                                    
                                    <div class="partner-info-section">
                                        <h4>Contact Information</h4>
                                        <div class="partner-info-item">
                                            <div class="partner-info-icon">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <div class="partner-info-content">
                                                <div class="partner-info-label">Email Address</div>
                                                <div class="partner-info-value"><?php echo htmlspecialchars($partner['email']); ?></div>
                                            </div>
                                        </div>
                                        <div class="partner-info-item">
                                            <div class="partner-info-icon">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                            <div class="partner-info-content">
                                                <div class="partner-info-label">Phone Number</div>
                                                <div class="partner-info-value"><?php echo htmlspecialchars($partner['phone']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($partner['message'])): ?>
                                    <div class="partner-notes">
                                        <h5><i class="fas fa-sticky-note me-2"></i>Additional Notes</h5>
                                        <div class="partner-notes-content">
                                            <?php echo nl2br(htmlspecialchars($partner['message'])); ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-lg-4">
                                    <div class="partner-contract-box">
                                        <h4 style="color: var(--primary-color); margin-bottom: 15px;">Contract Details</h4>
                                        <div class="partner-info-item">
                                            <div class="partner-info-icon">
                                                <i class="fas fa-file-contract"></i>
                                            </div>
                                            <div class="partner-info-content">
                                                <div class="partner-info-label">Contract Type</div>
                                                <div class="partner-info-value"><?php echo htmlspecialchars($partner['contractType'] ?? 'Not specified'); ?></div>
                                            </div>
                                        </div>
                                        
                                        <div class="partner-contract-dates">
                                            <div class="partner-contract-date">
                                                <div class="partner-contract-date-label">Start Date</div>
                                                <div class="partner-contract-date-value">
                                                    <?php echo !empty($partner['contractStart']) ? date('M d, Y', strtotime($partner['contractStart'])) : 'N/A'; ?>
                                                </div>
                                            </div>
                                            <div class="partner-contract-date">
                                                <div class="partner-contract-date-label">End Date</div>
                                                <div class="partner-contract-date-value">
                                                    <?php echo !empty($partner['contractEnd']) ? date('M d, Y', strtotime($partner['contractEnd'])) : 'N/A'; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if ($contractTemplate): ?>
                                        <div class="contract-template-details">
                                            <h5><?php echo htmlspecialchars($contractTemplate['name']); ?></h5>
                                            
                                            <div class="contract-template-section">
                                                <div class="contract-template-section-title">Description</div>
                                                <div class="contract-template-section-content">
                                                    <?php echo nl2br(htmlspecialchars($contractTemplate['description'])); ?>
                                                </div>
                                            </div>
                                            
                                            <div class="contract-template-section">
                                                <div class="contract-template-section-title">Benefits</div>
                                                <div class="contract-template-section-content">
                                                    <?php echo nl2br(htmlspecialchars($contractTemplate['benefits'])); ?>
                                                </div>
                                            </div>
                                            
                                            <div class="contract-template-section">
                                                <div class="contract-template-section-title">Terms</div>
                                                <div class="contract-template-section-content">
                                                    <?php echo nl2br(htmlspecialchars($contractTemplate['terms'])); ?>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between mt-3">
                                                <span style="color: var(--neon-green);">
                                                    <i class="fas fa-calendar-alt me-1"></i> <?php echo $contractTemplate['duration']; ?> months
                                                </span>
                                                <span style="color: var(--accent-color);">
                                                    <i class="fas fa-dollar-sign me-1"></i> $<?php echo number_format($contractTemplate['price_min'], 0); ?> - $<?php echo number_format($contractTemplate['price_max'], 0); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Status Update Form -->
                                    <div class="card mb-4" style="background-color: rgba(30, 30, 58, 0.5); border: 1px solid rgba(255, 255, 255, 0.1);">
                                        <div class="card-header" style="background-color: rgba(255, 0, 85, 0.1); border-bottom: 1px solid rgba(255, 0, 85, 0.2);">
                                            <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Update Status</h5>
                                        </div>
                                        <div class="card-body">
                                            <form method="POST" action="">
                                                <input type="hidden" name="action" value="updateStatus">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Partner Status</label>
                                                    <select class="form-select" id="status" name="status" style="background-color: rgba(255, 255, 255, 0.05); color: white; border: 1px solid rgba(255, 255, 255, 0.1);">
                                                        <option value="Pending" <?php echo ($partner['status'] ?? 'Pending') === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Active" <?php echo ($partner['status'] ?? '') === 'Active' ? 'selected' : ''; ?>>Active</option>
                                                        <option value="Inactive" <?php echo ($partner['status'] ?? '') === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="fas fa-save me-2"></i>Update Status
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="partner-actions">
                                <div>
                                    <a href="index.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Partners
                                    </a>
                                </div>
                                <div>
                                    <a href="edit_partner.php?id=<?php echo $partner['id']; ?>" class="btn btn-primary me-2">
                                        <i class="fas fa-edit me-2"></i>Edit Partner
                                    </a>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fas fa-trash-alt me-2"></i>Delete Partner
                                    </button>
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
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background-color: var(--light-color); color: white;">
                <div class="modal-header" style="border-bottom: 1px solid var(--primary-color);">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this partner? This action cannot be undone.</p>
                    <p><strong>Partner:</strong> <?php echo htmlspecialchars($partner['name']); ?> (<?php echo htmlspecialchars($partner['company']); ?>)</p>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--primary-color);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="delete_partner.php?id=<?php echo $partner['id']; ?>" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>
