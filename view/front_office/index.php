<?php
session_start();
// Redirigez vers la page de connexion si non connecté
if (!isset($_SESSION['user'])) {
    header('Location: /TechvibeS/view/front_office/welcome.php');
    exit();
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
    </style>

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

  <!-- ***** Header Area Start ***** -->
  <header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- ***** Logo Start ***** -->
                    <a href="index.html" class="logo">
                        <img src="assets/images/logo.png" alt="">
                    </a>
                    <!-- ***** Logo End ***** -->
                    <!-- ***** Menu Start ***** -->
                    <ul class="nav">
                        <li><a href="index.html" class="active">Home</a></li>
                        <li><a href="explore.html">Explore</a></li>
                        <li><a href="details.php">Artists</a></li>
                        <li><a href="author.php">Tickets</a></li>
                        <li><a href="create.html">Competitions</a></li>
                    </ul>  
                    <!-- Icône de profil -->
    <div class="user-profile">
        <div class="profile-icon" id="profileIcon">
        <img src="/livethemusic/<?= htmlspecialchars($user['image']) ?>" alt="Profile Image">

        </div>
        <div class="profile-dropdown" id="profileDropdown">
            <a href="profile.php"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></a>
            <a href="#">Settings</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
                    <a class='menu-trigger'>
                        <span>Menu</span>
                    </a>
                    <!-- ***** Menu End ***** -->
                </nav>
            </div>
        </div>
    </div>
  </header>
  <!-- ***** Header Area End ***** -->

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

  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <p>Copyright © 2022 <a href="#">Liberty</a> NFT Marketplace Co., Ltd. All rights reserved.
          &nbsp;&nbsp;
          Designed by <a title="HTML CSS Templates" rel="sponsored" href="https://templatemo.com" target="_blank">TemplateMo</a></p>
        </div>
      </div>
    </div>
  </footer>
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
    </script>

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

  </body>
</html>