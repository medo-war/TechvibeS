<?php
require_once('Config.php');
require_once __DIR__ . '/../Model/user.php';


class UserController{
    public function getUsers() {
        $sql="SELECT * FROM user";
        $db=config::getConnexion();
        try {
            $query=$db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            die('error:' .$e->getMessage());
        }

    }
    
    //AJOUT
// Modifier la méthode addUser pour bien gérer l'image
public function addUser($user) {
    $db = Config::getConnexion();
    $sql = "INSERT INTO user (first_name, last_name, email, pwd, phone, role, image) 
            VALUES (:first_name, :last_name, :email, :pwd, :phone, :role, :image)";
    try {
        $query = $db->prepare($sql);
        $query->execute([
            'first_name' => $user->getFirst_name(),
            'last_name' => $user->getLast_name(),
            'email' => $user->getEmail(),
            'pwd' => $user->getPwd(),
            'phone' => $user->getPhone(),
            'role' => $user->getRole(),
            'image' => $user->getImage()
        ]);
        return true; // Retourne true si l'insertion réussit
    } catch (Exception $e) {
        error_log('Erreur lors de l\'ajout de l\'utilisateur: ' . $e->getMessage());
        return false;
    }
}
public function deleteUser($id) {
    $db = Config::getConnexion();
    $sql = "DELETE FROM user WHERE id = :id"; 
    try {
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}
public function getUserById($id) {
    $db = Config::getConnexion();
    $sql = "SELECT * FROM user WHERE id = :id";
    try {
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
        return $query->fetch();
    } catch (Exception $e) {
        error_log("Erreur récupération user par ID: " . $e->getMessage());
        return false;
    }
}


public function updateUser($id, $first_name, $last_name, $email, $pwd, $phone, $role, $image) {
    $db = Config::getConnexion();
    $sql = "UPDATE user SET 
            first_name = :first_name, 
            last_name = :last_name, 
            email = :email, 
            pwd = :pwd, 
            phone = :phone, 
            role = :role, 
            image = :image 
            WHERE id = :id";
    
    try {
        $query = $db->prepare($sql);
        return $query->execute([
            'id' => $id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'pwd' => $pwd,
            'phone' => $phone,
            'role' => $role,
            'image' => $image
        ]);
    } catch (Exception $e) {
        error_log('Erreur mise à jour utilisateur: ' . $e->getMessage());
        return false;
    }
}
        
public function getUserByEmail($email) {
    $db = Config::getConnexion();
    $sql = "SELECT * FROM user WHERE email = :email";
    try {
        $query = $db->prepare($sql);
        $query->execute(['email' => $email]);
        $result = $query->fetch();
        if ($result) {
            return new User(
                $result['first_name'],
                $result['last_name'],
                $result['email'],
                $result['pwd'],
                $result['phone'],
                $result['role'],
                $result['image']
            );
        }
        return null;
    } catch (Exception $e) {
        error_log("Erreur récupération user par email: " . $e->getMessage());
        return null;
    }
}
}


?>