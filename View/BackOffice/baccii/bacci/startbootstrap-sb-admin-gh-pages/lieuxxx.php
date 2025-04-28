<?php
// Début de la mise en mémoire tampon
ob_start();

// Inclusion du contrôleur
$controllerPath = 'C:/xampp/htdocs/projetwebCRUD - ranim/Controller/lieuxController.php';
if (!file_exists($controllerPath)) {
    die("Erreur : Fichier contrôleur introuvable.");
}

require_once $controllerPath;

// Server-side validation function
function validateInput($nom_lieux, $adresse, $capacite) {
    $errors = [];
    if (empty(trim($nom_lieux))) {
        $errors['nom_lieux'] = "Le nom du lieu est requis.";
    } elseif (strlen(trim($nom_lieux)) < 3) {
        $errors['nom_lieux'] = "Le nom du lieu doit contenir au moins 3 caractères.";
    }
    if (empty(trim($adresse))) {
        $errors['adresse'] = "L'adresse est requise.";
    } elseif (strlen(trim($adresse)) < 3) {
        $errors['adresse'] = "L'adresse doit contenir au moins 3 caractères.";
    }
    if (empty($capacite) && $capacite !== '0') {
        $errors['capacite'] = "La capacité est requise.";
    } elseif (!is_numeric($capacite) || $capacite < 0) {
        $errors['capacite'] = "La capacité doit être un nombre positif.";
    }
    return $errors;
}

// Traitements CRUD
if (isset($_GET['delete_id'])) {
    $success = supprimerLieu($_GET['delete_id']);
    header("Location: lieuxxx.php?success=".($success ? '1' : '0')."&scroll=true");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter_ligne'])) {
        $nom_lieux = $_POST['nom_lieux'] ?? '';
        $adresse = $_POST['adresse'] ?? '';
        $capacite = $_POST['capacite'] ?? '';

        $errors = validateInput($nom_lieux, $adresse, $capacite);
        if (empty($errors)) {
            $success = ajouterLieu($nom_lieux, $adresse, $capacite);
            header("Location: lieuxxx.php?success=".($success ? '3' : '0')."&scroll=true");
        } else {
            $error_message = implode(' ', array_values($errors));
            header("Location: lieuxxx.php?success=0&error=".urlencode($error_message)."&scroll=true");
        }
        exit();
    } elseif (isset($_POST['modifier_lieu'])) {
        $id_lieux = $_POST['id_lieux'] ?? '';
        $nom_lieux = $_POST['nom_lieux'] ?? '';
        $adresse = $_POST['adresse'] ?? '';
        $capacite = $_POST['capacite'] ?? '';

        $errors = validateInput($nom_lieux, $adresse, $capacite);
        if (empty($errors)) {
            $success = modifierLieu($id_lieux, $nom_lieux, $adresse, $capacite);
            header("Location: lieuxxx.php?success=".($success ? '2' : '0')."&scroll=true");
        } else {
            $error_message = implode(' ', array_values($errors));
            header("Location: lieuxxx.php?success=0&error=".urlencode($error_message)."&scroll=true");
        }
        exit();
    }
}

