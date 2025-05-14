<?php

class Partner {
    private $id;
    private $name;
    private $company;
    private $email;
    private $phone;
    private $partnerType;
    private $partnershipValue;
    private $message;
    private $contractStart;
    private $contractEnd;
    private $contract_template_id;
    private $created_at;
    private $status;

    // Constructor
    public function __construct($name = null, $company = null, $email = null, $phone = null, 
                               $partnerType = null, $partnershipValue = null, $message = null, 
                               $contractStart = null, $contractEnd = null, 
                               $contract_template_id = null) {
        $this->name = $name;
        $this->company = $company;
        $this->email = $email;
        $this->phone = $phone;
        $this->partnerType = $partnerType;
        $this->partnershipValue = $partnershipValue;
        $this->message = $message;
        $this->contractStart = $contractStart;
        $this->contractEnd = $contractEnd;
        $this->contract_template_id = $contract_template_id;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCompany() {
        return $this->company;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getPartnerType() {
        return $this->partnerType;
    }

    public function getPartnershipValue() {
        return $this->partnershipValue;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getContractStart() {
        return $this->contractStart;
    }

    public function getContractEnd() {
        return $this->contractEnd;
    }

    public function getContractTemplateId() {
        return $this->contract_template_id;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
    
    public function getStatus() {
        return $this->status;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setCompany($company) {
        $this->company = $company;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function setPartnerType($partnerType) {
        $this->partnerType = $partnerType;
    }

    public function setPartnershipValue($partnershipValue) {
        $this->partnershipValue = $partnershipValue;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setContractStart($contractStart) {
        $this->contractStart = $contractStart;
    }

    public function setContractEnd($contractEnd) {
        $this->contractEnd = $contractEnd;
    }

    public function setContractTemplateId($contract_template_id) {
        $this->contract_template_id = $contract_template_id;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }
    
    public function setStatus($status) {
        $this->status = $status;
    }
}
?>
