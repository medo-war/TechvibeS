<?php
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/groupcontroller.php';

if ( isset($_GET['id'])) {
    $controller = new GroupController();
    $controller->deleteGroup($_GET['id']);
    header("Location: gestion_group.php");
    exit();
} else {
    // If not a POST request or ID not provided, redirect with error
    header("Location: gestion_group.php?error=invalid_request");
    exit();
}