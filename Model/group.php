<?php
class Group {
    private $id;
    private $name;
    private $image_url;
    private $genre;
    private $formation_year;
    private $country;
    private $bio;
    private $website_url;
    private $created_at;
    private $updated_at;

    public function __construct(
        $name = null,
        $image_url = null,
        $genre = null,
        $formation_year = null,
        $country = null,
        $bio = null,
        $website_url = null
    ) {
        $this->name = $name;
        $this->image_url = $image_url;
        $this->genre = $genre;
        $this->formation_year = $formation_year;
        $this->country = $country;
        $this->bio = $bio;
        $this->website_url = $website_url;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of image_url
     */ 
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * Set the value of image_url
     *
     * @return  self
     */ 
    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;

        return $this;
    }

    /**
     * Get the value of genre
     */ 
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set the value of genre
     *
     * @return  self
     */ 
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get the value of formation_year
     */ 
    public function getFormationYear()
    {
        return $this->formation_year;
    }

    /**
     * Set the value of formation_year
     *
     * @return  self
     */ 
    public function setFormationYear($formation_year)
    {
        $this->formation_year = $formation_year;

        return $this;
    }

    /**
     * Get the value of country
     */ 
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the value of country
     *
     * @return  self
     */ 
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the value of bio
     */ 
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set the value of bio
     *
     * @return  self
     */ 
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get the value of website_url
     */ 
    public function getWebsiteUrl()
    {
        return $this->website_url;
    }

    /**
     * Set the value of website_url
     *
     * @return  self
     */ 
    public function setWebsiteUrl($website_url)
    {
        $this->website_url = $website_url;

        return $this;
    }

    /**
     * Get the value of created_at
     */ 
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     *
     * @return  self
     */ 
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of updated_at
     */ 
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set the value of updated_at
     *
     * @return  self
     */ 
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}