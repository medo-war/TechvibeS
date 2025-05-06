<?php
// Début de la mise en mémoire tampon
ob_start();

// Inclusion du contrôleur
$controllerPath = 'C:/xampp/htdocs/projetwebCRUD - ranim/Controller/concertController.php';
if (!file_exists($controllerPath)) {
    die("Erreur : Fichier contrôleur introuvable.");
}

require_once $controllerPath;

// Traitement de la recherche
$search_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_id'])) {
    $search_id = trim($_GET['search_id']);
    if (!empty($search_id)) {
        $search_result = rechercherConcertParId($search_id);
    }
}

// Traitements CRUD
if (isset($_GET['delete_id'])) {
    $success = supprimerConcert($_GET['delete_id']);
    header("Location: concerttt.php?success=".($success ? '1' : '0')."&scroll=true");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_concert'])) {
    $success = ajouterConcert(
        $_POST['id_lieux'],
        $_POST['date_concert'],
        $_POST['prix_concert'],
        $_POST['genre'],
        $_POST['place_dispo'],
        $_FILES['image_concert'] // Bien passer le fichier ici
    );
    
    if ($success) {
        header("Location: concerttt.php?success=1");
    } else {
        header("Location: concerttt.php?error=Erreur lors de l'ajout");
    }
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_concert'])) {
    error_log(print_r($_POST, true));
    error_log(print_r($_FILES, true));
    
    $success = modifierConcert(
        $_POST['id_concert'], 
        $_POST['id_lieux'], 
        $_POST['date_concert'], 
        $_POST['prix_concert'], 
        $_POST['genre'], 
        $_POST['place_dispo'],
        $_FILES['image_concert']
    );
    
    error_log("Résultat modification: " . ($success ? 'succès' : 'échec'));
    
    header("Location: concerttt.php?success=".($success ? '2' : '0')."&scroll=true");
    exit();
}

