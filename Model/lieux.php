<?php
class Lieux {
    private ?int $id_lieux;
    private ?string $nom_lieux;
    private ?string $adresse;
    private ?int $capacite;


    // constructeur
    public function __construct(?string $nom_lieux, ?string $adresse, ?int $capacite) {   
        $this->nom_lieux = $nom_lieux;
        $this->adresse = $adresse;
        $this->capacite = $capacite;
    } 

    // Getters
    public function getIdLieux() {
        return $this->id_lieux;
    }

    public function getNomLieux() {
        return $this->nom_lieux;
    }

    public function getAdresse() {
        return $this->adresse;
    }

    public function getCapacite() {
        return $this->capacite;
    }

    // Setters
    public function setIdLieux($id_lieux) {
        $this->id_lieux = $id_lieux;
        return $this;
    }

    public function setNomLieux($nom_lieux) {
        $this->nom_lieux = $nom_lieux;
        return $this;
    }

    public function setAdresse($adresse) {
        $this->adresse = $adresse;
        return $this;
    }

    public function setCapacite($capacite) {
        $this->capacite = $capacite;
        return $this;
    }
}
?>