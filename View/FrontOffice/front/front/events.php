<?php
// Activation des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Inclusion du contrôleur
try {
    require_once 'C:/xampp/htdocs/projetwebCRUD - ranim/Controller/concertController.php';
    error_log("concertController.php inclus avec succès");
} catch (Exception $e) {
    error_log("Erreur lors de l'inclusion de concertController.php : " . $e->getMessage());
    die("Erreur critique : Impossible de charger le contrôleur. " . $e->getMessage());
}

// Vérification de la connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=projetweb', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Connexion à la base de données réussie");
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    $errorMessage = "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
    echo "<div style='color: red; text-align: center; padding: 20px;'>" . htmlspecialchars($errorMessage) . "</div>";
}

// Récupération du genre depuis l'URL
$selectedGenre = isset($_GET['genre']) ? htmlspecialchars($_GET['genre']) : 'all';

// Récupération du tri par date depuis l'URL
$sortDate = isset($_GET['sort_date']) ? htmlspecialchars($_GET['sort_date']) : 'asc';
if (!in_array($sortDate, ['asc', 'desc'])) {
    $sortDate = 'asc';
}

// Liste des genres
$genres = ['Rock', 'Pop', 'Jazz', 'Classique', 'Hip-Hop', 'Electro', 'Metal', 'Rap', 'RnB', 'Reggae'];

// Validation du genre
if ($selectedGenre !== 'all' && !in_array($selectedGenre, $genres)) {
    $selectedGenre = 'all';
}

// Récupération des concerts
try {
    $allConcerts = getConcert();
    if (!is_array($allConcerts)) {
        throw new Exception("Aucun concert trouvé ou erreur de récupération");
    }
    error_log("Données brutes de getConcert() : " . print_r($allConcerts, true));

    // Filtrer les concerts si un genre est sélectionné
    if ($selectedGenre !== 'all') {
        $filteredConcerts = [];
        foreach ($allConcerts as $concert) {
            if (isset($concert['genre']) && strtolower($concert['genre']) === strtolower($selectedGenre)) {
                $filteredConcerts[] = $concert;
            }
        }
        $allConcerts = $filteredConcerts;
    }

    // Trier les concerts par date
    usort($allConcerts, function ($a, $b) use ($sortDate) {
        $dateA = isset($a['date_concert']) ? strtotime($a['date_concert']) : 0;
        $dateB = isset($b['date_concert']) ? strtotime($b['date_concert']) : 0;
        return $sortDate === 'asc' ? $dateA - $dateB : $dateB - $dateA;
    });

} catch (Exception $e) {
    $errorMessage = "Désolé, nous rencontrons un problème technique lors de la récupération des concerts. Veuillez réessayer plus tard.";
    error_log("Erreur lors de getConcert() : " . $e->getMessage());
    $allConcerts = [];
    echo "<div style='color: red; text-align: center; padding: 20px;'>" . htmlspecialchars($errorMessage) . "</div>";
}

// Définir les concerts pour le calendrier (tous les concerts après filtrage et tri)
$calendarConcerts = $allConcerts;

// Pagination (appliquée uniquement à la liste des concerts affichée)
$concertsPerPage = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $concertsPerPage;
$totalConcerts = count($allConcerts);
$totalPages = ceil($totalConcerts / $concertsPerPage);

if ($page < 1) $page = 1;
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

// Extraire les concerts pour la page actuelle (pour la liste uniquement)
$concerts = array_slice($allConcerts, $offset, $concertsPerPage);

// Récupération des lieux
try {
    $lieux = getLieux();
    error_log("Lieux récupérés : " . print_r($lieux, true));
} catch (Exception $e) {
    $errorMessage = "Erreur lors de la récupération des lieux : " . $e->getMessage();
    error_log("Erreur lors de getLieux() : " . $e->getMessage());
    $lieux = [];
    echo "<div style='color: red; text-align: center; padding: 20px;'>" . htmlspecialchars($errorMessage) . "</div>";
}

