<?php
require 'Config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Model/ticketpurchase.php';

class TicketPurchaseController {
    
    // Add a new ticket purchase to the database
    public function addTicketPurchase($purchase) {
        $db = config::getConnexion();
        $sql = "INSERT INTO ticket_purchases 
                (first_name, last_name, email, phone, ticket_id, concert_name, 
                 ticket_price, quantity, total_amount, purchase_date, status, 
                 payment_method, transaction_id, ticket_code) 
                VALUES 
                (:first_name, :last_name, :email, :phone, :ticket_id, :concert_name, 
                 :ticket_price, :quantity, :total_amount, :purchase_date, :status, 
                 :payment_method, :transaction_id, :ticket_code)";

        try {
            $query = $db->prepare($sql);
            $query->execute(array(
                'first_name' => $purchase->getFirstName(),
                'last_name' => $purchase->getLastName(),
                'email' => $purchase->getEmail(),
                'phone' => $purchase->getPhone(),
                'ticket_id' => $purchase->getTicketId(),
                'concert_name' => $purchase->getConcertName(),
                'ticket_price' => $purchase->getTicketPrice(),
                'quantity' => $purchase->getQuantity(),
                'total_amount' => $purchase->getTotalAmount(),
                'purchase_date' => $purchase->getPurchaseDate(),
                'status' => $purchase->getStatus(),
                'payment_method' => $purchase->getPaymentMethod(),
                'transaction_id' => $purchase->getTransactionId(),
                'ticket_code' => $purchase->getTicketCode()
            ));
            return true; // Return success status
        }
        catch(PDOException $e) {
            error_log("Error adding ticket purchase: " . $e->getMessage());
            return false; // Return failure status
        }
    }

    // Get all ticket purchases from the database
    public function getTicketPurchases() {
        $sql = "SELECT * FROM ticket_purchases";
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

    // Get ticket purchases by user email
    public function getPurchasesByEmail($email) {
        $sql = "SELECT * FROM ticket_purchases WHERE email = :email";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(array('email' => $email));
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Update a ticket purchase in the database
    public function updateTicketPurchase($purchase) {
        $db = config::getConnexion();
        $sql = "UPDATE ticket_purchases SET 
                first_name = :first_name, 
                last_name = :last_name, 
                email = :email, 
                phone = :phone, 
                status = :status, 
                payment_method = :payment_method, 
                transaction_id = :transaction_id
                WHERE id = :id";

        try {
            $query = $db->prepare($sql);
            $query->execute(array(
                'first_name' => $purchase->getFirstName(),
                'last_name' => $purchase->getLastName(),
                'email' => $purchase->getEmail(),
                'phone' => $purchase->getPhone(),
                'status' => $purchase->getStatus(),
                'payment_method' => $purchase->getPaymentMethod(),
                'transaction_id' => $purchase->getTransactionId(),
                'id' => $purchase->getId()
            ));
            return true;
        }
        catch(PDOException $e) {
            error_log("Error updating ticket purchase: " . $e->getMessage());
            return false;
        }
    }

    // Delete a ticket purchase from the database
    public function deleteTicketPurchase($id) {
        $db = config::getConnexion();
        $sql = "DELETE FROM ticket_purchases WHERE id = :id";

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;  // Return true if successful
        } catch (PDOException $e) {
            error_log("Error deleting ticket purchase: " . $e->getMessage());
            return false;
        }
    }

    // Get a single ticket purchase by ID
    public function getPurchaseById($id) {
        $sql = "SELECT * FROM ticket_purchases WHERE id = :id";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(array('id' => $id));
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Update the status of a ticket purchase
    public function updatePurchaseStatus($id, $status) {
        $db = config::getConnexion();
        $sql = "UPDATE ticket_purchases SET status = :status WHERE id = :id";

        try {
            $query = $db->prepare($sql);
            $query->execute(array(
                'status' => $status,
                'id' => $id
            ));
            return true;
        }
        catch(PDOException $e) {
            error_log("Error updating ticket purchase status: " . $e->getMessage());
            return false;
        }
    }
}
?>