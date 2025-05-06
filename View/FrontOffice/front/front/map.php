<?php
// Activation des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Récupérer les informations du lieu passées via GET
$nomLieu = isset($_GET['nom']) ? htmlspecialchars(trim($_GET['nom'])) : 'Lieu inconnu';
$adresse = isset($_GET['adresse']) ? htmlspecialchars(trim($_GET['adresse'])) : 'Adresse non spécifiée';

// Nettoyer l'adresse : supprimer les espaces multiples
$adresse = preg_replace('/\s+/', ' ', $adresse);

// Fonction pour géocoder l'adresse avec Nominatim
function geocodeAddress($address) {
    // Liste des villes tunisiennes pour identifier les lieux
    $tunisianCities = ['Tunis', 'Jendouba', 'Sfax', 'Sousse', 'Monastir', 'Bizerte', 'Ariana'];
    $containsTunisianCity = false;
    foreach ($tunisianCities as $city) {
        if (stripos($address, $city) !== false) {
            $containsTunisianCity = true;
            break;
        }
    }
    // Cas spécifiques pour certaines villes
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
    // Ajouter ", Tunisie" uniquement si l'adresse semble être une ville tunisienne et ne contient pas déjà un pays
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
        return ['error' => 'Erreur de connexion à Nominatim. Vérifiez votre connexion Internet.'];
    }
    
    $data = json_decode($response, true);
    error_log("Réponse Nominatim pour adresse '$address': " . print_r($data, true));
    
    if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
        $displayName = isset($data[0]['display_name']) ? $data[0]['display_name'] : $address;
        // Assouplir la validation : accepter les coordonnées même si "Tunisia" n'est pas dans le display_name
        // Vérifier uniquement si l'adresse d'entrée contient déjà "Tunisie" ou "Tunisia"
        if ($containsTunisianCity && !preg_match('/(Tunisia|Tunisie)/i', $address)) {
            $suggestedAddress = preg_match('/,\s*(Tunisie|Tunisia)/i', $address) ? $address : $address . ', Tunisie';
            return ['error' => 'L\'adresse ne semble pas être en Tunisie. Essayez "' . $suggestedAddress . '".'];
        }
        // Vérifier si l'adresse contient "USA" ou "United States" pour New York
        if (stripos($address, 'New York') !== false && !preg_match('/(USA|United States)/i', $address)) {
            return ['error' => 'L\'adresse ne semble pas être aux États-Unis. Essayez "New York, USA".'];
        }
        return [
            'latitude' => floatval($data[0]['lat']),
            'longitude' => floatval($data[0]['lon']),
            'display_name' => $displayName
        ];
    }
    
    // Fallbacks pour les villes tunisiennes
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
    
    return ['error' => 'Adresse non trouvée. Essayez d\'inclure la ville et le pays (ex. "Sfax, Tunisie").'];
}

