<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/Config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/concertController.php';

$pdo = config::getConnexion();

// Create ticket_purchases table if it doesn't exist
try {
    $checkTable = $pdo->query("SHOW TABLES LIKE 'ticket_purchases'");
    if ($checkTable->rowCount() == 0) {
        $createTable = $pdo->exec("CREATE TABLE ticket_purchases (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            ticket_id VARCHAR(50) NOT NULL,
            concert_name VARCHAR(200) NOT NULL,
            ticket_price DECIMAL(10,2) NOT NULL,
            quantity INT NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status VARCHAR(20) DEFAULT 'completed',
            payment_method VARCHAR(50) NOT NULL,
            transaction_id VARCHAR(100),
            ticket_code VARCHAR(20) NOT NULL
        )");
        
        if ($createTable) {
            error_log("Created ticket_purchases table successfully");
        }
    }
} catch (PDOException $e) {
    error_log("Error creating ticket_purchases table: " . $e->getMessage());
}

// Check if concert ID is provided
if (isset($_GET['concert_id'])) {
    $concert_id = $_GET['concert_id'];
    
    // Get concert details
    $stmt = $pdo->prepare("SELECT c.*, l.nom_lieux, l.adresse FROM concert c JOIN lieux l ON c.id_lieux = l.id_lieux WHERE c.id_concert = ?");
    $stmt->execute([$concert_id]);
    $concert = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($concert) {
        // Create a ticket object from concert data
        // Format the image path correctly
        $imagePath = !empty($concert['image']) 
            ? '/livethemusic/' . $concert['image']
            : '/livethemusic/view/front_office/assets/images/default-concert.jpg';
            
        $ticket = [
            'id' => 'C' . $concert_id, // Prefix with 'C' to indicate it's from concert table
            'concert_name' => $concert['nom_lieux'] . ' Concert',
            'artist_name' => $concert['genre'] . ' Artist',
            'event_date' => $concert['date_concert'],
            'event_time' => '20:00:00', // Default time
            'venue' => $concert['nom_lieux'],
            'city' => explode(',', $concert['adresse'])[0],
            'country' => 'Tunisie',
            'price' => $concert['prix_concert'],
            'ticket_type' => 'General Admission',
            'available_quantity' => $concert['place_dispo'],
            'image_url' => $imagePath
        ];
        
        // Set ticket_id for form processing
        $ticket_id = 'C' . $concert_id;
    } else {
        // Concert not found
        header("Location: events.php?error=concert_not_found");
        exit();
    }
} 
// Check if ticket ID is provided (backward compatibility)
else if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];
    
    // Get ticket details
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If ticket doesn't exist, redirect
    if (!$ticket) {
        header("Location: events.php");
        exit();
    }
} else {
    // No ID provided
    header("Location: events.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $quantity = (int)$_POST['quantity'];
    $payment_method = htmlspecialchars($_POST['payment_method']);
    
    // Validate inputs
    $errors = [];
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (!$email) $errors[] = "Valid email is required";
    if ($quantity < 1 || $quantity > 10) $errors[] = "Quantity must be between 1 and 10";
    
    if (empty($errors)) {
        // Calculate totals
        $service_fee = $ticket['price'] * 0.1;
        $subtotal = $ticket['price'] * $quantity;
        $total = $subtotal + ($service_fee * $quantity);
        
        // Generate unique ticket code
        $ticket_code = 'TKT-' . strtoupper(substr(md5(uniqid()), 0, 8));
        
        try {
            // Insert purchase record
            $sql = "INSERT INTO ticket_purchases (
                first_name, last_name, email, phone, ticket_id, 
                concert_name, ticket_price, quantity, total_amount,
                payment_method, ticket_code
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $first_name, $last_name, $email, $phone, $ticket_id,
                $ticket['concert_name'], $ticket['price'], $quantity, $total,
                $payment_method, $ticket_code
            ]);
            
            // Update available quantity based on purchase type
            if (isset($_GET['concert_id'])) {
                // For concert purchases, update the concert table
                $update_sql = "UPDATE concert SET place_dispo = place_dispo - ? WHERE id_concert = ?";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->execute([$quantity, $_GET['concert_id']]);
            } else if (isset($_GET['id'])) {
                // For ticket purchases, update the tickets table
                $update_sql = "UPDATE tickets SET available_quantity = available_quantity - ? WHERE id = ?";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->execute([$quantity, $ticket_id]);
            }
            
            // Redirect to success page with ticket information for email and PDF generation
            header("Location: ../../Mailing/sendmail.php?email=" . urlencode($email) .
                "&ticket_code=" . urlencode($ticket_code) .
                "&concert_name=" . urlencode($ticket['concert_name']) .
                "&event_date=" . urlencode($ticket['event_date']) .
                "&event_time=" . urlencode($ticket['event_time']) .
                "&artist_name=" . urlencode($ticket['artist_name']) .
                "&venue=" . urlencode($ticket['venue']) .
                "&city=" . urlencode($ticket['city']) .
                "&price=" . urlencode($ticket['price']));
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Calculate initial totals for display
$service_fee = $ticket['price'] * 0.1;
$subtotal = $ticket['price'];
$total = $subtotal + $service_fee;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Ticket - LiveTheMusic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(90deg, #6a0dad, #b535f6);
            --dark-bg: #1a1a2e;
            --card-bg: #2a2a3e;
            --text-light: #f8f9fa;
            --text-muted: #a1a1b3;
            --accent-hover: #9a32e4;
            --shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a2e, #2a2a3e);
            color: var(--text-light);
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        .container {
            padding: 60px 20px;
            max-width: 1200px;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?= $ticket['image_url'] ?>');
            background-size: cover;
            background-position: center;
            color: var(--text-light);
            padding: 120px 0;
            text-align: center;
            border-radius: 20px;
            margin-bottom: 50px;
            box-shadow: var(--shadow);
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hero-section p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 10px auto;
        }

        /* Festival Card Styling */
        .festival-card {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            height: 100%;
        }

        .festival-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.5);
        }

        .festival-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            filter: brightness(0.9);
        }

        .festival-body {
            padding: 25px;
            text-align: center;
        }

        .festival-body h4 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-light);
        }

        .festival-info {
            font-size: 1rem;
            color: var(--text-muted);
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .explore-btn {
            background: var(--primary-gradient);
            color: white;
            font-weight: 500;
            padding: 12px 30px;
            border-radius: 25px;
            border: none;
            margin-top: 15px;
            transition: background 0.3s ease, transform 0.2s ease;
            display: inline-block;
        }

        .explore-btn:hover {
            background: linear-gradient(90deg, #9a32e4, #d953f6);
            transform: scale(1.05);
        }

        /* Form Card Styling */
        .form-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow);
            max-width: 600px;
            margin: 0 auto 50px;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-light);
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        .form-control, .form-select {
            background-color: #3a3a4e;
            border: 2px solid #4a4a5e;
            color: var(--text-light);
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #9a32e4;
            box-shadow: 0 0 0 4px rgba(106, 13, 173, 0.2);
            background-color: #3a3a4e;
            color: var(--text-light);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            color: white;
            font-weight: 600;
            padding: 14px 30px;
            border-radius: 12px;
            font-size: 1.1rem;
            transition: background 0.3s ease, transform 0.2s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #9a32e4, #d953f6);
            transform: scale(1.02);
        }

        /* Summary Card */
        .summary-card {
            background: #3a3a4e;
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
            border: 1px solid #4a4a5e;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: var(--text-muted);
            font-size: 1rem;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-weight: 600;
            font-size: 1.2rem;
            padding-top: 15px;
            border-top: 2px solid #4a4a5e;
            margin-top: 15px;
            color: var(--text-light);
        }

        .error-message {
            color: #ff5555;
            font-size: 0.95rem;
            margin-top: 5px;
            font-weight: 500;
        }

        .alert-danger {
            background-color: #ff4444;
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
            border: none;
            font-size: 1rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }
            .hero-section p {
                font-size: 1rem;
            }
            .festival-card {
                margin-bottom: 30px;
            }
            .form-card {
                padding: 20px;
            }
            .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1><?= htmlspecialchars($ticket['concert_name']) ?></h1>
            <p class="lead"><?= htmlspecialchars($ticket['artist_name']) ?> - An Unforgettable Experience</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <!-- Ticket Details -->
            <div class="col-lg-5">
                <div class="festival-card">
                    <img src="<?= htmlspecialchars($ticket['image_url']) ?>" alt="<?= htmlspecialchars($ticket['concert_name']) ?>" class="festival-img">
                    <div class="festival-body">
                        <h4><?= htmlspecialchars($ticket['concert_name']) ?></h4>
                        <p class="festival-info"><strong>Artist:</strong> <?= htmlspecialchars($ticket['artist_name']) ?></p>
                        <p class="festival-info"><strong>Date:</strong> <?= date('F j, Y', strtotime($ticket['event_date'])) ?></p>
                        <p class="festival-info"><strong>Time:</strong> <?= htmlspecialchars($ticket['event_time']) ?></p>
                        <p class="festival-info"><strong>Location:</strong> <?= htmlspecialchars($ticket['venue']) ?>, <?= htmlspecialchars($ticket['city']) ?></p>
                        <p class="festival-info"><strong>Type:</strong> <?= htmlspecialchars($ticket['ticket_type']) ?></p>
                        <button class="explore-btn">$<?= number_format($ticket['price'], 2) ?> per ticket</button>
                    </div>
                </div>
            </div>
            
            <!-- Purchase Form -->
            <div class="col-lg-7">
                <div class="form-card">
                    <h2 class="mb-4">Purchase Details</h2>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        
                        <div class="mb-4">
                            <label for="quantity" class="form-label">Number of Tickets</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="10" value="1" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required onchange="showPaymentDetails()">
                                <option value="">Select payment method</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <!-- Payment Details Sections -->
                        <div id="credit_card_info" class="payment-info" style="display: none;">
                            <h5>Credit Card Information</h5>
                            <input type="text" class="form-control mb-3" placeholder="Card Number" name="card_number" />
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" placeholder="Expiry Date (MM/YY)" name="expiry_date" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" placeholder="CVV" name="cvv" />
                                </div>
                            </div>
                        </div>

                        <div id="paypal_info" class="payment-info" style="display: none;">
                            <h5>Pay with PayPal</h5>
                            <div id="paypal-button-container" class="text-center"></div>
                        </div>

                        <div id="bank_transfer_info" class="payment-info" style="display: none;">
                            <h5>Bank Transfer Details</h5>
                            <p class="mb-3">Please transfer the total amount to the following account:</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><strong>Bank:</strong> MyBank</li>
                                <li class="mb-2"><strong>IBAN:</strong> FR76 3000 6000 0112 3456 7890 189</li>
                                <li><strong>BIC:</strong> AGRIFRPP</li>
                            </ul>
                        </div>
                        
                        <div class="summary-card">
                            <h5>Order Summary</h5>
                            <div class="summary-item">
                                <span>Price per ticket:</span>
                                <span id="price-per-ticket">$<?= number_format($ticket['price'], 2) ?></span>
                            </div>
                            <div class="summary-item">
                                <span>Service fee:</span>
                                <span id="service-fee">$<?= number_format($service_fee, 2) ?></span>
                            </div>
                            <div class="summary-total">
                                <span>Total:</span>
                                <span id="total-price">$<?= number_format($total, 2) ?></span>
                            </div>
                        </div>
                        
                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg">Complete Purchase</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Festival Cards Section -->
        <div class="row mt-5">
            <h2 class="mb-4 text-center">Explore More Festivals</h2>
            <div class="col-md-4">
                <div class="festival-card">
                    <img src="https://example.com/lollapalooza.jpg" alt="Lollapalooza" class="festival-img">
                    <div class="festival-body">
                        <h4>Lollapalooza</h4>
                        <p class="festival-info"><strong>Location:</strong> Chicago (USA), Berlin (Germany), Paris (France)</p>
                        <p class="festival-info"><strong>Category:</strong> Rock, Hip-Hop, EDM, Pop</p>
                        <a href="#" class="explore-btn">Explore</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="festival-card">
                    <img src="https://example.com/burningman.jpg" alt="Burning Man" class="festival-img">
                    <div class="festival-body">
                        <h4>Burning Man</h4>
                        <p class="festival-info"><strong>Location:</strong> Black Rock Desert, Nevada</p>
                        <p class="festival-info"><strong>Category:</strong> Experimental, Electronic, Ambient</p>
                        <a href="#" class="explore-btn">Explore</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="festival-card">
                    <img src="https://example.com/ultra.jpg" alt="Ultra Music Festival" class="festival-img">
                    <div class="festival-body">
                        <h4>Ultra Music Festival</h4>
                        <p class="festival-info"><strong>Location:</strong> Miami (USA), Seoul (South Korea)</p>
                        <p class="festival-info"><strong>Category:</strong> EDM, House, Techno, Dubstep</p>
                        <a href="#" class="explore-btn">Explore</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update totals when quantity changes
        document.getElementById('quantity').addEventListener('change', function() {
            const quantity = parseInt(this.value);
            const pricePerTicket = <?= $ticket['price'] ?>;
            const serviceFeePerTicket = pricePerTicket * 0.1;
            
            const subtotal = pricePerTicket * quantity;
            const totalServiceFee = serviceFeePerTicket * quantity;
            const total = subtotal + totalServiceFee;
            
            document.getElementById('price-per-ticket').textContent = '$' + pricePerTicket.toFixed(2);
            document.getElementById('service-fee').textContent = '$' + totalServiceFee.toFixed(2);
            document.getElementById('total-price').textContent = '$' + total.toFixed(2);
        });

        function showPaymentDetails() {
            const selected = document.getElementById("payment_method").value;
            document.querySelectorAll(".payment-info").forEach(div => div.style.display = "none");
            if (selected === "credit_card") document.getElementById("credit_card_info").style.display = "block";
            else if (selected === "paypal") document.getElementById("paypal_info").style.display = "block";
            else if (selected === "bank_transfer") document.getElementById("bank_transfer_info").style.display = "block";
        }
    </script>
    <script src="https://www.paypal.com/sdk/js?client-id=AXY0Jhcnhly9YBUa5n_sZph4xZaEoQ4E_m2n_sAHHJcomGbkq55Keaj1PrEGYOYd2KK7gA9OvmMvy1oz"></script>
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '10.00' // Replace with dynamic total
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Payment completed by ' + details.payer.name.given_name + '!');
                });
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>