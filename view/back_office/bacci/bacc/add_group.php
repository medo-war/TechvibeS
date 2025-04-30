<?php
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/groupcontroller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $genre = $_POST['genre'] ?? null;
    $formationYear = $_POST['formation_year'] ?? null;
    $country = $_POST['country'] ?? null;
    $websiteUrl = $_POST['website_url'] ?? null;
    $bio = $_POST['bio'] ?? null;
    $imageUrl = $_POST['current_image'] ?? 'default-group.jpg'; // Default image
    
    // Handle file upload if exists
    if (!empty($_FILES['image_url']['name'])) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/uploads/groups/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageFile = $_FILES['image_url'];
        
        // Check for upload errors
        if ($imageFile['error'] !== UPLOAD_ERR_OK) {
            die("Error uploading file: " . $imageFile['error']);
        }

        // Check file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            header("Location: gestion_group.php?error=invalid_file_type");
            exit();
        }

        // Generate unique filename
        $newFilename = uniqid('group_', true) . '.' . $fileExtension;
        $imageUrl = 'uploads/groups/' . $newFilename;
        
        // Move the uploaded file
        if (!move_uploaded_file($imageFile['tmp_name'], $uploadDir . $newFilename)) {
            header("Location: gestion_group.php?error=upload_failed");
            exit();
        }
    }

    // Create group object
    $group = new Group();
    $group->setId($id);
    $group->setName($name);
    $group->setGenre($genre);
    $group->setFormationYear($formationYear);
    $group->setCountry($country);
    $group->setWebsiteUrl($websiteUrl);
    $group->setBio($bio);
    $group->setImageUrl($imageUrl);

    $groupController = new GroupController();

    // Save to database
    if (!empty($id)) {
        $groupController->updateGroup($group);
    } else {
        $groupController->addGroup($group);
    }

    // Redirect back
    header("Location: gestion_group.php?success=1");
    exit();
} else {
    // If not a POST request, redirect with error
    header("Location: gestion_group.php?error=invalid_request");
    exit();
}