$concert = getConcert();
$lieux = getLieux();
$genres = ['Rock', 'Pop', 'Jazz', 'Classique', 'Hip-Hop', 'Electro', 'Metal', 'Rap', 'RnB', 'Reggae'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Gestion des Concerts - LiveTheMusic</title>
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
        }
        
        .form-control-dark:focus {
            background-color: rgba(255,255,255,0.2);
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(255,0,85,0.25);
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
        
        .search-highlight {
            background-color: rgba(0, 240, 255, 0.3);
            box-shadow: 0 0 10px rgba(0, 240, 255, 0.5);
        }
        
        .error-input {
            border-color: red !important;
        }
        
        .error-message {
            color: red;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark">
        <a class="navbar-brand ps-3" href="index.php">LIVE<span>THE</span>MUSIC</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <form method="GET" action="concerttt.php" class="input-group">
                <input class="form-control" type="text" name="search_id" placeholder="Rechercher par ID" 
                       value="<?= isset($_GET['search_id']) ? htmlspecialchars($_GET['search_id']) : '' ?>"
                       aria-label="Search" style="background-color: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,0,85,0.3);" />
                <button class="btn btn-primary-custom" type="submit"><i class="fas fa-search"></i></button>
                <?php if (isset($_GET['search_id'])): ?>
                    <a href="concerttt.php" class="btn btn-danger ms-2">Annuler</a>
                <?php endif; ?>
            </form>
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
                                <a class="nav-link" href="lieuxxx.php">Lieux</a>
                                <a class="nav-link active" href="concerttt.php">Concerts</a>
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
                    
                    <!-- Concert Table -->
                    <div class="card mb-4 table-container" id="scroll-target">
                        <div class="card-header" style="background-color: rgba(255,0,85,0.1); border-bottom: 1px solid rgba(255,0,85,0.3); color: var(--accent);">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Gestion des Concerts
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['search_id']) && empty($search_result)): ?>
                                <div class="alert alert-warning">
                                    Aucun concert trouvé avec l'ID <?= htmlspecialchars($_GET['search_id']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="table-responsive">
                                <table class="table table-custom text-white">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>Lieu</th>
                                            <th>Date</th>
                                            <th>Genre</th>
                                            <th>Prix (€)</th>
                                            <th>Places dispo</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Add Form -->
                                        <tr style="background-color: rgba(0,0,0,0.2);">
                                            <form method="POST" action="concerttt.php" id="add-form" enctype="multipart/form-data">
                                                <td>Nouveau</td>
                                                <td>
                                                    <input type="file" name="image_concert" class="form-control form-control-dark form-control-sm" accept="image/*">
                                                </td>
                                                <td>
                                                    <select name="id_lieux" class="form-control form-control-dark form-control-sm">
                                                        <option value="">Sélectionner un lieu</option>
                                                        <?php foreach ($lieux as $lieu): ?>
                                                            <option value="<?= $lieu['id_lieux'] ?>"><?= htmlspecialchars($lieu['nom_lieux']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td><input type="date" name="date_concert" class="form-control form-control-dark form-control-sm"></td>
                                                <td>
                                                    <select name="genre" class="form-control form-control-dark form-control-sm">
                                                        <option value="">Sélectionner un genre</option>
                                                        <?php foreach ($genres as $genre): ?>
                                                            <option value="<?= $genre ?>"><?= $genre ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td><input type="number" step="0.01" name="prix_concert" class="form-control form-control-dark form-control-sm"></td>
                                                <td><input type="number" name="place_dispo" class="form-control form-control-dark form-control-sm"></td>
                                                <td>
                                                    <button type="submit" name="ajouter_concert" class="btn btn-sm btn-primary-custom">
                                                        <i class="bi bi-plus-circle"></i> Ajouter
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                        
                                        <!-- Concerts Data -->
                                        <?php if (isset($_GET['search_id'])): ?>
                                            <?php if ($search_result): ?>
                                                <tr class="search-highlight">
                                                    <td><?= htmlspecialchars($search_result['id_concert']) ?></td>
                                                    <td>
                                                        <?php if (!empty($search_result['image']) && $search_result['image'] != 'Images/default-avatar.png'): ?>
                                                            <img src="<?= htmlspecialchars($search_result['image']) ?>" 
                                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                                                 alt="Image du concert">
                                                        <?php else: ?>
                                                            <span class="text-muted">Aucune image</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($search_result['nom_lieux']) ?></td>
                                                    <td><?= htmlspecialchars($search_result['date_concert']) ?></td>
                                                    <td><?= htmlspecialchars($search_result['genre']) ?></td>
                                                    <td><?= htmlspecialchars($search_result['prix_concert']) ?></td>
                                                    <td><?= htmlspecialchars($search_result['place_dispo']) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $search_result['id_concert'] ?>">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <a href="concerttt.php?delete_id=<?= $search_result['id_concert'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Edit Modal for search result -->
                                                <div class="modal fade" id="editModal<?= $search_result['id_concert'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content modal-content-dark">
                                                            <div class="modal-header modal-header-dark">
                                                                <h5 class="modal-title">Modifier Concert</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="POST" action="concerttt.php" enctype="multipart/form-data" class="edit-form">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id_concert" value="<?= $search_result['id_concert'] ?>">
                                                                    <!-- Champ Image -->
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Image actuelle</label>
                                                                        <?php if (!empty($search_result['image'])): ?>
                                                                            <img src="<?= htmlspecialchars($search_result['image']) ?>" 
                                                                                 class="img-thumbnail mb-2" 
                                                                                 style="max-width: 100px; display: block;">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" name="remove_image" id="removeImage<?= $search_result['id_concert'] ?>">
                                                                                <label class="form-check-label" for="removeImage<?= $search_result['id_concert'] ?>">
                                                                                    Supprimer l'image actuelle
                                                                                </label>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <p class="text-muted">Aucune image</p>
                                                                        <?php endif; ?>
                                                                        <input type="file" name="image_concert" class="form-control form-control-dark mt-2" accept="image/*">
                                                                        <small class="text-muted">Format: JPG, PNG, GIF (max 2MB)</small>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Lieu actuel</label>
                                                                        <input type="text" class="form-control" 
                                                                               value="<?= htmlspecialchars($search_result['nom_lieux'] ?? 'Non spécifié') ?>" 
                                                                               readonly>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Changer de lieu (optionnel)</label>
                                                                        <select name="id_lieux" class="form-control form-control-dark">
                                                                            <option value="">-- Conserver le lieu actuel --</option>
                                                                            <?php foreach ($lieux as $lieu): ?>
                                                                                <option value="<?= $lieu['id_lieux'] ?>"
                                                                                    <?= (isset($search_result['id_lieux']) && $lieu['id_lieux'] == $search_result['id_lieux']) ? 'selected' : '' ?>>
                                                                                    <?= htmlspecialchars($lieu['nom_lieux']) ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Date</label>
                                                                        <input type="date" name="date_concert" class="form-control form-control-dark" value="<?= htmlspecialchars($search_result['date_concert']) ?>">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Genre</label>
                                                                        <select name="genre" class="form-control form-control-dark">
                                                                            <?php foreach ($genres as $genre): ?>
                                                                                <option value="<?= $genre ?>" <?= $genre == $search_result['genre'] ? 'selected' : '' ?>>
                                                                                    <?= $genre ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Prix (€)</label>
                                                                        <input type="number" step="0.01" name="prix_concert" class="form-control form-control-dark" value="<?= htmlspecialchars($search_result['prix_concert']) ?>">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Places disponibles</label>
                                                                        <input type="number" name="place_dispo" class="form-control form-control-dark" value="<?= htmlspecialchars($search_result['place_dispo']) ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer modal-footer-dark">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                    <button type="submit" name="modifier_concert" class="btn btn-primary-custom">Enregistrer</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php foreach ($concert as $concert): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($concert['id_concert']) ?></td>
                                                    <td>
                                                        <?php if (!empty($concert['image']) && $concert['image'] != 'Images/default-avatar.png'): ?>
                                                            <img src="<?= htmlspecialchars($concert['image']) ?>" 
                                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                                                 alt="Image du concert">
                                                        <?php else: ?>
                                                            <span class="text-muted">Aucune image</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($concert['nom_lieux']) ?></td>
                                                    <td><?= htmlspecialchars($concert['date_concert']) ?></td>
                                                    <td><?= htmlspecialchars($concert['genre']) ?></td>
                                                    <td><?= htmlspecialchars($concert['prix_concert']) ?></td>
                                                    <td><?= htmlspecialchars($concert['place_dispo']) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $concert['id_concert'] ?>">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <a href="concerttt.php?delete_id=<?= $concert['id_concert'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Edit Modal -->
                                                <div class="modal fade" id="editModal<?= $concert['id_concert'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content modal-content-dark">
                                                            <div class="modal-header modal-header-dark">
                                                                <h5 class="modal-title">Modifier Concert</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="POST" action="concerttt.php" enctype="multipart/form-data" class="edit-form">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id_concert" value="<?= $concert['id_concert'] ?>">
                                                                    <!-- Champ Image -->
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Image actuelle</label>
                                                                        <?php if (!empty($concert['image'])): ?>
                                                                            <img src="<?= htmlspecialchars($concert['image']) ?>" 
                                                                                 class="img-thumbnail mb-2" 
                                                                                 style="max-width: 100px; display: block;">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" name="remove_image" id="removeImage<?= $concert['id_concert'] ?>">
                                                                                <label class="form-check-label" for="removeImage<?= $concert['id_concert'] ?>">
                                                                                    Supprimer l'image actuelle
                                                                                </label>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <p class="text-muted">Aucune image</p>
                                                                        <?php endif; ?>
                                                                        <input type="file" name="image_concert" class="form-control form-control-dark mt-2" accept="image/*">
                                                                        <small class="text-muted">Format: JPG, PNG, GIF (max 2MB)</small>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Lieu actuel</label>
                                                                        <input type="text" class="form-control" 
                                                                               value="<?= htmlspecialchars($concert['nom_lieux'] ?? 'Non spécifié') ?>" 
                                                                               readonly>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Changer de lieu (optionnel)</label>
                                                                        <select name="id_lieux" class="form-control form-control-dark">
                                                                            <option value="">-- Conserver le lieu actuel --</option>
                                                                            <?php foreach ($lieux as $lieu): ?>
                                                                                <option value="<?= $lieu['id_lieux'] ?>"
                                                                                    <?= (isset($concert['id_lieux']) && $lieu['id_lieux'] == $concert['id_lieux']) ? 'selected' : '' ?>>
                                                                                    <?= htmlspecialchars($lieu['nom_lieux']) ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Date</label>
                                                                        <input type="date" name="date_concert" class="form-control form-control-dark" value="<?= htmlspecialchars($concert['date_concert']) ?>">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Genre</label>
                                                                        <select name="genre" class="form-control form-control-dark">
                                                                            <?php foreach ($genres as $genre): ?>
                                                                                <option value="<?= $genre ?>" <?= $genre == $concert['genre'] ? 'selected' : '' ?>>
                                                                                    <?= $genre ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Prix (€)</label>
                                                                        <input type="number" step="0.01" name="prix_concert" class="form-control form-control-dark" value="<?= htmlspecialchars($concert['prix_concert']) ?>">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Places disponibles</label>
                                                                        <input type="number" name="place_dispo" class="form-control form-control-dark" value="<?= htmlspecialchars($concert['place_dispo']) ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer modal-footer-dark">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                    <button type="submit" name="modifier_concert" class="btn btn-primary-custom">Enregistrer</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
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
                        <div class="text-muted">Copyright &copy; LiveTheMusic 2023</div>
                        <div>
                            <a href="#" style="color: rgba(255,255,255,0.7);">Privacy Policy</a>
                            &middot;
                            <a href="#" style="color: rgba(255,255,255,0.7);">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script>
        // Fonction pour afficher un message d'erreur
        function showError(input, message) {
            const formControl = input.parentElement;
            const errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.textContent = message;
            
            // Supprimer les anciens messages d'erreur
            const existingError = formControl.querySelector('.error-message');
            if (existingError) {
                formControl.removeChild(existingError);
            }
            
            formControl.appendChild(errorElement);
            input.classList.add('error-input');
        }

        // Fonction pour supprimer les messages d'erreur
        function clearError(input) {
            const formControl = input.parentElement;
            const errorElement = formControl.querySelector('.error-message');
            if (errorElement) {
                formControl.removeChild(errorElement);
            }
            input.classList.remove('error-input');
        }

        // Fonction de validation pour les champs requis
        function validateRequired(input) {
            if (input.value.trim() === '') {
                showError(input, 'Ce champ est requis');
                return false;
            } else {
                clearError(input);
                return true;
            }
        }

        // Fonction de validation pour les nombres positifs
        function validatePositiveNumber(input) {
            if (input.value.trim() === '') {
                showError(input, 'Ce champ est requis');
                return false;
            } else if (parseFloat(input.value) <= 0) {
                showError(input, 'Doit être un nombre positif');
                return false;
            } else {
                clearError(input);
                return true;
            }
        }

        // Fonction de validation pour les dates futures
        function validateFutureDate(input) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const inputDate = new Date(input.value);
            
            if (input.value.trim() === '') {
                showError(input, 'Ce champ est requis');
                return false;
            } else if (inputDate < today) {
                showError(input, 'La date doit être dans le futur');
                return false;
            } else {
                clearError(input);
                return true;
            }
        }

        // Validation du formulaire d'ajout
        document.getElementById('add-form').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validation des champs requis
            const requiredFields = [
                this.querySelector('[name="id_lieux"]'),
                this.querySelector('[name="date_concert"]'),
                this.querySelector('[name="genre"]'),
                this.querySelector('[name="prix_concert"]'),
                this.querySelector('[name="place_dispo"]')
            ];
            
            requiredFields.forEach(field => {
                if (!validateRequired(field)) {
                    isValid = false;
                }
            });
            
            // Validation des nombres positifs
            const numberFields = [
                this.querySelector('[name="prix_concert"]'),
                this.querySelector('[name="place_dispo"]')
            ];
            
            numberFields.forEach(field => {
                if (!validatePositiveNumber(field)) {
                    isValid = false;
                }
            });
            
            // Validation de la date
            if (!validateFutureDate(this.querySelector('[name="date_concert"]'))) {
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                // Scroll vers le premier champ invalide
                const firstInvalid = this.querySelector('.error-input');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        // Validation des formulaires de modification
        document.querySelectorAll('.edit-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Validation des champs requis
                const requiredFields = [
                    this.querySelector('[name="date_concert"]'),
                    this.querySelector('[name="genre"]'),
                    this.querySelector('[name="prix_concert"]'),
                    this.querySelector('[name="place_dispo"]')
                ];
                
                requiredFields.forEach(field => {
                    if (!validateRequired(field)) {
                        isValid = false;
                    }
                });
                
                // Validation des nombres positifs
                const numberFields = [
                    this.querySelector('[name="prix_concert"]'),
                    this.querySelector('[name="place_dispo"]')
                ];
                
                numberFields.forEach(field => {
                    if (!validatePositiveNumber(field)) {
                        isValid = false;
                    }
                });
                
                // Validation de la date
                if (!validateFutureDate(this.querySelector('[name="date_concert"]'))) {
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });

        // Écouteurs d'événements pour effacer les erreurs lors de la saisie
        document.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', function() {
                if (this.name === 'prix_concert' || this.name === 'place_dispo') {
                    validatePositiveNumber(this);
                } else if (this.name === 'date_concert') {
                    validateFutureDate(this);
                } else if (this.value.trim() !== '') {
                    clearError(this);
                }
            });
        });

        // Scroll to table after form submission
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_GET['scroll'])): ?>
                document.getElementById('scroll-target').scrollIntoView({
                    behavior: 'smooth'
                });
            <?php endif; ?>
            
            // Highlight search result
            <?php if (isset($_GET['search_id']) && $search_result): ?>
                const target = document.querySelector('.search-highlight');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Flash animation
                    let count = 0;
                    const interval = setInterval(() => {
                        target.style.backgroundColor = count % 2 === 0 ? 
                            'rgba(0, 240, 255, 0.3)' : 'rgba(0, 240, 255, 0.6)';
                        count++;
                        if (count > 5) {
                            clearInterval(interval);
                            target.style.backgroundColor = 'rgba(0, 240, 255, 0.3)';
                        }
                    }, 300);
                }
            <?php endif; ?>
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>