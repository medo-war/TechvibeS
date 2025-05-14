<?php
// Start the session at the very beginning
session_start();

// Activation des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Inclusion du contrôleur
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/concertController.php';
    error_log("concertController.php inclus avec succès");
} catch (Exception $e) {
    error_log("Erreur lors de l'inclusion de concertController.php : " . $e->getMessage());
    die("Erreur critique : Impossible de charger le contrôleur. " . $e->getMessage());
}

// Vérification de la connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');
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
    
    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-liberty-market.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <style>
        :root {
            --neon-pink: #ff2a6d;
            --neon-blue: #05d9e8;
            --dark-blue: #01012b;
            --darker-blue: #000022;
            --light-pink: #ff7bbf;
            --purple: #7928ca;
            --cyan: #00ffff;
            --teal: #00b8d4;
            --text-light: #ffffff;
            --text-muted: #a0a0c0;
            --card-bg: rgba(1, 1, 43, 0.7);
        }

        body {
            background: linear-gradient(to bottom, #000022, #010136, #01012b);
            margin: 0;
            padding: 0;
            color: var(--text-light);
            overflow-x: hidden;
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            position: relative;
        }
        
        /* Background overlay */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -999;
            background: linear-gradient(to bottom, #000022, #010136, #01012b);
            pointer-events: none;
        }

        /* Page Heading Styles */
        .page-heading {
            background: linear-gradient(to right, #000022, #010136) !important;
            border-bottom: 1px solid var(--neon-blue);
            box-shadow: 0 0 20px rgba(5, 217, 232, 0.2);
            position: relative;
            overflow: hidden;
            padding: 80px 0 60px 0;
            margin-bottom: 50px;
            z-index: 1;
        }
        
        .page-heading::before {
            content: '';
            position: absolute;
            top: -150px;
            left: -150px;
            width: 300px;
            height: 300px;
            background: radial-gradient(
                circle,
                rgba(255, 42, 109, 0.8) 0%,
                rgba(255, 42, 109, 0.4) 30%,
                transparent 70%
            );
            border-radius: 50%;
            z-index: 1;
            animation: spotlight-move-pink 15s infinite alternate ease-in-out;
            filter: blur(10px);
        }
        
        .page-heading::after {
            content: '';
            position: absolute;
            top: -150px;
            right: -150px;
            width: 300px;
            height: 300px;
            background: radial-gradient(
                circle,
                rgba(5, 217, 232, 0.8) 0%,
                rgba(5, 217, 232, 0.4) 30%,
                transparent 70%
            );
            border-radius: 50%;
            z-index: 1;
            animation: spotlight-move-blue 15s infinite alternate-reverse ease-in-out;
            filter: blur(10px);
        }
        
        @keyframes spotlight-move-pink {
            0% {
                transform: translate(0, 0);
                opacity: 0.5;
            }
            25% {
                transform: translate(30%, 20%);
                opacity: 0.7;
            }
            50% {
                transform: translate(50%, 50%);
                opacity: 0.5;
            }
            75% {
                transform: translate(70%, 30%);
                opacity: 0.7;
            }
            100% {
                transform: translate(120%, 10%);
                opacity: 0.5;
            }
        }
        
        @keyframes spotlight-move-blue {
            0% {
                transform: translate(0, 0);
                opacity: 0.5;
            }
            25% {
                transform: translate(-30%, 20%);
                opacity: 0.7;
            }
            50% {
                transform: translate(-50%, 50%);
                opacity: 0.5;
            }
            75% {
                transform: translate(-70%, 30%);
                opacity: 0.7;
            }
            100% {
                transform: translate(-120%, 10%);
                opacity: 0.5;
            }
        }
        
        .page-heading h2 {
            font-size: 36px;
            font-weight: 700;
            color: var(--text-light);
            text-align: center;
            position: relative;
            z-index: 2;
            margin-bottom: 20px;
            text-shadow: 0 0 10px var(--neon-blue);
        }
        
        .page-heading h2 em {
            color: var(--neon-blue);
            font-style: normal;
            text-shadow: 0 0 7px var(--neon-blue),
                0 0 10px var(--neon-blue),
                0 0 21px var(--neon-blue),
                0 0 42px var(--neon-blue);
            animation: neon-text-pulse 2s infinite alternate-reverse;
            font-style: normal;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        @keyframes neon-text-pulse {
            0%, 18%, 22%, 25%, 53%, 57%, 100% {
                text-shadow:
                    0 0 7px var(--neon-blue),
                    0 0 10px var(--neon-blue),
                    0 0 21px var(--neon-blue),
                    0 0 42px var(--neon-blue);
            }
            20%, 24%, 55% {
                text-shadow: none;
            }
        }
        
        /* Card Styles */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--neon-blue);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            box-shadow: 0 0 15px rgba(5, 217, 232, 0.2);
            margin-bottom: 30px;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 20px rgba(5, 217, 232, 0.4), 0 0 30px rgba(5, 217, 232, 0.2);
            border-color: var(--neon-pink);
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
            gap: 12px;
            margin-bottom: 40px;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: rgba(1, 1, 43, 0.4);
            border-radius: 15px;
            border: 1px solid rgba(5, 217, 232, 0.2);
            box-shadow: 0 0 20px rgba(5, 217, 232, 0.1);
        }

        .filter-btn {
            background-color: rgba(1, 1, 43, 0.7);
            color: var(--text-muted);
            border: 1px solid var(--neon-blue);
            border-radius: 30px;
            padding: 8px 18px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 0 5px rgba(5, 217, 232, 0.3);
            letter-spacing: 0.5px;
        }

        .filter-btn:hover {
            background-color: rgba(5, 217, 232, 0.2);
            color: var(--text-light);
            box-shadow: 0 0 15px rgba(5, 217, 232, 0.5);
            transform: translateY(-2px);
        }
        
        .filter-btn.active {
            background-color: var(--neon-blue);
            color: var(--text-light);
            box-shadow: 0 0 15px rgba(5, 217, 232, 0.7);
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.7);
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
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--neon-pink);
            text-shadow: 0 0 5px rgba(255, 42, 109, 0.5);
            letter-spacing: 0.5px;
        }

        .card-text {
            color: var(--text-muted);
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        /* Card Image Styling */
        .card-image-wrapper {
            position: relative;
            overflow: hidden;
            height: 200px;
        }
        
        .card-img-top {
            height: 100%;
            width: 100%;
            object-fit: cover;
            border-bottom: 1px solid var(--neon-blue);
            transition: all 0.5s ease;
        }
        
        .card:hover .card-img-top {
            transform: scale(1.05);
            filter: brightness(1.1);
        }
        
        /* Card Overlay Elements */
        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 15px;
            background: linear-gradient(to bottom, rgba(0,0,0,0.4) 0%, transparent 40%, transparent 60%, rgba(0,0,0,0.8) 100%);
            transition: all 0.3s ease;
        }
        
        .badge-genre {
            align-self: flex-start;
            background-color: var(--neon-pink);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 0 10px rgba(255, 42, 109, 0.7);
            text-shadow: 0 0 5px rgba(255, 42, 109, 0.7);
        }
        
        .date-badge {
            align-self: flex-end;
            background-color: var(--neon-blue);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 0 10px rgba(5, 217, 232, 0.7);
            text-shadow: 0 0 5px rgba(5, 217, 232, 0.7);
        }

        .card-title a:hover {
            color: var(--neon-pink) !important;
        }
        
        /* Location Info Styling */
        .location-info {
            display: flex;
            align-items: center;
            margin: 15px 0;
            position: relative;
            background-color: rgba(1, 1, 43, 0.5);
            border-radius: 10px;
            padding: 10px;
            border: 1px solid rgba(5, 217, 232, 0.3);
        }
        
        .location-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--neon-blue);
            border-radius: 50%;
            margin-right: 10px;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(5, 217, 232, 0.5);
            transition: all 0.3s ease;
        }
        
        .location-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(5, 217, 232, 0.8);
        }
        
        .location-text {
            flex: 1;
            margin: 0;
            font-size: 0.85rem;
            color: var(--text-muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .btn-map-details {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: transparent;
            border: 1px solid var(--neon-pink);
            border-radius: 50%;
            color: var(--neon-pink);
            margin-left: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-map-details:hover {
            background-color: var(--neon-pink);
            color: white;
            box-shadow: 0 0 10px rgba(255, 42, 109, 0.7);
        }
        
        /* Concert Details Styling */
        .concert-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .detail-item i {
            margin-right: 5px;
            color: var(--neon-blue);
        }
        
        .detail-item.price {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--neon-pink);
            text-shadow: 0 0 5px rgba(255, 42, 109, 0.5);
        }
        
        /* Reserve Button Styling */
        .btn-reserve {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-reserve span {
            margin-right: 10px;
        }
        
        .btn-reserve i {
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }
        
        .btn-reserve:hover i {
            opacity: 1;
            transform: translateX(5px);
        }
        
        /* Fix for calendar events */
        .custom-calendar-event {
            z-index: 10;
            position: relative;
            cursor: pointer;
        }
        
        /* Ensure modals work correctly */
        .modal {
            z-index: 9999 !important;
        }
        
        .modal-backdrop {
            z-index: 0 !important;
        }
        
        /* Ensure modal content is visible */
        .modal-content {
            z-index: 10000 !important;
            position: relative;
        }
        
        /* Force modal to be visible */
        .modal.show {
            display: block !important;
            opacity: 1 !important;
        }
        
        /* Critical fix: ensure modal is above backdrop */
        #eventModal {
            z-index: 0 !important;
        }
        
        /* Override Bootstrap defaults */
        .modal-backdrop.show + .modal.show {
            z-index: 100001 !important;
        }
        
        /* Map Container Styling */
        .event-map-container {
            display: none;
            position: relative;
            margin-top: 20px;
            border-radius: 15px;
            overflow: hidden;
            height: 0;
            transition: all 0.5s ease;
            opacity: 0;
            transform: translateY(-10px);
        }

        .event-map-container.show {
            display: block;
            height: 200px;
            border: 1px solid var(--neon-blue);
            box-shadow: 0 0 15px rgba(5, 217, 232, 0.3);
            opacity: 1;
            transform: translateY(0);
        }
        
        .event-map {
            height: 100%;
            width: 100%;
            border-radius: 15px;
        }
        
        .close-map-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            background-color: var(--neon-pink);
            border: none;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(255, 42, 109, 0.7);
            transition: all 0.3s ease;
        }
        
        .close-map-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(255, 42, 109, 0.9);
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
            background-color: var(--neon-blue);
            border-color: var(--neon-blue);
            color: var(--text-light);
            padding: 10px 20px;
            font-weight: 500;
            border-radius: 30px;
            transition: all 0.3s ease;
            text-shadow: 0 0 5px var(--neon-blue);
            box-shadow: 0 0 10px rgba(5, 217, 232, 0.5);
            letter-spacing: 1px;
        }

        .btn-primary:hover {
            background-color: var(--neon-pink);
            border-color: var(--neon-pink);
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(255, 42, 109, 0.7), 0 0 30px rgba(255, 42, 109, 0.4);
            text-shadow: 0 0 8px var(--neon-pink);
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

        /* Particles.js styling */
        #particles-js-events {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            pointer-events: none;
        }
        
        .page-heading .container,
        .explore-items .container,
        .footer .container,
        .pagination-container,
        .modal,
        .filters-container,
        nav,
        header {
            position: relative;
            z-index: 2;
        }
        
        .page-heading,
        .explore-items,
        footer {
            position: relative;
        }

