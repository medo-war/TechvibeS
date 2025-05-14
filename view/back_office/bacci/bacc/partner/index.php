<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Model/partner.php';

// Initialize controller
$partnerController = new BackOfficePartnerController();

// Ensure tables exist
$partnerController->ensurePartnersTableExists();
$partnerController->ensurePartnerStatusColumn();

// Handle form submission for adding a new partner
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addPartner') {
    // Validate form data
    $name = trim($_POST['name'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $partnerType = trim($_POST['partnerType'] ?? '');
    $partnershipValue = floatval($_POST['partnershipValue'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    $status = trim($_POST['status'] ?? 'Pending');
    $contractType = trim($_POST['contractType'] ?? '');
    $contractStart = trim($_POST['contractStart'] ?? '');
    $contractEnd = trim($_POST['contractEnd'] ?? '');
    $contract_template_id = intval($_POST['contract_template_id'] ?? 0);
    
    // Validate required fields
    if (empty($name) || empty($company) || empty($email) || empty($phone) || empty($partnerType)) {
        $error = 'Please fill in all required fields';
    } else {
        // Create partner object
        $partner = new Partner(
            $name,
            $company,
            $email,
            $phone,
            $partnerType,
            $partnershipValue,
            $message,
            $contractType,
            $contractStart,
            $contractEnd,
            $contract_template_id
        );
        
        // Add partner to database
        if ($partnerController->addPartner($partner)) {
            // Update status if not default
            if ($status !== 'Pending') {
                $db = Config::getConnexion();
                $sql = "UPDATE partners SET status = :status WHERE email = :email AND name = :name ORDER BY id DESC LIMIT 1";
                $query = $db->prepare($sql);
                $query->execute([
                    'status' => $status,
                    'email' => $email,
                    'name' => $name
                ]);
            }
            $success = 'Partner added successfully!';
            
            // Refresh partners list
            $page = 1; // Reset to first page after adding
        } else {
            $error = 'Failed to add partner. Please try again.';
        }
    }
}

// Get partner statistics with forced refresh to ensure fresh data
$statistics = $partnerController->getPartnerStatistics(true);

// Get partners with pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

// Handle filters
$filters = [];
if (isset($_GET['partnerType'])) $filters['partnerType'] = $_GET['partnerType'];
if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
if (isset($_GET['search'])) $filters['search'] = $_GET['search'];
if (isset($_GET['sort'])) $filters['sort'] = $_GET['sort'];

// Add default filter to exclude pending partners
if (!isset($filters['status']) || $filters['status'] === 'all') {
    $filters['excludeStatus'] = 'Pending';
}

// Get partners
$partnersData = $partnerController->getPartnersPaginated($page, $limit, $filters);
$partners = $partnersData['partners'];

// Get contract templates for the form
$contractTemplates = $partnerController->getContractTemplates();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="LiveTheMusic - Partner Management" />
    <meta name="author" content="" />
    <title>Partner Management - LiveTheMusic</title>
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
        
        .sb-topnav {
            background-color: rgba(15, 15, 27, 0.9) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 0, 85, 0.3);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            text-shadow: 0 0 10px rgba(255, 0, 85, 0.7);
        }
        
        .navbar-brand span {
            color: var(--accent-color);
            text-shadow: 0 0 10px rgba(0, 240, 255, 0.7);
        }
        
        .sb-sidenav {
            background-color: var(--dark-color);
            border-right: 1px solid rgba(255, 0, 85, 0.2);
        }
        
        .sb-sidenav-dark {
            background-color: var(--dark-color);
            color: rgba(255, 255, 255, 0.8);
        }
        
        .sb-sidenav-dark .sb-sidenav-menu .nav-link {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .sb-sidenav-dark .sb-sidenav-menu .nav-link:hover {
            color: var(--accent-color);
            text-shadow: 0 0 10px rgba(0, 240, 255, 0.5);
        }
        
        .sb-sidenav-dark .sb-sidenav-menu .nav-link.active {
            color: var(--primary-color);
            text-shadow: 0 0 10px rgba(255, 0, 85, 0.5);
        }
        
        .sb-sidenav-dark .sb-sidenav-menu .sb-sidenav-menu-heading {
            color: var(--primary-color);
        }
        
        .sb-sidenav-dark .sb-sidenav-footer {
            background-color: var(--dark-color);
            border-top: 1px solid rgba(255, 0, 85, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            box-shadow: 0 0 15px rgba(255, 0, 85, 0.4);
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            box-shadow: 0 0 20px rgba(255, 0, 85, 0.6);
        }
        
        .card {
            background-color: var(--light-color);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        
        .card-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-bottom: none;
        }
        
        .breadcrumb-item a {
            color: var(--accent-color);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .table {
            color: white;
        }
        
        .table thead th {
            background-color: rgba(255, 0, 85, 0.1);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .table tbody td {
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        /* Partner card styles */
        .partner-card {
            background-color: var(--light-color);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
        }
        
        .partner-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border-color: var(--primary-color);
        }
        
        .partner-card .card-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
            padding: 15px;
            position: relative;
            overflow: hidden;
        }
        
        .partner-card .card-header::after {
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
        
        .partner-card .card-body {
            padding: 20px;
        }
        
        .partner-type {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 2;
        }
        
        .partner-type.sponsor {
            background-color: var(--primary-color);
            color: white;
        }
        
        .partner-type.venue {
            background-color: var(--accent-color);
            color: var(--dark-color);
        }
        
        .partner-type.media {
            background-color: var(--neon-purple);
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
        
        .partner-status {
            position: absolute;
            bottom: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .partner-status.active {
            background-color: var(--neon-green);
            color: var(--dark-color);
        }
        
        .partner-status.pending {
            background-color: var(--accent-color);
            color: var(--dark-color);
        }
        
        .partner-status.inactive {
            background-color: #6c757d;
            color: white;
        }
        
        .partner-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .partner-info {
            margin-bottom: 15px;
        }
        
        .partner-info p {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }
        
        .partner-info i {
            width: 20px;
            margin-right: 10px;
            color: var(--accent-color);
        }
        
        .partner-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        
        .partner-actions .btn {
            padding: 5px 10px;
            font-size: 0.8rem;
        }
        
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        
        .pagination .page-item .page-link {
            background-color: var(--light-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .pagination .page-item .page-link:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Statistics cards */
        .stat-card {
            border-left: 4px solid;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card.primary {
            border-left-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(255, 0, 85, 0.2), rgba(255, 42, 127, 0.2));
        }
        
        .stat-card.success {
            border-left-color: var(--neon-green);
            background: linear-gradient(135deg, rgba(0, 255, 170, 0.2), rgba(0, 200, 150, 0.2));
        }
        
        .stat-card.info {
            border-left-color: var(--accent-color);
            background: linear-gradient(135deg, rgba(0, 240, 255, 0.2), rgba(0, 200, 255, 0.2));
        }
        
        .stat-card.warning {
            border-left-color: var(--neon-purple);
            background: linear-gradient(135deg, rgba(168, 58, 251, 0.2), rgba(140, 30, 255, 0.2));
        }
        
        .stat-card .card-title {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 5px;
        }
        
        .stat-card .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .stat-card .card-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 2rem;
            opacity: 0.2;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <?php include_once '../includes/topnav.php'; ?>
    <div id="layoutSidenav">
        <?php include_once '../includes/sidenav.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4"><i class="fas fa-handshake me-2"></i>Partner Management</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="../index1.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Partners</li>
                    </ol>
                    
                    <!-- Statistics Row -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-card primary">
                                <div class="card-title">Total Partners</div>
                                <div class="card-value"><?php echo $statistics['total']; ?></div>
                                <div class="card-icon"><i class="fas fa-users"></i></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-card success">
                                <div class="card-title">Active Partners</div>
                                <?php
                                $activeCount = 0;
                                foreach ($statistics['byStatus'] as $status) {
                                    if ($status['status'] === 'Active') {
                                        $activeCount = $status['count'];
                                        break;
                                    }
                                }
                                ?>
                                <div class="card-value"><?php echo $activeCount; ?></div>
                                <div class="card-icon"><i class="fas fa-check-circle"></i></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-card info">
                                <div class="card-title">Pending Applications</div>
                                <?php
                                $pendingCount = 0;
                                foreach ($statistics['byStatus'] as $status) {
                                    if ($status['status'] === 'Pending') {
                                        $pendingCount = $status['count'];
                                        break;
                                    }
                                }
                                ?>
                                <div class="card-value"><?php echo $pendingCount; ?></div>
                                <div class="card-icon"><i class="fas fa-clock"></i></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-card warning">
                                <div class="card-title">Total Partnership Value</div>
                                <div class="card-value">$<?php echo number_format($statistics['totalValue'], 2); ?></div>
                                <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Partner Management Header -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h2 class="text-primary mb-0">Partner Directory</h2>
                                    <p class="text-light">Manage your venue and sponsor partnerships</p>
                                </div>
                                <div class="d-flex flex-wrap">
                                    <div class="search-box me-3 mb-2">
                                        <form method="GET" action="" id="searchForm">
                                            <div class="voice-search-container" style="position: relative; max-width: 450px;">
                                                <!-- Search Input Group with Neon Glow -->
                                                <div class="input-group" style="box-shadow: 0 0 20px rgba(255, 0, 85, 0.2); border-radius: 25px; overflow: hidden;">
                                                    <span class="input-group-text" style="background: linear-gradient(45deg, var(--dark-color), var(--light-color)); border: none; border-top-left-radius: 25px; border-bottom-left-radius: 25px;">
                                                        <i class="fas fa-search" style="color: var(--primary-color); text-shadow: 0 0 5px rgba(255, 0, 85, 0.7);"></i>
                                                    </span>
                                                    <input type="text" name="search" id="searchInput" class="form-control" 
                                                        style="background-color: rgba(30, 30, 58, 0.8); color: white; border: none; padding: 10px 80px 10px 15px; height: 45px;" 
                                                        placeholder="Search partners..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                                    <div class="voice-btn-container" style="position: absolute; right: 85px; top: 50%; transform: translateY(-50%); z-index: 10;">
                                                        <button type="button" id="voiceSearchBtn" class="btn voice-btn" 
                                                            style="background: linear-gradient(45deg, var(--accent-color), #00c8ff); border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 0 10px rgba(0, 240, 255, 0.5);">
                                                            <i class="fas fa-microphone" style="color: white;"></i>
                                                        </button>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px; background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); border: none;">Search</button>
                                                </div>
                                                
                                                <!-- Voice Recognition Status -->
                                                <div id="voiceStatus" style="position: absolute; top: -25px; right: 0; font-size: 12px; color: var(--accent-color); display: none; text-shadow: 0 0 5px rgba(0, 240, 255, 0.5);"></div>
                                                
                                                <!-- Voice Visualization -->
                                                <div id="voiceVisualization" style="position: absolute; bottom: -15px; left: 50px; right: 50px; height: 2px; display: none;">
                                                    <div class="visualizer-bars" style="display: flex; justify-content: space-between; height: 100%;">
                                                        <?php for($i = 0; $i < 20; $i++): ?>
                                                        <div class="visualizer-bar" style="width: 3px; height: 2px; background-color: var(--accent-color); box-shadow: 0 0 5px var(--accent-color);"></div>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addPartnerModal">
                                        <i class="fas fa-plus me-2"></i>Add Partner
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alert Container -->
                    <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Partner Filtering and Sorting Controls -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="" id="filterForm">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="mb-2 mb-md-0">
                                        <h5 class="text-accent mb-2"><i class="fas fa-filter me-2"></i>Filter & Sort</h5>
                                        <div class="d-flex flex-wrap gap-2">
                                            <select name="partnerType" id="partnerTypeFilter" class="form-select form-select-sm" style="background-color: var(--dark-color); color: white; border: 1px solid var(--accent-color); width: auto;" onchange="document.getElementById('filterForm').submit();">
                                                <option value="all" <?php echo (!isset($_GET['partnerType']) || $_GET['partnerType'] === 'all') ? 'selected' : ''; ?>>All Types</option>
                                                <option value="Sponsor" <?php echo (isset($_GET['partnerType']) && $_GET['partnerType'] === 'Sponsor') ? 'selected' : ''; ?>>Sponsors</option>
                                                <option value="Venue" <?php echo (isset($_GET['partnerType']) && $_GET['partnerType'] === 'Venue') ? 'selected' : ''; ?>>Venues</option>
                                                <option value="Media" <?php echo (isset($_GET['partnerType']) && $_GET['partnerType'] === 'Media') ? 'selected' : ''; ?>>Media</option>
                                                <option value="Technology" <?php echo (isset($_GET['partnerType']) && $_GET['partnerType'] === 'Technology') ? 'selected' : ''; ?>>Technology</option>
                                                <option value="Other" <?php echo (isset($_GET['partnerType']) && $_GET['partnerType'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                            
                                            <select name="status" id="statusFilter" class="form-select form-select-sm" style="background-color: var(--dark-color); color: white; border: 1px solid var(--neon-purple); width: auto;" onchange="document.getElementById('filterForm').submit();">
                                                <option value="all" <?php echo (!isset($_GET['status']) || $_GET['status'] === 'all') ? 'selected' : ''; ?>>All Statuses</option>
                                                <option value="Active" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                                                <option value="Pending" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Inactive" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                            
                                            <!-- Keep any search parameter when filtering -->
                                            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h5 class="text-accent mb-2"><i class="fas fa-sort me-2"></i>Sort By</h5>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm sort-btn <?php echo (!isset($_GET['sort']) || $_GET['sort'] === 'name') ? 'active' : ''; ?>" onclick="setSortAndSubmit('name')" style="background-color: var(--dark-color); color: white; border: 1px solid var(--primary-color);">
                                                <i class="fas fa-font me-1"></i>Name
                                                <i class="fas fa-sort ms-1"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm sort-btn <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'date') ? 'active' : ''; ?>" onclick="setSortAndSubmit('date')" style="background-color: var(--dark-color); color: white; border: 1px solid var(--primary-color);">
                                                <i class="fas fa-calendar-alt me-1"></i>Date
                                                <i class="fas fa-sort ms-1"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm sort-btn <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'price') ? 'active' : ''; ?>" onclick="setSortAndSubmit('price')" style="background-color: var(--dark-color); color: white; border: 1px solid var(--primary-color);">
                                                <i class="fas fa-dollar-sign me-1"></i>Value
                                                <i class="fas fa-sort ms-1"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" name="sort" id="sortInput" value="<?php echo isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : 'name'; ?>">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Partners Grid -->
                    <div class="row" id="partnersList">
                        <?php if (empty($partners)): ?>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    No partners found. <?php echo isset($_GET['search']) || isset($_GET['partnerType']) || isset($_GET['status']) ? 'Try adjusting your filters.' : ''; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($partners as $partner): ?>
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="partner-card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><?php echo htmlspecialchars($partner['name']); ?></h5>
                                            <small><?php echo htmlspecialchars($partner['company']); ?></small>
                                            <span class="partner-type <?php echo strtolower($partner['partnerType']); ?>">
                                                <?php echo htmlspecialchars($partner['partnerType']); ?>
                                            </span>
                                            <span class="partner-status <?php echo strtolower($partner['status'] ?? 'pending'); ?>">
                                                <?php echo htmlspecialchars($partner['status'] ?? 'Pending'); ?>
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <div class="partner-value">
                                                $<?php echo number_format($partner['partnershipValue'], 2); ?>
                                            </div>
                                            <div class="partner-info">
                                                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($partner['email']); ?></p>
                                                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($partner['phone']); ?></p>
                                                <?php if (!empty($partner['contractStart']) && !empty($partner['contractEnd'])): ?>
                                                <p><i class="fas fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($partner['contractStart'])); ?> - <?php echo date('M d, Y', strtotime($partner['contractEnd'])); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="partner-actions">
                                                <button type="button" class="btn btn-sm btn-outline-info view-partner-btn" data-partner-id="<?php echo $partner['id']; ?>">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-partner-btn" data-partner-id="<?php echo $partner['id']; ?>">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </button>
                                                <a href="manage_offers.php?id=<?php echo $partner['id']; ?>" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-gift me-1"></i>Offers
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-success show-card-btn" data-partner-id="<?php echo $partner['id']; ?>">
                                                    <i class="fas fa-id-card me-1"></i>Card
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?php echo $partner['id']; ?>)">
                                                    <i class="fas fa-trash-alt me-1"></i>Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($partnersData['totalPages'] > 1): ?>
                    <nav>
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['partnerType']) ? '&partnerType=' . htmlspecialchars($_GET['partnerType']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $partnersData['totalPages']; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['partnerType']) ? '&partnerType=' . htmlspecialchars($_GET['partnerType']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $partnersData['totalPages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['partnerType']) ? '&partnerType=' . htmlspecialchars($_GET['partnerType']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
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
                    Are you sure you want to delete this partner? This action cannot be undone.
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--primary-color);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Partner Modal -->
    <div class="modal fade" id="addPartnerModal" tabindex="-1" aria-labelledby="addPartnerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="background-color: var(--light-color); border: 1px solid var(--primary-color);">
                <div class="modal-header" style="background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); border-bottom: none;">
                    <h5 class="modal-title" id="addPartnerModalLabel"><i class="fas fa-plus-circle me-2"></i>Add New Partner</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="partnerForm">
                        <input type="hidden" name="action" value="addPartner">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label required-field">Contact Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required style="background-color: var(--dark-color); color: white; border: 1px solid var(--accent-color);">
                                </div>
                                <div class="mb-3">
                                    <label for="company" class="form-label required-field">Company/Organization</label>
                                    <input type="text" class="form-control" id="company" name="company" required style="background-color: var(--dark-color); color: white; border: 1px solid var(--accent-color);">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label required-field">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required style="background-color: var(--dark-color); color: white; border: 1px solid var(--accent-color);">
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label required-field">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required style="background-color: var(--dark-color); color: white; border: 1px solid var(--accent-color);">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="partnerType" class="form-label required-field">Partner Type</label>
                                    <select class="form-select" id="partnerType" name="partnerType" required style="background-color: var(--dark-color); color: white; border: 1px solid var(--accent-color);">
                                        <option value="">Select Partner Type</option>
                                        <option value="Sponsor">Sponsor</option>
                                        <option value="Venue">Venue</option>
                                        <option value="Media">Media Partner</option>
                                        <option value="Technology">Technology Provider</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="partnershipValue" class="form-label required-field">Partnership Value ($)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="partnershipValue" name="partnershipValue" required style="background-color: var(--dark-color); color: white; border: 1px solid var(--accent-color);">
                                    <div class="form-text" style="color: rgba(255, 255, 255, 0.6);">The monetary value of this partnership</div>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" style="background-color: var(--dark-color); color: white; border: 1px solid var(--accent-color);">
                                        <option value="Pending">Pending</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Additional Notes</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" style="background-color: var(--dark-color); color: white; border: 1px solid var(--accent-color);"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mb-3 mt-4 border-bottom pb-2" style="color: var(--accent-color);">Contract Details</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contractType" class="form-label">Contract Type</label>
                                    <select class="form-select" id="contractType" name="contractType" style="background-color: var(--dark-color); color: white; border: 1px solid var(--accent-color);">
                                        <option value="">Select Contract Type</option>
                                        <option value="Annual">Annual</option>
                                        <option value="Event-based">Event-based</option>
                                        <option value="Project-based">Project-based</option>
                                        <option value="Custom">Custom</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="contractStart" class="form-label">Contract Start Date</label>
                                    <input type="date" class="form-control" id="contractStart" name="contractStart" style="background-color: var(--dark-color); color: white; border: 1px solid var(--accent-color);">
                                </div>
                                <div class="mb-3">
                                    <label for="contractEnd" class="form-label">Contract End Date</label>
                                    <input type="date" class="form-control" id="contractEnd" name="contractEnd" style="background-color: var(--dark-color); color: white; border: 1px solid var(--accent-color);">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contract Template</label>
                                <input type="hidden" id="contract_template_id" name="contract_template_id" value="">
                                
                                <div class="contract-templates-container" style="max-height: 300px; overflow-y: auto;">
                                    <?php foreach ($contractTemplates as $template): ?>
                                    <div class="contract-template-card" data-id="<?php echo $template['id']; ?>" style="background-color: var(--dark-color); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 15px; margin-bottom: 10px; cursor: pointer; transition: all 0.3s ease;">
                                        <h5><?php echo htmlspecialchars($template['name']); ?></h5>
                                        <p><?php echo htmlspecialchars(substr($template['description'], 0, 100)); ?>...</p>
                                        <div class="d-flex justify-content-between">
                                            <span class="duration"><?php echo $template['duration']; ?> months</span>
                                            <span class="price">$<?php echo number_format($template['price_min'], 0); ?> - $<?php echo number_format($template['price_max'], 0); ?></span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Partner
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/partner-validation.js"></script>
    <script src="../js/scripts.js"></script>
    <script>
        // Function to set sort parameter and submit form
        function setSortAndSubmit(sortValue) {
            document.getElementById('sortInput').value = sortValue;
            document.getElementById('filterForm').submit();
        }
        
        // Function to show delete confirmation modal
        function confirmDelete(partnerId) {
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            confirmBtn.href = 'delete_partner.php?id=' + partnerId;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
        
        // Highlight active sort button
        document.addEventListener('DOMContentLoaded', function() {
            const sortBtns = document.querySelectorAll('.sort-btn');
            const currentSort = '<?php echo isset($_GET['sort']) ? $_GET['sort'] : 'name'; ?>';
            
            sortBtns.forEach(btn => {
                if (btn.getAttribute('data-sort') === currentSort) {
                    btn.classList.add('active');
                    btn.style.backgroundColor = 'var(--primary-color)';
                }
            });
            
            // Contract template selection
            const templateCards = document.querySelectorAll('.contract-template-card');
            const templateIdInput = document.getElementById('contract_template_id');
            
            templateCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove selected class from all cards
                    templateCards.forEach(c => c.classList.remove('selected'));
                    
                    // Add selected class to clicked card
                    this.classList.add('selected');
                    this.style.borderColor = 'var(--primary-color)';
                    this.style.boxShadow = '0 0 15px rgba(255, 0, 85, 0.3)';
                    
                    // Set template ID in hidden input
                    templateIdInput.value = this.getAttribute('data-id');
                });
            });
            
            // Show success message and auto-dismiss after 2 seconds
            <?php if (!empty($success)): ?>
            const successAlert = '<div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($success); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            document.querySelector('.container-fluid').insertAdjacentHTML('afterbegin', successAlert);
            
            // Auto-dismiss after 2 seconds
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
            <?php endif; ?>
            
            // Show error message and auto-dismiss after 2 seconds
            <?php if (!empty($error)): ?>
            const errorAlert = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($error); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            document.querySelector('.container-fluid').insertAdjacentHTML('afterbegin', errorAlert);
            
            // Auto-dismiss after 2 seconds
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
            <?php endif; ?>
            
            // Voice Search Functionality with Enhanced Feedback
            const voiceSearchBtn = document.getElementById('voiceSearchBtn');
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            const voiceStatus = document.getElementById('voiceStatus');
            const voiceVisualization = document.getElementById('voiceVisualization');
            const visualizerBars = document.querySelectorAll('.visualizer-bar');
            
            // Add styles for animations
            const styleElement = document.createElement('style');
            styleElement.textContent = `
                @keyframes pulse {
                    0% { transform: scale(1); opacity: 1; }
                    50% { transform: scale(1.1); opacity: 0.9; }
                    100% { transform: scale(1); opacity: 1; }
                }
                
                @keyframes listening {
                    0% { box-shadow: 0 0 5px 2px rgba(0, 240, 255, 0.5); }
                    50% { box-shadow: 0 0 15px 5px rgba(0, 240, 255, 0.7); }
                    100% { box-shadow: 0 0 5px 2px rgba(0, 240, 255, 0.5); }
                }
                
                @keyframes visualizerBar {
                    0% { height: 2px; }
                    50% { height: 15px; }
                    100% { height: 2px; }
                }
                
                .voice-btn.listening {
                    animation: listening 1.5s infinite;
                    background: linear-gradient(45deg, #00c8ff, var(--accent-color)) !important;
                }
                
                .visualizer-bar.active {
                    animation: visualizerBar 0.5s infinite;
                    animation-delay: calc(var(--bar-index) * 0.05s);
                }
            `;
            document.head.appendChild(styleElement);
            
            // Set index for each visualizer bar for staggered animation
            visualizerBars.forEach((bar, index) => {
                bar.style.setProperty('--bar-index', index);
            });
            
            if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                const recognition = new SpeechRecognition();
                
                // Enable interim results for real-time feedback
                recognition.continuous = false;
                recognition.interimResults = true;
                recognition.lang = 'en-US';
                
                // Start recognition
                recognition.onstart = function() {
                    // Update button appearance
                    voiceSearchBtn.innerHTML = '<i class="fas fa-microphone-alt" style="color: white;"></i>';
                    voiceSearchBtn.classList.add('listening');
                    
                    // Show status message
                    voiceStatus.textContent = 'Listening...';
                    voiceStatus.style.display = 'block';
                    
                    // Show and animate visualizer
                    voiceVisualization.style.display = 'block';
                    visualizerBars.forEach(bar => bar.classList.add('active'));
                    
                    // Clear previous input
                    searchInput.placeholder = 'Speak now...';
                    
                    // Add a subtle glow to the search input
                    searchInput.style.boxShadow = '0 0 10px rgba(0, 240, 255, 0.3) inset';
                };
                
                // Process interim results
                recognition.onresult = function(event) {
                    const transcript = event.results[0][0].transcript;
                    const confidence = event.results[0][0].confidence;
                    
                    // Update input field in real-time
                    searchInput.value = transcript;
                    
                    // Update status with confidence level
                    const confidencePercent = Math.round(confidence * 100);
                    voiceStatus.textContent = `Heard: "${transcript}" (${confidencePercent}% confident)`;
                    
                    // If this is a final result
                    if (event.results[0].isFinal) {
                        // Change status to indicate processing
                        voiceStatus.textContent = 'Processing "' + transcript + '"...';
                        voiceStatus.style.color = 'var(--primary-color)';
                        
                        // Auto-submit with a visual delay
                        setTimeout(() => {
                            searchForm.submit();
                        }, 800);
                    }
                    
                    // Adjust visualizer based on speech volume/confidence
                    const maxHeight = 20 + (confidence * 10); // Max height based on confidence
                    visualizerBars.forEach(bar => {
                        // Random height for each bar to simulate audio levels
                        const randomHeight = Math.random() * maxHeight;
                        bar.style.height = `${randomHeight}px`;
                    });
                };
                
                // Recognition ended
                recognition.onend = function() {
                    // Reset button appearance
                    voiceSearchBtn.innerHTML = '<i class="fas fa-microphone" style="color: white;"></i>';
                    voiceSearchBtn.classList.remove('listening');
                    
                    // Hide visualizer with a delay
                    setTimeout(() => {
                        voiceVisualization.style.display = 'none';
                        visualizerBars.forEach(bar => {
                            bar.classList.remove('active');
                            bar.style.height = '2px';
                        });
                    }, 500);
                    
                    // Reset input style
                    searchInput.placeholder = 'Search partners...';
                    searchInput.style.boxShadow = 'none';
                    
                    // Hide status after a delay if no results were processed
                    if (!searchInput.value) {
                        setTimeout(() => {
                            voiceStatus.style.display = 'none';
                        }, 2000);
                    }
                };
                
                // Handle errors
                recognition.onerror = function(event) {
                    console.error('Speech recognition error', event.error);
                    
                    // Update button to show error
                    voiceSearchBtn.innerHTML = '<i class="fas fa-microphone-slash" style="color: white;"></i>';
                    voiceSearchBtn.style.background = 'linear-gradient(45deg, #ff6b6b, #ff4757)';
                    voiceSearchBtn.classList.remove('listening');
                    
                    // Show error message
                    voiceStatus.textContent = 'Error: ' + (event.error === 'no-speech' ? 'No speech detected' : event.error);
                    voiceStatus.style.color = '#ff6b6b';
                    
                    // Reset after delay
                    setTimeout(() => {
                        voiceSearchBtn.innerHTML = '<i class="fas fa-microphone" style="color: white;"></i>';
                        voiceSearchBtn.style.background = 'linear-gradient(45deg, var(--accent-color), #00c8ff)';
                        voiceStatus.style.display = 'none';
                    }, 3000);
                    
                    // Hide visualizer
                    voiceVisualization.style.display = 'none';
                    visualizerBars.forEach(bar => {
                        bar.classList.remove('active');
                        bar.style.height = '2px';
                    });
                    
                    // Reset input style
                    searchInput.placeholder = 'Search partners...';
                    searchInput.style.boxShadow = 'none';
                };
                
                // Start voice recognition on button click
                voiceSearchBtn.addEventListener('click', function() {
                    if (voiceSearchBtn.classList.contains('listening')) {
                        // If already listening, stop recognition
                        recognition.stop();
                    } else {
                        // Start new recognition session
                        recognition.start();
                    }
                });
                
                // Add tooltip to voice button
                voiceSearchBtn.setAttribute('title', 'Search with your voice');
                
            } else {
                // Speech recognition not supported
                voiceSearchBtn.style.display = 'none';
                console.log('Speech recognition not supported in this browser');
            }
        });
    </script>
    <!-- View Partner Modal -->
    <div class="modal fade" id="viewPartnerModal" tabindex="-1" aria-labelledby="viewPartnerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--dark-color); border: 2px solid var(--accent-color);">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="modal-title" id="viewPartnerModalLabel"><i class="fas fa-user me-2"></i>Partner Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="partnerDetails" class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border text-info" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <p class="mt-2">Loading partner details...</p>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.1);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Partner Modal -->
    <div class="modal fade" id="editPartnerModal" tabindex="-1" aria-labelledby="editPartnerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="background-color: var(--dark-color); border: 2px solid var(--primary-color);">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="modal-title" id="editPartnerModalLabel"><i class="fas fa-edit me-2"></i>Edit Partner</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editPartnerForm" class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <p class="mt-2">Loading partner form...</p>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.1);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="savePartnerChanges">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Partner Card Modal -->
    <div class="modal fade" id="partnerCardModal" tabindex="-1" aria-labelledby="partnerCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--dark-color); border: 2px solid var(--primary-color);">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="modal-title" id="partnerCardModalLabel"><i class="fas fa-id-card me-2"></i>Partner Card</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cardContent" class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <p class="mt-2">Loading partner card...</p>
                    </div>
                    
                    <!-- Loyalty/Reward System Section -->
                    <div id="loyaltySection" class="mt-4" style="display: none;">
                        <div class="loyalty-info" style="background-color: rgba(30, 30, 58, 0.5); border: 1px solid #f5a425; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                            <h4 style="color: #f5a425; margin-bottom: 10px;"><i class="fas fa-gift"></i> Partner Loyalty/Reward System</h4>
                            <p>This partner card includes a QR code that links to a special offer page. When customers scan the QR code, they can access exclusive discounts or rewards from this partner.</p>
                            <p>The system tracks interactions and redemptions, helping you measure engagement and loyalty.</p>
                            <div class="text-center mt-3">
                                <a href="#" id="manageOfferBtn" class="btn btn-warning">
                                    <i class="fas fa-cog"></i> Manage Partner Offer
                                </a>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="stat-card" style="background-color: rgba(30, 30, 58, 0.5); border-left: 4px solid var(--primary-color); padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                                    <h3 id="totalScans">0</h3>
                                    <p>Total Scans</p>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="stat-card" style="background-color: rgba(30, 30, 58, 0.5); border-left: 4px solid var(--neon-green); padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                                    <h3 id="totalRedemptions">0</h3>
                                    <p>Redemptions</p>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="stat-card" style="background-color: rgba(30, 30, 58, 0.5); border-left: 4px solid var(--accent-color); padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                                    <h3 id="conversionRate">0%</h3>
                                    <p>Conversion Rate</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.1);">
                    <div class="d-flex justify-content-between w-100">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <div>
                            <a href="#" id="downloadCardBtn" class="btn btn-primary me-2">
                                <i class="fas fa-download"></i> Download Card
                            </a>
                            <a href="#" id="printCardBtn" class="btn btn-success">
                                <i class="fas fa-print"></i> Print Card
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Partner Modals Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all action buttons
            const cardButtons = document.querySelectorAll('.show-card-btn');
            const viewButtons = document.querySelectorAll('.view-partner-btn');
            const editButtons = document.querySelectorAll('.edit-partner-btn');
            
            // Add click events to all buttons
            cardButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const partnerId = this.getAttribute('data-partner-id');
                    openPartnerCardModal(partnerId);
                });
            });
            
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const partnerId = this.getAttribute('data-partner-id');
                    openViewPartnerModal(partnerId);
                });
            });
            
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const partnerId = this.getAttribute('data-partner-id');
                    openEditPartnerModal(partnerId);
                });
            });
            
            // Function to open card modal and load content
            function openPartnerCardModal(partnerId) {
                // Show modal
                const cardModal = new bootstrap.Modal(document.getElementById('partnerCardModal'));
                cardModal.show();
                
                // Reset content
                document.getElementById('cardContent').innerHTML = `
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <p class="mt-2">Loading partner card...</p>
                `;
                document.getElementById('loyaltySection').style.display = 'none';
                
                // Fetch card content via AJAX with original styling
                fetch(`get_card_content_styled.php?id=${partnerId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update card content
                            document.getElementById('cardContent').innerHTML = data.cardContent;
                            
                            // Update loyalty section
                            document.getElementById('totalScans').textContent = data.stats.total_scans;
                            document.getElementById('totalRedemptions').textContent = data.stats.total_redemptions;
                            document.getElementById('conversionRate').textContent = data.stats.conversion_rate + '%';
                            document.getElementById('loyaltySection').style.display = 'block';
                            
                            // Update buttons
                            document.getElementById('manageOfferBtn').href = `manage_offer.php?id=${data.partnerId}`;
                            document.getElementById('downloadCardBtn').href = `generate_card.php?id=${data.partnerId}&download=pdf`;
                            document.getElementById('printCardBtn').href = `generate_card.php?id=${data.partnerId}&print=true`;
                        } else {
                            document.getElementById('cardContent').innerHTML = `
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    ${data.error || 'An error occurred while loading the partner card.'}
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading partner card:', error);
                        document.getElementById('cardContent').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                An error occurred while loading the partner card. Please try again.
                            </div>
                        `;
                    });
            }
            
            // Function to open view partner modal
            function openViewPartnerModal(partnerId) {
                // Show modal
                const viewModal = new bootstrap.Modal(document.getElementById('viewPartnerModal'));
                viewModal.show();
                
                // Reset content
                document.getElementById('partnerDetails').innerHTML = `
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <p class="mt-2">Loading partner details...</p>
                `;
                
                // Fetch partner details via AJAX
                fetch(`get_partner_details.php?id=${partnerId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const partner = data.partner;
                            let partnerTypeClass = '';
                            
                            // Set class based on partner type
                            switch(partner.partnerType.toLowerCase()) {
                                case 'venue':
                                    partnerTypeClass = 'bg-primary';
                                    break;
                                case 'sponsor':
                                    partnerTypeClass = 'bg-success';
                                    break;
                                case 'media':
                                    partnerTypeClass = 'bg-info';
                                    break;
                                case 'artist':
                                    partnerTypeClass = 'bg-warning';
                                    break;
                                default:
                                    partnerTypeClass = 'bg-secondary';
                            }
                            
                            // Format contract dates
                            const contractStart = partner.contractStart ? new Date(partner.contractStart).toLocaleDateString() : 'N/A';
                            const contractEnd = partner.contractEnd ? new Date(partner.contractEnd).toLocaleDateString() : 'N/A';
                            
                            // Check if contract template is available
                            const contractTemplate = data.contractTemplate;
                            
                            // Build partner details HTML
                            const detailsHTML = `
                                <div class="row">
                                    <div class="col-md-4 mb-4">
                                        <div class="text-center mb-3">
                                            ${partner.logo ? 
                                                `<img src="${partner.logo}" alt="${partner.name} Logo" class="img-fluid rounded mb-3" style="max-height: 150px;">` : 
                                                `<div class="display-1 text-center mb-3" style="color: var(--primary-color);">${partner.name.charAt(0)}</div>`
                                            }
                                            <h3>${partner.name}</h3>
                                            <p class="text-muted">${partner.company}</p>
                                            <span class="badge ${partnerTypeClass} mb-2">${partner.partnerType}</span>
                                            <span class="badge ${partner.status === 'Active' ? 'bg-success' : 'bg-warning'} mb-2">${partner.status}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-8">
                                        <div class="card" style="background-color: rgba(30, 30, 58, 0.5); border: 1px solid var(--primary-color);">
                                            <div class="card-header" style="background-color: rgba(30, 30, 58, 0.8);">
                                                <h5 class="mb-0">Contact Information</h5>
                                            </div>
                                            <div class="card-body">
                                                <p><i class="fas fa-envelope me-2"></i> ${partner.email}</p>
                                                <p><i class="fas fa-phone me-2"></i> ${partner.phone}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="card mt-3" style="background-color: rgba(30, 30, 58, 0.5); border: 1px solid var(--primary-color);">
                                            <div class="card-header" style="background-color: rgba(30, 30, 58, 0.8);">
                                                <h5 class="mb-0">Partnership Details</h5>
                                            </div>
                                            <div class="card-body">
                                                <p><i class="fas fa-dollar-sign me-2"></i> <strong>Value:</strong> $${parseFloat(partner.partnershipValue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
                                                <p><i class="fas fa-file-contract me-2"></i> <strong>Contract Type:</strong> ${partner.contractType || 'Not specified'}</p>
                                                <p><i class="fas fa-calendar-alt me-2"></i> <strong>Contract Period:</strong> ${contractStart} - ${contractEnd}</p>
                                                ${partner.contract_template_id ? `<p><i class="fas fa-file-signature me-2"></i> <strong>Contract Template:</strong> ${contractTemplate ? contractTemplate.name : 'Template #' + partner.contract_template_id}</p>` : ''}
                                            </div>
                                        </div>
                                        
                                        ${contractTemplate ? `
                                        <div class="card mt-3" style="background-color: rgba(30, 30, 58, 0.5); border: 1px solid var(--primary-color);">
                                            <div class="card-header" style="background-color: rgba(30, 30, 58, 0.8);">
                                                <h5 class="mb-0">Contract Details</h5>
                                            </div>
                                            <div class="card-body">
                                                <h6 class="text-primary">${contractTemplate.name}</h6>
                                                <p>${contractTemplate.description}</p>
                                                
                                                <h6 class="mt-3">Benefits:</h6>
                                                <pre style="background-color: rgba(0,0,0,0.2); padding: 10px; border-radius: 5px; white-space: pre-wrap;">${contractTemplate.benefits}</pre>
                                                
                                                <h6 class="mt-3">Terms:</h6>
                                                <pre style="background-color: rgba(0,0,0,0.2); padding: 10px; border-radius: 5px; white-space: pre-wrap;">${contractTemplate.terms}</pre>
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-4">
                                                        <div class="card bg-dark">
                                                            <div class="card-body text-center">
                                                                <h3>${contractTemplate.duration}</h3>
                                                                <p class="mb-0">Months Duration</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="card bg-dark">
                                                            <div class="card-body text-center">
                                                                <h3>$${parseFloat(contractTemplate.price_min).toLocaleString()} - $${parseFloat(contractTemplate.price_max).toLocaleString()}</h3>
                                                                <p class="mb-0">Price Range</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>` : ''}
                                        
                                        ${partner.message ? `
                                        <div class="card mt-3" style="background-color: rgba(30, 30, 58, 0.5); border: 1px solid var(--primary-color);">
                                            <div class="card-header" style="background-color: rgba(30, 30, 58, 0.8);">
                                                <h5 class="mb-0">Message</h5>
                                            </div>
                                            <div class="card-body">
                                                <p>${partner.message.replace(/\n/g, '<br>')}</p>
                                            </div>
                                        </div>` : ''}
                                    </div>
                                </div>
                            `;
                            
                            document.getElementById('partnerDetails').innerHTML = detailsHTML;
                        } else {
                            document.getElementById('partnerDetails').innerHTML = `
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    ${data.error || 'An error occurred while loading the partner details.'}
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading partner details:', error);
                        document.getElementById('partnerDetails').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                An error occurred while loading the partner details. Please try again.
                            </div>
                        `;
                    });
            }
            
            // Function to open edit partner modal
            function openEditPartnerModal(partnerId) {
                // Show modal
                const editModal = new bootstrap.Modal(document.getElementById('editPartnerModal'));
                editModal.show();
                
                // Reset content
                document.getElementById('editPartnerForm').innerHTML = `
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <p class="mt-2">Loading partner form...</p>
                `;
                
                // Fetch partner details for editing
                fetch(`get_partner_details.php?id=${partnerId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const partner = data.partner;
                            
                            // Build edit form HTML
                            const formHTML = `
                                <form id="partnerEditForm" class="text-start">
                                    <input type="hidden" name="partnerId" value="${partner.id}">
                                    <input type="hidden" name="action" value="updatePartner">
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="${partner.name}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="company" class="form-label">Company <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="company" name="company" value="${partner.company}" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" value="${partner.email}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" id="phone" name="phone" value="${partner.phone}" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="partnerType" class="form-label">Partner Type <span class="text-danger">*</span></label>
                                            <select class="form-select" id="partnerType" name="partnerType" required>
                                                <option value="Venue" ${partner.partnerType === 'Venue' ? 'selected' : ''}>Venue</option>
                                                <option value="Sponsor" ${partner.partnerType === 'Sponsor' ? 'selected' : ''}>Sponsor</option>
                                                <option value="Media" ${partner.partnerType === 'Media' ? 'selected' : ''}>Media</option>
                                                <option value="Artist" ${partner.partnerType === 'Artist' ? 'selected' : ''}>Artist</option>
                                                <option value="Technology" ${partner.partnerType === 'Technology' ? 'selected' : ''}>Technology</option>
                                                <option value="Other" ${partner.partnerType === 'Other' ? 'selected' : ''}>Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="Active" ${partner.status === 'Active' ? 'selected' : ''}>Active</option>
                                                <option value="Inactive" ${partner.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                                                <option value="Pending" ${partner.status === 'Pending' ? 'selected' : ''}>Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="partnershipValue" class="form-label">Partnership Value ($)</label>
                                            <input type="number" class="form-control" id="partnershipValue" name="partnershipValue" value="${partner.partnershipValue}" min="0" step="0.01">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="contract_template_id" class="form-label">Contract Template</label>
                                            <select class="form-select" id="contract_template_id" name="contract_template_id">
                                                <option value="">Select a template</option>
                                                <?php foreach ($contractTemplates as $template): ?>
                                                <option value="<?php echo $template['id']; ?>" data-template="<?php echo htmlspecialchars(json_encode($template)); ?>"><?php echo htmlspecialchars($template['name']); ?> ($<?php echo number_format($template['price_min']); ?> - $<?php echo number_format($template['price_max']); ?>)</option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="form-text text-light">Selecting a template will auto-fill contract details</div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="contractStart" class="form-label">Contract Start Date</label>
                                            <input type="date" class="form-control" id="contractStart" name="contractStart" value="${partner.contractStart || ''}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="contractEnd" class="form-label">Contract End Date</label>
                                            <input type="date" class="form-control" id="contractEnd" name="contractEnd" value="${partner.contractEnd || ''}">
                                        </div>
                                    </div>
                                    
                                    <!-- Contract End Date moved to previous row -->
                                    
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" name="message" rows="4">${partner.message || ''}</textarea>
                                    </div>
                                </form>
                            `;
                            
                            document.getElementById('editPartnerForm').innerHTML = formHTML;
                            
                            // Set up contract template selection
                            const templateSelect = document.getElementById('contract_template_id');
                            if (templateSelect) {
                                // Set the selected template if it exists
                                if (partner.contract_template_id) {
                                    templateSelect.value = partner.contract_template_id;
                                }
                                
                                // Add change event listener
                                templateSelect.addEventListener('change', function() {
                                    const selectedOption = this.options[this.selectedIndex];
                                    if (selectedOption.value && selectedOption.dataset.template) {
                                        try {
                                            const template = JSON.parse(selectedOption.dataset.template);
                                            
                                            // Auto-fill contract details
                                            document.getElementById('contractType').value = template.name;
                                            
                                            // Set partnership value if it's not already set or is 0
                                            const partnershipValueField = document.getElementById('partnershipValue');
                                            if (!partnershipValueField.value || parseFloat(partnershipValueField.value) === 0) {
                                                partnershipValueField.value = template.price_min;
                                            }
                                            
                                            // Set contract dates if they're not already set
                                            const contractStartField = document.getElementById('contractStart');
                                            const contractEndField = document.getElementById('contractEnd');
                                            
                                            if (!contractStartField.value) {
                                                // Set start date to today
                                                const today = new Date();
                                                contractStartField.value = today.toISOString().split('T')[0];
                                                
                                                // Set end date to today + duration months
                                                if (!contractEndField.value && template.duration) {
                                                    const endDate = new Date();
                                                    endDate.setMonth(endDate.getMonth() + template.duration);
                                                    contractEndField.value = endDate.toISOString().split('T')[0];
                                                }
                                            }
                                        } catch (e) {
                                            console.error('Error parsing template data:', e);
                                        }
                                    }
                                });
                            }
                            
                            // Set up save button event
                            document.getElementById('savePartnerChanges').addEventListener('click', function() {
                                savePartnerChanges(partnerId);
                            });
                        } else {
                            document.getElementById('editPartnerForm').innerHTML = `
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    ${data.error || 'An error occurred while loading the partner form.'}
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading partner form:', error);
                        document.getElementById('editPartnerForm').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                An error occurred while loading the partner form. Please try again.
                            </div>
                        `;
                    });
            }
            
            // Function to save partner changes
            function savePartnerChanges(partnerId) {
                const form = document.getElementById('partnerEditForm');
                
                // Validate form
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                
                // Show loading state
                const saveButton = document.getElementById('savePartnerChanges');
                const originalText = saveButton.innerHTML;
                saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
                saveButton.disabled = true;
                
                // Get form data
                const formData = new FormData(form);
                
                // Debug: Log form data
                console.log('Form data being submitted:');
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
                
                // Send data via AJAX
                fetch('update_partner.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Server response:', data);
                    
                    if (data.success) {
                        // Show success message
                        alert('Partner updated successfully!');
                        
                        // Close modal and refresh page
                        bootstrap.Modal.getInstance(document.getElementById('editPartnerModal')).hide();
                        window.location.reload();
                    } else {
                        // Show detailed error message
                        const errorMsg = data.error || 'An error occurred while saving changes.';
                        console.error('Update failed:', errorMsg);
                        alert('Error: ' + errorMsg);
                        
                        // Reset button
                        saveButton.innerHTML = originalText;
                        saveButton.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error saving partner changes:', error);
                    alert('An error occurred while saving changes: ' + error.message);
                    
                    // Reset button
                    saveButton.innerHTML = originalText;
                    saveButton.disabled = false;
                });
            }
        });
    </script>
</body>
</html>
