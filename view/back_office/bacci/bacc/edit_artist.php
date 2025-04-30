<?php
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/artcont.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   
    $id = $_POST['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $groupName = $_POST['group_name'];
    $genre = $_POST['genre'];
    $country = $_POST['country'];
    $bio = $_POST['bio'];



    $artistController = new ArtistController();
    $artist = new Artist($id, $name, $username, $groupName, $genre, $country, $bio, '');
    $artistController->updateArtist($artist);

    header("Location: gestion_user.php");
    exit;
}
?>