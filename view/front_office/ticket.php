<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="author" content="templatemo">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <title>LiveTheMusic - Available Tickets</title>

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
        .ticket-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 15px;
            transition: transform 0.3s ease;
            margin-bottom: 30px;
            overflow: hidden;
            border: 1px solid rgba(255, 166, 35, 0.2);
        }
        .ticket-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(245, 166, 35, 0.2);
        }
        .ticket-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .ticket-icon {
            font-size: 1.5rem;
            color: #f5a623;
            margin-bottom: 10px;
        }
        .price-badge {
            background: #f5a623;
            color: #000;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .buy-btn {
            background: #f5a623;
            color: #000;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
            width: 100%;
        }
        .buy-btn:hover {
            background: #e69100;
            transform: scale(1.05);
        }
        .date-badge {
            background: rgba(255,255,255,0.1);
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .ticket-type {
            color: #f5a623;
            font-weight: bold;
        }
        .remaining {
            color: #f5a623;
            font-size: 0.9rem;
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
                        <li><a href="index.php">Home</a></li>
                        <li><a href="explore.html">Events</a></li>
                        <li><a href="details.php">Artists</a></li>
                        <li><a href="groups.php">Groups</a></li>
                        <li><a href="tickets.php" class="active">Tickets</a></li>
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
          <h2>Available Concert Tickets</h2>
          <p>Secure your spot at the hottest music events this season</p>
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
            <h2>Upcoming <em>Concerts</em></h2>
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

                  // Prepare and execute the SQL statement for upcoming tickets
                  $stmt = $db->prepare("SELECT * FROM tickets ");
                  $stmt->execute();

                  // Fetch all tickets
                  $tickets = $stmt->fetchAll();

                  // Loop through the tickets and display them
                  foreach ($tickets as $ticket):
                      $eventDate = new DateTime($ticket['event_date']);
              ?>
              <div class="col-lg-4 col-md-6">
                <div class="ticket-card">
                  <img src="<?= htmlspecialchars($ticket['image_url']); ?>" alt="Concert Image" class="ticket-img">
                  <div class="p-4">
                    <div class="ticket-icon">
                      <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h4><?= htmlspecialchars($ticket['concert_name']); ?></h4>
                    <p class="text-muted"><?= htmlspecialchars($ticket['artist_name']); ?></p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <span class="date-badge">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <?= $eventDate->format('M j, Y'); ?> at <?= $ticket['event_time']; ?>
                      </span>
                      <span class="ticket-type"><?= htmlspecialchars($ticket['ticket_type']); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <span>
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        <?= htmlspecialchars($ticket['venue']); ?>, <?= htmlspecialchars($ticket['city']); ?>
                      </span>
                      <span class="remaining"><?= $ticket['available_quantity']; ?> left</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                      <span class="price-badge">$<?= number_format($ticket['price'], 2); ?></span>
                      <button class="buy-btn" onclick="window.location.href='purchase.php?id=<?= $ticket['id']; ?>'">
                        <i class="fas fa-shopping-cart mr-2"></i> Buy Now
                      </button>
                    </div>
                    
                    <!-- More info dropdown -->
                    <button class="btn btn-outline-light btn-sm mt-3 w-100" type="button" data-toggle="collapse" 
                            data-target="#ticket-<?= $ticket['id']; ?>" aria-expanded="false" 
                            aria-controls="ticket-<?= $ticket['id']; ?>">
                        Event Details
                    </button>
                    
                    <div class="collapse mt-3" id="ticket-<?= $ticket['id']; ?>">
                      <div class="card card-body bg-dark border-secondary">
                        <p><strong>Venue:</strong> <?= htmlspecialchars($ticket['venue']); ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($ticket['city']); ?>, <?= htmlspecialchars($ticket['country']); ?></p>
                        <p><strong>Date:</strong> <?= $eventDate->format('l, F j, Y'); ?></p>
                        <p><strong>Time:</strong> <?= date('g:i A', strtotime($ticket['event_time'])); ?></p>
                        <p><strong>Ticket Type:</strong> <?= htmlspecialchars($ticket['ticket_type']); ?></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php
                  endforeach;
                  
                  if (count($tickets) === 0) {
                      echo '<div class="col-12 text-center py-5">
                              <h4 class="text-muted">No upcoming tickets available at this time</h4>
                              <p>Check back later for new events!</p>
                            </div>';
                  }
              } catch (PDOException $e) {
                  echo "<div class='col-12'><div class='alert alert-danger'>Error loading tickets: " . $e->getMessage() . "</div></div>";
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
            <h2>Why Buy From Us?</h2>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="main-button">
            <a href="faq.html">View FAQs</a>
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
            <h4>Guaranteed Authentic</h4>
            <p>All tickets are verified and 100% authentic with our anti-fraud protection.</p>
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
            <h4>Instant Delivery</h4>
            <p>Receive your tickets instantly via email or mobile delivery after purchase.</p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="item">
            <div class="icon">
              <img src="assets/images/icon-06.png" alt="">
            </div>
            <h4>Customer Support</h4>
            <p>24/7 customer service to assist you with any questions or issues.</p>
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
          The best seats for the best moments</p>
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