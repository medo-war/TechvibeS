<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/Config.php';

// Check if user is logged in and get user ID
$user_id = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;

// Handle song creation form submission
$generated_song = null;
$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lyrics']) && isset($_POST['genre'])) {
    $lyrics = trim($_POST['lyrics']);
    $genre = trim($_POST['genre']);
    $title = trim($_POST['title'] ?? 'Untitled Song');
    
    if (empty($lyrics)) {
        $error = "Please enter some lyrics for your song.";
    } elseif (empty($genre)) {
        $error = "Please select a genre for your song.";
    } else {
        // In a real application, this would call an AI music generation API
        // For demonstration, we'll simulate a response
        $generated_song = [
            'title' => $title,
            'genre' => $genre,
            'lyrics' => $lyrics,
            'audio_url' => 'assets/demo/generated_song.mp3', // Demo audio file
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
        // Save the generated song to session for demo purposes
        $_SESSION['generated_song'] = $generated_song;
        $success = true;
    }
}

// Get the generated song from session if available
if (!$generated_song && isset($_SESSION['generated_song'])) {
    $generated_song = $_SESSION['generated_song'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="templatemo">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <title>LiveTheMusic - Song Creation</title>

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
        
        /* Form Styling */
        .song-creation-form {
            background: rgba(1, 1, 43, 0.5);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(5, 217, 232, 0.2);
            box-shadow: 0 10px 30px rgba(5, 217, 232, 0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 40px;
        }
        
        .form-label {
            color: var(--neon-blue);
            font-weight: 500;
            margin-bottom: 10px;
            text-shadow: 0 0 5px var(--neon-blue);
        }
        
        .form-control {
            background: rgba(0, 0, 34, 0.5);
            border: 1px solid rgba(5, 217, 232, 0.3);
            color: #fff;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background: rgba(0, 0, 34, 0.7);
            border-color: var(--neon-blue);
            box-shadow: 0 0 15px rgba(5, 217, 232, 0.3);
            color: #fff;
        }
        
        .form-select {
            background-color: rgba(0, 0, 34, 0.5);
            border: 1px solid rgba(5, 217, 232, 0.3);
            color: #fff;
            border-radius: 10px;
            padding: 12px 15px;
        }
        
        .form-select:focus {
            background-color: rgba(0, 0, 34, 0.7);
            border-color: var(--neon-blue);
            box-shadow: 0 0 15px rgba(5, 217, 232, 0.3);
            color: #fff;
        }
        
        .btn-create {
            background: rgba(255, 42, 109, 0.2);
            color: var(--neon-pink);
            border: 1px solid var(--neon-pink);
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: bold;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            text-shadow: 0 0 5px var(--neon-pink);
            margin-top: 20px;
        }
        
        .btn-create:hover {
            background: rgba(255, 42, 109, 0.4);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 42, 109, 0.3);
            color: #fff;
        }
        
        /* Result Card Styling */
        .result-card {
            background: rgba(1, 1, 43, 0.5);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(5, 217, 232, 0.2);
            box-shadow: 0 10px 30px rgba(5, 217, 232, 0.1);
            backdrop-filter: blur(10px);
            margin-top: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .result-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--gradient-1);
            z-index: 1;
        }
        
        .result-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--neon-pink);
            text-shadow: 0 0 10px var(--neon-pink);
        }
        
        .result-genre {
            display: inline-block;
            background: rgba(5, 217, 232, 0.2);
            color: var(--neon-blue);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            border: 1px solid var(--neon-blue);
            text-shadow: 0 0 5px var(--neon-blue);
            margin-bottom: 20px;
        }
        
        .result-lyrics {
            background: rgba(0, 0, 34, 0.5);
            border: 1px solid rgba(5, 217, 232, 0.2);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            white-space: pre-line;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .audio-player {
            width: 100%;
            margin-top: 20px;
            filter: drop-shadow(0 0 10px var(--neon-blue));
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
        
        /* Animations */
        @keyframes pulse-glow {
            0%, 100% {
                filter: brightness(1) blur(0.5px);
            }
            50% {
                filter: brightness(1.5) blur(1px);
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
  
  <!-- Particles Background -->
  <div id="particles-js" class="particles-container"></div>

  <div class="page-heading normal-space">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h6 class="sub-heading">LiveTheMusic</h6>
          <h2 class="main-heading">Song <em>Creation</em></h2>
          <div class="line-dec mx-auto"></div>
          <span class="section-description">Create your own music with AI by entering lyrics and selecting a genre</span>
          
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
        <!-- Song Creation Form -->
        <div class="col-lg-8 offset-lg-2">
          <?php if ($error): ?>
            <div class="alert alert-danger">
              <?php echo htmlspecialchars($error); ?>
            </div>
          <?php endif; ?>
          
          <?php if ($success): ?>
            <div class="alert alert-success">
              Your song has been created successfully!
            </div>
          <?php endif; ?>
          
          <div class="song-creation-form">
            <h3 class="mb-4">Create Your Song</h3>
            
            <form method="post" action="">
              <div class="mb-4">
                <label for="title" class="form-label">Song Title</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Enter a title for your song">
              </div>
              
              <div class="mb-4">
                <label for="genre" class="form-label">Music Genre</label>
                <select class="form-select" id="genre" name="genre" required>
                  <option value="" selected disabled>Select a genre</option>
                  <option value="pop">Pop</option>
                  <option value="rock">Rock</option>
                  <option value="hiphop">Hip Hop</option>
                  <option value="rnb">R&B</option>
                  <option value="electronic">Electronic</option>
                  <option value="jazz">Jazz</option>
                  <option value="country">Country</option>
                  <option value="classical">Classical</option>
                  <option value="metal">Metal</option>
                  <option value="folk">Folk</option>
                </select>
              </div>
              
              <div class="mb-4">
                <label for="lyrics" class="form-label">Lyrics</label>
                <textarea class="form-control" id="lyrics" name="lyrics" rows="10" placeholder="Enter your lyrics here..." required></textarea>
              </div>
              
              <div class="text-center">
                <button type="submit" class="btn btn-create">
                  <i class="fas fa-music me-2"></i> Create Song
                </button>
              </div>
            </form>
          </div>
          
          <!-- Generated Song Result -->
          <?php if ($generated_song): ?>
          <div class="result-card">
            <h3 class="result-title"><?php echo htmlspecialchars($generated_song['title']); ?></h3>
            <span class="result-genre"><?php echo htmlspecialchars($generated_song['genre']); ?></span>
            
            <!-- Music Visualizer -->
            <div class="visualizer-container">
              <div class="visualizer-bar"></div>
              <div class="visualizer-bar"></div>
              <div class="visualizer-bar"></div>
              <div class="visualizer-bar"></div>
              <div class="visualizer-bar"></div>
            </div>
            
            <div class="result-lyrics">
              <?php echo nl2br(htmlspecialchars($generated_song['lyrics'])); ?>
            </div>
            
            <div class="audio-player-container">
              <h4>Listen to Your Song</h4>
              <audio controls class="audio-player">
                <source src="<?php echo htmlspecialchars($generated_song['audio_url']); ?>" type="audio/mpeg">
                Your browser does not support the audio element.
              </audio>
            </div>
            
            <div class="mt-4 text-center">
              <a href="#" class="btn btn-create" onclick="downloadSong(); return false;">
                <i class="fas fa-download me-2"></i> Download Song
              </a>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/popup.js"></script>
  <script src="assets/js/custom.js"></script>
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
    });
    
    // Function to handle song download (demo)
    function downloadSong() {
      // In a real application, this would trigger a download of the generated audio file
      alert('Song download feature would be implemented here with a real API.');
    }
  </script>
</body>
</html>
