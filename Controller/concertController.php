<?php
// Inclusion du fichier de configuration
include 'C:\xampp\htdocs\projetwebCRUD - ranim\Config.php';

// Fonction pour ajouter un concert (avec image)
function ajouterConcert($id_lieux, $date_concert, $prix_concert, $genre, $place_dispo, $imageFile = null) {
    $pdo = config::getConnexion();
    
    try {
        // Chemin par défaut
        $imagePath = 'Images/default-avatar.png';
        
        // Si une image est uploadée
        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadConcertImage($imageFile);
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];
            } else {
                throw new Exception($uploadResult['message']);
            }
        }

        $stmt = $pdo->prepare("INSERT INTO concert (id_lieux, date_concert, prix_concert, genre, place_dispo, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_lieux, $date_concert, $prix_concert, $genre, $place_dispo, $imagePath]);
        
        return true;
    } catch (Exception $e) {
        error_log("Erreur ajout concert: " . $e->getMessage());
        return false;
    }
}

// Fonction pour récupérer tous les concerts avec les informations du lieu (inclut maintenant l'image)
function getConcert() {
    $conn = config::getConnexion();
    try {
        $stmt = $conn->prepare("
            SELECT c.id_concert, c.date_concert, c.prix_concert, 
                   c.genre, c.place_dispo, c.image, 
                   l.nom_lieux, l.adresse 
            FROM concert c
            JOIN lieux l ON c.id_lieux = l.id_lieux
            ORDER BY c.date_concert ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur getConcert: " . $e->getMessage());
        return [];
    }
}

// Fonction pour supprimer un concert (inchangée mais gère aussi la suppression de l'image associée)
function supprimerConcert($id_concert) {
    $conn = config::getConnexion();

    try {
        // Récupérer le nom de l'image avant suppression
        $image = $conn->query("SELECT image FROM concert WHERE id_concert = $id_concert")->fetchColumn();
        
        // Supprimer le concert
        $stmt = $conn->prepare("DELETE FROM concert WHERE id_concert = ?");
        $stmt->execute([$id_concert]);
        
        // Supprimer le fichier image si ce n'est pas l'image par défaut
        if ($image && $image != 'images/default-avatar.png' && file_exists($image)) {
            unlink($image);
        }
        
        return true;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return false;
    }
}


function modifierConcert($id_concert, $id_lieux, $date_concert, $prix_concert, $genre, $place_dispo, $image) {
    $pdo = config::getConnexion();
    
    try {
        // 1. Récupération des données actuelles
        $current_data = getConcertById($id_concert);
        if (!$current_data) {
            throw new Exception("Concert introuvable");
        }

        // 2. Gestion du lieu
        if (empty($id_lieux)) {
            $id_lieux = $current_data['id_lieux']; // Conserve l'ancien lieu si non modifié
        }

        // 3. Gestion de l'image
        $image_path = $current_data['image'];
        
        // Si nouvelle image uploadée
        if ($image['error'] === UPLOAD_ERR_OK) {
            // Supprime l'ancienne image si elle existe
            if (!empty($image_path) && file_exists($image_path) && $image_path !== 'Images/default-avatar.png') {
                unlink($image_path);
            }

            // Valide et déplace la nouvelle image
            $upload_dir = 'uploads/concerts/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $file_ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $valid_extensions)) {
                throw new Exception("Format d'image invalide");
            }

            $image_name = uniqid() . '_' . basename($image['name']);
            $image_path = $upload_dir . $image_name;

            if (!move_uploaded_file($image['tmp_name'], $image_path)) {
                throw new Exception("Échec de l'upload de l'image");
            }
        } 
        // Si case "Supprimer l'image" cochée
        elseif (isset($_POST['remove_image']) && $_POST['remove_image'] === 'on') {
            if (!empty($image_path) && file_exists($image_path) && $image_path !== 'Images/default-avatar.png') {
                unlink($image_path);
            }
            $image_path = 'Images/default-avatar.png';
        }

        // 4. Mise à jour en base
        $stmt = $pdo->prepare("UPDATE concert SET 
            id_lieux = ?, 
            date_concert = ?, 
            prix_concert = ?, 
            genre = ?, 
            place_dispo = ?, 
            image = ? 
            WHERE id_concert = ?");

        $success = $stmt->execute([
            $id_lieux,
            $date_concert,
            $prix_concert,
            $genre,
            $place_dispo,
            $image_path,
            $id_concert
        ]);

        if (!$success) {
            throw new Exception("Échec de la mise à jour en base");
        }

        return true;

    } catch (Exception $e) {
        error_log("Erreur modification concert: " . $e->getMessage());
        return false;
    }
}


// Fonction pour récupérer un concert par son ID (inclut maintenant l'image)
function getConcertById($id_concert) {
    $pdo = config::getConnexion();
    try {
        $stmt = $pdo->prepare("
            SELECT concert.*, lieux.nom_lieux 
            FROM concert 
            LEFT JOIN lieux ON concert.id_lieux = lieux.id_lieux 
            WHERE concert.id_concert = ?
        ");
        $stmt->execute([$id_concert]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur getConcertById: " . $e->getMessage());
        return false;
    }
}


// Fonction pour récupérer tous les lieux (inchangée)
function getLieux() {
    $conn = config::getConnexion();

    try {
        $stmt = $conn->prepare("SELECT * FROM lieux ORDER BY nom_lieux ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}

// Fonction pour vérifier la disponibilité des places (inchangée)
function verifierDisponibilite($id_lieux, $date_concert) {
    $conn = config::getConnexion();

    try {
        $stmt = $conn->prepare("SELECT capacite FROM lieux WHERE id_lieux = ?");
        $stmt->execute([$id_lieux]);
        $lieu = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$lieu) return false;
        
        $capacite = $lieu['capacite'];
        
        $stmt = $conn->prepare("
            SELECT SUM(place_dispo) as places_reservees 
            FROM concert
            WHERE id_lieux = ? AND date_concert = ?
        ");
        $stmt->execute([$id_lieux, $date_concert]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $places_reservees = $result['places_reservees'] ?? 0;
        
        return ($capacite - $places_reservees) > 0;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return false;
    }
}

// Fonction supplémentaire pour gérer l'upload d'image
function uploadConcertImage($file) {
    // Vérifier si un fichier a été uploadé
    if (!isset($file['error']) || $file['error'] == UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'message' => 'Aucun fichier uploadé'];
    }

    // Vérifier les erreurs d'upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Erreur d\'upload: ' . $file['error']];
    }

    $targetDir = "uploads/concerts/";
    if (!file_exists($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            return ['success' => false, 'message' => 'Impossible de créer le dossier'];
        }
    }

    $fileName = uniqid() . '_' . basename($file["name"]);
    $targetFile = $targetDir . $fileName;
    
    // Vérification de sécurité
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ['success' => false, 'message' => 'Le fichier n\'est pas une image valide'];
    }

    // Vérifier l'extension
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Type de fichier non autorisé'];
    }

    // Vérifier la taille (max 2MB)
    if ($file["size"] > 2000000) {
        return ['success' => false, 'message' => 'Fichier trop volumineux (>2MB)'];
    }

    // Déplacer le fichier
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return ['success' => true, 'path' => $targetFile];
    } else {
        return ['success' => false, 'message' => 'Erreur lors du déplacement du fichier'];

    }
} 
// Fonction pour rechercher un concert par ID
function rechercherConcertParId($id_concert) {
    $pdo = config::getConnexion();
    try {
        $stmt = $pdo->prepare("
            SELECT c.id_concert, c.date_concert, c.prix_concert, 
                   c.genre, c.place_dispo, c.image, 
                   l.nom_lieux, l.adresse 
            FROM concert c
            JOIN lieux l ON c.id_lieux = l.id_lieux
            WHERE c.id_concert = ?
        ");
        $stmt->execute([$id_concert]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur rechercherConcertParId: " . $e->getMessage());
        return null;
    }
}

?>