<?php
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/Config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $artist_id = $_POST['artist_id'];
    $action = $_POST['action'];

    try {
        $db = config::getConnexion();

        // Check if user_id exists in the user table
        $stmt = $db->prepare("SELECT COUNT(*) FROM user WHERE id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        if ($stmt->fetchColumn() == 0) {
            echo "Error: User not found.";

            exit();
        }

        // Check if artist_id exists in the artists table
        $stmt = $db->prepare("SELECT COUNT(*) FROM artists WHERE id = :artist_id");
        $stmt->execute([':artist_id' => $artist_id]);
        if ($stmt->fetchColumn() == 0) {
            echo "Error: Artist not found.";
            exit();
        }

        if ($action === 'favorite') {
            // Toggle the favorite status
            $stmt = $db->prepare("SELECT is_favorite FROM ratings WHERE user_id = :user_id AND artist_id = :artist_id");
            $stmt->execute([':user_id' => $user_id, ':artist_id' => $artist_id]);
            $favorite = $stmt->fetchColumn();

            $new_favorite = $favorite ? 0 : 1; // Toggle favorite status

            // Update the favorite status
            $stmt = $db->prepare("INSERT INTO ratings (user_id, artist_id, is_favorite)
                                  VALUES (:user_id, :artist_id, :is_favorite)
                                  ON DUPLICATE KEY UPDATE is_favorite = :is_favorite");
            $stmt->execute([':user_id' => $user_id, ':artist_id' => $artist_id, ':is_favorite' => $new_favorite]);
        }

        if ($action === 'rating') {
            $rating = $_POST['rating'];

            // Check if the rating exists
            $stmt = $db->prepare("SELECT COUNT(*) FROM ratings WHERE user_id = :user_id AND artist_id = :artist_id");
            $stmt->execute([':user_id' => $user_id, ':artist_id' => $artist_id]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                // Update existing rating
                $stmt = $db->prepare("UPDATE ratings SET stars = :stars WHERE user_id = :user_id AND artist_id = :artist_id");
            } else {
                // Insert new rating
                $stmt = $db->prepare("INSERT INTO ratings (user_id, artist_id, stars) VALUES (:user_id, :artist_id, :stars)");
            }

            $stmt->execute([':user_id' => $user_id, ':artist_id' => $artist_id, ':stars' => $rating]);
        }

        // Redirect back to the artist list
        header("Location: details.php"); // Update with the correct URL
        exit;

    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>
