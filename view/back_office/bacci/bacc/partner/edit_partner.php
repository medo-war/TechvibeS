<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Model/partner.php';

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

// Get contract templates for the form
$contractTemplates = $partnerController->getContractTemplates();

// Handle form submission
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $name = trim($_POST['name'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $partnerType = $_POST['partnerType'] ?? '';
    $partnershipValue = floatval($_POST['partnershipValue'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    $contractType = $_POST['contractType'] ?? '';
    $contractStart = $_POST['contractStart'] ?? '';
    $contractEnd = $_POST['contractEnd'] ?? '';
    $contract_template_id = intval($_POST['contract_template_id'] ?? 0);
    $status = $_POST['status'] ?? 'Pending';
    
    // Validate required fields
    if (empty($name) || empty($company) || empty($email) || empty($phone) || empty($partnerType)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($partnershipValue <= 0) {
        $error = 'Partnership value must be greater than zero.';
    } else {
        // Create partner object
        $partnerObj = new Partner(
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
        
        // Set partner ID for update
        $partnerObj->setId($partnerId);
        
        // Update partner in database
        if ($partnerController->updatePartner($partnerObj)) {
            // Update status if provided
            if (!empty($status)) {
                $partnerController->updatePartnerStatus($partnerId, $status);
            }
            
            $success = 'Partner updated successfully!';
            
            // Refresh partner data
            $partner = $partnerController->getPartnerById($partnerId);
        } else {
            $error = 'Failed to update partner. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="LiveTheMusic - Edit Partner" />
    <meta name="author" content="" />
    <title>Edit Partner - LiveTheMusic</title>
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
        
        /* Form styles */
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            box-shadow: 0 0 10px rgba(255, 0, 85, 0.3);
            color: white;
        }
        
        .form-label {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }
        
        .form-text {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .required-field::after {
            content: '*';
            color: var(--primary-color);
            margin-left: 4px;
        }
        
        .form-card {
            background-color: var(--light-color);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .form-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: rgba(255, 0, 85, 0.3);
        }
        
        .form-card .card-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
            border-bottom: none;
            border-radius: 10px 10px 0 0;
            position: relative;
            overflow: hidden;
        }
        
        .form-card .card-header::after {
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
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            box-shadow: 0 4px 15px rgba(255, 0, 85, 0.3);
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 0, 85, 0.5);
        }
        
        .btn-outline-secondary {
            color: white;
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .btn-outline-secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        /* Contract template selection */
        .contract-template-card {
            background-color: rgba(30, 30, 58, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .contract-template-card:hover {
            background-color: rgba(30, 30, 58, 0.8);
            border-color: var(--accent-color);
            transform: translateY(-2px);
        }
        
        .contract-template-card.selected {
            background-color: rgba(0, 240, 255, 0.1);
            border-color: var(--accent-color);
            box-shadow: 0 0 15px rgba(0, 240, 255, 0.3);
        }
        
        .contract-template-card h5 {
            color: white;
            margin-bottom: 5px;
        }
        
        .contract-template-card p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 10px;
            font-size: 0.9rem;
        }
        
        .contract-template-card .price {
            color: var(--neon-green);
            font-weight: 600;
        }
        
        .contract-template-card .duration {
            color: var(--accent-color);
            font-weight: 600;
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
                    <h1 class="mt-4"><i class="fas fa-edit me-2"></i>Edit Partner</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="../index1.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Partners</a></li>
                        <li class="breadcrumb-item active">Edit Partner</li>
                    </ol>
                    
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card form-card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fas fa-handshake me-2"></i>Edit Partner Information</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" id="partnerForm">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label required-field">Contact Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($partner['name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="company" class="form-label required-field">Company/Organization</label>
                                            <input type="text" class="form-control" id="company" name="company" value="<?php echo htmlspecialchars($partner['company']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label required-field">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($partner['email']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="phone" class="form-label required-field">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($partner['phone']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="partnerType" class="form-label required-field">Partner Type</label>
                                            <select class="form-select" id="partnerType" name="partnerType" required>
                                                <option value="">Select Partner Type</option>
                                                <option value="Sponsor" <?php echo $partner['partnerType'] === 'Sponsor' ? 'selected' : ''; ?>>Sponsor</option>
                                                <option value="Venue" <?php echo $partner['partnerType'] === 'Venue' ? 'selected' : ''; ?>>Venue</option>
                                                <option value="Media" <?php echo $partner['partnerType'] === 'Media' ? 'selected' : ''; ?>>Media Partner</option>
                                                <option value="Technology" <?php echo $partner['partnerType'] === 'Technology' ? 'selected' : ''; ?>>Technology Provider</option>
                                                <option value="Other" <?php echo $partner['partnerType'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="partnershipValue" class="form-label required-field">Partnership Value ($)</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="partnershipValue" name="partnershipValue" value="<?php echo htmlspecialchars($partner['partnershipValue']); ?>" required>
                                            <div class="form-text">The monetary value of this partnership</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="Pending" <?php echo ($partner['status'] ?? 'Pending') === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Active" <?php echo ($partner['status'] ?? '') === 'Active' ? 'selected' : ''; ?>>Active</option>
                                                <option value="Inactive" <?php echo ($partner['status'] ?? '') === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="message" class="form-label">Additional Notes</label>
                                            <textarea class="form-control" id="message" name="message" rows="4"><?php echo htmlspecialchars($partner['message']); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <h5 class="mb-3 mt-4 border-bottom pb-2" style="color: var(--accent-color);">Contract Details</h5>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contractType" class="form-label">Contract Type</label>
                                            <select class="form-select" id="contractType" name="contractType">
                                                <option value="">Select Contract Type</option>
                                                <option value="Annual" <?php echo $partner['contractType'] === 'Annual' ? 'selected' : ''; ?>>Annual</option>
                                                <option value="Event-based" <?php echo $partner['contractType'] === 'Event-based' ? 'selected' : ''; ?>>Event-based</option>
                                                <option value="Project-based" <?php echo $partner['contractType'] === 'Project-based' ? 'selected' : ''; ?>>Project-based</option>
                                                <option value="Custom" <?php echo $partner['contractType'] === 'Custom' ? 'selected' : ''; ?>>Custom</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="contractStart" class="form-label">Contract Start Date</label>
                                            <input type="date" class="form-control" id="contractStart" name="contractStart" value="<?php echo $partner['contractStart'] ?? ''; ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="contractEnd" class="form-label">Contract End Date</label>
                                            <input type="date" class="form-control" id="contractEnd" name="contractEnd" value="<?php echo $partner['contractEnd'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contract Template</label>
                                        <input type="hidden" id="contract_template_id" name="contract_template_id" value="<?php echo $partner['contract_template_id'] ?? ''; ?>">
                                        
                                        <div class="contract-templates-container">
                                            <?php foreach ($contractTemplates as $template): ?>
                                            <div class="contract-template-card <?php echo ($partner['contract_template_id'] == $template['id']) ? 'selected' : ''; ?>" data-id="<?php echo $template['id']; ?>">
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
                                    <a href="view_partner.php?id=<?php echo $partnerId; ?>" class="btn btn-outline-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Partner
                                    </button>
                                </div>
                            </form>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Contract template selection
            const templateCards = document.querySelectorAll('.contract-template-card');
            const templateIdInput = document.getElementById('contract_template_id');
            
            templateCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove selected class from all cards
                    templateCards.forEach(c => c.classList.remove('selected'));
                    
                    // Add selected class to clicked card
                    this.classList.add('selected');
                    
                    // Set template ID in hidden input
                    templateIdInput.value = this.getAttribute('data-id');
                });
            });
            
            // Form validation
            const partnerForm = document.getElementById('partnerForm');
            partnerForm.addEventListener('submit', function(event) {
                const name = document.getElementById('name').value.trim();
                const company = document.getElementById('company').value.trim();
                const email = document.getElementById('email').value.trim();
                const phone = document.getElementById('phone').value.trim();
                const partnerType = document.getElementById('partnerType').value;
                const partnershipValue = parseFloat(document.getElementById('partnershipValue').value);
                
                let isValid = true;
                let errorMessage = '';
                
                if (!name || !company || !email || !phone || !partnerType) {
                    isValid = false;
                    errorMessage = 'Please fill in all required fields.';
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address.';
                } else if (isNaN(partnershipValue) || partnershipValue <= 0) {
                    isValid = false;
                    errorMessage = 'Partnership value must be greater than zero.';
                }
                
                if (!isValid) {
                    event.preventDefault();
                    alert(errorMessage);
                }
            });
        });
    </script>
</body>
</html>
