<?php
// Activation des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclusion du contrôleur
require_once 'C:/xampp/htdocs/projetwebCRUD - ranim/Controller/concertController.php';

// Récupération des données
try {
    $concerts = getConcert();
    
    if (!is_array($concerts)) {
        throw new Exception("Aucun concert trouvé ou erreur de récupération");
    }
} catch (Exception $e) {
    $errorMessage = "Désolé, nous rencontrons un problème technique. Veuillez réessayer plus tard.";
    error_log("Erreur events.php: " . $e->getMessage());
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
    <style>
        :root {
            --primary-color: #FF0055;
            --secondary-color: #00F0FF;
            --dark-color: #0F0F1B;
        }
        
        .main-banner {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/images/concert-bg.jpg');
            background-size: cover;
            padding: 100px 0;
            color: white;
            text-align: center;
        }
        
        .main-banner h2 {
            font-size: 2.5rem;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        
        .main-banner h6 {
            color: var(--secondary-color);
            font-size: 1rem;
            letter-spacing: 2px;
        }
        
        .buttons .border-button,
        .buttons .main-button {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            margin: 0 10px;
            display: inline-block;
        }
        
        .buttons .border-button {
            border: 2px solid white;
            color: white;
            background: transparent;
        }
        
        .buttons .main-button {
            background: var(--primary-color);
            color: white;
            border: none;
        }
        
        .concert-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: none;
        }
        
        .concert-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .concert-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        
        .badge-genre {
            background-color: var(--primary-color);
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 10px;
            color: white;
        }
        
        .filter-btn {
            border-radius: 20px;
            margin: 0 5px 10px 5px;
        }
        
        .section-heading {
            margin-bottom: 50px;
        }
        
        .section-heading h2 {
            font-weight: 700;
        }
        
        .section-heading em {
            color: var(--primary-color);
            font-style: normal;
        }
        
        .line-dec {
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
            margin: 0 auto 30px;
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

<!-- Main Banner (NEWS FLASH Section) -->
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
        
        <!-- Filtres -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="text-center">
                    <button class="btn btn-outline-primary filter-btn active" data-filter="all">Tous</button>
                    <button class="btn btn-outline-primary filter-btn" data-filter="rock">Rock</button>
                    <button class="btn btn-outline-primary filter-btn" data-filter="pop">Pop</button>
                    <button class="btn btn-outline-primary filter-btn" data-filter="hiphop">Hip-Hop</button>
                    <button class="btn btn-outline-primary filter-btn" data-filter="electronic">Électronique</button>
                </div>
            </div>
        </div>
        
        <!-- Liste des concerts -->
        <div class="row" id="concerts-container">
            <?php if (isset($errorMessage)): ?>
                <div class="col-12">
                    <div class="alert alert-danger text-center"><?= htmlspecialchars($errorMessage) ?></div>
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
                    $genreClass = strtolower(preg_replace('/[^a-z0-9]+/', '-', $genre));
                    ?>
                    
                    <div class="col-lg-4 col-md-6 concert-item" data-genre="<?= $genreClass ?>">
                        <div class="card h-100 concert-card">
                            <img src="<?= $imagePath ?>" class="card-img-top concert-img" alt="Concert <?= $genre ?>">
                            <div class="card-body">
                                <span class="badge-genre"><?= $genre ?></span>
                                <h5 class="card-title"><?= $lieu ?></h5>
                                <p class="text-muted"><i class="fas fa-calendar-alt me-2"></i><?= $date ?></p>
                                <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i><?= $adresse ?></p>
                                <div class="d-flex justify-content-between mb-3">
                                    <span><i class="fas fa-ticket-alt me-2"></i><?= $places ?> places</span>
                                    <span class="fw-bold"><?= $prix ?> €</span>
                                </div>
                                <a href="reservation.php?id=<?= $id ?>" class="btn btn-primary w-100">Réserver</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<footer class="bg-dark text-white py-4">
    <div class="container text-center">
        <p>&copy; <?= date('Y') ?> Live The Music. Tous droits réservés.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.filter-btn').click(function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        const filter = $(this).data('filter');
        
        if (filter === 'all') {
            $('.concert-item').fadeIn();
        } else {
            $('.concert-item').hide();
            $(`.concert-item[data-genre="${filter}"]`).fadeIn();
        }
    });
});
</script>
<?php
// Activation des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclusion du contrôleur
require_once 'C:/xampp/htdocs/projetwebCRUD - ranim/Controller/concertController.php';

// Récupération des données
try {
    $concerts = getConcert();
    if (!is_array($concerts)) throw new Exception("Erreur de récupération");
} catch (Exception $e) {
    $errorMessage = "Problème technique. Réessayez plus tard.";
}

