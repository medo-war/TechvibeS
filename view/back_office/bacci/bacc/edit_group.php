<?php
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/groupController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = $_POST['id'];
    $name = $_POST['name'];
    $genre = $_POST['genre'];
    $formationYear = $_POST['formation_year'] ?? null;
    $country = $_POST['country'];
    $websiteUrl = $_POST['website_url'] ?? null;
    $bio = $_POST['bio'];
    $currentImage = $_POST['current_image'] ?? 'default-group.jpg';

    // Handle image upload if new file was provided
    $imageUrl = $currentImage; // Default to current image
    
    if (!empty($_FILES['image_url']['name'])) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/uploads/groups/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageFile = $_FILES['image_url'];
        
        // Validate file
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        
        if ($imageFile['error'] !== UPLOAD_ERR_OK) {
            header("Location: gestion_group.php?error=upload_error");
            exit();
        }

        if (!in_array($fileExtension, $allowedExtensions)) {
            header("Location: gestion_group.php?error=invalid_file_type");
            exit();
        }

        // Generate unique filename and save
        $newFilename = uniqid('group_', true) . '.' . $fileExtension;
        $imageUrl = 'uploads/groups/' . $newFilename;
        
        if (!move_uploaded_file($imageFile['tmp_name'], $uploadDir . $newFilename)) {
            header("Location: gestion_group.php?error=upload_failed");
            exit();
        }

        // Optionally: Delete old image file if it's not the default
        if ($currentImage !== 'default-group.jpg' && file_exists($_SERVER['DOCUMENT_ROOT'] . '/livethemusic/' . $currentImage)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/livethemusic/' . $currentImage);
        }
    }

    // Update group in database
    $groupController = new GroupController();
    $group = new Group();
    
    $group->setId($id);
    $group->setName($name);
    $group->setGenre($genre);
    $group->setFormationYear($formationYear);
    $group->setCountry($country);
    $group->setWebsiteUrl($websiteUrl);
    $group->setBio($bio);
    $group->setImageUrl($imageUrl);

    $groupController->updateGroup($group);

    // Redirect back with success message
    header("Location: gestion_group.php?success=1");
    exit();
} else {
    // If not a POST request, redirect with error
    header("Location: gestion_group.php?error=invalid_request");
    exit();
}