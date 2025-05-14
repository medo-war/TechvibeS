<?php

require 'Config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Model/artist.php';


class ArtistController {
    
    // Add a new artist to the database
    public function addArtist($artist) {
    $db = config::getConnexion();
    $sql = "INSERT INTO artists (name, username, group_name, genre, country, bio, image_url) 
            VALUES (:name, :username, :group_name, :genre, :country, :bio, :image_url)";

    try {
        $query = $db->prepare($sql);
        $query->execute(array(
            'name' => $artist->getName(),
            'username' => $artist->getUsername(),
            'group_name' => $artist->getGroupName(),
            'genre' => $artist->getGenre(),
            'country' => $artist->getCountry(),
            'bio' => $artist->getBio(),
            'image_url' => $artist->getImageUrl()
        ));
        return true; // Return success status
    }
    catch(PDOException $e) {
        error_log("Error adding artist: " . $e->getMessage());
        return false; // Return failure status
    }
}

    // Get all artists from the database
    public function getArtists() {
        
        $sql = "SELECT * FROM artists";
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

    // Update an existing artist in the database
    public function updateArtist($artist) {
        $db = config::getConnexion();
        $sql = "UPDATE artists SET name = :name, username = :username, group_name = :group_name, genre = :genre, 
                country = :country, bio = :bio, image_url = :image_url WHERE id = :id";

        echo "Updating artist with ID: " . $artist->getName() . "<br>";

        try {
            $query = $db->prepare($sql);
            $query->execute(array(
                'name' => $artist->getName(),
                'username' => $artist->getUsername(),
                'group_name' => $artist->getGroupName(),
                'genre' => $artist->getGenre(),
                'country' => $artist->getCountry(),
                'bio' => $artist->getBio(),
                'image_url' => $artist->getImageUrl(),
                'id' => $artist->getId()
            ));
        }
        catch(PDOException $e) {
            die("Error: ". $e->getMessage());
        }
    }

    // Delete an artist from the database
    public function deleteArtist($id) {
        echo "Reached here";
        $db = config::getConnexion();
        $sql = "DELETE FROM artists WHERE id = :id";

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;  // Return true if successful
        } catch (PDOException $e) {
            // Return false in case of error
            return false;
        }
    }
    
    // Search artists by name
    public function searchArtistsByName($searchTerm) {
        $db = config::getConnexion();
        $sql = "SELECT * FROM artists WHERE name LIKE :searchTerm OR username LIKE :searchTerm OR group_name LIKE :searchTerm";
        
        try {
            $searchTerm = "%" . $searchTerm . "%"; // Add wildcards for partial matching
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error searching artists: " . $e->getMessage());
            return [];
        }
    }
    
}
?>