.btn-reserve i {
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s ease;
}

.btn-reserve:hover i {
    opacity: 1;
    transform: translateX(0);
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

            .custom-calendar-event {
                z-index: 10;
                position: relative;
                cursor: pointer;
            }
            
            .modal {
                z-index: 1055 !important;
            }
            
            .modal-backdrop {
                z-index: 1050 !important;
            }
        }
    </style>
</head>
<body class="bg-dark">

<!-- Include the navbar -->
<?php include 'includes/navbar.php'; ?>

<!-- Particles.js Background (Stars animation) -->
<div id="particles-js-events"></div>

<!-- Page Heading -->
<div class="page-heading">
    
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Discover Our <em>Upcoming</em> Concerts</h2>
                <span>Explore the latest live events and book your tickets</span>
            </div>
        </div>
    </div>
</div>

<!-- Concerts Section -->
<section class="explore-items">
    <div class="container">
        <div class="row">

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
                        ? '/livethemusic/' . htmlspecialchars($concert['image'])
                        : 'assets/images/default-concert.jpg';
                    // For debugging
                    error_log("Image path for concert {$concert['id_concert']}: {$imagePath}");
                    $geocodeResult = geocodeAddress($adresse);
                    $latitude = isset($geocodeResult['latitude']) ? $geocodeResult['latitude'] : 36.8065;
                    $longitude = isset($geocodeResult['longitude']) ? $geocodeResult['longitude'] : 10.1815;
                    $displayAddress = isset($geocodeResult['display_name']) ? $geocodeResult['display_name'] : $adresse;
                    $geocodeError = isset($geocodeResult['error']) ? $geocodeResult['error'] : false;
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card concert-card h-100">
                            <div class="card-image-wrapper">
                                <img src="<?php echo $imagePath; ?>" class="card-img-top concert-img" alt="Concert <?php echo $genre; ?>">
                                <div class="card-overlay">
                                    <span class="badge-genre"><?php echo $genre; ?></span>
                                    <div class="date-badge">
                                        <i class="fas fa-calendar-alt"></i> <?php echo $date; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="map.php?nom=<?php echo urlencode($lieu); ?>&adresse=<?php echo urlencode($adresse); ?>">
                                        <?php echo $lieu; ?>
                                    </a>
                                </h5>
                                
                                <div class="location-info">
                                    <div class="location-icon" onclick="toggleMap(<?php echo $id; ?>, <?php echo $latitude; ?>, <?php echo $longitude; ?>, '<?php echo addslashes($lieu); ?>', '<?php echo addslashes($displayAddress); ?>', <?php echo $geocodeError ? 'true' : 'false'; ?>)">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <p class="location-text"><?php echo $adresse; ?></p>
                                    <button class="btn-map-details" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#largeMapModal-<?php echo $id; ?>" 
                                            data-lat="<?php echo $latitude; ?>" 
                                            data-lon="<?php echo $longitude; ?>" 
                                            data-event-name="<?php echo addslashes($lieu); ?>" 
                                            data-display-address="<?php echo addslashes($displayAddress); ?>" 
                                            data-has-error="<?php echo $geocodeError ? 'true' : 'false'; ?>" 
                                            data-error-message="<?php echo $geocodeError ? addslashes($geocodeError) : ''; ?>">
                                        <i class="fas fa-expand"></i>
                                    </button>
                                </div>
                                
                                <div class="concert-details">
                                    <div class="detail-item">
                                        <i class="fas fa-ticket-alt"></i>
                                        <span><?php echo $places; ?> places</span>
                                    </div>
                                    <div class="detail-item price">
                                        <span><?php echo $prix; ?> €</span>
                                    </div>
                                </div>
                                
                                <a href="purchase.php?concert_id=<?php echo $id; ?>" class="btn btn-primary btn-reserve">
                                    <span>Réserver</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                                
                                <div class="event-map-container" id="map-container-<?php echo $id; ?>">
                                    <button class="close-map-btn" onclick="toggleMap(<?php echo $id; ?>, <?php echo $latitude; ?>, <?php echo $longitude; ?>, '<?php echo addslashes($lieu); ?>', '<?php echo addslashes($displayAddress); ?>', <?php echo $geocodeError ? 'true' : 'false'; ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <div class="event-map" id="map-<?php echo $id; ?>"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal pour la carte en grande taille -->
                        <div class="modal fade" id="largeMapModal-<?php echo $id; ?>" tabindex="-1" aria-labelledby="largeMapModalLabel-<?php echo $id; ?>" aria-hidden="true" style="z-index:9999 !important; padding: 0 !important;">
                            <div class="modal-dialog modal-lg modal-dialog-centered" style="position:fixed !important; top:50% !important; left:50% !important; transform:translate(-50%, -50%) !important; margin:0 !important; max-width:800px !important; width:90% !important;">
                                <div class="modal-content" style="box-shadow: 0 0 40px rgba(255, 0, 85, 0.8);">
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
        
        // Make sure all other maps are closed
        document.querySelectorAll('.event-map-container').forEach(container => {
            if (container.id !== `map-container-${eventId}` && container.classList.contains('show')) {
                container.classList.remove('show');
                const otherId = container.id.split('-')[2];
                if (maps[otherId]) {
                    maps[otherId].remove();
                    delete maps[otherId];
                }
            }
        });

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

    // Fonction pour la carte modale (grande carte)
    function initModalMapButtons() {
        console.log('Initializing modal map buttons');
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                const modalId = this.getAttribute('data-bs-target').substring(1);
                const eventId = modalId.split('-')[1]; // Extract the event ID from the modal ID
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lon = parseFloat(this.getAttribute('data-lon'));
                const eventName = this.getAttribute('data-event-name');
                const displayAddress = this.getAttribute('data-display-address');
                const hasError = this.getAttribute('data-has-error') === 'true';
                const errorMessage = this.getAttribute('data-error-message');

                console.log(`Modal map for ${modalId}: lat=${lat}, lon=${lon}, hasError=${hasError}`);
                
                // Force modal to appear on top when clicked
                const modalElement = document.getElementById(modalId);
                if (modalElement) {
                    // Apply these styles directly when the button is clicked
                    modalElement.style.zIndex = '2000';
                }
                
                // Make sure backdrop doesn't cover the modal
                document.addEventListener('shown.bs.modal', function() {
                    // Fix backdrop z-index
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => {
                        backdrop.style.zIndex = '1999';
                    });
                    
                    // Fix modal positioning and z-index
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.style.zIndex = '2000';
                        modal.style.display = 'block';
                    }
                });

                // Attendre que la modale soit compltement ouverte
                document.getElementById(modalId).addEventListener('shown.bs.modal', function() {
                    // Fix backdrop positioning
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.style.position = 'fixed';
                        backdrop.style.top = '0';
                        backdrop.style.left = '0';
                        backdrop.style.width = '100%';
                        backdrop.style.height = '100%';
                        backdrop.style.zIndex = '9998';
                    }
                    
                    const modalMapElement = document.getElementById(`modal-map-${eventId}`);
                    
                    // Fix the modal and backdrop one more time
                    this.style.zIndex = '2000';
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => {
                        backdrop.style.zIndex = '1999';
                    });
                    
                    // Make sure the modal dialog is centered
                    const modalDialog = this.querySelector('.modal-dialog');
                    if (modalDialog) {
                        modalDialog.style.display = 'flex';
                        modalDialog.style.alignItems = 'center';
                        modalDialog.style.justifyContent = 'center';
                        modalDialog.style.height = '100%';
                    }
                    
                    if (!hasError) {
                        if (!modalMaps[eventId]) {
                            const modalMap = L.map(`modal-map-${eventId}`).setView([lat, lon], 13);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '  OpenStreetMap contributors'
                            }).addTo(modalMap);
                            
                            const marker = L.marker([lat, lon]).addTo(modalMap);
                            marker.bindPopup(`<b>${eventName}</b><br>${displayAddress}`).openPopup();
                            
                            modalMaps[eventId] = modalMap;
                            
                            // Force the map to render correctly
                            setTimeout(() => {
                                modalMap.invalidateSize();
                            }, 100);
                        } else {
                            modalMaps[eventId].invalidateSize();
                        }
                    } else {
                        modalMapElement.innerHTML = `<div class="alert alert-warning">${errorMessage || 'Impossible de localiser cette adresse sur la carte.'}</div>`;
                    }
                });

                // Nettoyer la carte lorsque la modale est fermée
                document.getElementById(modalId).addEventListener('hidden.bs.modal', function() {
                    if (modalMaps[eventId]) {
                        modalMaps[eventId].remove();
                        delete modalMaps[eventId];
                    }
                });
            });
        });
    }
    
    // Initialize all maps when the document is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded, initializing maps');
        initModalMapButtons();
        
        // Fix for calendar events making the page unscrollable
        document.addEventListener('click', function(e) {
            // Check if the click target is a calendar event or inside a calendar event
            if (e.target.closest('.fc-event') || e.target.classList.contains('fc-event')) {
                console.log('Calendar event clicked, ensuring scrolling still works');
                // Prevent any default behaviors that might interfere with scrolling
                e.preventDefault();
                
                // Make sure body overflow is not hidden
                setTimeout(function() {
                    document.body.style.overflow = '';
                    document.documentElement.style.overflow = '';
                    
                    // Re-enable pointer events on the entire document
                    document.body.style.pointerEvents = '';
                    
                    // Ensure all modals are properly handled
                    document.querySelectorAll('.modal').forEach(modal => {
                        if (!modal.classList.contains('show')) {
                            modal.style.display = 'none';
                        }
                    });
                    
                    // Remove any leftover modal backdrops
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                        if (!document.querySelector('.modal.show')) {
                            backdrop.remove();
                        }
                    });
                }, 300);
                
                return false;
            }
        }, true);
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

