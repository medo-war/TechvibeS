<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="author" content="templatemo">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <title>LiveTheMusic - Concert Tickets</title>

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
            transition: transform 0.3s;
            margin-bottom: 30px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .ticket-card:hover {
            transform: translateY(-10px);
        }
        .ticket-header {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: white;
            padding: 15px;
        }
        .ticket-price {
            font-size: 24px;
            font-weight: bold;
            color: #ffd700;
        }
        .ticket-body {
            padding: 20px;
            background: white;
        }
        .btn-purchase {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .concert-date {
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .venue-info {
            color: #6c757d;
            font-size: 14px;
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
                        <img src="assets/images/logo.png" alt="">
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
          <h3>Upcoming Concerts</h3>
          <h6>Get your tickets before they sell out!</h6>
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
            <h2>Available <em>Concert Tickets</em></h2>
          </div>
        </div>
        
        <?php
        // Static concert data
        $concerts = [
            [
                'id' => 1,
                'performer_name' => 'The Rolling Stones',
                'performer_image' => 'assets/images/rolling-stones.jpg',
                'date' => '2023-12-15',
                'time' => '20:00',
                'venue' => 'Madison Square Garden',
                'city' => 'New York',
                'country' => 'USA',
                'price' => 199.99,
                'genre' => 'Rock',
                'is_weekend' => true,
                'available_tickets' => 42,
                'description' => 'Legendary rock band The Rolling Stones performing their greatest hits in a 3-hour spectacular show.'
            ],
            [
                'id' => 2,
                'performer_name' => 'Beyoncé',
                'performer_image' => 'assets/images/beyonce.jpg',
                'date' => '2023-11-28',
                'time' => '19:30',
                'venue' => 'Stade de France',
                'city' => 'Paris',
                'country' => 'France',
                'price' => 149.50,
                'genre' => 'Pop/R&B',
                'is_weekend' => false,
                'available_tickets' => 15,
                'description' => 'Queen B returns to Europe with her Renaissance World Tour. Expect stunning visuals and powerful vocals.'
            ],
            [
                'id' => 3,
                'performer_name' => 'Hans Zimmer',
                'performer_image' => 'assets/images/hans-zimmer.jpg',
                'date' => '2024-02-10',
                'time' => '19:00',
                'venue' => 'Royal Albert Hall',
                'city' => 'London',
                'country' => 'UK',
                'price' => 89.99,
                'genre' => 'Film Score',
                'is_weekend' => true,
                'available_tickets' => 78,
                'description' => 'The Oscar-winning composer performs his greatest film scores live with a full orchestra.'
            ],
            [
                'id' => 4,
                'performer_name' => 'Blackpink',
                'performer_image' => 'assets/images/blackpink.jpg',
                'date' => '2023-12-05',
                'time' => '20:00',
                'venue' => 'Tokyo Dome',
                'city' => 'Tokyo',
                'country' => 'Japan',
                'price' => 129.99,
                'genre' => 'K-Pop',
                'is_weekend' => false,
                'available_tickets' => 3,
                'description' => 'The global K-Pop sensation brings their Born Pink World Tour to Tokyo for one night only.'
            ],
            [
                'id' => 5,
                'performer_name' => 'Metallica',
                'performer_image' => 'assets/images/metallica.jpg',
                'date' => '2024-01-20',
                'time' => '19:30',
                'venue' => 'Foro Sol',
                'city' => 'Mexico City',
                'country' => 'Mexico',
                'price' => 179.00,
                'genre' => 'Metal',
                'is_weekend' => true,
                'available_tickets' => 112,
                'description' => 'Thrash metal legends Metallica perform their classic albums Master of Puppets and Ride the Lightning in full.'
            ],
            [
                'id' => 6,
                'performer_name' => 'Taylor Swift',
                'performer_image' => 'assets/images/taylor-swift.jpg',
                'date' => '2024-03-08',
                'time' => '18:30',
                'venue' => 'SoFi Stadium',
                'city' => 'Los Angeles',
                'country' => 'USA',
                'price' => 249.99,
                'genre' => 'Pop',
                'is_weekend' => true,
                'available_tickets' => 0,
                'description' => 'The Eras Tour continues with a special 3-hour performance covering all of Taylor Swift\'s musical eras.'
            ]
        ];
        
        foreach ($concerts as $concert):
            $date = new DateTime($concert['date']);
        ?>
        <div class="col-lg-4 col-md-6">
            <div class="ticket-card">
                <div class="ticket-header">
                    <h4><?= htmlspecialchars($concert['performer_name']) ?></h4>
                    <div class="concert-date">
                        <?= $date->format('F j, Y') ?> at <?= $concert['time'] ?>
                    </div>
                </div>
                <div class="ticket-body">
                    <div class="text-center mb-3">
                        <img src="<?= htmlspecialchars($concert['performer_image']) ?>" 
                             alt="<?= htmlspecialchars($concert['performer_name']) ?>" 
                             class="img-fluid rounded" 
                             style="max-height: 150px;">
                    </div>
                    
                    <p class="venue-info">
                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($concert['venue']) ?><br>
                        <i class="fas fa-city"></i> <?= htmlspecialchars($concert['city']) ?>, <?= htmlspecialchars($concert['country']) ?>
                    </p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge badge-primary"><?= htmlspecialchars($concert['genre']) ?></span>
                            <?php if ($concert['is_weekend']): ?>
                                <span class="badge badge-success">Weekend</span>
                            <?php endif; ?>
                        </div>
                        <div class="ticket-price">$<?= number_format($concert['price'], 2) ?></div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-outline-secondary" data-toggle="collapse" 
                                data-target="#details-<?= $concert['id'] ?>">
                            Details
                        </button>
                        <?php if ($concert['available_tickets'] > 0): ?>
                            <button class="btn-purchase" 
                                    onclick="window.location.href='purchase.php?id=<?= $concert['id'] ?>'">
                                Buy Ticket
                            </button>
                        <?php else: ?>
                            <button class="btn btn-danger" disabled>
                                Sold Out
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <div class="collapse mt-3" id="details-<?= $concert['id'] ?>">
                        <div class="card card-body">
                            <p><?= nl2br(htmlspecialchars($concert['description'])) ?></p>
                            <p><strong>Available tickets:</strong> <?= $concert['available_tickets'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <p>Copyright © 2023 <a href="#">LiveTheMusic</a>. All rights reserved.
          &nbsp;&nbsp;
          Designed by <a title="HTML CSS Templates" rel="sponsored" href="https://templatemo.com" target="_blank">TemplateMo</a></p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/popup.js"></script>
  <script src="assets/js/custom.js"></script>
  
  <script>
    // Highlight current date in calendar
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.querySelectorAll('.concert-date').forEach(el => {
            if (el.getAttribute('data-date') === today) {
                el.classList.add('today');
            }
        });
    });
  </script>
</body>
</html>