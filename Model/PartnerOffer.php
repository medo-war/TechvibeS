<?php
/**
 * Partner Offer Model
 * Represents a special offer or discount provided by a partner
 */
class PartnerOffer {
    private $id;
    private $partner_id;
    private $title;
    private $description;
    private $discount_amount;
    private $discount_type; // percentage or fixed
    private $start_date;
    private $end_date;
    private $offer_code;
    private $is_active;
    private $created_at;
    private $updated_at;

    /**
     * Constructor
     */
    public function __construct(
        $title = null, 
        $description = null, 
        $discount_amount = null, 
        $discount_type = null, 
        $start_date = null, 
        $end_date = null, 
        $offer_code = null, 
        $partner_id = null
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->discount_amount = $discount_amount;
        $this->discount_type = $discount_type;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->offer_code = $offer_code;
        $this->partner_id = $partner_id;
        $this->is_active = true;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getPartnerId() {
        return $this->partner_id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getDiscountAmount() {
        return $this->discount_amount;
    }

    public function getDiscountType() {
        return $this->discount_type;
    }

    public function getStartDate() {
        return $this->start_date;
    }

    public function getEndDate() {
        return $this->end_date;
    }

    public function getOfferCode() {
        return $this->offer_code;
    }

    public function getIsActive() {
        return $this->is_active;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setPartnerId($partner_id) {
        $this->partner_id = $partner_id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setDiscountAmount($discount_amount) {
        $this->discount_amount = $discount_amount;
    }

    public function setDiscountType($discount_type) {
        $this->discount_type = $discount_type;
    }

    public function setStartDate($start_date) {
        $this->start_date = $start_date;
    }

    public function setEndDate($end_date) {
        $this->end_date = $end_date;
    }

    public function setOfferCode($offer_code) {
        $this->offer_code = $offer_code;
    }

    public function setIsActive($is_active) {
        $this->is_active = $is_active;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt($updated_at) {
        $this->updated_at = $updated_at;
    }

    /**
     * Generate a random offer code
     * @return string
     */
    public static function generateOfferCode() {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $code;
    }

    /**
     * Format discount for display
     * @return string
     */
    public function getFormattedDiscount() {
        if ($this->discount_type === 'percentage') {
            return $this->discount_amount . '%';
        } else {
            return '$' . number_format($this->discount_amount, 2);
        }
    }
}
?>
