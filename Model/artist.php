<?php
class Artist {

    private $id;
    private $name;
    private $username;
    private $group_name;
    private $genre;
    private $country;
    private $bio;
    private $image_url; // New property

    // Updated constructor to include image_url
    public function __construct($id = null,$name = null, $username = null, $group_name = null, $genre = null, $country = null, $bio = null, $image_url = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->group_name = $group_name;
        $this->genre = $genre;
        $this->country = $country;
        $this->bio = $bio;
        $this->image_url = $image_url;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getGroupName()
    {
        return $this->group_name;
    }

    public function setGroupName($group_name)
    {
        $this->group_name = $group_name;
        return $this;
    }

    public function getGenre()
    {
        return $this->genre;
    }

    public function setGenre($genre)
    {
        $this->genre = $genre;
        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;
        return $this;
    }

    // Getter for image_url
    public function getImageUrl()
    {
        return $this->image_url;
    }

    // Setter for image_url
    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;
        return $this;
    }
}
?>
