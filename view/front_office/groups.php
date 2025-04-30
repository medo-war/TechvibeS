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

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-liberty-market.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .group-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 15px;
            transition: transform 0.3s ease;
            margin-bottom: 30px;
            overflow: hidden;
        }
        .group-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }
        .group-img {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
        .music-icon {
            font-size: 2rem;
            color: #f5a623;
            margin-bottom: 15px;
        }
        .genre-badge {
            background: #f5a623;
            color: #000;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .listen-btn {
            background: #f5a623;
            color: #000;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .listen-btn:hover {
            background: #e69100;
            transform: scale(1.05);
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

  <!-- ***** Header Area Start ***** -->
  <header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- ***** Logo Start ***** -->
                    <a href="index.html" class="logo">
                        <img src="assets/images/logo.png" alt="LiveTheMusic Logo">
                    </a>
                    <!-- ***** Logo End ***** -->
                    <!-- ***** Menu Start ***** -->
                    <ul class="nav">
                        <li><a href="index.php" class="active">Home</a></li>
                        <li><a href="explore.html">Events</a></li>
                        <li><a href="details.php">Artists</a></li>
                        <li><a href="groups.php">Groups</a></li>
                        <li><a href="tickets.html">Tickets</a></li>
                    </ul>  
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

  <div class="page-heading normal-space">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <h2>Discover The Best Music Groups</h2>
          <p>Explore top bands and musical collectives from around the world</p>
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
                <div class="group-card">
                  <img src="<?= htmlspecialchars($group['image_url']); ?>" alt="Group Image" class="group-img">
                  <div class="p-4">
                    <div class="music-icon">
                      <i class="fas fa-users"></i>
                    </div>
                    <h4><?= htmlspecialchars($group['name']); ?></h4>
                    <span class="genre-badge"><?= htmlspecialchars($group['genre']); ?></span>
                    <p class="mt-3"><strong>Formed:</strong> <?= htmlspecialchars($group['formation_year']); ?></p>
                    
                    <!-- Dropdown for more info -->
                    <button class="btn btn-outline-light btn-sm mt-2" type="button" data-toggle="collapse" 
                            data-target="#group-<?= $group['id']; ?>" aria-expanded="false" 
                            aria-controls="group-<?= $group['id']; ?>">
                        More Info
                    </button>

                    <div class="collapse mt-3" id="group-<?= $group['id']; ?>">
                      <p><strong>Country:</strong> <?= htmlspecialchars($group['country']); ?></p>
            
                      <p><strong>Bio:</strong> <?= nl2br(htmlspecialchars($group['bio'])); ?></p>
                    </div>
                    
                    <a href="<?= htmlspecialchars($group['website_url']); ?>" target="_blank" class="listen-btn btn btn-block mt-3">
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

  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <p>Copyright © 2023 <a href="#">LiveTheMusic</a>. All rights reserved.
          &nbsp;&nbsp;
          Music brings us together</p>
        </div>
      </div>
    </div>
  </footer>

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
</body>
</html>