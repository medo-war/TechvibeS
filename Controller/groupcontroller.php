<?php
require_once('Config.php');
require_once __DIR__ . '/../Model/group.php';

class GroupController {
    public function getGroups() {
        $sql = "SELECT * FROM groups";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    
    public function addGroup($group) {
        $db = Config::getConnexion();
        $sql = "INSERT INTO groups (name, image_url, genre, formation_year, country, bio, website_url) 
                VALUES (:name, :image_url, :genre, :formation_year, :country, :bio, :website_url)";
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name' => $group->getName(),
                'image_url' => $group->getImageUrl(),
                'genre' => $group->getGenre(),
                'formation_year' => $group->getFormationYear(),
                'country' => $group->getCountry(),
                'bio' => $group->getBio(),
                'website_url' => $group->getWebsiteUrl()
            ]);
            return true;
        } catch (Exception $e) {
            error_log('Error adding group: ' . $e->getMessage());
            return false;
        }
    }
    
    public function deleteGroup($id) {
        $db = Config::getConnexion();
        $sql = "DELETE FROM groups WHERE id = :id";
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log('Error deleting group: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getGroupById($id) {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM groups WHERE id = :id";
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            error_log("Error getting group by ID: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateGroup($group) {
        $db = Config::getConnexion();
        $sql = "UPDATE groups SET 
                name = :name, 
                image_url = :image_url, 
                genre = :genre, 
                formation_year = :formation_year, 
                country = :country, 
                bio = :bio, 
                website_url = :website_url 
                WHERE id = :id";
        
        try {
            $query = $db->prepare($sql);
            return $query->execute([
                'id' => $group->getId(),
                'name' => $group->getName(),
                'image_url' => $group->getImageUrl(),
                'genre' => $group->getGenre(),
                'formation_year' => $group->getFormationYear(),
                'country' => $group->getCountry(),
                'bio' => $group->getBio(),
                'website_url' => $group->getWebsiteUrl()
            ]);
        } catch (Exception $e) {
            error_log('Error updating group: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getGroupByName($name) {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM groups WHERE name = :name";
        try {
            $query = $db->prepare($sql);
            $query->execute(['name' => $name]);
            $result = $query->fetch();
            if ($result) {
                return new Group(
                    $result['name'],
                    $result['image_url'],
                    $result['genre'],
                    $result['formation_year'],
                    $result['country'],
                    $result['bio'],
                    $result['website_url']
                );
            }
            return null;
        } catch (Exception $e) {
            error_log("Error getting group by name: " . $e->getMessage());
            return null;
        }
    }
    
    public function searchGroupsByName($searchTerm) {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM groups WHERE name LIKE :searchTerm OR genre LIKE :searchTerm OR country LIKE :searchTerm";
        
        try {
            $searchTerm = "%" . $searchTerm . "%"; // Add wildcards for partial matching
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error searching groups: " . $e->getMessage());
            return [];
        }
    }
}