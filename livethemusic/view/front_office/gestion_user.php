<?php
session_start();
require_once __DIR__ . '/../../Controller/userController.php';
require_once __DIR__ . '/../../Model/user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    // Gestion de l'upload d'image
    $image = 'uploads/' . $fileName;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            $image = 'uploads/' . $fileName; // Chemin relatif enregistré
        }
    }
    

    // Sécurisation des données envoyées
    $first_name = htmlspecialchars($_POST['first_name'] ?? '');
    $last_name = htmlspecialchars($_POST['last_name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['pwd'] ?? '';
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? 'user';

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        die('Tous les champs obligatoires doivent être remplis.');
    }

    // Création de l'objet utilisateur
    $user = new User($first_name, $last_name, $email, $password, $phone, $role, $image);

    // Enregistrement en BDD
    $userController = new UserController();
    $added = $userController->addUser($user);

    if ($added) {
        // Récupérez l'utilisateur complet depuis la base
        $newUser = $userController->getUserByEmail($email);
        
        // Stockez les informations dans la session
        $_SESSION['user'] = [
            'id' => $newUser->getId(),
            'email' => $newUser->getEmail(),
            'first_name' => $newUser->getFirst_name(),
            'last_name' => $newUser->getLast_name(),
            'role' => $newUser->getRole(),
            'image' => $newUser->getImage(),
            'logged_in' => true
        ];

        header('Location: ../../view/front_office/index.php');
        exit();
    } else {
        echo "Une erreur est survenue lors de l'enregistrement.";
    }
} else {
    header('Location: ../welcome.php');
    exit();
}
