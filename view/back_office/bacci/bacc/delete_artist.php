<?php
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/artcont.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $controller = new ArtistController();
    $controller->deleteArtist($_POST['id']);
    header("Location: bac/gestion_user.php"); // or wherever your artist list is
    exit();
}
?>
