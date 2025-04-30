<?php
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/ticketcontroller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $concertName = $_POST['concert_name'];
    $artistName = $_POST['artist_name'];
    $eventDate = $_POST['event_date'];
    $eventTime = $_POST['event_time'];
    $venue = $_POST['venue'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $price = $_POST['price'];
    $ticketType = $_POST['ticket_type'];
    $availableQuantity = $_POST['available_quantity'];
    $imageUrl = 'uploads/tickets/default_ticket.jpg'; // default image

    // Handle image upload
    if (!empty($_FILES['image_url']['name'])) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/uploads/tickets/';

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageFile = $_FILES['image_url'];
        $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed)) {
            header("Location: gestion_ticket.php?error=invalid_file");
            exit();
        }

        $newFilename = uniqid('ticket_', true) . '.' . $ext;
        $imagePath = $uploadDir . $newFilename;

        if (move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
            $imageUrl = 'uploads/tickets/' . $newFilename;
        } else {
            header("Location: gestion_ticket.php?error=upload_fail");
            exit();
        }
    }

    // Create Ticket object and save (assumes a Ticket class with setters)
    $ticket = new Ticket();
    $ticket->setConcertName($concertName);
    $ticket->setArtistName($artistName);
    $ticket->setEventDate($eventDate);
    $ticket->setEventTime($eventTime);
    $ticket->setVenue($venue);
    $ticket->setCity($city);
    $ticket->setCountry($country);
    $ticket->setPrice($price);
    $ticket->setTicketType($ticketType);
    $ticket->setAvailableQuantity($availableQuantity);
    $ticket->setImageUrl($imageUrl);

    $ticketController = new TicketController();
    $ticketController->addTicket($ticket);

    header("Location: gestion_ticket.php?success=1");
    exit();
} else {
    header("Location: gestion_ticket.php?error=invalid_request");
    exit();
}
