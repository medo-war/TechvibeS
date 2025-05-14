<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate_captcha'])) {
    $_SESSION['captcha_verified'] = true;
    $_SESSION['captcha_time'] = time();
    
    echo json_encode(['success' => true]);
    exit();
}

echo json_encode(['success' => false]);