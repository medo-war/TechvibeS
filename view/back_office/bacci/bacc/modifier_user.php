<?php
require_once __DIR__.'/../../../../Controller/userController.php';

$controller = new UserController();

// Récupérer l'utilisateur existant
if (isset($_POST['id'])) {
    $existingUser = $controller->getUserById($_POST['id']);
    
    if (!$existingUser) {
        die("Utilisateur non trouvé");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $id = $_POST['id'];
    $first_name = $_POST['first_name'] ?? $existingUser['first_name'];
    $last_name = $_POST['last_name'] ?? $existingUser['last_name'];
    $email = $_POST['email'] ?? $existingUser['email'];
    $phone = $_POST['phone'] ?? $existingUser['phone'];
    $role = $_POST['role'] ?? $existingUser['role'];
    
    // Gestion du mot de passe (ne change que si fourni)
    $pwd = !empty($_POST['pwd']) ? $_POST['pwd'] : $existingUser['pwd']; // Modification ici
    
    // Gestion de l'image
    $image = $existingUser['image']; // Conserve l'image actuelle par défaut
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../../uploads/users/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $image = 'uploads/users/' . $filename;
            
            // Supprimer l'ancienne image si elle existe
            if (!empty($existingUser['image']) && file_exists(__DIR__ . '/../../../../' . $existingUser['image'])) {
                unlink(__DIR__ . '/../../../../' . $existingUser['image']);
            }
        }
    }
    
    // Mettre à jour l'utilisateur
    $success = $controller->updateUser(
        $id,
        $first_name,
        $last_name,
        $email,
        $pwd,
        $phone,
        $role,
        $image
    );
    
    if ($success) {
        header("Location: gestion_user1.php?success=1");
    } else {
        header("Location: gestion_user1.php?error=1");
    }
    exit;
}
?>