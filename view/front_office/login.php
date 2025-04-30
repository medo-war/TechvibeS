<?php
session_start();
require_once __DIR__ . '/../../Controller/userController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validation basique
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Email et mot de passe requis';
        header("Location: welcome.php");
        exit();
    }

    $userController = new UserController();
    $user = $userController->getUserByEmail($email);

    // Vérification de l'existence de l'utilisateur
    if (!$user) {
        $_SESSION['login_error'] = 'Email ou mot de passe incorrect';
        header("Location: welcome.php");
        exit();
    }

    // Vérification SIMPLE du mot de passe (sans hachage)
    if ($password === $user->getPwd()) {
        // Connexion réussie
        $_SESSION['user'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'first_name' => $user->getFirst_name(),
            'last_name' => $user->getLast_name(),
            'role' => $user->getRole(),
            'image' => $user->getImage(),
            'logged_in' => true



            
        ];
        
        header("Location: index.php");
        exit();
    } else {
        // Échec d'authentification
        $_SESSION['login_error'] = 'Email ou mot de passe incorrect';
        header("Location: welcome.php");
        exit();
    }
} else {
    // Accès direct sans POST
    header("Location: welcome.php");
    exit();
}