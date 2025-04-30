<?php
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/artcont.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $username = $_POST['username'];
    $groupName = $_POST['group_id'] ?? null;
    $genre = $_POST['genre'] ?? null;
    $country = $_POST['country'] ?? null;
    $bio = $_POST['bio'] ?? null;
    $imageUrl = $_POST['imageUrl']; // Default image
    
    // Handle file upload if exists
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/uploads/';
        $imageFile = $_FILES['image'];
        
        // Check for upload errors
        if ($imageFile['error'] !== UPLOAD_ERR_OK) {
            die("Error uploading file: " . $imageFile['error']);
        }

        // Check file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            die('Invalid file type. Please upload an image file.');
        }

        // Generate image URL and move the file
        $imageUrl = 'uploads/' . basename($imageFile['name']);
        if (move_uploaded_file($imageFile['tmp_name'], $uploadDir . $imageFile['name'])) {
            echo "File uploaded successfully!";
        } else {
            die("Error moving the file.");
        }
    }

    // Create artist object
    $artist = new Artist($id, $name, $username, $groupName, $genre, $country, $bio, $imageUrl);
    $artistController = new ArtistController();

    // Save to database
    if (!empty($id)) {
        $artist->setId($id);
        $artistController->updateArtist($artist);
    } else {
        $artistController->addArtist($artist);
    }

    // Redirect back
    header("Location: gestion_user.php");
    exit;
}