<!-- Scripts for navbar functionality -->
<!-- Bootstrap core JavaScript -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- Additional JavaScript Files -->
<script src="assets/js/isotope.min.js"></script>
<script src="assets/js/owl-carousel.js"></script>
<script src="assets/js/tabs.js"></script>
<script src="assets/js/popup.js"></script>
<script src="assets/js/custom.js"></script>

<!-- Particles.js for stars animation -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script>
// Initialize particles.js (stars animation) for the events section
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('particles-js-events')) {
        particlesJS('particles-js-events', {
            "particles": {
                "number": {
                    "value": 100,
                    "density": {
                        "enable": true,
                        "value_area": 800
                    }
                },
                "color": {
                    "value": "#ffffff"
                },
                "shape": {
                    "type": "circle",
                    "stroke": {
                        "width": 0,
                        "color": "#000000"
                    },
                    "polygon": {
                        "nb_sides": 5
                    }
                },
                "opacity": {
                    "value": 0.5,
                    "random": true,
                    "anim": {
                        "enable": true,
                        "speed": 1,
                        "opacity_min": 0.1,
                        "sync": false
                    }
                },
                "size": {
                    "value": 3,
                    "random": true,
                    "anim": {
                        "enable": true,
                        "speed": 2,
                        "size_min": 0.1,
                        "sync": false
                    }
                },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": "#7453fc",
                    "opacity": 0.2,
                    "width": 1
                },
                "move": {
                    "enable": true,
                    "speed": 1,
                    "direction": "none",
                    "random": true,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false,
                    "attract": {
                        "enable": false,
                        "rotateX": 600,
                        "rotateY": 600
                    }
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": {
                        "enable": true,
                        "mode": "bubble"
                    },
                    "onclick": {
                        "enable": true,
                        "mode": "push"
                    },
                    "resize": true
                },
                "modes": {
                    "grab": {
                        "distance": 400,
                        "line_linked": {
                            "opacity": 1
                        }
                    },
                    "bubble": {
                        "distance": 200,
                        "size": 4,
                        "duration": 2,
                        "opacity": 0.8,
                        "speed": 3
                    },
                    "repulse": {
                        "distance": 200,
                        "duration": 0.4
                    },
                    "push": {
                        "particles_nb": 4
                    },
                    "remove": {
                        "particles_nb": 2
                    }
                }
            },
            "retina_detect": true
        });
    }
});
    // Fix modal positioning for map modals
    document.addEventListener('DOMContentLoaded', function() {
        // Apply special styling to map modals
        const mapModals = document.querySelectorAll('[id^="largeMapModal-"]');
        mapModals.forEach(modal => {
            // Set up a listener for when the modal is about to be shown
            modal.addEventListener('show.bs.modal', function() {
                // Add custom styling for the modal
                this.style.zIndex = '9999';
                
                // Get the modal dialog
                const modalDialog = this.querySelector('.modal-dialog');
                if (modalDialog) {
                    modalDialog.style.position = 'fixed';
                    modalDialog.style.top = '50%';
                    modalDialog.style.left = '50%';
                    modalDialog.style.transform = 'translate(-50%, -50%)';
                    modalDialog.style.margin = '0';
                    modalDialog.style.maxWidth = '800px';
                    modalDialog.style.width = '90%';
                }
                
                // Get the modal content
                const modalContent = this.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.style.boxShadow = '0 0 40px rgba(255, 0, 85, 0.8)';
                    modalContent.style.border = '2px solid rgba(255, 0, 85, 0.5)';
                    modalContent.style.borderRadius = '15px';
                }
            });
        });
    });
</script>
</body>

</html>