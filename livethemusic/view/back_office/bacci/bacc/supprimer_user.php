<?php
require_once __DIR__.'/../../../../Controller/userController.php';

if (isset($_GET['id'])) {
    $controller = new UserController();
    $controller->deleteUser($_GET['id']);
}

header('Location: gestion_user1.php');
exit;
?>
