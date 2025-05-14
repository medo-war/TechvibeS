<?php
class Ticket {
    private $id;
    private $concert_name;
    private $artist_name;
    private $event_date;
    private $event_time;
    private $venue;
    private $city;
    private $country;
    private $price;
    private $ticket_type;
    private $available_quantity;
    private $image_url;
    private $created_at;
    private $updated_at;

    public function __construct(
        $concert_name = null,
        $artist_name = null,
        $event_date = null,
        $event_time = null,
        $venue = null,
        $city = null,
        $country = null,
        $price = null,
        $ticket_type = 'General Admission',
        $available_quantity = 0,
        $image_url = null
    ) {
        $this->concert_name = $concert_name;
        $this->artist_name = $artist_name;
        $this->event_date = $event_date;
        $this->event_time = $event_time;
        $this->venue = $venue;
        $this->city = $city;
        $this->country = $country;
        $this->price = $price;
        $this->ticket_type = $ticket_type;
        $this->available_quantity = $available_quantity;
        $this->image_url = $image_url;
    }

    // ID
    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    // Concert Name
    public function getConcertName() {
        return $this->concert_name;
    }
    public function setConcertName($concert_name) {
        $this->concert_name = $concert_name;
        return $this;
    }

    // Artist Name
    public function getArtistName() {
        return $this->artist_name;
    }
    public function setArtistName($artist_name) {
        $this->artist_name = $artist_name;
        return $this;
    }

    // Event Date
    public function getEventDate() {
        return $this->event_date;
    }
    public function setEventDate($event_date) {
        $this->event_date = $event_date;
        return $this;
    }

    // Event Time
    public function getEventTime() {
        return $this->event_time;
    }
    public function setEventTime($event_time) {
        $this->event_time = $event_time;
        return $this;
    }

    // Venue
    public function getVenue() {
        return $this->venue;
    }
    public function setVenue($venue) {
        $this->venue = $venue;
        return $this;
    }

    // City
    public function getCity() {
        return $this->city;
    }
    public function setCity($city) {
        $this->city = $city;
        return $this;
    }

    // Country
    public function getCountry() {
        return $this->country;
    }
    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }

    // Price
    public function getPrice() {
        return $this->price;
    }
    public function setPrice($price) {
        $this->price = $price;
        return $this;
    }

    // Ticket Type
    public function getTicketType() {
        return $this->ticket_type;
    }
    public function setTicketType($ticket_type) {
        $this->ticket_type = $ticket_type;
        return $this;
    }

    // Available Quantity
    public function getAvailableQuantity() {
        return $this->available_quantity;
    }
    public function setAvailableQuantity($available_quantity) {
        $this->available_quantity = $available_quantity;
        return $this;
    }

    // Image URL
    public function getImageUrl() {
        return $this->image_url;
    }
    public function setImageUrl($image_url) {
        $this->image_url = $image_url;
        return $this;
    }

    // Created At
    public function getCreatedAt() {
        return $this->created_at;
    }
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
        return $this;
    }

    // Updated At
    public function getUpdatedAt() {
        return $this->updated_at;
    }
    public function setUpdatedAt($updated_at) {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * Decrease available quantity when tickets are sold
     */
    public function sellTickets($quantity) {
        if ($this->available_quantity >= $quantity) {
            $this->available_quantity -= $quantity;
            return true;
        }
        return false;
    }

    /**
     * Increase available quantity if tickets are returned
     */
    public function returnTickets($quantity) {
        $this->available_quantity += $quantity;
        return $this;
    }

    /**
     * Check if tickets are available
     */
    public function isAvailable() {
        return $this->available_quantity > 0;
    }

    /**
     * Get full location string
     */
    public function getFullLocation() {
        return $this->venue . ', ' . $this->city . ', ' . $this->country;
    }

    /**
     * Get formatted date and time
     */
    public function getFormattedDateTime() {
        $date = new DateTime($this->event_date);
        return $date->format('F j, Y') . ' at ' . $this->event_time;
    }

    /**
     * Convert object to array for database operations
     */
    public function toArray() {
        return [
            'concert_name' => $this->concert_name,
            'artist_name' => $this->artist_name,
            'event_date' => $this->event_date,
            'event_time' => $this->event_time,
            'venue' => $this->venue,
            'city' => $this->city,
            'country' => $this->country,
            'price' => $this->price,
            'ticket_type' => $this->ticket_type,
            'available_quantity' => $this->available_quantity,
            'image_url' => $this->image_url
        ];
    }
}