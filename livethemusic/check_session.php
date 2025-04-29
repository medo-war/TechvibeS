<?php
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_lifetime' => 86400,
            'cookie_secure'   => false,
            'cookie_httponly' => true
        ]);
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn($url = '/livethemusic/view/front_office/welcome.php') {
    if (!isLoggedIn()) {
        header("Location: $url");
        exit();
    }
}
?>