<?php
// Inclusion du fichier de configuration
<<<<<<< HEAD
include __DIR__ . '/Config.php';
=======
include 'C:\xampp\htdocs\projetwebCRUD - ranim\Config.php';
>>>>>>> 211c8e7a9104aeaddd4dbc77946169988c3378b2

// Fonction pour ajouter un concert (avec image)
function ajouterConcert($id_lieux, $date_concert, $prix_concert, $genre, $place_dispo, $imageFile = null) {
    $pdo = config::getConnexion();
    
    try {
<<<<<<< HEAD
        // Log des paramètres reçus
        error_log("Début ajout concert - Paramètres: id_lieux=$id_lieux, date=$date_concert, prix=$prix_concert, genre=$genre, places=$place_dispo");
        if ($imageFile) {
            error_log("Image reçue: " . print_r($imageFile, true));
        } else {
            error_log("Aucune image reçue");
        }
        
        // Vérification des paramètres obligatoires
        if (empty($id_lieux)) {
            throw new Exception("ID du lieu manquant");
        }
        if (empty($date_concert)) {
            throw new Exception("Date du concert manquante");
        }
        if (!is_numeric($prix_concert)) {
            throw new Exception("Prix invalide");
        }
        if (empty($genre)) {
            throw new Exception("Genre musical manquant");
        }
        if (!is_numeric($place_dispo) || $place_dispo <= 0) {
            throw new Exception("Nombre de places invalide");
        }
        
=======
>>>>>>> 211c8e7a9104aeaddd4dbc77946169988c3378b2
        // Chemin par défaut
        $imagePath = 'Images/default-avatar.png';
        
        // Si une image est uploadée
        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
<<<<<<< HEAD
            error_log("Tentative d'upload d'image...");
            $uploadResult = uploadConcertImage($imageFile);
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];
                error_log("Image uploadée avec succès: " . $imagePath);
            } else {
                error_log("Erreur upload image: " . $uploadResult['message']);
=======
            $uploadResult = uploadConcertImage($imageFile);
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];
            } else {
>>>>>>> 211c8e7a9104aeaddd4dbc77946169988c3378b2
                throw new Exception($uploadResult['message']);
            }
        }

