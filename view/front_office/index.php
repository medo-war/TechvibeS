<?php
session_start();
$captchaVerified = isset($_SESSION['captcha_verified']) && $_SESSION['captcha_verified'] === true;
// Redirigez vers la page de connexion si non connecté
if (!isset($_SESSION['user'])) {
    header('Location: /view/front_office/welcome.php');
    exit();
}
$captchaVerified = false;
if (isset($_SESSION['captcha_verified'], $_SESSION['captcha_time'])) {
    // Valide pendant 1 heure (3600 secondes)
    $captchaVerified = ($_SESSION['captcha_verified'] === true) && 
                      (time() - $_SESSION['captcha_time'] < 3600);
}

// Si CAPTCHA validé, afficher le site directement
if ($captchaVerified) {
    echo '<style>#captchaOverlay { display: none !important; }</style>';
    echo '<style>div[style*="display: none"] { display: block !important; }</style>';
}
// Récupérez les infos utilisateur
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="author" content="templatemo">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <title>LIVE THE MUSIC</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-liberty-market.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <!-- Partner With Us Section CSS -->
    <link rel="stylesheet" href="assets/css/partner.css">
<!--

TemplateMo 577 Liberty Market

https://templatemo.com/tm-577-liberty-market

-->
  </head>
  <style>
        /* Style néon rouge pour l'icône de profil */
        .user-profile {
            position: relative;
            display: inline-block;
            margin-left: 30px;
            z-index: 1000;
        }

        .profile-icon {
            cursor: pointer;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(20, 0, 0, 0.8);
            border: 1px solid #ff073a;
            box-shadow: 0 0 10px #ff073a, 0 0 20px #ff073a, 0 0 30px #ff073a;
            transition: all 0.3s ease;
            animation: neonPulseRed 1.5s infinite alternate;
        }

        .profile-icon:hover {
            box-shadow: 0 0 15px #ff073a, 0 0 30px #ff073a, 0 0 45px #ff073a;
            transform: scale(1.1);
        }

        .profile-icon img {
            width: 80%;
            height: 80%;
            border-radius: 50%;
            object-fit: cover;
            filter: drop-shadow(0 0 8px #ff073a);
        }

        /* Menu déroulant néon rouge */
        .profile-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 65px;
            background: rgba(10, 0, 0, 0.9);
            min-width: 200px;
            border-radius: 5px;
            border: 1px solid #ff073a;
            box-shadow: 0 0 20px #ff073a;
            z-index: 1001;
            overflow: hidden;
            backdrop-filter: blur(5px);
        }

        .profile-dropdown a {
            color: #fff;
            padding: 12px 25px;
            text-decoration: none;
            display: block;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            letter-spacing: 1.5px;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 7, 58, 0.3);
            text-shadow: 0 0 8px #ff073a;
            position: relative;
            overflow: hidden;
        }

        .profile-dropdown a:before {
            content: '';
            position: absolute;
            left: -100%;
            top: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 7, 58, 0.2), transparent);
            transition: 0.5s;
        }

        .profile-dropdown a:hover {
            background: rgba(255, 7, 58, 0.15);
            color: #ff5c8a;
            padding-left: 30px;
        }

        .profile-dropdown a:hover:before {
            left: 100%;
        }

        .profile-dropdown a:last-child {
            border-bottom: none;
            color: #ff8fa3;
        }

        /* Animations néon rouge */
        @keyframes neonPulseRed {
            0% { box-shadow: 0 0 5px #ff073a, 0 0 10px #ff073a; }
            100% { box-shadow: 0 0 15px #ff073a, 0 0 30px #ff073a, 0 0 45px #ff073a; }
        }

        @keyframes neonFlicker {
            0%, 19%, 21%, 23%, 25%, 54%, 56%, 100% { opacity: 1; }
            20%, 22%, 24%, 55% { opacity: 0.7; }
        }

        .profile-icon {
            animation: neonPulseRed 2s infinite alternate, neonFlicker 3s infinite;
        }

        /* Animations menu */
        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes dropdownFadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-15px); }
        }
        /* Style pour le CAPTCHA */
/* Overlay style */
#captchaOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 20, 0.95); /* Fond bleu nuit */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    backdrop-filter: blur(5px);
}

/* Boîte CAPTCHA */
.captcha-box {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    padding: 2.5rem;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 0 30px rgba(100, 65, 165, 0.6); /* Violet néon */
    border: 1px solid #4a00e0;
    max-width: 450px;
    width: 90%;
}

/* Titre */
.captcha-box h3 {
    color: #f8f8f8;
    font-size: 1.8rem;
    margin-bottom: 1rem;
    text-shadow: 0 0 10px #8e2de2;
    font-weight: 700;
}

/* Instructions */
.captcha-box p {
    color: #a1a1ff;
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

/* Élément à glisser (note de musique) */
#captchaElement {
    cursor: grab;
    font-size: 3rem;
    color: #ff4da6; /* Rose électrique */
    text-shadow: 0 0 15px #ff4da6;
    margin: 1.5rem 0;
    transition: all 0.3s ease;
    position: relative;
    display: inline-block;
}

