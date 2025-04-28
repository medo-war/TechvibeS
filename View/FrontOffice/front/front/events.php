<?php
// Activation des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclusion du contrôleur
require_once 'C:/xampp/htdocs/projetwebCRUD - ranim/Controller/concertController.php';

// Récupération du genre depuis l'URL (si spécifié)
$selectedGenre = isset($_GET['genre']) ? $_GET['genre'] : 'all';

// Liste des genres disponibles (doit correspondre à ceux dans concertController.php)
$genres = ['Rock', 'Pop', 'Jazz', 'Classique', 'Hip-Hop', 'Electro', 'Metal', 'Rap', 'RnB', 'Reggae'];

// Validation du genre (pour éviter les valeurs invalides)
if ($selectedGenre !== 'all' && !in_array($selectedGenre, $genres)) {
    $selectedGenre = 'all';
}

// Pagination : définir le nombre de concerts par page
$concertsPerPage = 6; // Afficher 6 concerts par page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $concertsPerPage;

// Récupération des concerts avec ou sans filtre
try {
    $allConcerts = getConcert(); // Récupère tous les concerts (fonction existante)

    // Si un genre est sélectionné, on filtre les concerts manuellement
    if ($selectedGenre !== 'all') {
        $filteredConcerts = [];
        foreach ($allConcerts as $concert) {
            if (strtolower($concert['genre']) === strtolower($selectedGenre)) {
                $filteredConcerts[] = $concert;
            }
        }
        $allConcerts = $filteredConcerts; // Remplace la liste par les concerts filtrés
    }

    if (!is_array($allConcerts)) {
        throw new Exception("Aucun concert trouvé ou erreur de récupération");
    }

    // Calculer le nombre total de concerts et de pages
    $totalConcerts = count($allConcerts);
    $totalPages = ceil($totalConcerts / $concertsPerPage);

    // S'assurer que la page demandée est valide
    if ($page < 1) $page = 1;
    if ($page > $totalPages) $page = $totalPages;

    // Extraire les concerts pour la page actuelle
    $concerts = array_slice($allConcerts, $offset, $concertsPerPage);

} catch (Exception $e) {
    $errorMessage = "Désolé, nous rencontrons un problème technique. Veuillez réessayer plus tard.";
    error_log("Erreur events.php: " . $e->getMessage());
    $concerts = [];
    $totalPages = 1;
}

