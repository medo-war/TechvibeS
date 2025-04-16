<?php
require_once __DIR__.'/../../../../Controller/userController.php';

$controller = new UserController();
$user = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user = $controller->getUserById($id); // ajoute cette méthode dans ton contrôleur si elle n’existe pas
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->updateUser(
        $_POST['id'],
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['pwd'],
        $_POST['phone'],
        $_POST['role'],
        $_POST['image'] // ou gérer l’upload d’un nouveau fichier (voir ci-dessous)
    );
}
?>

<!-- Formulaire HTML -->
<?php if ($user): ?>
<form method="post">
    <input type="hidden" name="id" value="<?= $user['id'] ?>">
    <label>Prénom: <input type="text" name="first_name" value="<?= $user['first_name'] ?>"></label><br>
    <label>Nom: <input type="text" name="last_name" value="<?= $user['last_name'] ?>"></label><br>
    <label>Email: <input type="email" name="email" value="<?= $user['email'] ?>"></label><br>
    <label>Mot de passe: <input type="text" name="pwd" value="<?= $user['pwd'] ?>"></label><br>
    <label>Téléphone: <input type="text" name="phone" value="<?= $user['phone'] ?>"></label><br>
    <label>Rôle: <input type="text" name="role" value="<?= $user['role'] ?>"></label><br>
    <button type="submit">Modifier</button>
</form>
<?php else: ?>
<p>Utilisateur non trouvé.</p>
<?php endif; ?>
<?php
