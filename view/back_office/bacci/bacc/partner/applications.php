<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php';

// Initialize controller
$partnerController = new BackOfficePartnerController();

// Ensure tables exist
$partnerController->ensurePartnersTableExists();
$partnerController->ensurePartnerStatusColumn();

// Get partner applications (pending partners)
$applications = $partnerController->getPartnerApplications();

// Handle status update
$statusMessage = $statusError = '';
if (isset($_POST['action']) && $_POST['action'] === 'updateStatus') {
    $partnerId = intval($_POST['partnerId']);
    $newStatus = $_POST['status'];
    
    if ($partnerController->updatePartnerStatus($partnerId, $newStatus)) {
        $statusMessage = 'Partner status updated successfully!';
        // Refresh applications list
        $applications = $partnerController->getPartnerApplications();
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
    <meta name="description" content="LiveTheMusic - Partner Applications" />
    <meta name="author" content="" />
    <title>Partner Applications - LiveTheMusic</title>
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
                    <h1 class="mt-4"><i class="fas fa-clipboard-list me-2"></i>Partner Applications</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="../index1.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Partners</a></li>
                        <li class="breadcrumb-item active">Applications</li>
                    </ol>
                    
                    <?php if (!empty($statusMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($statusMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($statusError)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($statusError); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card mb-4" style="background-color: var(--light-color); border: 1px solid rgba(255, 255, 255, 0.1);">
                        <div class="card-header" style="background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));">
                            <h4 class="mb-0 text-white"><i class="fas fa-inbox me-2"></i>Pending Applications</h4>
                        </div>
                        <div class="card-body">
                            <?php if (empty($applications)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-4x mb-3" style="color: rgba(255, 255, 255, 0.2);"></i>
                                <h5 style="color: rgba(255, 255, 255, 0.7);">No pending applications</h5>
                                <p style="color: rgba(255, 255, 255, 0.5);">All partnership applications have been processed.</p>
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover" style="color: white;">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Company</th>
                                            <th>Type</th>
                                            <th>Value</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applications as $application): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($application['name']); ?></td>
                                            <td><?php echo htmlspecialchars($application['company']); ?></td>
                                            <td>
                                                <span class="badge partner-type <?php echo strtolower($application['partnerType']); ?>">
                                                    <?php echo htmlspecialchars($application['partnerType']); ?>
                                                </span>
                                            </td>
                                            <td>$<?php echo number_format($application['partnershipValue'], 2); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($application['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="view_partner.php?id=<?php echo $application['id']; ?>" class="btn btn-sm btn-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-success" onclick="approveApplication(<?php echo $application['id']; ?>)" title="Approve Application">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <a href="#" class="btn btn-sm btn-dark" onclick="confirmDelete(<?php echo $application['id']; ?>, '<?php echo htmlspecialchars(addslashes($application['name'])); ?>')" title="Delete Application">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
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
    
    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background-color: var(--light-color); color: white;">
                <div class="modal-header" style="border-bottom: 1px solid var(--primary-color);">
                    <h5 class="modal-title" id="statusModalLabel">Update Application Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="updateStatus">
                    <input type="hidden" name="partnerId" id="partnerIdInput">
                    <input type="hidden" name="status" id="statusInput">
                    <div class="modal-body">
                        <p id="statusConfirmMessage"></p>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--primary-color);">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" id="confirmStatusBtn">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script>
        // Function to show approve application modal
        function approveApplication(partnerId) {
            document.getElementById('partnerIdInput').value = partnerId;
            document.getElementById('statusInput').value = 'Active';
            document.getElementById('statusConfirmMessage').textContent = 'Are you sure you want to approve this partnership application?';
            
            const confirmBtn = document.getElementById('confirmStatusBtn');
            confirmBtn.className = 'btn btn-success';
            confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i>Approve';
            
            const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
            statusModal.show();
        }
        
        // Function to show reject application modal
        function rejectApplication(partnerId) {
            document.getElementById('partnerIdInput').value = partnerId;
            document.getElementById('statusInput').value = 'Inactive';
            document.getElementById('statusConfirmMessage').textContent = 'Are you sure you want to reject this partnership application?';
            
            const confirmBtn = document.getElementById('confirmStatusBtn');
            confirmBtn.className = 'btn btn-danger';
            confirmBtn.innerHTML = '<i class="fas fa-times me-2"></i>Reject';
            
            const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
            statusModal.show();
        }
        
        // Function to confirm delete application
        function confirmDelete(partnerId, partnerName) {
            if (confirm('Are you sure you want to permanently delete the application from ' + partnerName + '? This action cannot be undone.')) {
                window.location.href = 'delete_application.php?id=' + partnerId;
            }
        }
    </script>
</body>
</html>