#captchaElement:active {
    cursor: grabbing;
    transform: scale(1.2);
}

/* Zone cible (enceinte musicale) */
#captchaTarget {
    width: 100px;
    height: 100px;
    border: 2px dashed #8e2de2;
    border-radius: 50%;
    margin: 1.5rem auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(30, 30, 60, 0.7);
    position: relative;
    overflow: hidden;
    box-shadow: inset 0 0 20px rgba(142, 45, 226, 0.5);
}

/* Animation de la zone cible */
#captchaTarget::before {
    content: "";
    position: absolute;
    width: 60%;
    height: 60%;
    background: radial-gradient(circle, #4a00e0 0%, transparent 70%);
    border-radius: 50%;
    animation: pulse 2s infinite alternate;
}

@keyframes pulse {
    0% { transform: scale(0.8); opacity: 0.6; }
    100% { transform: scale(1.1); opacity: 0.9; }
}

/* Message de validation */
#captchaTarget::after {
    content: "✓";
    position: absolute;
    color: #00ff88;
    font-size: 3rem;
    opacity: 0;
    transition: all 0.3s ease;
}

/* Style lorsque validé */
#captchaTarget.valid {
    border-color: #00ff88;
    background: rgba(0, 255, 136, 0.1);
}

#captchaTarget.valid::before {
    background: radial-gradient(circle, #00ff88 0%, transparent 70%);
}

#captchaTarget.valid::after {
    opacity: 1;
}

/* Note de bas de page */
.captcha-box small {
    display: block;
    color: #6a6a8e;
    font-size: 0.9rem;
    margin-top: 2rem;
    font-style: italic;
}

/* Effets sonores visuels */
.music-note {
    position: absolute;
    font-size: 1.5rem;
    opacity: 0;
    animation: floatNote 3s linear forwards;
}

@keyframes floatNote {
    0% { transform: translateY(0); opacity: 1; }
    100% { transform: translateY(-100px); opacity: 0; }
}
/* Animation des notes */
.music-notes {
    position: absolute;
    font-size: 2rem;
    animation: floatUp 1s ease-out forwards;
    opacity: 0;
}

@keyframes floatUp {
    0% { transform: translateY(0); opacity: 1; }
    100% { transform: translateY(-50px); opacity: 0; }
}

/* Style post-validation */
#captchaTarget.validated {
    background: rgba(0, 255, 136, 0.1);
    box-shadow: 0 0 20px rgba(0, 255, 136, 0.5);
}
/* Style des notes flottantes */
.music-note {
    position: absolute;
    font-size: 1.8rem;
    opacity: 0;
    pointer-events: none;
    animation: floatNote 2s ease-out forwards;
}

@keyframes floatNote {
    0% {
        transform: translateY(0) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(-100px) rotate(30deg);
        opacity: 0;
    }
}

/* Style pendant le drag */
.dragging-note {
    transform: scale(1.2);
    filter: drop-shadow(0 0 10px #ff4da6);
    transition: all 0.2s ease;
}
/* Notes de succès */
.success-note {
    position: absolute;
    font-size: 2rem;
    animation: 
        floatNote 1.5s ease-out forwards,
        pulse 0.5s ease-in-out infinite alternate;
}

@keyframes pulse {
    from { transform: scale(1); }
    to { transform: scale(1.2); }
}
/* Animation des notes */
.music-note {
    position: absolute;
    font-size: 2rem;
    opacity: 0;
    pointer-events: none;
    z-index: 100;
    animation: floatNote 1.5s ease-out forwards;
}

@keyframes floatNote {
    0% {
        transform: translateY(0) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(-100px) rotate(30deg);
        opacity: 0;
    }
}

/* Style de validation */
#captchaTarget.validating {
    background: rgba(0, 255, 136, 0.1);
    border-color: #00ff88 !important;
}
/* Note à glisser */
#captchaElement {
    cursor: grab;
    font-size: 3rem;
    margin: 1.5rem 0;
    transition: all 0.3s ease;
    display: inline-block;
    text-shadow: 0 0 10px currentColor;
}

#captchaElement:active {
    cursor: grabbing;
}

/* Notes flottantes */
.floating-note {
    position: fixed;
    font-size: 1.8rem;
    animation: floatNote 1.5s ease-out forwards;
    pointer-events: none;
    z-index: 100;
    transform: translate(0, 0);
}

@keyframes floatNote {
    to {
        transform: translate(
            calc(var(--x) * 100px),
            calc(var(--y) * 100px)
        );
        opacity: 0;
    }
}
.random-game-container {
    opacity: 0;
    transition: opacity 0.5s ease;
    background: linear-gradient(to right, #0f0f1f, #1a1a2e);
    padding: 40px 0;
    border-radius: 15px;
    margin: 30px 0;
    box-shadow: 0 0 30px rgba(142, 45, 226, 0.3);
}

.random-game-container h2 {
    color: #fff;
    text-align: center;
    margin-bottom: 30px;
}
#musicQuizPopup {
    transition: opacity 0.5s ease;
    opacity: 0;
}