$lieux = getLieux();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Gestion des Lieux - LiveTheMusic</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --primary: #FF0055;
            --accent: #00F0FF;
            --dark: #0F0F1B;
            --light: #1E1E3A;
            --neon-purple: #A83AFB;
            --neon-green: #00FFAA;
        }
        
        body {
            background-color: var(--dark);
            font-family: 'Poppins', sans-serif;
        }
        
        .sb-sidenav {
            background-color: var(--dark);
            border-right: 1px solid rgba(255,0,85,0.2);
        }
        
        .sb-sidenav .nav-link {
            color: rgba(255,255,255,0.7);
        }
        
        .sb-sidenav .nav-link:hover {
            color: white;
            text-shadow: 0 0 5px var(--primary);
        }
        
        .sb-sidenav .nav-link .sb-nav-link-icon {
            color: var(--accent);
        }
        
        .sb-sidenav-footer {
            background-color: rgba(255,0,85,0.1);
            color: white;
            border-top: 1px solid rgba(255,0,85,0.2);
        }
        
        .stat-card {
            border-left: 4px solid;
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
        }
        
        .stat-card.primary { 
            border-left-color: var(--primary); 
            background: linear-gradient(135deg, rgba(255,0,85,0.2), rgba(255,42,127,0.2));
        }
        
        .stat-card.accent { 
            border-left-color: var(--accent); 
            background: linear-gradient(135deg, rgba(0,240,255,0.2), rgba(0,200,255,0.2));
        }
        
        .news-flash {
            background: linear-gradient(135deg, var(--primary), var(--neon-purple));
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }
        
        .news-flash::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(to bottom right, transparent, transparent, transparent, rgba(255,255,255,0.1));
            transform: rotate(30deg);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: rotate(30deg) translate(-10%, -10%); }
            100% { transform: rotate(30deg) translate(10%, 10%); }
        }
        
        .table-container {
            background-color: var(--light);
            border-radius: 10px;
            border: 1px solid rgba(255,0,85,0.3);
        }
        
        .table-custom th {
            background-color: rgba(255,0,85,0.1);
            color: var(--accent);
            border-bottom: 2px solid var(--primary);
        }
        
        .table-custom tr:hover {
            background-color: rgba(255,0,85,0.05);
        }
        
        .btn-primary-custom {
            background-color: var(--primary);
            border: none;
            box-shadow: 0 0 10px rgba(255,0,85,0.5);
        }
        
        .btn-primary-custom:hover {
            background-color: #E0004D;
            box-shadow: 0 0 15px rgba(255,0,85,0.8);
        }
        
        .form-control-dark {
            background-color: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,0,85,0.3);
            color: white;
            transition: all 0.3s ease;
        }
        
        .form-control-dark:focus {
            background-color: rgba(255,255,255,0.2);
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(255,0,85,0.25);
        }
        
        .form-control-dark.valid {
            border-color: var(--neon-green);
        }
        
        .form-control-dark.invalid {
            border-color: red;
            animation: shake 0.3s;
        }
        
        .modal-content-dark {
            background-color: var(--light);
            color: white;
            border: 1px solid rgba(255,0,85,0.3);
        }
        
        .modal-header-dark {
            border-bottom: 1px solid rgba(255,0,85,0.3);
        }
        
        .modal-footer-dark {
            border-top: 1px solid rgba(255,0,85,0.3);
        }
        
        #scroll-target {
            scroll-margin-top: 100px;
        }
        
        .error-message {
            color: red;
            font-size: 0.8rem;
            margin-top: 2px;
            min-height: 1rem;
        }
        
        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            50% { transform: translateX(4px); }
            75% { transform: translateX(-4px); }
            100% { transform: translateX(0); }
        }
        
        .form-group {
            position: relative;
            margin-bottom: 14px;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark">
        <a class="navbar-brand ps-3" href="index.php">LIVE<span>THE</span>MUSIC</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Taper id pour rechercher" aria-label="Search" style="background-color: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,0,85,0.3);" />
                <button class="btn btn-primary-custom" type="button"><i class="fas fa-search"></i></button>
            </div>
        </div>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" style="background-color: var(--light); border: 1px solid rgba(255,0,85,0.3);">
                    <li><a class="dropdown-item" href="#" style="color: white;">Profil</a></li>
                    <li><a class="dropdown-item" href="#" style="color: white;">Paramètres</a></li>
                    <li><hr class="dropdown-divider" style="border-color: rgba(255,0,85,0.3);" /></li>
                    <li><a class="dropdown-item" href="#" style="color: white;">Déconnexion</a></li>
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
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Music</div>
                        <a class="nav-link" href="gestion_user.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Users
                        </a>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEvents" aria-expanded="false" aria-controls="collapseEvents">
                            <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                            Events
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseEvents" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link active" href="lieuxxx.php">Lieux</a>
                                <a class="nav-link" href="concerttt.php">Concerts</a>
                            </nav>
                        </div>
                        <a class="nav-link" href="artistes.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-music"></i></div>
                            Artistes
                        </a>
                        <a class="nav-link" href="tickets.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-ticket-alt"></i></div>
                            Tickets
                        </a>
                        <div class="sb-sidenav-menu-heading">Community</div>
                        <a class="nav-link" href="amis.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-friends"></i></div>
                            Friends
                        </a>
                        <a class="nav-link" href="messages.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                            Messages
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Music Lover
                </div>
            </nav>
        </div>
        
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Music Overview</li>
                    </ol>
                    
                    <!-- News Flash -->
                    <div class="news-flash mb-4 text-white p-4">
                        <h2>NEWS FLASH</h2>
                        <p>EXCLUSIVE PRE-SALE FOR OUR TOP LISTENERS STARTS TODAY!</p>
                        <div>
                            <a href="#" class="btn btn-light me-2" style="border-radius: 50px; color: var(--primary); font-weight: 600;">View Concerts</a>
                            <a href="#" class="btn btn-light" style="border-radius: 50px; color: var(--primary); font-weight: 600;">Get Tickets</a>
                        </div>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-card primary text-white p-3 mb-4">
                                <div class="card-title">UPCOMING EVENTS</div>
                                <div class="card-value fs-3 my-2">24</div>
                                <div class="small">View Details <i class="fas fa-angle-right ms-1"></i></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-card accent text-white p-3 mb-4">
                                <div class="card-title">FRIENDS ONLINE</div>
                                <div class="card-value fs-3 my-2">15</div>
                                <div class="small">View Details <i class="fas fa-angle-right ms-1"></i></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-card accent text-white p-3 mb-4">
                                <div class="card-title">NEW MESSAGES</div>
                                <div class="card-value fs-3 my-2">8</div>
                                <div class="small">View Details <i class="fas fa-angle-right ms-1"></i></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-card primary text-white p-3 mb-4">
                                <div class="card-title">ACHIEVEMENTS</div>
                                <div class="card-value fs-3 my-2">12</div>
                                <div class="small">View Details <i class="fas fa-angle-right ms-1"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lieux Table -->
                    <div class="card mb-4 table-container" id="scroll-target">
                        <div class="card-header" style="background-color: rgba(255,0,85,0.1); border-bottom: 1px solid rgba(255,0,85,0.3); color: var(--accent);">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            Gestion des Lieux
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['success'])): ?>
                                <div class="alert alert-<?= $_GET['success'] ? 'success' : 'danger' ?> alert-dismissible fade show">
                                    <?php
                                    switch($_GET['success']) {
                                        case '1': echo "Lieu supprimé avec succès"; break;
                                        case '2': echo "Lieu modifié avec succès"; break;
                                        case '3': echo "Lieu ajouté avec succès"; break;
                                        default: echo isset($_GET['error']) ? urldecode($_GET['error']) : "Erreur lors de l'opération";
                                    }
                                    ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="table-responsive">
                                <table class="table table-custom text-white">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Adresse</th>
                                            <th>Capacité</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Add Form -->
                                        <tr style="background-color: rgba(0,0,0,0.2);">
                                            <form method="POST" action="lieuxxx.php" id="add-form" novalidate>
                                                <td>Nouveau</td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" name="nom_lieux" class="form-control form-control-dark form-control-sm" id="add-nom-lieux" required>
                                                        <div class="error-message" id="add-nom-lieux-error"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" name="adresse" class="form-control form-control-dark form-control-sm" id="add-adresse" required>
                                                        <div class="error-message" id="add-adresse-error"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="number" name="capacite" class="form-control form-control-dark form-control-sm" id="add-capacite" required>
                                                        <div class="error-message" id="add-capacite-error"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button type="submit" name="ajouter_ligne" class="btn btn-sm btn-primary-custom">
                                                        <i class="bi bi-plus-circle"></i> Ajouter
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                        
                                        <!-- Lieux Data -->
                                        <?php foreach ($lieux as $lieu): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($lieu['id_lieux']) ?></td>
                                            <td><?= htmlspecialchars($lieu['nom_lieux']) ?></td>
                                            <td><?= htmlspecialchars($lieu['adresse']) ?></td>
                                            <td><?= htmlspecialchars($lieu['capacite']) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $lieu['id_lieux'] ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="lieuxxx.php?delete_id=<?= $lieu['id_lieux'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        
                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal<?= $lieu['id_lieux'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content modal-content-dark">
                                                    <div class="modal-header modal-header-dark">
                                                        <h5 class="modal-title">Modifier Lieu</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form method="POST" action="lieuxxx.php" class="edit-form" id="edit-form-<?= $lieu['id_lieux'] ?>" novalidate>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_lieux" value="<?= $lieu['id_lieux'] ?>">
                                                            <div class="mb-3 form-group">
                                                                <label class="form-label">Nom du lieu</label>
                                                                <input type="text" name="nom_lieux" class="form-control form-control-dark" id="edit-nom-lieux-<?= $lieu['id_lieux'] ?>" value="<?= htmlspecialchars($lieu['nom_lieux']) ?>" required>
                                                                <div class="error-message" id="edit-nom-lieux-error-<?= $lieu['id_lieux'] ?>"></div>
                                                            </div>
                                                            <div class="mb-3 form-group">
                                                                <label class="form-label">Adresse</label>
                                                                <input type="text" name="adresse" class="form-control form-control-dark" id="edit-adresse-<?= $lieu['id_lieux'] ?>" value="<?= htmlspecialchars($lieu['adresse']) ?>" required>
                                                                <div class="error-message" id="edit-adresse-error-<?= $lieu['id_lieux'] ?>"></div>
                                                            </div>
                                                            <div class="mb-3 form-group">
                                                                <label class="form-label">Capacité</label>
                                                                <input type="number" name="capacite" class="form-control form-control-dark" id="edit-capacite-<?= $lieu['id_lieux'] ?>" value="<?= htmlspecialchars($lieu['capacite']) ?>" required>
                                                                <div class="error-message" id="edit-capacite-error-<?= $lieu['id_lieux'] ?>"></div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer modal-footer-dark">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" name="modifier_lieu" class="btn btn-primary-custom">Enregistrer</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Genre Distribution -->
                    <div class="card mb-4" style="background-color: var(--light); border: 1px solid rgba(255,0,85,0.3);">
                        <div class="card-header" style="background-color: rgba(255,0,85,0.1); border-bottom: 1px solid rgba(255,0,85,0.3); color: var(--accent);">
                            <i class="fas fa-music me-1"></i>
                            MUSIC GENRE DISTRIBUTION
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <span class="badge rounded-pill" style="background-color: var(--primary);">Pop</span>
                                <span class="badge rounded-pill" style="background-color: var(--neon-purple);">Rock</span>
                                <span class="badge rounded-pill" style="background-color: var(--neon-green);">Hip-Hop</span>
                                <span class="badge rounded-pill" style="background-color: var(--accent);">Electronic</span>
                                <span class="badge rounded-pill" style="background-color: #FF5500;">Jazz</span>
                                <span class="badge rounded-pill" style="background-color: #FF00AA;">Classical</span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            
            <footer class="py-4 mt-auto" style="background-color: var(--dark); border-top: 1px solid rgba(255,0,85,0.3);">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright © LiveTheMusic 2023</div>
                        <div>
                            <a href="#" style="color: rgba(255,255,255,0.7);">Privacy Policy</a>
                            ·
                            <a href="#" style="color: rgba(255,255,255,0.7);">Terms & Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="model/controleSaisie.js"></script>
    <script>
        // Scroll to table after form submission
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_GET['scroll'])): ?>
                document.getElementById('scroll-target').scrollIntoView({
                    behavior: 'smooth'
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>