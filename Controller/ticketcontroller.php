<?php

require 'Config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Model/ticket.php';

class TicketController {
    
    // Add a new ticket to the database
    public function addTicket($ticket) {
        $db = config::getConnexion();
        $sql = "INSERT INTO tickets (
                    concert_name, 
                    artist_name, 
                    event_date, 
                    event_time, 
                    venue, 
                    city, 
                    country, 
                    price, 
                    ticket_type, 
                    available_quantity, 
                    image_url
                ) VALUES (
                    :concert_name, 
                    :artist_name, 
                    :event_date, 
                    :event_time, 
                    :venue, 
                    :city, 
                    :country, 
                    :price, 
                    :ticket_type, 
                    :available_quantity, 
                    :image_url
                )";

        try {
            $query = $db->prepare($sql);
            $query->execute(array(
                'concert_name' => $ticket->getConcertName(),
                'artist_name' => $ticket->getArtistName(),
                'event_date' => $ticket->getEventDate(),
                'event_time' => $ticket->getEventTime(),
                'venue' => $ticket->getVenue(),
                'city' => $ticket->getCity(),
                'country' => $ticket->getCountry(),
                'price' => $ticket->getPrice(),
                'ticket_type' => $ticket->getTicketType(),
                'available_quantity' => $ticket->getAvailableQuantity(),
                'image_url' => $ticket->getImageUrl()
            ));
            return true; // Return success status
        }
        catch(PDOException $e) {
            error_log("Error adding ticket: " . $e->getMessage());
            return false; // Return failure status
        }
    }

    // Get all tickets from the database
    public function getTickets() {
        $sql = "SELECT * FROM tickets";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Get tickets by artist name
    public function getTicketsByArtist($artist_name) {
        $sql = "SELECT * FROM tickets WHERE artist_name = :artist_name";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['artist_name' => $artist_name]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Get upcoming tickets (future events)
    public function getUpcomingTickets() {
        $sql = "SELECT * FROM tickets WHERE event_date >= CURDATE() ORDER BY event_date ASC";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Update an existing ticket in the database
    public function updateTicket($ticket) {
        $db = config::getConnexion();
        $sql = "UPDATE tickets SET 
                    concert_name = :concert_name, 
                    artist_name = :artist_name, 
                    event_date = :event_date, 
                    event_time = :event_time, 
                    venue = :venue, 
                    city = :city, 
                    country = :country, 
                    price = :price, 
                    ticket_type = :ticket_type, 
                    available_quantity = :available_quantity, 
                    image_url = :image_url 
                WHERE id = :id";

        try {
            $query = $db->prepare($sql);
            $query->execute(array(
                'concert_name' => $ticket->getConcertName(),
                'artist_name' => $ticket->getArtistName(),
                'event_date' => $ticket->getEventDate(),
                'event_time' => $ticket->getEventTime(),
                'venue' => $ticket->getVenue(),
                'city' => $ticket->getCity(),
                'country' => $ticket->getCountry(),
                'price' => $ticket->getPrice(),
                'ticket_type' => $ticket->getTicketType(),
                'available_quantity' => $ticket->getAvailableQuantity(),
                'image_url' => $ticket->getImageUrl(),
                'id' => $ticket->getId()
            ));
            return true;
        }
        catch(PDOException $e) {
            error_log("Error updating ticket: " . $e->getMessage());
            return false;
        }
    }

    // Delete a ticket from the database
    public function deleteTicket($id) {
        $db = config::getConnexion();
        $sql = "DELETE FROM tickets WHERE id = :id";

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;  // Return true if successful
        } catch (PDOException $e) {
            error_log("Error deleting ticket: " . $e->getMessage());
            return false; // Return false in case of error
        }
    }

    // Decrease available quantity when tickets are sold
    public function sellTickets($ticket_id, $quantity) {
        $db = config::getConnexion();
        $sql = "UPDATE tickets SET available_quantity = available_quantity - :quantity 
                WHERE id = :id AND available_quantity >= :quantity";

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'quantity' => $quantity,
                'id' => $ticket_id
            ]);
            return $query->rowCount() > 0; // Returns true if quantity was updated
        }
        catch(PDOException $e) {
            error_log("Error selling tickets: " . $e->getMessage());
            return false;
        }
    }

    // Increase available quantity if tickets are returned
    public function returnTickets($ticket_id, $quantity) {
        $db = config::getConnexion();
        $sql = "UPDATE tickets SET available_quantity = available_quantity + :quantity 
                WHERE id = :id";

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'quantity' => $quantity,
                'id' => $ticket_id
            ]);
            return true;
        }
        catch(PDOException $e) {
            error_log("Error returning tickets: " . $e->getMessage());
            return false;
        }
    }
}
?>