<?php
// Chemin absolu vers le controller
$controllerPath = 'C:/xampp/htdocs/projetwebCRUD - ranim/Controller/concertController.php';
if (!file_exists($controllerPath)) {
    die("Erreur : Fichier contrôleur introuvable.");
}

require_once $controllerPath;

// Vérifier si le formulaire de modification est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_concert'])) {
    // Récupérer l'ID du concert
    $id_concert = $_POST['id_concert'];
    
    // Récupérer le concert existant
    $existingConcert = getConcertById($id_concert);
    if (!$existingConcert) {
        die("Concert non trouvé");
    }

    // Récupérer les données du formulaire
    $id_lieux = $_POST['id_lieux'];
    $date_concert = $_POST['date_concert'];
    $prix_concert = $_POST['prix_concert'];
    $genre = $_POST['genre'];
    $place_dispo = $_POST['place_dispo'];
    
    // Gestion de l'image
    $imagePath = $existingConcert['image']; // Conserver l'image actuelle par défaut
    
    // Vérifier si on doit supprimer l'image actuelle
    if (isset($_POST['remove_image'])) {
        if (!empty($existingConcert['image']) && $existingConcert['image'] != 'Images/default-avatar.png') {
            if (file_exists($existingConcert['image'])) {
                unlink($existingConcert['image']);
            }
        }
        $imagePath = 'Images/default-avatar.png';
    }
    
    // Vérifier si une nouvelle image est uploadée
    if (isset($_FILES['image_concert']) && $_FILES['image_concert']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'C:/xampp/htdocs/projetwebCRUD - ranim/uploads/concerts/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $extension = pathinfo($_FILES['image_concert']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image_concert']['tmp_name'], $destination)) {
            $imagePath = 'uploads/concerts/' . $filename;
            
            // Supprimer l'ancienne image si elle existe
            if (!empty($existingConcert['image']) && $existingConcert['image'] != 'Images/default-avatar.png') {
                if (file_exists($existingConcert['image'])) {
                    unlink($existingConcert['image']);
                }
            }
        }
    }
    
    // Appeler la fonction de modification
    $success = modifierConcert(
        $id_concert,
        $id_lieux,
        $date_concert,
        $prix_concert,
        $genre,
        $place_dispo,
        $imagePath
    );
    
    // Redirection
    if ($success) {
        header("Location: concerttt.php?success=2&scroll=true");
    } else {
        header("Location: concerttt.php?error=Erreur lors de la modification");
    }
    exit;
}

// Si on arrive ici, c'est qu'il y a eu un problème
header("Location: concerttt.php?error=Requête invalide");
exit;