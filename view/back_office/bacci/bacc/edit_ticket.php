<?php
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/ticketController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $concertName = $_POST['concert_name'];
    $artistName = $_POST['artist_name'];
    $eventDate = $_POST['event_date'];
    $eventTime = $_POST['event_time'];
    $venue = $_POST['venue'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $price = $_POST['price'];
    $ticketType = $_POST['ticket_type'];
    $quantity = $_POST['available_quantity'];
    $currentImage = $_POST['current_image'] ?? 'uploads/tickets/default.jpg';
    $imageUrl = $currentImage;

    if (!empty($_FILES['image_url']['name'])) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/uploads/tickets/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageFile = $_FILES['image_url'];
        $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if ($imageFile['error'] !== UPLOAD_ERR_OK) {
            header("Location: gestion_ticket.php?error=upload_error");
            exit();
        }

        if (!in_array($ext, $allowed)) {
            header("Location: gestion_ticket.php?error=invalid_file_type");
            exit();
        }

        $newFilename = uniqid('ticket_', true) . '.' . $ext;
        if (move_uploaded_file($imageFile['tmp_name'], $uploadDir . $newFilename)) {
            $imageUrl = 'uploads/tickets/' . $newFilename;

            if ($currentImage !== 'uploads/tickets/default.jpg' && file_exists($_SERVER['DOCUMENT_ROOT'] . '/livethemusic/' . $currentImage)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/livethemusic/' . $currentImage);
            }
        } else {
            header("Location: gestion_ticket.php?error=upload_failed");
            exit();
        }
    }

    $ticket = new Ticket();
    $ticket->setId($id);
    $ticket->setConcertName($concertName);
    $ticket->setArtistName($artistName);
    $ticket->setEventDate($eventDate);
    $ticket->setEventTime($eventTime);
    $ticket->setVenue($venue);
    $ticket->setCity($city);
    $ticket->setCountry($country);
    $ticket->setPrice($price);
    $ticket->setTicketType($ticketType);
    $ticket->setAvailableQuantity($quantity);
    $ticket->setImageUrl($imageUrl);

    $controller = new TicketController();
    $controller->updateTicket($ticket);

    header("Location: gestion_ticket.php?success=1");
    exit();
} else {
    header("Location: gestion_ticket.php?error=invalid_request");
    exit();
}