<<<<<<< HEAD
        error_log("Préparation de l'insertion en base de données avec image: " . $imagePath);
        $stmt = $pdo->prepare("INSERT INTO concert (id_lieux, date_concert, prix_concert, genre, place_dispo, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_lieux, $date_concert, $prix_concert, $genre, $place_dispo, $imagePath]);
        error_log("Concert ajouté avec succès");
        
        return true;
    } catch (Exception $e) {
        error_log("ERREUR CRITIQUE - Ajout concert: " . $e->getMessage());
=======
        $stmt = $pdo->prepare("INSERT INTO concert (id_lieux, date_concert, prix_concert, genre, place_dispo, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_lieux, $date_concert, $prix_concert, $genre, $place_dispo, $imagePath]);
        
        return true;
    } catch (Exception $e) {
        error_log("Erreur ajout concert: " . $e->getMessage());
>>>>>>> 211c8e7a9104aeaddd4dbc77946169988c3378b2
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
<<<<<<< HEAD
            // Utiliser la fonction uploadConcertImage pour gérer l'upload
            $uploadResult = uploadConcertImage($image);
            if ($uploadResult['success']) {
                // Supprime l'ancienne image si elle existe et n'est pas l'image par défaut
                $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/' . $image_path;
                if (!empty($image_path) && file_exists($fullImagePath) && $image_path !== 'Images/default-avatar.png') {
                    unlink($fullImagePath);
                }
                
                // Utiliser le nouveau chemin d'image
                $image_path = $uploadResult['path'];
            } else {
                // Journaliser l'erreur mais continuer la mise à jour sans changer l'image
                error_log("Erreur lors de l'upload de l'image: " . $uploadResult['message']);
=======
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
>>>>>>> 211c8e7a9104aeaddd4dbc77946169988c3378b2
            }
        } 
        // Si case "Supprimer l'image" cochée
        elseif (isset($_POST['remove_image']) && $_POST['remove_image'] === 'on') {
<<<<<<< HEAD
            $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/' . $image_path;
            if (!empty($image_path) && file_exists($fullImagePath) && $image_path !== 'Images/default-avatar.png') {
                unlink($fullImagePath);
                error_log("Image supprimée: " . $fullImagePath);
=======
            if (!empty($image_path) && file_exists($image_path) && $image_path !== 'Images/default-avatar.png') {
                unlink($image_path);
>>>>>>> 211c8e7a9104aeaddd4dbc77946169988c3378b2
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
<<<<<<< HEAD
    error_log("Début de la fonction uploadConcertImage");
    
    // Vérifier si un fichier a été uploadé
    if (!isset($file['error'])) {
        error_log("Erreur: Paramètre 'error' manquant dans le fichier");
        return ['success' => false, 'message' => 'Paramètre de fichier invalide'];
    }
    
    if ($file['error'] == UPLOAD_ERR_NO_FILE) {
        error_log("Erreur: Aucun fichier n'a été uploadé");
=======
    // Vérifier si un fichier a été uploadé
    if (!isset($file['error']) || $file['error'] == UPLOAD_ERR_NO_FILE) {
>>>>>>> 211c8e7a9104aeaddd4dbc77946169988c3378b2
        return ['success' => false, 'message' => 'Aucun fichier uploadé'];
    }

    // Vérifier les erreurs d'upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
<<<<<<< HEAD
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'Le fichier dépasse la taille maximale définie dans php.ini',
            UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximale définie dans le formulaire HTML',
            UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement uploadé',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été uploadé',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
            UPLOAD_ERR_CANT_WRITE => 'Impossible d\'enregistrer le fichier sur le disque',
            UPLOAD_ERR_EXTENSION => 'Upload arrêté par une extension PHP'
        ];
        $errorMessage = isset($errorMessages[$file['error']]) ? $errorMessages[$file['error']] : 'Erreur inconnue';
        error_log("Erreur d'upload: " . $errorMessage . " (code: " . $file['error'] . ")");
        return ['success' => false, 'message' => 'Erreur d\'upload: ' . $errorMessage];
    }

    // Vérifier si le fichier existe
    if (!isset($file["tmp_name"]) || !file_exists($file["tmp_name"])) {
        error_log("Erreur: Fichier temporaire introuvable");
        return ['success' => false, 'message' => 'Fichier temporaire introuvable'];
    }

    // Définir le chemin absolu pour le dossier d'upload
    $rootPath = $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/';
    error_log("Chemin racine: " . $rootPath);
    
    // Créer un dossier uploads s'il n'existe pas
    if (!file_exists($rootPath . "uploads/")) {
        if (!mkdir($rootPath . "uploads/", 0777)) {
            error_log("Impossible de créer le dossier uploads/");
            return ['success' => false, 'message' => 'Impossible de créer le dossier d\'upload principal'];
        }
    }
    
    // Créer le dossier concerts s'il n'existe pas
    $targetDir = $rootPath . "uploads/concerts/";
    if (!file_exists($targetDir)) {
        if (!mkdir($targetDir, 0777)) {
            error_log("Impossible de créer le dossier: " . $targetDir);
            return ['success' => false, 'message' => 'Impossible de créer le dossier d\'upload pour les concerts'];
        }
    }
    
    error_log("Dossier d'upload: " . $targetDir);

    // Générer un nom de fichier unique
    $fileName = uniqid() . '_' . basename($file["name"]);
    $targetFile = $targetDir . $fileName;
    $webPath = "uploads/concerts/" . $fileName; // Chemin relatif pour la base de données
    
    error_log("Fichier cible: " . $targetFile);
    
    // Vérification de sécurité
    try {
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            error_log("Le fichier n'est pas une image valide");
            return ['success' => false, 'message' => 'Le fichier n\'est pas une image valide'];
        }
        error_log("Vérification d'image réussie: " . $check[0] . "x" . $check[1]);
    } catch (Exception $e) {
        error_log("Erreur lors de la vérification de l'image: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur lors de la vérification de l\'image'];
=======
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
>>>>>>> 211c8e7a9104aeaddd4dbc77946169988c3378b2
    }

    // Vérifier l'extension
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
<<<<<<< HEAD
        error_log("Type de fichier non autorisé: " . $imageFileType);
        return ['success' => false, 'message' => 'Type de fichier non autorisé (.' . $imageFileType . ')'];
=======
        return ['success' => false, 'message' => 'Type de fichier non autorisé'];
>>>>>>> 211c8e7a9104aeaddd4dbc77946169988c3378b2
    }

    // Vérifier la taille (max 2MB)
    if ($file["size"] > 2000000) {
<<<<<<< HEAD
        error_log("Fichier trop volumineux: " . $file["size"] . " bytes");
        return ['success' => false, 'message' => 'Fichier trop volumineux (' . round($file["size"]/1024/1024, 2) . ' MB)'];
    }

    // Vérifier les permissions du dossier
    if (!is_writable($targetDir)) {
        error_log("Le dossier d'upload n'est pas accessible en écriture: " . $targetDir);
        chmod($targetDir, 0777); // Tentative de correction des permissions
        if (!is_writable($targetDir)) {
            return ['success' => false, 'message' => 'Le dossier d\'upload n\'est pas accessible en écriture'];
        }
=======
        return ['success' => false, 'message' => 'Fichier trop volumineux (>2MB)'];
>>>>>>> 211c8e7a9104aeaddd4dbc77946169988c3378b2
    }

    // Déplacer le fichier
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
<<<<<<< HEAD
        error_log("Fichier uploadé avec succès: " . $targetFile);
        return ['success' => true, 'path' => $webPath];
    } else {
        $lastError = error_get_last();
        error_log("Erreur lors du déplacement du fichier vers: " . $targetFile . " - Détails: " . ($lastError ? $lastError['message'] : 'Inconnue'));
        return ['success' => false, 'message' => 'Erreur lors du déplacement du fichier. Vérifiez les permissions.'];
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

=======
        return ['success' => true, 'path' => $targetFile];
    } else {
        return ['success' => false, 'message' => 'Erreur lors du déplacement du fichier'];
    }
} 
>>>>>>> 211c8e7a9104aeaddd4dbc77946169988c3378b2
?>