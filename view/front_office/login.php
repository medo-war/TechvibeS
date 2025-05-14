<?php
session_start();
require_once __DIR__ . '/../../Controller/Config.php'; // Assuming this contains your database connection
require_once __DIR__ . '/../../Controller/userController.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Email and password are required';
        header("Location: welcome.php");
        exit();
    }

    try {
        $db = Config::getConnexion();
        
        // Prepare SQL query to get user by email and password
        $stmt = $db->prepare("SELECT * FROM user WHERE email = :email AND pwd = :password");
        $stmt->execute([
            ':email' => $email,
            ':password' => $password // Note: This is insecure without hashing
        ]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists
        if (!$user) {
            $_SESSION['login_error'] = 'Invalid email or password';
            header("Location: welcome.php");
            exit();
        }

        // Login successful
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role' => $user['role'],
            'image' => $user['image'],
            'logged_in' => true
        ];
        
        header("Location: index.php");
        exit();
        if (!isset($_SESSION['captcha_verified']) || !$_SESSION['captcha_verified']) {
            $_SESSION['login_error'] = 'Veuillez compléter la vérification CAPTCHA';
            header("Location: welcome.php");
            exit();
        }
        

    } catch (PDOException $e) {
        $_SESSION['login_error'] = 'Database error occurred';
        header("Location: welcome.php");
        exit();
    }
} else {
    // Direct access without POST
    header("Location: welcome.php");
    exit();
}