<?php
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/ticketpurchasecont.php';

if (isset($_GET['id'])) {
    $controller = new TicketPurchaseController();
    
    // Attempt to delete the ticket purchase
    if ($controller->deleteTicketPurchase($_GET['id'])) {
        // Success - redirect with success message
        header("Location: gestion_purchased_ticket.php.php?success=purchase_deleted");
    } else {
        // Failure - redirect with error message
        header("Location: gestion_purchased_ticket.php.php?error=delete_failed");
    }
    exit();
} else {
    // If ID not provided, redirect with error
    header("Location: gestion_purchased_ticket.php.php?error=invalid_request");
    exit();
}