<?php
class TicketPurchase {
    private $id;
    private $firstName;
    private $lastName;
    private $email;
    private $phone;
    private $ticketId;
    private $concertName;
    private $ticketPrice;
    private $quantity;
    private $totalAmount;
    private $purchaseDate;
    private $status;
    private $paymentMethod;
    private $transactionId;
    private $ticketCode;

    public function __construct(
        $firstName = null,
        $lastName = null,
        $email = null,
        $phone = null,
        $ticketId = null,
        $concertName = null,
        $ticketPrice = null,
        $quantity = 1,
        $totalAmount = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->ticketId = $ticketId;
        $this->concertName = $concertName;
        $this->ticketPrice = $ticketPrice;
        $this->quantity = $quantity;
        $this->totalAmount = $totalAmount;
        $this->status = 'pending';
        $this->purchaseDate = date('Y-m-d H:i:s');
    }

    // ID
    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    // First Name
    public function getFirstName() {
        return $this->firstName;
    }
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
        return $this;
    }

    // Last Name
    public function getLastName() {
        return $this->lastName;
    }
    public function setLastName($lastName) {
        $this->lastName = $lastName;
        return $this;
    }

    // Email
    public function getEmail() {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    // Phone
    public function getPhone() {
        return $this->phone;
    }
    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    // Ticket ID
    public function getTicketId() {
        return $this->ticketId;
    }
    public function setTicketId($ticketId) {
        $this->ticketId = $ticketId;
        return $this;
    }

    // Concert Name
    public function getConcertName() {
        return $this->concertName;
    }
    public function setConcertName($concertName) {
        $this->concertName = $concertName;
        return $this;
    }

    // Ticket Price
    public function getTicketPrice() {
        return $this->ticketPrice;
    }
    public function setTicketPrice($ticketPrice) {
        $this->ticketPrice = $ticketPrice;
        return $this;
    }

    // Quantity
    public function getQuantity() {
        return $this->quantity;
    }
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
        return $this;
    }

    // Total Amount
    public function getTotalAmount() {
        return $this->totalAmount;
    }
    public function setTotalAmount($totalAmount) {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    // Purchase Date
    public function getPurchaseDate() {
        return $this->purchaseDate;
    }
    public function setPurchaseDate($purchaseDate) {
        $this->purchaseDate = $purchaseDate;
        return $this;
    }

    // Status
    public function getStatus() {
        return $this->status;
    }
    public function setStatus($status) {
        $validStatuses = ['pending', 'completed', 'cancelled', 'refunded'];
        if (in_array($status, $validStatuses)) {
            $this->status = $status;
        }
        return $this;
    }

    // Payment Method
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }
    public function setPaymentMethod($paymentMethod) {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    // Transaction ID
    public function getTransactionId() {
        return $this->transactionId;
    }
    public function setTransactionId($transactionId) {
        $this->transactionId = $transactionId;
        return $this;
    }

    // Ticket Code
    public function getTicketCode() {
        return $this->ticketCode;
    }
    public function setTicketCode($ticketCode) {
        $this->ticketCode = $ticketCode;
        return $this;
    }

    /**
     * Calculate total amount based on ticket price and quantity
     */
    public function calculateTotal() {
        if ($this->ticketPrice !== null && $this->quantity !== null) {
            $this->totalAmount = $this->ticketPrice * $this->quantity;
        }
        return $this;
    }

    /**
     * Generate a unique ticket code
     */
    public function generateTicketCode() {
        $prefix = substr(strtoupper($this->concertName), 0, 3);
        $random = bin2hex(random_bytes(3));
        $this->ticketCode = $prefix . '-' . strtoupper($random);
        return $this;
    }

    /**
     * Mark purchase as completed
     */
    public function completePurchase($paymentMethod, $transactionId) {
        $this->status = 'completed';
        $this->paymentMethod = $paymentMethod;
        $this->transactionId = $transactionId;
        $this->generateTicketCode();
        return $this;
    }

    /**
     * Cancel the purchase
     */
    public function cancelPurchase() {
        $this->status = 'cancelled';
        return $this;
    }

    /**
     * Mark as refunded
     */
    public function refundPurchase() {
        $this->status = 'refunded';
        return $this;
    }

    /**
     * Convert object to array for database operations
     */
    public function toArray() {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'ticket_id' => $this->ticketId,
            'concert_name' => $this->concertName,
            'ticket_price' => $this->ticketPrice,
            'quantity' => $this->quantity,
            'total_amount' => $this->totalAmount,
            'purchase_date' => $this->purchaseDate,
            'status' => $this->status,
            'payment_method' => $this->paymentMethod,
            'transaction_id' => $this->transactionId,
            'ticket_code' => $this->ticketCode
        ];
    }

    /**
     * Get formatted purchase date
     */
    public function getFormattedPurchaseDate($format = 'F j, Y g:i a') {
        $date = new DateTime($this->purchaseDate);
        return $date->format($format);
    }

    /**
     * Get customer full name
     */
    public function getCustomerFullName() {
        return $this->firstName . ' ' . $this->lastName;
    }
}