#closeQuizBtn {
    transition: all 0.3s;
    font-size: 24px;
    line-height: 1;
}

#closeQuizBtn:hover {
    background: #ff4da6 !important;
    transform: scale(1.1);
}

.stage {
    background: rgba(20, 20, 40, 0.95);
    border-radius: 20px;
    padding: 30px;
    margin: 20px auto;
    max-width: 800px;
    box-shadow: 0 0 40px rgba(142, 45, 226, 0.5);
}
/* Style pour le popup du quiz */
#musicQuizPopup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.95);
    z-index: 9999;
    display: none;
    overflow-y: auto;
    opacity: 0;
    transition: opacity 0.5s ease;
}

#musicQuizPopup.show {
    opacity: 1;
    display: block;
}

#closeQuizBtn {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #ff073a;
    border: none;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    z-index: 10000;
    font-size: 24px;
    line-height: 1;
    transition: all 0.3s;
}

#closeQuizBtn:hover {
    background: #ff4da6 !important;
    transform: scale(1.1);
}
    
    </style>

<body>
<?php if (!$captchaVerified): ?>
<div id="captchaOverlay">
    <div class="captcha-box">
        <h3>Vérification de sécurité</h3>
        <p>Glissez la note musicale dans la zone pour continuer :</p>
        
        <div id="captchaElement" draggable="true">♪</div>
        <div id="captchaTarget">🎵</div>
        
        <small>Cette étape nous aide à lutter contre les robots.</small>
    </div>