$lieux = getLieux();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live The Music - Événements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --red: #FF0033;
            --dark: #0A0A0A;
            --gray: #1A1A1A;
            --white: #FFF;
        }
        body {
            background-color: var(--dark);
            color: var(--white);
            font-family: 'Poppins', sans-serif;
        }
        .main-banner {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), url('assets/images/concert-bg.jpg');
            background-size: cover;
            padding: 100px 0;
            text-align: center;
        }
        .main-banner h2 {
            font-weight: bold;
            font-size: 2.8rem;
            color: white;
        }
        .main-banner h6 {
            color: var(--red);
            letter-spacing: 2px;
            font-weight: bold;
        }
        .btn-main, .btn-outline {
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 600;
        }
        .btn-main {
            background-color: var(--red);
            border: none;
            color: white;
        }
        .btn-outline {
            border: 2px solid white;
            color: white;
            background: transparent;
        }
        .concert-card {
            background: var(--gray);
            border: none;
            transition: 0.3s;
        }
        .concert-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 20px var(--red);
        }
        .badge-genre {
            background: var(--red);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        footer {
            background: var(--gray);
            padding: 20px;
            color: white;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand text-danger fw-bold" href="index.php">Live The Music</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link active" href="events.php">Événements</a></li>
                <li class="nav-item"><a class="nav-link" href="artists.php">Artistes</a></li>
                <li class="nav-item"><a class="nav-link" href="tickets.php">Billets</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- BANNIÈRE -->
<div class="main-banner">
    <div class="container">
        <h6>NEWS FLASH</h6>
        <h2>DON'T MISS OUT THE UPCOMING CONCERTS</h2>
        <div class="mt-4">
            <a href="#concerts-container" class="btn btn-outline me-2">Explore Concerts</a>
            <a href="tickets.php" class="btn btn-main">Buy Your Ticket</a>
        </div>
    </div>
</div>

<!-- CARROUSEL -->
<div id="carouselExampleFade" class="carousel slide carousel-fade my-5" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="assets/images/concert1.jpg" class="d-block w-100" style="height:500px; object-fit:cover;">
        </div>
        <div class="carousel-item">
            <img src="assets/images/concert2.jpg" class="d-block w-100" style="height:500px; object-fit:cover;">
        </div>
        <div class="carousel-item">
            <img src="assets/images/concert3.jpg" class="d-block w-100" style="height:500px; object-fit:cover;">
        </div>
    </div>
</div>

<!-- LISTE DES CONCERTS -->
<div class="container mb-5" id="concerts-container">
    <h2 class="text-center mb-4"><span style="color:var(--red)">Upcoming</span> Concerts</h2>
    <div class="row">
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($errorMessage) ?></div>
        <?php else: ?>
            <?php foreach ($concerts as $concert): 
                $id = htmlspecialchars($concert['id_concert']);
                $genre = htmlspecialchars($concert['genre']);
                $date = !empty($concert['date_concert']) ? date('d/m/Y', strtotime($concert['date_concert'])) : 'À venir';
                $lieu = htmlspecialchars($concert['nom_lieux'] ?? 'Lieu non spécifié');
                $adresse = htmlspecialchars($concert['adresse'] ?? '');
                $prix = htmlspecialchars($concert['prix_concert']);
                $places = htmlspecialchars($concert['place_dispo']);
                $image = !empty($concert['image']) ? '/projetwebCRUD - ranim/View/BackOffice/baccii/bacci/startbootstrap-sb-admin-gh-pages/' . htmlspecialchars($concert['image']) : 'assets/images/default-concert.jpg';
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card concert-card h-100">
                        <img src="<?= $image ?>" class="card-img-top" alt="Concert">
                        <div class="card-body">
                            <span class="badge-genre"><?= $genre ?></span>
                            <h5 class="card-title mt-2"><?= $lieu ?></h5>
                            <p><i class="fas fa-calendar"></i> <?= $date ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?= $adresse ?></p>
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-ticket-alt"></i> <?= $places ?> places</span>
                                <strong><?= $prix ?> €</strong>
                            </div>
                            <a href="reservation.php?id=<?= $id ?>" class="btn btn-main mt-3 w-100">Réserver</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- PIED DE PAGE -->
<footer class="text-center">
    <div class="container">
        <p>&copy; <?= date("Y") ?> Live The Music. Tous droits réservés.</p>
    </div>
</footer>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const genre = btn.dataset.filter;
        document.querySelectorAll('.concert-item').forEach(item => {
            item.style.display = genre === 'all' || item.dataset.genre === genre ? 'block' : 'none';
        });
    });
});
</script>
</body>
</html>


</body>
</html>