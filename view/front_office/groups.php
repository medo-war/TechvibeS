<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/Config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="author" content="templatemo">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <title>LiveTheMusic - Groups</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-liberty-market.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="style.css">
    
    <style>
        :root {
            --neon-pink: #ff2a6d;
            --neon-blue: #05d9e8;
            --dark-blue: #01012b;
            --darker-blue: #000022;
            --light-pink: #ff7bbf;
            --purple: #7928ca;
            --teal: #00c6bd;
            --cyan: #01c5c4;
            --gradient-1: linear-gradient(135deg, var(--neon-pink), var(--purple));
            --gradient-2: linear-gradient(135deg, var(--neon-blue), var(--teal));
            --gradient-3: linear-gradient(135deg, var(--purple), var(--neon-blue));
        }
        
        body {
            background: var(--darker-blue);
            color: #fff;
            font-family: 'Roboto', sans-serif;
            overflow-x: hidden;
            position: relative;
        }
        
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
                rgba(255, 42, 109, 0.3) 0%,
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
                rgba(5, 217, 232, 0.3) 0%,
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
                transform: translate(20%, 50%);
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
                transform: translate(-20%, 50%);
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
        
        /* Group Card Styles */
        .group-card {
            background: linear-gradient(135deg, rgba(1, 1, 43, 0.5), rgba(0, 0, 34, 0.5));
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-bottom: 40px;
            overflow: hidden;
            border: 1px solid rgba(5, 217, 232, 0.2);
            box-shadow: 0 10px 30px rgba(5, 217, 232, 0.1);
            backdrop-filter: blur(20px);
            position: relative;
            transform-style: preserve-3d;
        }
        
        .group-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: var(--gradient-3);
            z-index: 1;
            opacity: 0.8;
        }
        
        .group-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            padding: 1px;
            background: linear-gradient(135deg, var(--neon-pink), transparent, var(--neon-blue));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0.5;
            transition: opacity 0.4s ease;
        }
        
        .group-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(5, 217, 232, 0.2);
        }
        
        .group-card:hover::after {
            opacity: 1;
        }
        
        .group-img {
            height: 250px;
            object-fit: cover;
            width: 100%;
            transition: all 0.5s ease;
            filter: brightness(0.9);
        }
        
        .group-card:hover .group-img {
            filter: brightness(1.1);
            transform: scale(1.05);
        }
        
        .card-body {
            padding: 25px;
            position: relative;
            z-index: 2;
        }
        
        .group-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #fff;
            text-shadow: 0 0 10px var(--neon-blue);
            position: relative;
            display: inline-block;
        }
        
        .group-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--neon-blue);
            box-shadow: 0 0 10px var(--neon-blue);
            transition: width 0.3s ease;
        }
        
        .group-card:hover .group-title::after {
            width: 100%;
        }
        
        .music-icon {
            font-size: 2rem;
            color: var(--neon-blue);
            margin-bottom: 15px;
            text-shadow: 0 0 10px var(--neon-blue);
            transition: all 0.3s ease;
        }
        
        .group-card:hover .music-icon {
            transform: scale(1.1) rotate(10deg);
            color: var(--neon-pink);
            text-shadow: 0 0 15px var(--neon-pink);
        }
        
        .genre-badge {
            background: rgba(5, 217, 232, 0.2);
            color: var(--neon-blue);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            border: 1px solid var(--neon-blue);
            text-shadow: 0 0 5px var(--neon-blue);
            transition: all 0.3s ease;
            margin-right: 5px;
            margin-bottom: 5px;
            display: inline-block;
        }
        
        .genre-badge:hover {
            background: rgba(5, 217, 232, 0.3);
            box-shadow: 0 0 10px var(--neon-blue);
            transform: translateY(-2px);
        }
        
        .listen-btn {
            background: rgba(255, 42, 109, 0.2);
            color: var(--neon-pink);
            border: 1px solid var(--neon-pink);
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            text-shadow: 0 0 5px var(--neon-pink);
        }
        
        .listen-btn:hover {
            background: rgba(255, 42, 109, 0.4);
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(255, 42, 109, 0.5);
            color: #fff;
        }
        
        .group-description {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 20px;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        /* Section Title Styling */
        .section-heading h4 {
            position: relative;
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 40px;
            text-align: center;
            text-shadow: 0 0 10px var(--neon-blue);
        }
        
        .section-heading h4 em {
            font-style: normal;
            color: var(--neon-pink);
            text-shadow: 0 0 10px var(--neon-pink);
        }
        
        .section-heading h4::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 2px;
            background: linear-gradient(to right, var(--neon-pink), var(--neon-blue));
            box-shadow: 0 0 10px var(--neon-blue);
        }
        
        /* Animations */
        @keyframes pulse-glow {
            0%, 100% {
                filter: brightness(1) blur(0.5px);
            }
            50% {
                filter: brightness(1.5) blur(1px);
            }
        }
        
        /* Beat wave animations */
        .beat-container {
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2;
            opacity: 0.6;
            perspective: 1000px;
            transform-style: preserve-3d;
            margin-top: 20px;
            height: 50px;
        }
        
        .beat-wave {
            width: 8px;
            height: 30px;
            margin: 0 5px;
            border-radius: 8px;
            animation: beat-animation 1.8s infinite ease-in-out;
            transform-origin: bottom;
            box-shadow: 0 0 10px currentColor;
        }
        
        @keyframes beat-animation {
            0% {
                transform: scaleY(0.2) translateY(0);
                opacity: 0.7;
            }
            20% {
                transform: scaleY(1) translateY(-5px);
                opacity: 1;
            }
            40% {
                transform: scaleY(0.6) translateY(-3px);
                opacity: 0.8;
            }
            60% {
                transform: scaleY(0.8) translateY(-4px);
                opacity: 0.9;
            }
            80% {
                transform: scaleY(0.4) translateY(-1px);
                opacity: 0.8;
            }
            100% {
                transform: scaleY(0.2) translateY(0);
                opacity: 0.7;
            }
        }
        
        /* Page heading styles */
        .sub-heading {
            color: var(--neon-blue);
            font-size: 1rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
            text-shadow: 0 0 10px var(--neon-blue);
            animation: pulse-glow 2s infinite alternate;
        }
        
        .main-heading {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #fff;
            text-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
        }
        
        .main-heading em {
            color: var(--neon-pink);
            font-style: normal;
            text-shadow: 0 0 15px var(--neon-pink);
        }
        
        .line-dec {
            width: 100px;
            height: 2px;
            background: linear-gradient(to right, var(--neon-pink), var(--neon-blue));
            margin-bottom: 20px;
            box-shadow: 0 0 10px var(--neon-blue);
        }
        
        .section-description {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            display: block;
            margin-bottom: 30px;
        }
        
        /* Particles container */
        .particles-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }
        
        /* Glow effects */
        .glow-effect {
            position: relative;
            overflow: hidden;
        }
        
        .glow-effect::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(5, 217, 232, 0.3) 0%, transparent 70%);
            animation: rotate-glow 15s linear infinite;
            opacity: 0;
            transition: opacity 0.5s ease;
            z-index: -1;
        }
        
        .glow-effect:hover::before {
            opacity: 1;
        }
        
        @keyframes rotate-glow {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        
        /* Neon text effect */
        .neon-text-effect {
            text-shadow: 
                0 0 5px var(--neon-blue),
                0 0 10px var(--neon-blue),
                0 0 20px var(--neon-blue),
                0 0 40px var(--neon-blue);
            animation: neon-pulse 2s infinite alternate;
        }
        
        @keyframes neon-pulse {
            0%, 18%, 22%, 25%, 53%, 57%, 100% {
                text-shadow: 
                    0 0 5px var(--neon-blue),
                    0 0 10px var(--neon-blue),
                    0 0 20px var(--neon-blue),
                    0 0 40px var(--neon-blue);
            }
            20%, 24%, 55% {
                text-shadow: none;
            }
        }
        
        /* Music visualizer effect */
        .visualizer-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            height: 50px;
            margin: 20px auto;
            width: 100px;
        }
        
        .visualizer-bar {
            width: 4px;
            background: linear-gradient(to top, var(--neon-pink), var(--neon-blue));
            margin: 0 2px;
            border-radius: 2px;
            box-shadow: 0 0 10px var(--neon-blue);
            animation: visualizer-animation 1.5s infinite ease-in-out;
        }
        
        @keyframes visualizer-animation {
            0%, 100% { height: 10px; }
            50% { height: 40px; }
        }
        
        .visualizer-bar:nth-child(1) { animation-delay: 0.1s; }
        .visualizer-bar:nth-child(2) { animation-delay: 0.5s; }
        .visualizer-bar:nth-child(3) { animation-delay: 0.2s; }
        .visualizer-bar:nth-child(4) { animation-delay: 0.6s; }
        .visualizer-bar:nth-child(5) { animation-delay: 0.3s; }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            .group-card {
                margin-bottom: 30px;
            }
            
            .section-heading h4 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 767px) {
            .group-img {
                height: 200px;
            }
            
            .section-heading h4 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>

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

  <div class="page-heading normal-space">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h6 class="sub-heading">LiveTheMusic</h6>
          <h2 class="main-heading">Music <em>Groups</em></h2>
          <div class="line-dec mx-auto"></div>
          <span class="section-description">Explore the best music groups from around the world.</span>
          
          <!-- Animated Beat Waves -->
          <div class="beat-container mt-4">
            <div class="beat-wave" style="background: var(--neon-pink); animation-delay: 0.1s;"></div>
            <div class="beat-wave" style="background: var(--neon-blue); animation-delay: 0.2s;"></div>
            <div class="beat-wave" style="background: var(--purple); animation-delay: 0.3s;"></div>
            <div class="beat-wave" style="background: var(--teal); animation-delay: 0.4s;"></div>
            <div class="beat-wave" style="background: var(--neon-pink); animation-delay: 0.5s;"></div>
            <div class="beat-wave" style="background: var(--neon-blue); animation-delay: 0.6s;"></div>
            <div class="beat-wave" style="background: var(--purple); animation-delay: 0.7s;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="item-details-page">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="section-heading">
            <div class="line-dec"></div>
            <h2>Featured <em>Music Groups</em></h2>
          </div>
        </div>
        
        <div class="col-lg-12">
          <div class="current-bid">
            <div class="row">
              <?php
              require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/Config.php';

              try {
                  // Get the database connection
                  $db = config::getConnexion();

                  // Prepare and execute the SQL statement for groups
                  $stmt = $db->prepare("SELECT * FROM groups");
                  $stmt->execute();

                  // Fetch all groups
                  $groups = $stmt->fetchAll();

                  // Loop through the groups and display them
                  foreach ($groups as $group):
              ?>
              <div class="col-lg-4 col-md-6">
                <div class="group-card glow-effect">
                  <img src="<?= htmlspecialchars($group['image_url']); ?>" alt="Group Image" class="group-img">
                  <div class="card-body">
                    <div class="music-icon">
                      <i class="fas fa-users"></i>
                    </div>
                    <h4 class="group-title neon-text-effect"><?= htmlspecialchars($group['name']); ?></h4>
                    <span class="genre-badge"><?= htmlspecialchars($group['genre']); ?></span>
                    <p class="group-description mt-3"><strong>Formed:</strong> <?= htmlspecialchars($group['formation_year']); ?></p>
                    
                    <!-- Music Visualizer -->
                    <div class="visualizer-container">
                      <div class="visualizer-bar"></div>
                      <div class="visualizer-bar"></div>
                      <div class="visualizer-bar"></div>
                      <div class="visualizer-bar"></div>
                      <div class="visualizer-bar"></div>
                    </div>
                    
                    <!-- Dropdown for more info -->
                    <button class="btn btn-outline-light btn-sm mt-3" type="button" data-toggle="collapse" 
                            data-target="#group-<?= $group['id']; ?>" aria-expanded="false" 
                            aria-controls="group-<?= $group['id']; ?>">
                        <i class="fas fa-info-circle"></i> More Info
                    </button>

                    <div class="collapse mt-3" id="group-<?= $group['id']; ?>">
                      <p><strong>Country:</strong> <?= htmlspecialchars($group['country']); ?></p>
            
                      <p><strong>Bio:</strong> <?= nl2br(htmlspecialchars($group['bio'])); ?></p>
                    </div>
                    
                    <a href="<?= htmlspecialchars($group['website_url']); ?>" target="_blank" class="listen-btn mt-3">
                      <i class="fas fa-play mr-2"></i> Listen Now
                    </a>
                  </div>
                </div>
              </div>
              <?php
                  endforeach;
              } catch (PDOException $e) {
                  echo "<div class='col-12'><div class='alert alert-danger'>Error loading groups: " . $e->getMessage() . "</div></div>";
              }
              ?>
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
            <h2>Upcoming Group Performances</h2>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="main-button">
            <a href="events.html">View All Events</a>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="item first-item">
            <div class="number">
              <h6>1</h6>
            </div>
            <div class="icon">
              <img src="assets/images/icon-02.png" alt="">
            </div>
            <h4>Festival Headliners</h4>
            <p>Discover which major music festivals your favorite groups will be performing at this season.</p>
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
            <h4>World Tours</h4>
            <p>Check out the global tour schedules for top musical groups and plan your concert experience.</p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="item">
            <div class="icon">
              <img src="assets/images/icon-06.png" alt="">
            </div>
            <h4>New Album Releases</h4>
            <p>Stay updated on the latest album releases and special edition recordings from popular groups.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Particles Background -->
  <div id="particles-js" class="particles-container"></div>

  <!-- Scripts -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/popup.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
  
  <script>
    // Particles.js Configuration and Initialization
    document.addEventListener('DOMContentLoaded', function() {
      particlesJS('particles-js', {
        "particles": {
          "number": {
            "value": 160,
            "density": {
              "enable": true,
              "value_area": 800
            }
          },
          "color": {
            "value": "#ffffff"
          },
          "shape": {
            "type": "star",
            "stroke": {
              "width": 0,
              "color": "#000000"
            },
            "polygon": {
              "nb_sides": 5
            }
          },
          "opacity": {
            "value": 0.7,
            "random": true,
            "anim": {
              "enable": true,
              "speed": 1,
              "opacity_min": 0.1,
              "sync": false
            }
          },
          "size": {
            "value": 4,
            "random": true,
            "anim": {
              "enable": true,
              "speed": 2,
              "size_min": 0.5,
              "sync": false
            }
          },
          "line_linked": {
            "enable": false
          },
          "move": {
            "enable": true,
            "speed": 1.5,
            "direction": "bottom",
            "random": true,
            "straight": false,
            "out_mode": "out",
            "bounce": false,
            "attract": {
              "enable": false,
              "rotateX": 600,
              "rotateY": 1200
            }
          }
        },
        "interactivity": {
          "detect_on": "canvas",
          "events": {
            "onhover": {
              "enable": true,
              "mode": "repulse"
            },
            "onclick": {
              "enable": true,
              "mode": "push"
            },
            "resize": true
          },
          "modes": {
            "repulse": {
              "distance": 100,
              "duration": 0.4
            },
            "push": {
              "particles_nb": 4
            }
          }
        },
        "retina_detect": true
      });
      
      // Add hover effects to group cards
      const groupCards = document.querySelectorAll('.group-card');
      groupCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-15px) scale(1.03)';
          this.style.boxShadow = '0 20px 40px rgba(5, 217, 232, 0.3)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0) scale(1)';
          this.style.boxShadow = '0 10px 30px rgba(5, 217, 232, 0.1)';
        });
      });
    });
  </script>
</body>
</html>