// Fonction pour géocoder l'adresse avec Nominatim
function geocodeAddress($address) {
    $tunisianCities = ['Tunis', 'Jendouba', 'Sfax', 'Sousse', 'Monastir', 'Bizerte', 'Ariana'];
    $containsTunisianCity = false;
    foreach ($tunisianCities as $city) {
        if (stripos($address, $city) !== false) {
            $containsTunisianCity = true;
            break;
        }
    }
    if (strtolower($address) === 'ariana' && !preg_match('/,\s*(Tunisie|Tunisia)/i', $address)) {
        $address = 'Ariana, Tunisie';
        $containsTunisianCity = true;
    }
    if (strtolower($address) === 'jendouba' && !preg_match('/,\s*(Tunisie|Tunisia)/i', $address)) {
        $address = 'Jendouba, Tunisie';
        $containsTunisianCity = true;
    }
    if (strtolower($address) === 'sfax' && !preg_match('/,\s*(Tunisie|Tunisia)/i', $address)) {
        $address = 'Sfax, Tunisie';
        $containsTunisianCity = true;
    }
    if (strtolower($address) === 'sousse' && !preg_match('/,\s*(Tunisie|Tunisia)/i', $address)) {
        $address = 'Sousse, Tunisie';
        $containsTunisianCity = true;
    }
    if (strtolower($address) === 'tunis' && !preg_match('/,\s*(Tunisie|Tunisia)/i', $address)) {
        $address = 'Tunis, Tunisie';
        $containsTunisianCity = true;
    }
    if (strtolower($address) === 'new york' && !preg_match('/,\s*(USA|United States)/i', $address)) {
        $address = 'New York, USA';
    }
    if (!empty($address) && !preg_match('/,\s*(Tunisie|Tunisia|USA|United States|France)/i', $address) && $containsTunisianCity) {
        $address .= ', Tunisie';
    }
    
    $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address) . "&format=json&limit=1&addressdetails=1";
    $options = [
        'http' => [
            'header' => "User-Agent: LiveTheMusic/1.0\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        error_log("Erreur Nominatim pour adresse '$address': Connexion échouée");
        return ['error' => 'Erreur de connexion à Nominatim.', 'latitude' => 36.8065, 'longitude' => 10.1815];
    }
    
    $data = json_decode($response, true);
    error_log("Réponse Nominatim pour adresse '$address': " . print_r($data, true));
    
    if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
        $displayName = isset($data[0]['display_name']) ? $data[0]['display_name'] : $address;
        if ($containsTunisianCity && !preg_match('/(Tunisia|Tunisie)/i', $address)) {
            $suggestedAddress = preg_match('/,\s*(Tunisie|Tunisia)/i', $address) ? $address : $address . ', Tunisie';
            return ['error' => 'L\'adresse ne semble pas être en Tunisie. Essayez "' . $suggestedAddress . '".', 'latitude' => 36.8065, 'longitude' => 10.1815];
        }
        if (stripos($address, 'New York') !== false && !preg_match('/(USA|United States)/i', $address)) {
            return ['error' => 'L\'adresse ne semble pas être aux États-Unis. Essayez "New York, USA".', 'latitude' => 40.7128, 'longitude' => -74.0060];
        }
        return [
            'latitude' => floatval($data[0]['lat']),
            'longitude' => floatval($data[0]['lon']),
            'display_name' => $displayName
        ];
    }
    
    $fallbacks = [
        'jendouba' => ['lat' => 36.5011, 'lon' => 8.7803, 'display_name' => 'Jendouba, Tunisie'],
        'tunis' => ['lat' => 36.8065, 'lon' => 10.1815, 'display_name' => 'Tunis, Tunisie'],
        'sfax' => ['lat' => 34.7406, 'lon' => 10.7603, 'display_name' => 'Sfax, Tunisie'],
        'sousse' => ['lat' => 35.8256, 'lon' => 10.6412, 'display_name' => 'Sousse, Tunisie'],
        'ariana' => ['lat' => 36.8601, 'lon' => 10.1934, 'display_name' => 'Ariana, Tunisie']
    ];
    
    foreach ($fallbacks as $city => $coords) {
        if (stripos($address, $city) !== false) {
            error_log("Fallback utilisé pour $city: {$coords['lat']}, {$coords['lon']}");
            return [
                'latitude' => $coords['lat'],
                'longitude' => $coords['lon'],
                'display_name' => $coords['display_name']
            ];
        }
    }
    
    return ['error' => 'Adresse non trouvée.', 'latitude' => 36.8065, 'longitude' => 10.1815];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live The Music - Événements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --primary-color: #DC2626;
            --secondary-color: #F87171;
            --dark-bg: #0A0A0A;
            --card-bg: #1F1F1F;
            --text-light: #F5F5F5;
            --text-muted: #B0B8C4;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text-light);
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .navbar {
            background-color: var(--dark-bg);
            border-bottom: 2px solid var(--primary-color);
            padding: 15px 0;
        }

        .navbar-brand img {
            height: 45px;
        }

        .navbar-nav .nav-link {
            color: var(--text-muted);
            font-weight: 500;
            margin: 0 15px;
            padding: 8px 15px;
            border-radius: 20px;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary-color);
            background-color: rgba(220, 38, 38, 0.1);
        }

        .main-banner {
            background: url('assets/images/concert-bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
            text-align: center;
            border-bottom: 3px solid var(--secondary-color);
        }

        .main-banner h6 {
            color: var(--secondary-color);
            font-size: 1.2rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .main-banner h2 {
            font-size: 3.2rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--text-light);
        }

        .main-banner .buttons a {
            padding: 12px 35px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
        }

        .main-banner .border-button a {
            border: 2px solid var(--text-light);
            color: var(--text-light);
        }

        .main-banner .border-button a:hover {
            background-color: var(--secondary-color);
            color: var(--dark-bg);
            border-color: var(--secondary-color);
        }

        .main-banner .main-button a {
            background-color: var(--primary-color);
            color: var(--text-light);
        }

        .main-banner .main-button a:hover {
            background-color: #991B1B;
        }

        .explore-items {
            padding: 80px 0;
        }

        .section-heading {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-heading h2 {
            font-size: 2.8rem;
            font-weight: 600;
            color: var(--text-light);
        }

        .section-heading em {
            color: var(--secondary-color);
            font-style: normal;
        }

        .line-dec {
            width: 80px;
            height: 4px;
            background-color: var(--primary-color);
            margin: 15px auto;
            border-radius: 2px;
        }

        .filter-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-bottom: 50px;
        }

        .filter-btn {
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 500;
            border: 1px solid var(--primary-color);
            color: var(--text-light);
            background-color: transparent;
            text-decoration: none;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background-color: var(--primary-color);
            border-color: transparent;
            color: var(--text-light);
        }

        .sort-select {
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid var(--primary-color);
            background-color: var(--card-bg);
            color: var(--text-light);
            font-weight: 500;
        }

        .sort-select:focus {
            outline: none;
            border-color: var(--secondary-color);
        }

        .concert-card {
            background-color: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        .concert-img {
            height: 220px;
            object-fit: cover;
            width: 100%;
        }

        .card-body {
            padding: 25px;
        }

        .badge-genre {
            background-color: var(--primary-color);
            font-size: 0.9rem;
            padding: 8px 15px;
            border-radius: 25px;
            margin-bottom: 20px;
            display: inline-block;
            color: var(--text-light);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-light);
        }

        .card-title a {
            color: var(--text-light) !important;
            text-decoration: none;
        }

        .card-title a:hover {
            color: var(--secondary-color) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
            font-size: 1rem;
            margin-bottom: 12px;
        }

        .text-muted i {
            margin-right: 10px;
            color: var(--primary-color);
            font-size: 1.1rem;
            cursor: pointer;
        }

        .text-muted i:hover {
            color: var(--secondary-color);
        }

        .location-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-large-map {
            background-color: var(--primary-color);
            border: none;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.85rem;
            color: var(--text-light);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-large-map:hover {
            background-color: #991B1B;
        }

        .d-flex span {
            font-size: 1rem;
            color: var(--text-muted);
        }

        .d-flex .fw-bold {
            font-size: 1.2rem;
            color: var(--secondary-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 30px;
            padding: 12px 25px;
            font-weight: 600;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #991B1B;
        }

        .alert {
            border-radius: 15px;
            margin: 30px 0;
            text-align: center;
            font-size: 1rem;
            background-color: rgba(220, 38, 38, 0.1);
            border: 1px solid var(--primary-color);
            color: var(--text-light);
        }

        .alert-info {
            background-color: rgba(248, 113, 113, 0.1);
            border-color: var(--secondary-color);
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 50px;
        }

        .pagination {
            display: flex;
            gap: 10px;
        }

        .page-item .page-link {
            background-color: var(--card-bg);
            border: 1px solid var(--primary-color);
            color: var(--text-light);
            padding: 10px 16px;
            border-radius: 25px;
            font-weight: 500;
            text-decoration: none;
        }

        .page-item .page-link:hover {
            background-color: var(--primary-color);
            border-color: transparent;
            color: var(--text-light);
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: transparent;
            color: var(--text-light);
        }

        .page-item.disabled .page-link {
            background-color: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.4);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .event-map-container {
            position: relative;
            margin-top: 15px;
            display: none;
        }

        .event-map-container.show {
            display: block;
        }

        .event-map {
            height: 200px;
            width: 100%;
            border-radius: 20px;
            margin-bottom: 10px;
            border: 1px solid var(--primary-color);
        }

        .close-map-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary-color);
            color: var(--text-light);
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .close-map-btn:hover {
            background: var(--secondary-color);
        }

        .modal-content {
            background-color: var(--dark-bg);
            border: 1px solid var(--primary-color);
            border-radius: 20px;
        }

        .modal-header {
            border-bottom: 1px solid var(--primary-color);
            color: var(--text-light);
        }

        .modal-body {
            padding: 20px;
        }

        .modal-map {
            height: 500px;
            width: 100%;
            border-radius: 20px;
            border: 1px solid var(--primary-color);
        }

        .modal-footer {
            border-top: 1px solid var(--primary-color);
        }

        .leaflet-popup-content-wrapper {
            background: var(--card-bg);
            color: var(--text-light);
            border-radius: 10px;
            border: 1px solid var(--primary-color);
        }

        .leaflet-popup-content {
            margin: 10px 15px;
            font-size: 1rem;
        }

        .leaflet-popup-content b {
            color: var(--secondary-color);
        }

        .leaflet-popup-tip {
            background: var(--card-bg);
            border-top: 1px solid var(--primary-color);
        }

        footer {
            background-color: var(--dark-bg);
            border-top: 2px solid var(--primary-color);
            padding: 40px 0;
        }

        footer p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .main-banner {
                padding: 60px 0;
            }

            .main-banner h2 {
                font-size: 2rem;
            }

            .concert-img {
                height: 180px;
            }

            .filter-container {
                flex-direction: column;
                align-items: center;
            }

            .filter-btn {
                width: 100%;
                text-align: center;
            }

            .sort-select {
                width: 100%;
            }

            .section-heading h2 {
                font-size: 2rem;
            }

            .card-title {
                font-size: 1.3rem;
            }

            .text-muted {
                font-size: 0.9rem;
            }

            .d-flex span {
                font-size: 0.9rem;
            }

            .d-flex .fw-bold {
                font-size: 1rem;
            }

            .card-body {
                padding: 20px;
            }

            .event-map {
                height: 150px;
            }

            .modal-map {
                height: 350px;
            }

            .btn-large-map {
                padding: 3px 8px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
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
                <div class="buttons d-flex justify-content-center gap-3">
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

        <!-- Display Error Message if Exists -->
        <?php if (isset($errorMessage)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Calendar Section -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <?php
                // Passer $calendarConcerts (qui contient tous les concerts) au calendrier
                if (empty($calendarConcerts)) {
                    echo '<div class="alert alert-info">Aucun concert disponible pour afficher le calendrier.</div>';
                } else {
                    $concerts = $calendarConcerts; // Temporairement définir $concerts comme $calendarConcerts pour calendar.php
                    include 'calendar.php';
                    $concerts = array_slice($allConcerts, $offset, $concertsPerPage); // Restaurer $concerts pour la liste paginée
                }
                ?>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filter-container">
            <a href="events.php?genre=all&sort_date=<?php echo $sortDate; ?>" class="filter-btn <?php echo $selectedGenre === 'all' ? 'active' : ''; ?>">Tous</a>
            <?php foreach ($genres as $genre): ?>
                <a href="events.php?genre=<?php echo urlencode($genre); ?>&sort_date=<?php echo $sortDate; ?>" class="filter-btn <?php echo strtolower($selectedGenre) === strtolower($genre) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($genre); ?>
                </a>
            <?php endforeach; ?>
            <select class="sort-select" onchange="window.location.href='events.php?genre=<?php echo urlencode($selectedGenre); ?>&sort_date=' + this.value">
                <option value="asc" <?php echo $sortDate === 'asc' ? 'selected' : ''; ?>>Plus ancien au plus récent</option>
                <option value="desc" <?php echo $sortDate === 'desc' ? 'selected' : ''; ?>>Plus récent au plus ancien</option>
            </select>
        </div>

        <!-- Liste des concerts -->
        <div class="row" id="concerts-container">
            <?php if (isset($errorMessage)): ?>
                <div class="col-12">
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
                </div>
            <?php elseif (count($concerts) == 0): ?>
                <div class="col-12">
                    <div class="alert alert-info">Aucun concert trouvé pour ce genre.</div>
                </div>
            <?php else: ?>
                <?php foreach ($concerts as $concert): ?>
                    <?php
                    $id = isset($concert['id_concert']) ? htmlspecialchars($concert['id_concert']) : '';
                    $genre = isset($concert['genre']) ? htmlspecialchars($concert['genre']) : 'Inconnu';
                    $date = isset($concert['date_concert']) && !empty($concert['date_concert']) ? date('d/m/Y', strtotime($concert['date_concert'])) : 'Date à venir';
                    $lieu = isset($concert['nom_lieux']) ? htmlspecialchars(ucfirst(strtolower(trim($concert['nom_lieux'])))) : 'Lieu non spécifié';
                    $adresse = isset($concert['adresse']) ? htmlspecialchars(ucfirst(strtolower(trim($concert['adresse'])))) : '';
                    $adresse = preg_replace('/\s+/', ' ', $adresse);
                    $tunisianCities = ['Tunis', 'Jendouba', 'Sfax', 'Sousse', 'Monastir', 'Bizerte', 'Ariana'];
                    $containsTunisianCity = false;
                    foreach ($tunisianCities as $city) {
                        if (stripos($adresse, $city) !== false) {
                            $containsTunisianCity = true;
                            break;
                        }
                    }
                    if (strtolower($adresse) === 'ariana' && !preg_match('/,\s*(Tunisie|Tunisia)/i', $adresse)) {
                        $adresse = 'Ariana, Tunisie';
                        $containsTunisianCity = true;
                    }
                    if (strtolower($adresse) === 'jendouba' && !preg_match('/,\s*(Tunisie|Tunisia)/i', $adresse)) {
                        $adresse = 'Jendouba, Tunisie';
                        $containsTunisianCity = true;
                    }
                    if (strtolower($adresse) === 'sfax' && !preg_match('/,\s*(Tunisie|Tunisia)/i', $adresse)) {
                        $adresse = 'Sfax, Tunisie';
                        $containsTunisianCity = true;
                    }
                    if (strtolower($adresse) === 'sousse' && !preg_match('/,\s*(Tunisie|Tunisia)/i', $adresse)) {
                        $adresse = 'Sousse, Tunisie';
                        $containsTunisianCity = true;
                    }
                    if (strtolower($adresse) === 'tunis' && !preg_match('/,\s*(Tunisie|Tunisia)/i', $adresse)) {
                        $adresse = 'Tunis, Tunisie';
                        $containsTunisianCity = true;
                    }
                    if (strtolower($adresse) === 'new york' && !preg_match('/,\s*(USA|United States)/i', $adresse)) {
                        $adresse = 'New York, USA';
                    }
                    if (!empty($adresse) && !preg_match('/,\s*(Tunisie|Tunisia|USA|United States|France)/i', $adresse) && $containsTunisianCity) {
                        $adresse .= ', Tunisie';
                    }
                    error_log("Concert ID: $id, Lieu: $lieu, Adresse: $adresse");
                    $prix = isset($concert['prix_concert']) ? htmlspecialchars($concert['prix_concert']) : '0';
                    $places = isset($concert['place_dispo']) ? htmlspecialchars($concert['place_dispo']) : '0';
                    $imagePath = !empty($concert['image']) 
                        ? '/projetwebCRUD%20-%20ranim/View/BackOffice/baccii/bacci/startbootstrap-sb-admin-gh-pages/' . htmlspecialchars($concert['image'])
                        : 'assets/images/default-concert.jpg';
                    $geocodeResult = geocodeAddress($adresse);
                    $latitude = isset($geocodeResult['latitude']) ? $geocodeResult['latitude'] : 36.8065;
                    $longitude = isset($geocodeResult['longitude']) ? $geocodeResult['longitude'] : 10.1815;
                    $displayAddress = isset($geocodeResult['display_name']) ? $geocodeResult['display_name'] : $adresse;
                    $geocodeError = isset($geocodeResult['error']) ? $geocodeResult['error'] : false;
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card concert-card h-100">
                            <img src="<?php echo $imagePath; ?>" class="card-img-top concert-img" alt="Concert <?php echo $genre; ?>">
                            <div class="card-body">
                                <span class="badge-genre"><?php echo $genre; ?></span>
                                <h5 class="card-title">
                                    <a href="map.php?nom=<?php echo urlencode($lieu); ?>&adresse=<?php echo urlencode($adresse); ?>">
                                        <?php echo $lieu; ?>
                                    </a>
                                </h5>
                                <p class="text-muted"><i class="fas fa-calendar-alt"></i> <?php echo $date; ?></p>
                                <div class="location-info">
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-map-marker-alt" onclick="toggleMap(<?php echo $id; ?>, <?php echo $latitude; ?>, <?php echo $longitude; ?>, '<?php echo addslashes($lieu); ?>', '<?php echo addslashes($displayAddress); ?>', <?php echo $geocodeError ? 'true' : 'false'; ?>)"></i> 
                                        <?php echo $adresse; ?>
                                    </p>
                                    <button class="btn-large-map" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#largeMapModal-<?php echo $id; ?>" 
                                            data-lat="<?php echo $latitude; ?>" 
                                            data-lon="<?php echo $longitude; ?>" 
                                            data-event-name="<?php echo addslashes($lieu); ?>" 
                                            data-display-address="<?php echo addslashes($displayAddress); ?>" 
                                            data-has-error="<?php echo $geocodeError ? 'true' : 'false'; ?>" 
                                            data-error-message="<?php echo $geocodeError ? addslashes($geocodeError) : ''; ?>">
                                        <i class="fas fa-expand"></i> Voir la localisation détaillée
                                    </button>
                                </div>
                                <div class="d-flex justify-content-between mb-3 mt-3">
                                    <span><i class="fas fa-ticket-alt"></i> <?php echo $places; ?> places</span>
                                    <span class="fw-bold"><?php echo $prix; ?> €</span>
                                </div>
                                <a href="reservation.php?id=<?php echo $id; ?>" class="btn btn-primary">Réserver</a>
                                <div class="event-map-container" id="map-container-<?php echo $id; ?>">
                                    <button class="close-map-btn" onclick="toggleMap(<?php echo $id; ?>, <?php echo $latitude; ?>, <?php echo $longitude; ?>, '<?php echo addslashes($lieu); ?>', '<?php echo addslashes($displayAddress); ?>', <?php echo $geocodeError ? 'true' : 'false'; ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <div class="event-map" id="map-<?php echo $id; ?>"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal pour la carte en grande taille -->
                        <div class="modal fade" id="largeMapModal-<?php echo $id; ?>" tabindex="-1" aria-labelledby="largeMapModalLabel-<?php echo $id; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="largeMapModalLabel-<?php echo $id; ?>">Localisation : <?php echo $lieu; ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="modal-map" id="modal-map-<?php echo $id; ?>"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>
                                    </div>
                                </div>
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
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="events.php?genre=<?php echo urlencode($selectedGenre); ?>&sort_date=<?php echo $sortDate; ?>&page=<?php echo $page - 1; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);

                    if ($startPage > 1) {
                        echo '<li class="page-item"><a class="page-link" href="events.php?genre=' . urlencode($selectedGenre) . '&sort_date=' . $sortDate . '&page=1">1</a></li>';
                        if ($startPage > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }

                    for ($i = $startPage; $i <= $endPage; $i++) {
                        echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">';
                        echo '<a class="page-link" href="events.php?genre=' . urlencode($selectedGenre) . '&sort_date=' . $sortDate . '&page=' . $i . '">' . $i . '</a>';
                        echo '</li>';
                    }

                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="events.php?genre=' . urlencode($selectedGenre) . '&sort_date=' . $sortDate . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
                    }
                    ?>
                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="events.php?genre=<?php echo urlencode($selectedGenre); ?>&sort_date=<?php echo $sortDate; ?>&page=<?php echo $page + 1; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Footer -->
<footer class="text-white">
    <div class="container text-center">
        <p>© <?php echo date('Y'); ?> Live The Music. Tous droits réservés.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    console.log("Scripts loaded: Bootstrap, FullCalendar, Leaflet");

    const maps = {};
    const modalMaps = {};

    // Fonction pour la petite carte (au clic sur l'icône de lieu)
    function toggleMap(eventId, lat, lon, eventName, displayAddress, hasError) {
        console.log(`Toggling map for event ${eventId}: lat=${lat}, lon=${lon}, hasError=${hasError}`);
        const mapContainer = document.getElementById(`map-container-${eventId}`);
        const mapElement = document.getElementById(`map-${eventId}`);
        const isVisible = mapContainer.classList.contains('show');

        if (isVisible) {
            mapContainer.classList.remove('show');
            if (maps[eventId]) {
                maps[eventId].remove();
                delete maps[eventId];
            }
            return;
        }

        mapContainer.classList.add('show');

        if (!hasError) {
            const map = L.map(`map-${eventId}`).setView([lat, lon], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            const marker = L.marker([lat, lon])
                .addTo(map)
                .bindPopup(`<b>${eventName}</b><br>${displayAddress}`)
                .openPopup();

            maps[eventId] = map;
        } else {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning';
            alertDiv.textContent = hasError;
            mapElement.innerHTML = '';
            mapElement.appendChild(alertDiv);
        }
    }

    // Gestion des cartes dans les modals (grande taille)
    document.querySelectorAll('.btn-large-map').forEach(button => {
        button.addEventListener('click', function () {
            const eventId = this.getAttribute('data-bs-target').split('-')[1];
            const lat = parseFloat(this.getAttribute('data-lat'));
            const lon = parseFloat(this.getAttribute('data-lon'));
            const eventName = this.getAttribute('data-event-name');
            const displayAddress = this.getAttribute('data-display-address');
            const hasError = this.getAttribute('data-has-error') === 'true';
            const errorMessage = this.getAttribute('data-error-message');

            console.log(`Opening modal map for event ${eventId}: lat=${lat}, lon=${lon}, hasError=${hasError}`);

            const modal = document.getElementById(`largeMapModal-${eventId}`);
            modal.addEventListener('shown.bs.modal', function () {
                if (!modalMaps[eventId]) {
                    const mapElement = document.getElementById(`modal-map-${eventId}`);
                    if (!hasError) {
                        const map = L.map(`modal-map-${eventId}`).setView([lat, lon], 8);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);

                        const marker = L.marker([lat, lon])
                            .addTo(map)
                            .bindPopup(`<b>${eventName}</b><br>${displayAddress}`)
                            .openPopup();

                        setTimeout(() => {
                            map.invalidateSize();
                        }, 100);

                        modalMaps[eventId] = map;
                    } else {
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-warning';
                        alertDiv.textContent = errorMessage;
                        mapElement.innerHTML = '';
                        mapElement.appendChild(alertDiv);
                    }
                }
            }, { once: true });
        });
    });

    // Nettoyer la carte lorsque le modal est fermé
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            const eventId = this.id.split('-')[1];
            if (modalMaps[eventId]) {
                modalMaps[eventId].remove();
                delete modalMaps[eventId];
            }
        });
    });
</script>
</body>

</html> 