// Récupération des lieux
$lieux = getLieux();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live The Music - Événements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Ajout de FullCalendar (requis pour calendar.php) -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <style>
        /* --------------------------------------
           Variables de couleurs et styles globaux
        -------------------------------------- */
        :root {
            --primary-color: #FF0055;      /* Rose néon principal */
            --secondary-color: #00F0FF;    /* Cyan néon secondaire */
            --dark-color: #0F0F1B;         /* Fond sombre */
            --light-gray: #1A1A1A;         /* Gris clair pour les cartes */
            --neon-purple: #A83AFB;        /* Violet néon pour accents */
            --neon-green: #00FFAA;         /* Vert néon pour accents */
        }

        body {
            background-color: var(--dark-color);
            color: white;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* --------------------------------------
           Navigation
        -------------------------------------- */
        .navbar {
            background-color: var(--dark-color) !important;
            border-bottom: 1px solid rgba(255, 0, 85, 0.3);
            padding: 15px 0;
        }

        .navbar-brand img {
            height: 40px;
            transition: transform 0.3s ease;
        }

        .navbar-brand img:hover {
            transform: scale(1.1);
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            font-size: 1.1rem;
            margin: 0 10px;
            transition: color 0.3s ease, text-shadow 0.3s ease;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary-color);
            text-shadow: 0 0 8px var(--primary-color);
        }

        /* --------------------------------------
           Main Banner
        -------------------------------------- */
        .main-banner {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('assets/images/concert-bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 140px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .main-banner::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(to bottom right, transparent, transparent, transparent, rgba(255, 0, 85, 0.2));
            transform: rotate(30deg);
            animation: shine 5s infinite;
        }

        @keyframes shine {
            0% { transform: rotate(30deg) translate(-10%, -10%); }
            100% { transform: rotate(30deg) translate(10%, 10%); }
        }

        .main-banner h6 {
            color: var(--secondary-color);
            font-size: 1.3rem;
            letter-spacing: 4px;
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .main-banner h2 {
            font-size: 3.5rem;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 40px;
            text-shadow: 0 0 15px var(--primary-color), 0 0 30px var(--primary-color);
        }

        .main-banner .buttons .border-button,
        .main-banner .buttons .main-button {
            padding: 14px 35px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0 15px;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }

        .main-banner .buttons .border-button {
            border: 2px solid white;
            color: white;
            background: transparent;
        }

        .main-banner .buttons .border-button:hover {
            background-color: white;
            color: var(--primary-color);
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
        }

        .main-banner .buttons .main-button {
            background: var(--primary-color);
            color: white;
            border: none;
            box-shadow: 0 0 15px rgba(255, 0, 85, 0.6);
        }

        .main-banner .buttons .main-button:hover {
            background: #E0004D;
            box-shadow: 0 0 25px rgba(255, 0, 85, 0.9);
        }

        /* --------------------------------------
           Section Concerts
        -------------------------------------- */
        .explore-items {
            padding: 70px 0;
        }

        .section-heading {
            margin-bottom: 60px;
        }

        .section-heading h2 {
            font-size: 2.8rem;
            font-weight: 700;
            text-align: center;
        }

        .section-heading em {
            color: var(--primary-color);
            font-style: normal;
        }

        .line-dec {
            width: 70px;
            height: 5px;
            background-color: var(--primary-color);
            margin: 20px auto;
            border-radius: 3px;
            box-shadow: 0 0 10px var(--primary-color);
        }

        /* --------------------------------------
           Filtres
        -------------------------------------- */
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-bottom: 50px;
        }

        .filter-btn {
            border-radius: 30px;
            padding: 12px 25px;
            font-weight: 500;
            font-size: 1rem;
            border: 1px solid rgba(255, 0, 85, 0.5);
            color: white;
            background-color: transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            flex: 0 0 auto;
        }

        .filter-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 0, 85, 0.2), transparent);
            transition: 0.5s;
        }

        .filter-btn:hover::after {
            left: 100%;
        }

        .filter-btn:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(255, 0, 85, 0.6);
        }

        .filter-btn.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 0 20px rgba(255, 0, 85, 0.8);
        }

        /* --------------------------------------
           Cartes de concerts
        -------------------------------------- */
        .concert-card {
            background-color: var(--light-gray);
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 30px;
            position: relative;
        }

        .concert-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(255, 0, 85, 0.3);
        }

        .concert-img {
            height: 240px;
            object-fit: cover;
            width: 100%;
            transition: transform 0.3s ease;
        }

        .concert-card:hover .concert-img {
            transform: scale(1.05);
        }

        .card-body {
            padding: 25px;
        }

        .badge-genre {
            background-color: var(--primary-color);
            font-size: 0.95rem;
            padding: 8px 15px;
            border-radius: 25px;
            display: inline-block;
            margin-bottom: 15px;
            font-weight: 500;
            box-shadow: 0 0 10px rgba(255, 0, 85, 0.5);
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .text-muted {
            color: rgba(255, 255, 255, 0.7) !important;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .text-muted i {
            margin-right: 10px;
            color: var(--secondary-color);
        }

        .d-flex span {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .d-flex .fw-bold {
            font-size: 1.2rem;
            color: var(--secondary-color);
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 30px;
            padding: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(255, 0, 85, 0.5);
        }

        .btn-primary:hover {
            background-color: #E0004D;
            box-shadow: 0 0 20px rgba(255, 0, 85, 0.8);
            transform: scale(1.02);
        }

        /* --------------------------------------
           Messages d'erreur ou d'information
        -------------------------------------- */
        .alert {
            border-radius: 10px;
            margin: 20px 0;
            font-size: 1.1rem;
            text-align: center;
        }

        .alert-danger {
            background-color: rgba(255, 0, 85, 0.2);
            border-color: var(--primary-color);
            color: white;
        }

        .alert-info {
            background-color: rgba(0, 240, 255, 0.2);
            border-color: var(--secondary-color);
            color: white;
        }

        /* --------------------------------------
           Pagination
        -------------------------------------- */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }

        .pagination {
            display: flex;
            gap: 10px;
            padding: 0;
            list-style: none;
        }

        .page-item .page-link {
            background-color: rgba(255, 0, 85, 0.1);
            border: 1px solid rgba(255, 0, 85, 0.3);
            color: white;
            padding: 10px 15px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .page-item .page-link:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(255, 0, 85, 0.5);
        }

        .page-item.active .page-link {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 0 20px rgba(255, 0, 85, 0.8);
        }

        .page-item.disabled .page-link {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.4);
            cursor: not-allowed;
        }

        /* --------------------------------------
           Footer
        -------------------------------------- */
        footer {
            background-color: var(--dark-color);
            border-top: 1px solid rgba(255, 0, 85, 0.3);
            padding: 40px 0;
        }

        footer p {
            margin: 0;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.7);
            letter-spacing: 1px;
        }

        /* --------------------------------------
           Animations et transitions
        -------------------------------------- */
        .concert-item {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="assets/images/logo.png" alt="Live The Music" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link active" href="events.php">Événements</a></li>
                <li class="nav-item"><a class="nav-link" href="artists.php">Artistes</a></li>
                <li class="nav-item"><a class="nav-link" href="tickets.php">Billets</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Banner -->
<div class="main-banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h6>NEWS FLASH</h6>
                <h2>DON'T MISS OUT THE UPCOMING CONCERTS</h2>
                <div class="buttons">
                    <div class="border-button">
                        <a href="#concerts-container">Explore Concerts</a>
                    </div>
                    <div class="main-button">
                        <a href="tickets.php">Buy Your Ticket</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Concerts Section -->
<section class="explore-items">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-heading">
                    <div class="line-dec"></div>
                    <h2>Discover Our <em>Upcoming</em> Concerts</h2>
                </div>
            </div>
        </div>

        <!-- Inclusion du calendrier -->
        <?php include 'calendar.php'; ?>

        <!-- Filtres -->
        <div class="filter-container">
            <a href="events.php?genre=all" class="filter-btn <?= $selectedGenre === 'all' ? 'active' : '' ?>">Tous</a>
            <?php foreach ($genres as $genre): ?>
                <a href="events.php?genre=<?= urlencode($genre) ?>" class="filter-btn <?= strtolower($selectedGenre) === strtolower($genre) ? 'active' : '' ?>">
                    <?= htmlspecialchars($genre) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Liste des concerts -->
        <div class="row" id="concerts-container">
            <?php if (isset($errorMessage)): ?>
                <div class="col-12">
                    <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
                </div>
            <?php elseif (empty($concerts)): ?>
                <div class="col-12">
                    <div class="alert alert-info">Aucun concert trouvé pour ce genre.</div>
                </div>
            <?php else: ?>
                <?php foreach ($concerts as $concert): ?>
                    <?php
                    $id = htmlspecialchars($concert['id_concert'] ?? '');
                    $genre = htmlspecialchars($concert['genre'] ?? 'Inconnu');
                    $date = !empty($concert['date_concert']) ? date('d/m/Y', strtotime($concert['date_concert'])) : 'Date à venir';
                    $lieu = htmlspecialchars($concert['nom_lieux'] ?? 'Lieu non spécifié');
                    $adresse = htmlspecialchars($concert['adresse'] ?? '');
                    $prix = htmlspecialchars($concert['prix_concert'] ?? '0');
                    $places = htmlspecialchars($concert['place_dispo'] ?? '0');
                    $imagePath = !empty($concert['image']) 
                        ? '/projetwebCRUD%20-%20ranim/View/BackOffice/baccii/bacci/startbootstrap-sb-admin-gh-pages/' . htmlspecialchars($concert['image'])
                        : 'assets/images/default-concert.jpg';
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4 concert-item">
                        <div class="card concert-card h-100">
                            <img src="<?= $imagePath ?>" class="card-img-top concert-img" alt="Concert <?= $genre ?>">
                            <div class="card-body">
                                <span class="badge-genre"><?= $genre ?></span>
                                <h5 class="card-title"><?= $lieu ?></h5>
                                <p class="text-muted"><i class="fas fa-calendar-alt"></i> <?= $date ?></p>
                                <p class="text-muted"><i class="fas fa-map-marker-alt"></i> <?= $adresse ?></p>
                                <div class="d-flex justify-content-between mb-4">
                                    <span><i class="fas fa-ticket-alt"></i> <?= $places ?> places</span>
                                    <span class="fw-bold"><?= $prix ?> €</span>
                                </div>
                                <a href="reservation.php?id=<?= $id ?>" class="btn btn-primary w-100">Réserver</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-container">
                <ul class="pagination">
                    <!-- Bouton Précédent -->
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="events.php?genre=<?= urlencode($selectedGenre) ?>&page=<?= $page - 1 ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>

                    <!-- Numéros de pages -->
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);

                    // Ajouter "..." avant si nécessaire
                    if ($startPage > 1) {
                        echo '<li class="page-item"><a class="page-link" href="events.php?genre=' . urlencode($selectedGenre) . '&page=1">1</a></li>';
                        if ($startPage > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }

                    // Afficher les numéros de pages
                    for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="events.php?genre=<?= urlencode($selectedGenre) ?>&page=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor;

                    // Ajouter "..." après si nécessaire
                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="events.php?genre=' . urlencode($selectedGenre) . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
                    }
                    ?>

                    <!-- Bouton Suivant -->
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="events.php?genre=<?= urlencode($selectedGenre) ?>&page=<?= $page + 1 ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-4">
    <div class="container text-center">
        <p>© <?= date('Y') ?> Live The Music. Tous droits réservés.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>