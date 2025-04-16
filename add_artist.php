<?php require_once 'C:\xampp\htdocs\front\artcont.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $groupName = $_POST['group_name'];
    $genre = $_POST['genre'];
    $country = $_POST['country'];
    $bio = $_POST['bio'];

    $artistController = new ArtistController();
    $artist = new Artist($name, $username, $groupName, $genre, $country, $bio);

    if (!empty($id)) {
        // This is an update
        $artist->setId($id);
        $artistController->updateArtist($artist);
    } else {
        // This is a new insert
        $artistController->addArtist($artist);
    }

    header("Location: bac/gestion_user.php"); // or wherever your artist list is
    exit;
}
?>