</div>
<?php endif; ?>
<div style="display: <?= $captchaVerified ? 'block' : 'none' ?>;">
  <!-- ***** Preloader Start ***** -->
  <div id="js-preloader" class="js-preloader">
    <div class="preloader-inner">
      <span class="dot"></span>
      <div class="dots">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </div>
  <!-- ***** Preloader End ***** -->

  <?php include 'includes/navbar.php'; ?>

  <!-- ***** Main Banner Area Start ***** -->
  <div class="main-banner">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 align-self-center">
          <div class="header-text">
            <h6>NEWS FLASH</h6>
            <h2>DON'T MISS OUT THE UPCOMING CONCERTS !</h2>
            <p></p>
            <div class="buttons">
              <div class="border-button">
                <a href="explore.html">Explore Concerts</a>
              </div>
              <div class="main-button">
                <a href="https://youtube.com/templatemo" target="_blank">Buy your Ticket</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5 offset-lg-1">
          <div class="owl-banner owl-carousel">
            <div class="item">
              <img src="assets/images/banner-01.png" alt="">
            </div>
            <div class="item">
              <img src="assets/images/banner-02.png" alt="">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- ***** Main Banner Area End ***** -->
  
  <!-- ***** Partner With Us Section Start ***** -->
  <div class="become-partner">
    <!-- Particles.js Background (Stars animation) -->
    <div id="particles-js-index"></div>
    
    <!-- Blurred music logos background -->
    <div class="partner-music-logos">
      <div class="partner-music-logo partner-logo-1">
        <i class="fa fa-music"></i>
      </div>
      <div class="partner-music-logo partner-logo-2">
        <i class="fa fa-headphones"></i>
      </div>
      <div class="partner-music-logo partner-logo-3">
        <i class="fa fa-play-circle"></i>
      </div>
      <div class="partner-music-logo partner-logo-4">
        <i class="fa fa-microphone"></i>
      </div>
    </div>
    
    <!-- Neon glow circles -->
    <div class="partner-neon-circle partner-neon-circle-1"></div>
    <div class="partner-neon-circle partner-neon-circle-2"></div>
    
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="partner-content text-center">
            <h2>BECOME A PARTNER</h2>
            <p>Join our growing network of music industry partners and help us bring amazing experiences to music lovers worldwide. Get exclusive access to our platform and connect with artists and fans.</p>
            <div class="partner-buttons">
              <a href="partner.php" class="partner-button">Partner With Us</a>
              <a href="partners_directory.php" class="partner-button partner-button-secondary">Our Partners</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Equalizer bars -->
    <div class="partner-equalizer">
      <div class="partner-equalizer-bar"></div>
      <div class="partner-equalizer-bar"></div>
      <div class="partner-equalizer-bar"></div>
      <div class="partner-equalizer-bar"></div>
      <div class="partner-equalizer-bar"></div>
      <div class="partner-equalizer-bar"></div>
      <div class="partner-equalizer-bar"></div>
      <div class="partner-equalizer-bar"></div>
      <div class="partner-equalizer-bar"></div>
      <div class="partner-equalizer-bar"></div>
    </div>
  </div>
  <!-- ***** Partner With Us Section End ***** -->
  
  <div class="categories-collections">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="categories">
            <div class="row">
              <div class="col-lg-12">
                <div class="section-heading">
                  <div class="line-dec"></div>
                  <h2>Browse Through Our <em>Categories</em></h2>
                </div>
              </div>
              <div class="col-lg-2 col-sm-6">
                <div class="item">
                  <div class="icon">
                    <img src="assets/images/icon-01.png" alt="">
                  </div>
                  <h4>POP</h4>
                  <div class="icon-button">
                    <a href="#"><i class="fa fa-angle-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-sm-6">
                <div class="item">
                  <div class="icon">
                    <img src="assets/images/icon-02.png" alt="">
                  </div>
                  <h4>KPOP</h4>
                  <div class="icon-button">
                    <a href="#"><i class="fa fa-angle-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-sm-6">
                <div class="item">
                  <div class="icon">
                    <img src="assets/images/icon-03.png" alt="">
                  </div>
                  <h4>HIP-HOP</h4>
                  <div class="icon-button">
                    <a href="#"><i class="fa fa-angle-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-sm-6">
                <div class="item">
                  <div class="icon">
                    <img src="assets/images/icon-04.png" alt="">
                  </div>
                  <h4>ROCK</h4>
                  <div class="icon-button">
                    <a href="#"><i class="fa fa-angle-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-sm-6">
                <div class="item">
                  <div class="icon">
                    <img src="assets/images/icon-05.png" alt="">
                  </div>
                  <h4>LATIN</h4>
                  <div class="icon-button">
                    <a href="#"><i class="fa fa-angle-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-sm-6">
                <div class="item">
                  <div class="icon">
                    <img src="assets/images/icon-06.png" alt="">
                  </div>
                  <h4>SOUL</h4>
                  <div class="icon-button">
                    <a href="#"><i class="fa fa-angle-right"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="collections">
            <div class="row">
              <div class="col-lg-12">
                <div class="section-heading">
                  <div class="line-dec"></div>
                  <h2>Explore NEW <em>FESTIVALS</em> THIS MONTH.</h2>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="owl-collection owl-carousel">
                  <div class="item">
                    <img src="assets/images/collection-02.jpg" alt="">
                    <div class="down-content">
                      <h4>Burning Man</h4>
                      <span class="collection">Location:<br><strong>Black Rock Desert, Nevada</strong></span>
                      <span class="category">Category:<br><strong>Experimental, Electronic, Ambient</strong></span>
                      <div class="main-button">
                        <a href="explore.html">Explore</a>
                      </div>
                    </div>
                  </div>
                  <div class="item">
                    <img src="assets/images/collection-03.jpg" alt="">
                    <div class="down-content">
                      <h4>Ultra Music Festival</h4>
                      <span class="collection">Location:<br><strong>Miami(USA), Seoul(South Korea),..</strong></span>
                      <span class="category">Category:<br><strong>EDM, House, Techno, Dubstep</strong></span>
                      <div class="main-button">
                        <a href="explore.html">Explore</a>
                      </div>
                    </div>
                  </div>
                  <div class="item">
                    <img src="assets/images/collection-01.jpg" alt="">
                    <div class="down-content">
                      <h4>COACHELLA</h4>
                      <span class="collection">Location:<br><strong>Indio, California</strong></span>
                      <span class="category">Category:<br><strong>Pop, Rock, Hip-Hop, Electronic, Indie</strong></span>
                      <div class="main-button">
                        <a href="explore.html">Explore</a>
                      </div>
                    </div>
                  </div>
                  <div class="item">
                    <img src="assets/images/collection-04.jpg" alt="">
                    <div class="down-content">
                      <h4>Lollapalooza</h4>
                      <span class="collection">Location:<br><strong>Chicago (USA), Berlin (Germany), Paris (France),</strong></span>
                      <span class="category">Category:<br><strong>Rock, Hip-Hop, EDM, Pop</strong></span>
                      <div class="main-button">
                        <a href="explore.html">Explore</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div> 
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="create-nft">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <div class="section-heading">
            <div class="line-dec"></div>
            <h2>WITH "LIVE THE MUSIC" GO ANS SEE YOUR FAVOURITE ARTISTS ON STAGE!.</h2>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="main-button">
            <a href="create.html"></a>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="item first-item">
            <div class="number">
              <h6></h6>
            </div>
            <div class="icon">
              <img src="assets/images/icon-02.png" alt="">
            </div>
            <h4>TELL YOUR FRIENDS</h4>
            <p></p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="item second-item">
            <div class="number">
              <h6>2</h6>
            </div>
            <div class="icon">
              <img src="assets/images/icon-04.png" alt="">
            </div>
            <h4>            <h4>Set Up Your Wallet</h4>
          </h4>
            <p><a href="https://templatemo.com/page/1" target="_blank" rel="nofollow">website template</a></p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="item">
            <div class="icon">
              <img src="assets/images/icon-06.png" alt="">
            </div>
            <h4>LIVE THE MUSIC!</h4>
            <p><a rel="nofollow" href="https://templatemo.com/contact" target="_parent"></a> </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="currently-market">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <div class="section-heading">
            <div class="line-dec"></div>
            <h2><em>CONCERTS</em> Currently THIS MONTH.</h2>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="filters">
            <ul>
              <li data-filter="*"  class="active">All GENRES</li>
              <li data-filter=".msc">ROCK</li>
              <li data-filter=".dig">POP</li>
              <li data-filter=".blc">KPOP</li>
              <li data-filter=".vtr">SOUL</li>
            </ul>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="row grid">
            <div class="col-lg-6 currently-market-item all msc">
              <div class="item">
                <div class="left-image">
                  <img src="assets/images/market-01.jpg" alt="" style="border-radius: 20px; min-width: 195px;">
                </div>
                <div class="right-content">
                  <h4>LISA'S TOUR</h4>
                  <span class="author">
                    <img src="assets/images/author.jpg" alt="" style="max-width: 50px; border-radius: 50%;">
                    <h6>LISA<br><a href="#">@lalisamanoban</a></h6>
                  </span>
                  <div class="line-dec"></div>
                   <span class="bid">
                    FROM<br><strong>(July 13th, 2025)</strong><br><em>($1500)</em>
                  </span>
                  <span class="ends">
                    Ends In<br><strong>(July 24th, 2025)</strong><br><em></em>
                  </span>
                  <div class="text-button">
                    <a href="details.html">View places</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 currently-market-item all dig">
              <div class="item">
                <div class="left-image">
                  <img src="assets/images/market-02.jpg" alt="" style="border-radius: 20px; min-width: 195px;">
                </div>
                <div class="right-content">
                  <h4>THE WEEKEND</h4>
                  <span class="author">
                    <img src="assets/images/author.jpg" alt="" style="max-width: 50px; border-radius: 50%;">
                    <h6>THE WEEKEND<br><a href="#">@theweekend</a></h6>
                  </span>
                  <div class="line-dec"></div>
                  <span class="bid">
                    FROM<br><strong>(July 10th, 2025)</strong><br><em>($7000)</em>
                  </span>
                  <span class="ends">
                    Ends In<br><strong>(July 26th, 2025)</strong><br><em></em>
                  </span>
                  <div class="text-button">
                    <a href="details.html">View places</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 currently-market-item all blc">
              <div class="item">
                <div class="left-image">
                  <img src="assets/images/market-03.jpg" alt="" style="border-radius: 20px; min-width: 195px;">
                </div>
                <div class="right-content">
                  <h4>ARIANA GRANDE</h4>
                  <span class="author">
                    <img src="assets/images/author.jpg" alt="" style="max-width: 50px; border-radius: 50%;">
                    <h6>ARIANA GRANDE<br><a href="#">@arianagrande</a></h6>
                  </span>
                  <div class="line-dec"></div>
                  <span class="bid">
                    FROM<br><strong>(July 28th, 2026)</strong><br><em>($800)</em>
                  </span>
                  <span class="ends">
                    Ends In<br><strong>(July 15th, 2027)</strong><br><em></em>
                  </span>
                  <div class="text-button">
                    <a href="details.html">View places</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 currently-market-item all vtr">
              <div class="item">
                <div class="left-image">
                  <img src="assets/images/market-04.jpg" alt="" style="border-radius: 20px; min-width: 195px;">
                </div>
                <div class="right-content">
                  <h4>BLACKPINK COACHELLA</h4>
                  <span class="author">
                    <img src="assets/images/author.jpg" alt="" style="max-width: 50px; border-radius: 50%;">
                    <h6>BLACKPINK<br><a href="#">@blackpink_official</a></h6>
                  </span>
                  <div class="line-dec"></div>
                  <span class="bid">
                    FROM<br><strong>(July 14th, 2025)</strong><br><em>($1000)</em>
                  </span>
                  <span class="ends">
                    Ends In<br><strong>(July 23th, 2025)</strong><br><em></em>
                  </span>
                  <div class="text-button">
                    <a href="details.html">View places</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 currently-market-item all vrt dig">
              <div class="item">
                <div class="left-image">
                  <img src="assets/images/market-05.jpg" alt="" style="border-radius: 20px; min-width: 195px;">
                </div>
                <div class="right-content">
                  <h4>BTS'S WORLD TOUR</h4>
                  <span class="author">
                    <img src="assets/images/author.jpg" alt="" style="max-width: 50px; border-radius: 50%;">
                    <h6>BTS<br><a href="#">@bts_official</a></h6>
                  </span>
                  <div class="line-dec"></div>
                  <span class="bid">
                    FROM<br><strong>(July 16th, 2026)</strong><br><em>($1600)</em>
                  </span>
                  <span class="ends">
                    Ends In<br><strong>(July 30th, 2026)</strong><br><em></em>
                  </span>
                  <div class="text-button">
                    <a href="details.html">View places</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 currently-market-item all msc blc">
              <div class="item">
                <div class="left-image">
                  <img src="assets/images/market-06.jpg" alt="" style="border-radius: 20px; min-width: 195px;">
                </div>
                <div class="right-content">
                  <h4>BILLIE EILISH'S TOUR</h4>
                  <span class="author">
                    <img src="assets/images/author.jpg" alt="" style="max-width: 50px; border-radius: 50%;">
                    <h6>BILLIE EILISH<br><a href="#">@BILLIEEILISH</a></h6>
                  </span>
                  <div class="line-dec"></div>
                  <span class="bid">
                    FROM<br><strong>(July 18th, 2027)</strong><br><em>($8,200.50)</em>
                  </span>
                  <span class="ends">
                    Ends In<br><strong>(July 22th, 2022)</strong><br><em></em>
                  </span>
                  <div class="text-button">
                    <a href="details.html">View places</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- Popup du jeu -->