// Obtenir les coordonnées
$result = geocodeAddress($adresse);
if (isset($result['error'])) {
    $geocodeError = $result['error'];
    $latitude = 36.8065; // Par défaut : Tunis, Tunisie
    $longitude = 10.1815;
    $displayAddress = $adresse;
} else {
    $geocodeError = false;
    $latitude = $result['latitude'];
    $longitude = $result['longitude'];
    $displayAddress = $result['display_name'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Localisation - <?php echo $nomLieu; ?> | Live The Music</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #DC2626; /* Rouge vif */
            --secondary-color: #F87171; /* Rouge atténué */
            --dark-bg: #0A0A0A; /* Noir profond */
            --card-bg: #1F1F1F; /* Gris foncé pour les cartes */
            --text-light: #F5F5F5; /* Gris clair */
            --text-muted: #B0B8C4; /* Gris atténué plus clair */
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
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .navbar-brand img {
            height: 45px;
            transition: transform 0.3s ease;
        }

        .navbar-brand img:hover {
            transform: rotate(5deg) scale(1.05);
        }

        .navbar-nav .nav-link {
            color: var(--text-muted);
            font-weight: 500;
            margin: 0 15px;
            padding: 8px 15px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary-color);
            background-color: rgba(220, 38, 38, 0.1);
            box-shadow: 0 0 10px rgba(220, 38, 38, 0.3);
        }

        .map-container {
            padding: 50px 0;
            max-width: 1200px;
            margin: 0 auto;
        }

        .map-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .map-header h2 {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-light);
            text-shadow: 0 0 10px rgba(220, 38, 38, 0.3);
        }

        .map-header p {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin: 0;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .map-header p i {
            color: var(--primary-color);
            margin-right: 10px;
        }

        #map {
            height: 500px;
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.2);
            margin-bottom: 30px;
            transition: all 0.3s ease;
            border: 1px solid var(--primary-color);
        }

        .leaflet-popup-content-wrapper {
            background: var(--card-bg);
            color: var(--text-light);
            border-radius: 10px;
            border: 1px solid var(--primary-color);
            box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3);
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

        .btn-back, .btn-search {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 30px;
            padding: 12px 25px;
            color: var(--text-light);
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-back:hover, .btn-search:hover {
            background: linear-gradient(45deg, #991B1B, #EF4444);
            box-shadow: 0 0 15px rgba(220, 38, 38, 0.5);
            transform: translateY(-3px);
        }

        .search-container {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
        }

        .search-container input {
            flex: 1;
            padding: 12px 20px;
            border: 1px solid var(--primary-color);
            border-radius: 30px;
            background-color: var(--card-bg);
            color: var(--text-light);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-container input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 10px rgba(248, 113, 113, 0.3);
        }

        #suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: var(--card-bg);
            border: 1px solid var(--primary-color);
            border-radius: 10px;
            margin-top: 5px;
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
            display: none;
        }

        .suggestion-item {
            padding: 10px 20px;
            color: var(--text-light);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .suggestion-item:hover {
            background-color: rgba(220, 38, 38, 0.2);
        }

        .alert {
            border-radius: 15px;
            margin-bottom: 30px;
            font-size: 1rem;
            text-align: center;
            background-color: rgba(220, 38, 38, 0.1);
            border: 1px solid var(--primary-color);
            color: var(--text-light);
        }

        .alert-warning {
            background-color: rgba(248, 113, 113, 0.1);
            border-color: var(--secondary-color);
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
            .map-container {
                padding: 30px 15px;
            }

            .map-header h2 {
                font-size: 1.8rem;
            }

            #map {
                height: 350px;
            }

            .search-container {
                flex-direction: column;
            }

            .btn-back, .btn-search {
                width: 100%;
                justify-content: center;
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

<!-- Map Section -->
<div class="container map-container">
    <div class="map-header">
        <h2>Localisation : <?php echo $nomLieu; ?></h2>
        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($displayAddress); ?></p>
    </div>

    <div class="search-container">
        <div style="position: relative; flex: 1;">
            <input type="text" id="search-address" placeholder="Précisez l'adresse (ex. Sfax, Tunisie)" value="<?php echo htmlspecialchars($adresse); ?>">
            <div id="suggestions"></div>
        </div>
        <button class="btn-search"><i class="fas fa-search"></i> Rechercher</button>
    </div>

    <a href="events.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour aux événements</a>

    <?php if ($geocodeError): ?>
        <div class="alert alert-warning">
            <?php echo htmlspecialchars($geocodeError); ?>
        </div>
    <?php endif; ?>

    <div id="map"></div>
</div>

<!-- Footer -->
<footer class="text-white">
    <div class="container text-center">
        <p>© <?php echo date('Y'); ?> Live The Music. Tous droits réservés.</p>
    </div>
</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialiser la carte
    const map = L.map('map').setView([<?php echo $latitude; ?>, <?php echo $longitude; ?>], 8); // Zoom ajusté à 8
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Ajouter un marqueur si la géolocalisation a réussi
    <?php if (!$geocodeError): ?>
        const marker = L.marker([<?php echo $latitude; ?>, <?php echo $longitude; ?>])
            .addTo(map)
            .bindPopup('<b><?php echo addslashes($nomLieu); ?></b><br><?php echo addslashes($displayAddress); ?>')
            .openPopup();
    <?php endif; ?>

    // Liste des villes pour les suggestions
    const cities = [
        'Tunis, Tunisie',
        'Jendouba, Tunisie',
        'Sfax, Tunisie',
        'Sousse, Tunisie',
        'Ariana, Tunisie',
        'New York, USA'
    ];

    const searchInput = document.getElementById('search-address');
    const suggestionsDiv = document.getElementById('suggestions');

    // Afficher des suggestions pendant la saisie
    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();
        suggestionsDiv.innerHTML = '';
        if (!query) {
            suggestionsDiv.style.display = 'none';
            return;
        }

        const filteredCities = cities.filter(city => city.toLowerCase().includes(query));
        if (filteredCities.length > 0) {
            filteredCities.forEach(city => {
                const suggestionItem = document.createElement('div');
                suggestionItem.className = 'suggestion-item';
                suggestionItem.textContent = city;
                suggestionItem.addEventListener('click', () => {
                    searchInput.value = city;
                    suggestionsDiv.innerHTML = '';
                    suggestionsDiv.style.display = 'none';
                    document.querySelector('.btn-search').click();
                });
                suggestionsDiv.appendChild(suggestionItem);
            });
            suggestionsDiv.style.display = 'block';
        } else {
            suggestionsDiv.style.display = 'none';
        }
    });

    // Cacher les suggestions quand on clique ailleurs
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.style.display = 'none';
        }
    });

    // Fonction pour rechercher une nouvelle adresse
    document.querySelector('.btn-search').addEventListener('click', () => {
        const address = searchInput.value.trim();
        if (!address) {
            alert('Veuillez entrer une adresse valide.');
            return;
        }

        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1&addressdetails=1`, {
            headers: { 'User-Agent': 'LiveTheMusic/1.0' }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Réponse Nominatim (recherche manuelle):', data);
            if (data && data[0] && data[0].lat && data[0].lon) {
                const lat = parseFloat(data[0].lat);
                const lon = parseFloat(data[0].lon);
                const displayName = data[0].display_name || address;

                // Vérifier si l'adresse contient "Tunisia" ou "Tunisie" pour les villes tunisiennes
                const tunisianCities = ['Tunis', 'Jendouba', 'Sfax', 'Sousse', 'Monastir', 'Bizerte', 'Ariana'];
                const isTunisianCity = tunisianCities.some(city => address.toLowerCase().includes(city.toLowerCase()));
                if (isTunisianCity && !/Tunisia|Tunisie/i.test(address)) {
                    document.querySelector('.alert')?.remove();
                    const suggestedAddress = /,\s*(Tunisie|Tunisia)/i.test(address) ? address : address + ', Tunisie';
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-warning';
                    alertDiv.textContent = 'L\'adresse ne semble pas être en Tunisie. Essayez "' + suggestedAddress + '".';
                    document.querySelector('.search-container').after(alertDiv);
                    return;
                }
                // Vérifier si l'adresse contient "USA" ou "United States" pour New York
                if (address.toLowerCase().includes('new york') && !/USA|United States/i.test(address)) {
                    document.querySelector('.alert')?.remove();
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-warning';
                    alertDiv.textContent = 'L\'adresse ne semble pas être aux États-Unis. Essayez "New York, USA".';
                    document.querySelector('.search-container').after(alertDiv);
                    return;
                }

                map.eachLayer(layer => {
                    if (layer instanceof L.Marker) map.removeLayer(layer);
                });
                const marker = L.marker([lat, lon])
                    .addTo(map)
                    .bindPopup(`<b><?php echo addslashes($nomLieu); ?></b><br>${displayName}`)
                    .openPopup();
                map.setView([lat, lon], 8); // Zoom ajusté à 8

                // Mettre à jour l'affichage de l'adresse
                document.querySelector('.map-header p').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${displayName}`;
                document.querySelector('.alert')?.remove();
            } else {
                // Fallbacks pour les villes tunisiennes
                const fallbacks = {
                    'jendouba': { lat: 36.5011, lon: 8.7803, display_name: 'Jendouba, Tunisie' },
                    'tunis': { lat: 36.8065, lon: 10.1815, display_name: 'Tunis, Tunisie' },
                    'sfax': { lat: 34.7406, lon: 10.7603, display_name: 'Sfax, Tunisie' },
                    'sousse': { lat: 35.8256, lon: 10.6412, display_name: 'Sousse, Tunisie' },
                    'ariana': { lat: 36.8601, lon: 10.1934, display_name: 'Ariana, Tunisie' }
                };

                let foundFallback = false;
                for (const [city, coords] of Object.entries(fallbacks)) {
                    if (address.toLowerCase().includes(city)) {
                        console.log(`Fallback utilisé pour ${city}: ${coords.lat}, ${coords.lon}`);
                        map.eachLayer(layer => {
                            if (layer instanceof L.Marker) map.removeLayer(layer);
                        });
                        const marker = L.marker([coords.lat, coords.lon])
                            .addTo(map)
                            .bindPopup(`<b><?php echo addslashes($nomLieu); ?></b><br>${coords.display_name}`)
                            .openPopup();
                        map.setView([coords.lat, coords.lon], 8); // Zoom ajusté à 8

                        document.querySelector('.map-header p').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${coords.display_name}`;
                        document.querySelector('.alert')?.remove();
                        foundFallback = true;
                        break;
                    }
                }

                if (!foundFallback) {
                    document.querySelector('.alert')?.remove();
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-warning';
                    alertDiv.textContent = 'Adresse non trouvée. Essayez d\'inclure la ville et le pays (ex. "Sfax, Tunisie").';
                    document.querySelector('.search-container').after(alertDiv);
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors de la recherche:', error);
            document.querySelector('.alert')?.remove();
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning';
            alertDiv.textContent = 'Erreur de connexion. Vérifiez votre connexion Internet.';
            document.querySelector('.search-container').after(alertDiv);
        });
    });

    // Permettre la recherche avec la touche Entrée
    searchInput.addEventListener('keypress', e => {
        if (e.key === 'Enter') document.querySelector('.btn-search').click();
    });
</script>
</body>
</html>