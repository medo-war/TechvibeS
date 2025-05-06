<?php
require $_SERVER['DOCUMENT_ROOT'] . '/TechvibeS/Controller/Config.php';

$pdo = config::getConnexion();
// Check if ticket ID is provided
if (!isset($_GET['id'])) {
    header("Location: ticket.php");
    exit();
}

$ticket_id = $_GET['id'];

// Get ticket details
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

// If ticket doesn't exist, redirect
if (!$ticket) {
    header("Location: ticket.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: ticket.php");
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
            
            // Update available quantity
            $update_sql = "UPDATE tickets SET available_quantity = available_quantity - ? WHERE id = ?";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([$quantity, $ticket_id]);
            

            // Redirect to success page
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
            --primary-color: #f5a623;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .navbar {
            background-color: var(--dark-color);
        }
        
        .navbar-brand img {
            height: 40px;
        }
        
        .nav-link {
            color: white !important;
            font-weight: 500;
        }
        
        .nav-link.active {
            color: var(--primary-color) !important;
        }
        
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('<?= $ticket['image_url'] ?>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
        }
        
        .hero-section p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .ticket-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .ticket-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .ticket-body {
            padding: 20px;
        }
        
        .info-item {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .info-item i {
            color: var(--primary-color);
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .price-tag {
            background: var(--primary-color);
            color: black;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 10px;
        }
        
        .form-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .form-label {
            font-weight: 500;
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(245, 166, 35, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: black;
            font-weight: 600;
            padding: 12px 25px;
            border-radius: 8px;
        }
        
        .btn-primary:hover {
            background-color: #e69100;
            border-color: #e69100;
        }
        
        .summary-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 1.1rem;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            margin-top: 10px;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .alert-danger {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1><?= htmlspecialchars($ticket['concert_name']) ?></h1>
            <p class="lead"><?= htmlspecialchars($ticket['artist_name']) ?></p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <!-- Ticket Details -->
            <div class="col-lg-5">
                <div class="ticket-card">
                    <img src="<?= htmlspecialchars($ticket['image_url']) ?>" alt="<?= htmlspecialchars($ticket['concert_name']) ?>" class="ticket-img">
                    <div class="ticket-body">
                        <h3><?= htmlspecialchars($ticket['concert_name']) ?></h3>
                        <p class="text-muted"><?= htmlspecialchars($ticket['artist_name']) ?></p>
                        
                        <div class="info-item">
                            <i class="bi bi-calendar-event"></i>
                            <span><?= date('F j, Y', strtotime($ticket['event_date'])) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-clock"></i>
                            <span><?= htmlspecialchars($ticket['event_time']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-geo-alt"></i>
                            <span><?= htmlspecialchars($ticket['venue']) ?>, <?= htmlspecialchars($ticket['city']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-tag"></i>
                            <span><?= htmlspecialchars($ticket['ticket_type']) ?></span>
                        </div>
                        
                        <div class="price-tag">
                            $<?= number_format($ticket['price'], 2) ?> per ticket
                        </div>
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
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Number of Tickets</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="10" value="1" required>
                        </div>
                        
                        <div class="mb-3">
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
                            <input type="text" class="form-control mb-2" placeholder="Card Number" name="card_number" />
                            <input type="text" class="form-control mb-2" placeholder="Expiry Date (MM/YY)" name="expiry_date" />
                            <input type="text" class="form-control mb-2" placeholder="CVV" name="cvv" />
                        </div>

                        <div id="paypal_info" class="payment-info" style="display: none;">
                            <h5>Pay with PayPal</h5>
                            <div id="paypal-button-container"></div>
                        </div>

                        <div id="bank_transfer_info" class="payment-info" style="display: none;">
                            <h5>Bank Transfer Details</h5>
                            <p>Please transfer the total amount to the following account:</p>
                            <ul>
                                <li><strong>Bank:</strong> MyBank</li>
                                <li><strong>IBAN:</strong> FR76 3000 6000 0112 3456 7890 189</li>
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
                        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Complete Purchase</button>
                        </div>
                    </form>
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
            
            // Calculate new totals
            const subtotal = pricePerTicket * quantity;
            const totalServiceFee = serviceFeePerTicket * quantity;
            const total = subtotal + totalServiceFee;
            
            // Update display
            document.getElementById('price-per-ticket').textContent = '$' + pricePerTicket.toFixed(2);
            document.getElementById('service-fee').textContent = '$' + totalServiceFee.toFixed(2);
            document.getElementById('total-price').textContent = '$' + total.toFixed(2);
        });
    </script>
    <script src="https://www.paypal.com/sdk/js?client-id=AXY0Jhcnhly9YBUa5n_sZph4xZaEoQ4E_m2n_sAHHJcomGbkq55Keaj1PrEGYOYd2KK7gA9OvmMvy1oz"></script>

    <script>
        function showPaymentDetails() {
            const selected = document.getElementById("payment_method").value;
            
            // Hide all
            document.querySelectorAll(".payment-info").forEach(div => div.style.display = "none");
            
            // Show selected
            if (selected === "credit_card") {
                document.getElementById("credit_card_info").style.display = "block";
            } else if (selected === "paypal") {
                document.getElementById("paypal_info").style.display = "block";
            } else if (selected === "bank_transfer") {
                document.getElementById("bank_transfer_info").style.display = "block";
            }
        }
        </script>
        <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                // This is just a demo setup — you should dynamically pass the amount
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
                    // Redirect or submit form here
                });
            }
        }).render('#paypal-button-container'); // This ID must match your container
        </script>

</body>
</html>