<div id="musicQuizPopup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.95); z-index:9999; overflow-y:auto;">
    <button id="closeQuizBtn" style="position:fixed; top:20px; right:20px; background:#ff073a; color:white; border:none; padding:10px 15px; border-radius:5px; cursor:pointer; font-size:24px; z-index:10000;">× Fermer</button>
    <div id="quizGameContainer" style="min-height:100vh; padding:20px;">
        <?php include __DIR__.'/../view/front_office/mini_jeu.html'; ?>
    </div>
</div>
    
    <!-- Ajoutez ici tout le CSS de votre mini-jeu -->
    <style>
       :root {
            --neon-pink: #ff4da6;
            --neon-purple: #8e2de2;
            --neon-blue: #4a00e0;
            --neon-green: #00ff88;
            --neon-yellow: #fff44f;
            --dark-bg: #0f0f1f;
            --glow: 0 0 20px;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: var(--dark-bg);
            color: white;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(255, 77, 166, 0.15) 0%, transparent 25%),
                radial-gradient(circle at 80% 70%, rgba(142, 45, 226, 0.15) 0%, transparent 25%);
            overflow-x: hidden;
        }

        .stage {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            perspective: 1000px;
        }

        .spotlight {
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(0,0,0,0) 60%);
            pointer-events: none;
            animation: pan-spotlight 15s infinite alternate;
            z-index: -1;
        }

        @keyframes pan-spotlight {
            0% { transform: translate(-30%, -30%); }
            100% { transform: translate(30%, 30%); }
        }

        .game-title {
            font-family: 'Montserrat', sans-serif;
            text-align: center;
            font-size: 3.5rem;
            margin-bottom: 20px;
            background: linear-gradient(90deg, var(--neon-pink), var(--neon-purple), var(--neon-blue));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 10px rgba(255, 77, 166, 0.3);
            letter-spacing: 2px;
            position: relative;
            animation: title-glow 2s infinite alternate;
        }

        @keyframes title-glow {
            0% { text-shadow: 0 0 10px rgba(255, 77, 166, 0.3); }
            100% { text-shadow: 0 0 20px var(--neon-pink), 0 0 30px var(--neon-purple); }
        }

        .mode-selector {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .mode-btn {
            padding: 12px 25px;
            background: rgba(30, 30, 60, 0.7);
            border: 2px solid var(--neon-purple);
            color: white;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            font-size: 1.1rem;
        }

        .mode-btn::before {
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
        }

        .mode-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 5px 15px rgba(142, 45, 226, 0.4);
        }

        .mode-btn.active {
            background: var(--neon-purple);
            box-shadow: var(--glow) var(--neon-purple);
            border-color: white;
            animation: mode-pulse 1.5s infinite;
        }

        .mode-btn.active::after {
            content: '✦';
            position: absolute;
            right: 10px;
            animation: twinkle 1.5s infinite;
        }

        @keyframes mode-pulse {
            0%, 100% { transform: scale(1); box-shadow: var(--glow) var(--neon-purple); }
            50% { transform: scale(1.05); box-shadow: 0 0 30px var(--neon-purple); }
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

        .quiz-card {
            background: rgba(20, 20, 40, 0.8);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            
            border: 1px solid rgba(255, 255, 255, 0.1);
            transform-style: preserve-3d;
            transition: transform 0.5s;
            position: relative;
            overflow: hidden;
        }

        .quiz-card::before {
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
                rgba(255, 255, 255, 0.03)
            );
            transform: rotate(30deg);
            animation: shine 8s infinite;
        }

        @keyframes shine {
            0% { transform: translate(-100%, -100%) rotate(30deg); }
            100% { transform: translate(100%, 100%) rotate(30deg); }
        }

        .quiz-card:hover {
            transform: rotateY(5deg) rotateX(5deg) scale(1.02);
            box-shadow: 0 15px 40px rgba(142, 45, 226, 0.6);
        }

        .question-container {
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
        }

        .audio-question {
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s;
        }

        .audio-question:hover {
            transform: scale(1.02);
        }

        .lyrics-question {
            font-size: 1.8rem;
            text-align: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            width: 100%;
            position: relative;
            transition: all 0.3s;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .lyrics-question.blurred {
            filter: blur(5px);
            background: rgba(0, 0, 0, 0.5);
        }

        .lyrics-question::after {
            content: '👆 Clique pour révéler';
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .cover-question {
            max-width: 250px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            transition: filter 0.5s, transform 0.3s;
            cursor: pointer;
        }

        .cover-question:hover {
            transform: scale(1.05);
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .option-btn {
            padding: 15px;
            background: rgba(40, 40, 80, 0.7);
            border: none;
            color: white;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 1;
        }

        .option-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: 0.5s;
            z-index: -1;
        }

        .option-btn:hover {
            background: rgba(60, 60, 100, 0.7);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 5px 15px rgba(142, 45, 226, 0.3);
        }

        .option-btn:hover::before {
            left: 100%;
        }

        .progress-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            background: rgba(0, 0, 0, 0.3);
            padding: 15px;
            border-radius: 50px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .progress-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            animation: progress-glow 3s infinite;
        }

        @keyframes progress-glow {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .score-display {
            font-size: 1.2rem;
            font-weight: bold;
            background: linear-gradient(90deg, var(--neon-pink), var(--neon-blue));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: flex;
            align-items: center;
            min-width: 120px;
        }

        .score-display::before {
            content: '★';
            margin-right: 5px;
            font-size: 1.5rem;
        }

        .streak-display {
            font-size: 1rem;
            background: linear-gradient(90deg, var(--neon-yellow), #ff9a00);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: flex;
            align-items: center;
            margin-left: 10px;
        }

        .streak-display::before {
            content: '🔥';
            margin-right: 5px;
        }

        .timer-container {
            flex-grow: 1;
            margin: 0 20px;
            height: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }

        .timer-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--neon-pink), var(--neon-purple));
            width: 100%;
            border-radius: 5px;
            transition: width 0.5s linear;
            position: relative;
        }

        .timer-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: timer-glow 2s infinite;
        }

        @keyframes timer-glow {
            0% { opacity: 0; }
            50% { opacity: 1; }
            100% { opacity: 0; }
        }

        #time-left {
            min-width: 40px;
            text-align: center;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .reward-notification {
            position: fixed;
            top: 30px;
            right: 30px;
            background: rgba(0, 255, 136, 0.9);
            color: #16213e;
            padding: 15px 25px;
            border-radius: 10px;
            font-weight: bold;
            box-shadow: 0 5px 20px rgba(0, 255, 136, 0.5);
            transform: translateX(200%);
            transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 100;
            display: flex;
            align-items: center;
            border: 2px solid white;
        }

        .reward-notification::before {
            content: '🎁';
            margin-right: 10px;
            font-size: 1.5rem;
        }

        .reward-notification.show {
            transform: translateX(0);
        }

        .powerup-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .powerup-btn {
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid var(--neon-yellow);
            color: white;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .powerup-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: translateX(-100%);
        }

        .powerup-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 0 15px var(--neon-yellow);
        }

        .powerup-btn:hover::before {
            animation: powerup-shine 1.5s infinite;
        }

        @keyframes powerup-shine {
            100% { transform: translateX(100%); }
        }

        .powerup-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            border-color: #666;
        }

        .powerup-btn .powerup-icon {
            font-size: 1.2rem;
        }

        .powerup-btn .powerup-count {
            background: var(--neon-yellow);
            color: #16213e;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background-color: var(--neon-pink);
            opacity: 0;
            z-index: 99;
            animation: confetti-fall 3s ease-in-out forwards;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(-100px) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }

        .combo-effect {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 5rem;
            font-weight: bold;
            color: var(--neon-yellow);
            text-shadow: 0 0 20px var(--neon-yellow);
            opacity: 0;
            z-index: 100;
            pointer-events: none;
            animation: combo-pop 1s forwards;
        }

        @keyframes combo-pop {
            0% { transform: translate(-50%, -50%) scale(0.5); opacity: 0; }
            50% { transform: translate(-50%, -50%) scale(1.2); opacity: 1; }
            100% { transform: translate(-50%, -50%) scale(1); opacity: 0; }
        }

        .multiplier-display {
            position: absolute;
            top: -10px;
            right: -10px;
            background: var(--neon-yellow);
            color: #16213e;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            box-shadow: 0 0 10px var(--neon-yellow);
            animation: multiplier-pulse 1s infinite alternate;
        }

        @keyframes multiplier-pulse {
            0% { transform: scale(1); }
            100% { transform: scale(1.2); }
        }

        .difficulty-indicator {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 5px;
        }

        .difficulty-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
        }

        .difficulty-dot.active {
            background: var(--neon-pink);
            box-shadow: 0 0 5px var(--neon-pink);
        }

        .lives-container {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-right: 15px;
        }

        .life {
            width: 20px;
            height: 20px;
            background: var(--neon-pink);
            clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%);
            transition: all 0.3s;
        }

        .life.lost {
            transform: scale(0);
            opacity: 0;
        }

        /* Animations spéciales */
        @keyframes pulse-glow {
            0% { box-shadow: 0 0 5px var(--neon-pink); }
            50% { box-shadow: 0 0 20px var(--neon-pink); }
            100% { box-shadow: 0 0 5px var(--neon-pink); }
        }

        @keyframes correct-answer {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); background-color: var(--neon-green); box-shadow: 0 0 20px var(--neon-green); }
            100% { transform: scale(1); }
        }

        @keyframes wrong-answer {
            0% { transform: translateX(0); }
            20% { transform: translateX(-10px); }
            40% { transform: translateX(10px); }
            60% { transform: translateX(-10px); }
            80% { transform: translateX(10px); }
            100% { transform: translateX(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .options-grid {
                grid-template-columns: 1fr;
            }
            
            .game-title {
                font-size: 2.5rem;
            }
            
            .mode-btn {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .powerup-container {
                gap: 10px;
            }
            
            .powerup-btn {
                padding: 8px 12px;
                font-size: 0.8rem;
            }
        }
    </style>
    
    <!-- Ajoutez ici tout le JavaScript de votre mini-jeu -->
    
</div>
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <p>Copyright &copy; 2022 <a href="#">Liberty</a> NFT Marketplace Co., Ltd. All rights reserved.
          &nbsp;&nbsp;
          Designed by <a title="HTML CSS Templates" rel="sponsored" href="https://templatemo.com" target="_blank">TemplateMo</a></p>
        </div>
      </div>
    </div>
    </div>
  </footer>


    <!-- Ajoutez ces animations CSS -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }

        .profile-dropdown {
            animation: fadeIn 0.3s forwards;
        }
    </style>


  <!-- Scripts -->
  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>

  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/popup.js"></script>
  <script src="assets/js/custom.js"></script>
  <script>
        // Script amélioré avec animation
        document.getElementById('profileIcon').addEventListener('click', function(e) {
            e.stopPropagation();
            var dropdown = document.getElementById('profileDropdown');
            if (dropdown.style.display === 'block') {
                dropdown.style.animation = 'fadeOut 0.3s forwards';
                setTimeout(() => {
                    dropdown.style.display = 'none';
                }, 300);
            } else {
                dropdown.style.display = 'block';
                dropdown.style.animation = 'fadeIn 0.3s forwards';
            }
        });

        window.addEventListener('click', function() {
            var dropdown = document.getElementById('profileDropdown');
            if (dropdown.style.display === 'block') {
                dropdown.style.animation = 'fadeOut 0.3s forwards';
                setTimeout(() => {
                    dropdown.style.display = 'none';
                }, 300);
            }
        });

        // Empêche la fermeture quand on clique dans le menu
        document.getElementById('profileDropdown').addEventListener('click', function(e) {
            e.stopPropagation();
        });
// Nouveau script CAPTCHA amélioré
document.addEventListener('DOMContentLoaded', function() {
    const captchaElement = document.getElementById('captchaElement');
    const captchaTarget = document.getElementById('captchaTarget');
    const captchaOverlay = document.getElementById('captchaOverlay');
    
    // Si le CAPTCHA est déjà validé (côté PHP), on ne fait rien
    if (<?= $captchaVerified ? 'true' : 'false' ?>) {
        return;
    }

    // Drag & Drop
    captchaElement.addEventListener('dragstart', function(e) {
        e.dataTransfer.setData('text/plain', 'musicCaptcha');
    });

    captchaTarget.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('drag-over');
    });

    captchaTarget.addEventListener('dragleave', function() {
        this.classList.remove('drag-over');
    });

    captchaTarget.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
        
        if (e.dataTransfer.getData('text/plain') === 'musicCaptcha') {
            // Animation de validation
            this.classList.add('valid');
            
            // Envoie la requête au serveur pour valider le CAPTCHA
            fetch('validate_captcha.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'validate_captcha=true'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cache l'overlay avec une animation
                    captchaOverlay.style.transition = 'opacity 0.5s ease';
                    captchaOverlay.style.opacity = '0';
                    
                    // Affiche le contenu principal après l'animation
                    setTimeout(() => {
                        captchaOverlay.style.display = 'none';
                        document.querySelector('div[style*="display: none"]').style.display = 'block';
                        
                        // Recharge la page pour s'assurer que PHP voit la validation
                        window.location.reload();
                    }, 500);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
});
</script>

<!-- Particles.js for stars animation -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script>
// Initialize particles.js (stars animation) for the partner section
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('particles-js-index')) {
        particlesJS('particles-js-index', {
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
</script>

<!-- Add CSS for particles-js-index -->
<style>
#particles-js-index {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 1;
}

.partner-content {
    position: relative;
    z-index: 2;
}
</style>

</body>
</html> 