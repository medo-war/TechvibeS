<?php
require_once 'artcont.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   
    $id = $_POST['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $groupName = $_POST['group_name'];
    $genre = $_POST['genre'];
    $country = $_POST['country'];
    $bio = $_POST['bio'];

    echo $name;


    $artistController = new ArtistController();
    $artist = new Artist($id, $name, $username, $groupName, $genre, $country, $bio, '');
    $artistController->updateArtist($artist);


    exit;
}
?>