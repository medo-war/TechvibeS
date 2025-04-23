<?php
class Concert {
    private ?int $id_concert;
    private ?string $date_concert;
    private ?float $prix_concert;
    private ?string $genre;
    private ?int $place_dispo;
    private ?string $image; // Nouvel attribut pour l'image

    // Constructeur mis à jour
    public function __construct(
        ?string $date_concert, 
        ?float $prix_concert, 
        ?string $genre, 
        ?int $place_dispo,
        ?string $image = null // Paramètre optionnel pour l'image
    ) {
        $this->date_concert = $date_concert;
        $this->prix_concert = $prix_concert;
        $this->genre = $genre;
        $this->place_dispo = $place_dispo;
        $this->image = $image;
    }

    // Getters existants
    public function getIdConcert(): ?int {
        return $this->id_concert;
    }

    public function getDateConcert(): ?string {
        return $this->date_concert;
    }

    public function getPrixConcert(): ?float {
        return $this->prix_concert;
    }

    public function getGenre(): ?string {
        return $this->genre;
    }

    public function getPlaceDispo(): ?int {
        return $this->place_dispo;
    }

    // Nouveau getter pour l'image
    public function getImage(): ?string {
        return $this->image;
    }

    // Setters existants
    public function setIdConcert(?int $id_concert): self {
        $this->id_concert = $id_concert;
        return $this;
    }

    public function setDateConcert(?string $date_concert): self {
        $this->date_concert = $date_concert;
        return $this;
    }

    public function setPrixConcert(?float $prix_concert): self {
        $this->prix_concert = $prix_concert;
        return $this;
    }

    public function setGenre(?string $genre): self {
        $this->genre = $genre;
        return $this;
    }

    public function setPlaceDispo(?int $place_dispo): self {
        $this->place_dispo = $place_dispo;
        return $this;
    }

    // Nouveau setter pour l'image
    public function setImage(?string $image): self {
        $this->image = $image;
        return $this;
    }

    // Méthode utilitaire pour vérifier la présence d'une image
    public function hasImage(): bool {
        return !empty($this->image);
    }
}
?>