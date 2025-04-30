<?php
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/groupController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'];
    $genre = $_POST['genre'];
    $formationYear = $_POST['formation_year'] ?? null;
    $country = $_POST['country'];
    $websiteUrl = $_POST['website_url'] ?? null;
    $bio = $_POST['bio'];
    $imageUrl = 'uploads/groups/default-group.jpg'; // Default image

    // Handle image upload if a file was provided
    if (!empty($_FILES['image_url']['name'])) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/uploads/groups/';
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageFile = $_FILES['image_url'];
        $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if ($imageFile['error'] !== UPLOAD_ERR_OK) {
            header("Location: gestion_group.php?error=upload_error");
            exit();
        }

        if (!in_array($ext, $allowed)) {
            header("Location: gestion_group.php?error=invalid_file_type");
            exit();
        }

        $newFilename = uniqid('group_', true) . '.' . $ext;
        $imagePath = $uploadDir . $newFilename;

        if (move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
            $imageUrl = 'uploads/groups/' . $newFilename;
        } else {
            header("Location: gestion_group.php?error=upload_failed");
            exit();
        }
    }

    // Create group and save
    $groupController = new GroupController();
    $group = new Group();

    $group->setName($name);
    $group->setGenre($genre);
    $group->setFormationYear($formationYear);
    $group->setCountry($country);
    $group->setWebsiteUrl($websiteUrl);
    $group->setBio($bio);
    $group->setImageUrl($imageUrl);

    $groupController->addGroup($group);

    header("Location: gestion_group.php?success=1");
    exit();
} else {
    header("Location: gestion_group.php?error=invalid_request");
    exit();
}
