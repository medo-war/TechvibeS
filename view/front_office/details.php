<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/Config.php';

// Check if user is logged in and get user ID
$user_id = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;

// Include song data and error session handling for music recognition
$song_data = $_SESSION['song_data'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['song_data'], $_SESSION['error']);

// Handle file upload and audio data for music recognition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_FILES['audio_file']) || isset($_POST['audio_data']))) {
    if (isset($_FILES['audio_file'])) {
        $uploaded_file = $_FILES['audio_file']['tmp_name'];
        $file_type = mime_content_type($uploaded_file);
        $file_name = $_FILES['audio_file']['name'];

        $data = [
            'file' => new CURLFile($uploaded_file, $file_type, $file_name),
            'return' => 'apple_music,spotify',
            'api_token' => '6ba73550edbe294def797823a3192407',
        ];

    } elseif (isset($_POST['audio_data'])) {
        $audio_data = $_POST['audio_data'];
        $audio_data = str_replace('data:audio/webm;base64,', '', $audio_data);
        $audio_data = str_replace(' ', '+', $audio_data);
        $decoded_audio = base64_decode($audio_data);

        $temp_file = tempnam(sys_get_temp_dir(), 'audio_') . '.webm';
        file_put_contents($temp_file, $decoded_audio);

        $data = [
            'file' => new CURLFile($temp_file, 'audio/webm', 'recording.webm'),
            'return' => 'apple_music,spotify',
            'api_token' => '6ba73550edbe294def797823a3192407',
        ];
    }

    if (isset($data)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.audd.io/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (isset($temp_file)) {
            unlink($temp_file);
        }
        
        if ($http_code === 200) {
            $result = json_decode($response, true);
            if (isset($result['status']) && $result['status'] === 'success') {
                $_SESSION['song_data'] = $result['result'];
            } else {
                $_SESSION['error'] = "Song recognition failed: " . ($result['error']['error_message'] ?? 'Unknown error');
            }
        } else {
            $_SESSION['error'] = "API request failed with HTTP code $http_code";
        }

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="templatemo">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <title>Liberty Template - NFT Item Detail Page</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome CDN Direct Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-liberty-market.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="style.css">
    
    <style>
        :root {
            --neon-pink: #ff2a6d;
            --neon-blue: #05d9e8;
            --dark-blue: #01012b;
            --darker-blue: #000022;
            --light-pink: #ff7bbf;
            --purple: #7928ca;
            --cyan: #21d4fd;
            --teal: #00ffe7;
            --magenta: #ff00ff;
            --yellow: #ffff00;
            --orange: #ff9d00;
            --gradient-1: linear-gradient(45deg, #ff2a6d, #7928ca);
            --gradient-2: linear-gradient(45deg, #21d4fd, #05d9e8);
            --gradient-3: linear-gradient(45deg, #ff00ff, #00ffe7);
            --gradient-4: linear-gradient(45deg, #ff9d00, #ffff00);
        }
        
        body {
            background-color: #000;
            color: white;
            background-image: none !important;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Particles.js Container */
        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -999;
            background: linear-gradient(to bottom, #000022, #010136, #01012b);
            pointer-events: none;
        }
        
        /* Sound Wave Animation */
        @keyframes quiet {
            25% {
                transform: scaleY(.6);
            }
            50% {
                transform: scaleY(.4);
            }
            75% {
                transform: scaleY(.8);
            }
        }

        @keyframes normal {
            25% {
                transform: scaleY(1);
            }
            50% {
                transform: scaleY(.4);
            }
            75% {
                transform: scaleY(.6);
            }
        }

        @keyframes loud {
            25% {
                transform: scaleY(1);
            }
            50% {
                transform: scaleY(.4);
            }
            75% {
                transform: scaleY(1.2);
            }
        }
        
        .sound-wave-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            overflow: hidden;
            z-index: -1;
            opacity: 0.6;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .boxContainer {
            display: flex;
            justify-content: space-between;
            height: 64px;
            --boxSize: 8px;
            --gutter: 4px;
            margin: 0 auto;
        }
        
        .box {
            transform: scaleY(.4);
            height: 100%;
            width: var(--boxSize);
            background: var(--teal);
            animation-duration: 1.2s;
            animation-timing-function: ease-in-out;
            animation-iteration-count: infinite;
            border-radius: 8px;
            margin: 0 var(--gutter);
            box-shadow: 0 0 10px var(--teal);
        }
        
        .box1 {
            animation-name: quiet;
            background: var(--neon-pink);
            box-shadow: 0 0 10px var(--neon-pink);
        }
        
        .box2 {
            animation-name: normal;
            background: var(--magenta);
            box-shadow: 0 0 10px var(--magenta);
        }
        
        .box3 {
            animation-name: quiet;
            background: var(--purple);
            box-shadow: 0 0 10px var(--purple);
        }
        
        .box4 {
            animation-name: loud;
            background: var(--neon-blue);
            box-shadow: 0 0 10px var(--neon-blue);
        }
        
        .box5 {
            animation-name: quiet;
            background: var(--cyan);
            box-shadow: 0 0 10px var(--cyan);
        }
        
        /* Page Heading Styles */
        .page-heading {
            background: linear-gradient(to right, #000022, #010136) !important;
            border-bottom: 1px solid var(--neon-blue);
            box-shadow: 0 0 20px rgba(5, 217, 232, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        /* Spotlight Effect */
        .page-heading::before {
            content: '';
            position: absolute;
            top: -150px;
            left: -150px;
            width: 300px;
            height: 300px;
            background: radial-gradient(
                circle,
                rgba(255, 42, 109, 0.15) 0%,
                rgba(255, 42, 109, 0.05) 40%,
                transparent 70%
            );
            border-radius: 50%;
            z-index: 1;
            animation: spotlight-move-pink 15s infinite alternate ease-in-out;
            filter: blur(10px);
        }
        
        .page-heading::after {
            content: '';
            position: absolute;
            top: -150px;
            right: -150px;
            width: 300px;
            height: 300px;
            background: radial-gradient(
                circle,
                rgba(5, 217, 232, 0.15) 0%,
                rgba(5, 217, 232, 0.05) 40%,
                transparent 70%
            );
            border-radius: 50%;
            z-index: 1;
            animation: spotlight-move-blue 15s infinite alternate-reverse ease-in-out;
            filter: blur(10px);
        }
        
        @keyframes spotlight-move-pink {
            0% {
                transform: translate(0, 0);
                opacity: 0.5;
            }
            25% {
                transform: translate(50%, 20%);
                opacity: 0.7;
            }
            50% {
                transform: translate(100%, 0%);
                opacity: 0.5;
            }
            75% {
                transform: translate(70%, 30%);
                opacity: 0.7;
            }
            100% {
                transform: translate(120%, 10%);
                opacity: 0.5;
            }
        }
        
        @keyframes spotlight-move-blue {
            0% {
                transform: translate(0, 0);
                opacity: 0.5;
            }
            25% {
                transform: translate(-30%, 20%);
                opacity: 0.7;
            }
            50% {
                transform: translate(-80%, 0%);
                opacity: 0.5;
            }
            75% {
                transform: translate(-50%, 30%);
                opacity: 0.7;
            }
            100% {
                transform: translate(-100%, 10%);
                opacity: 0.5;
            }
        }
        
        .page-heading h2 {
            color: white !important;
            position: relative;
            z-index: 5;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }
        
        .page-heading em {
            position: relative;
            z-index: 2;
        }
        
        .neon-pulse {
            color: var(--neon-blue) !important;
            text-shadow: 
                0 0 7px var(--neon-blue),
                0 0 10px var(--neon-blue),
                0 0 21px var(--neon-blue),
                0 0 42px var(--neon-blue);
            animation: neon-text-pulse 2s infinite alternate-reverse;
            font-style: normal;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        @keyframes neon-text-pulse {
            0%, 18%, 22%, 25%, 53%, 57%, 100% {
                text-shadow:
                    0 0 7px var(--neon-blue),
                    0 0 10px var(--neon-blue),
                    0 0 21px var(--neon-blue),
                    0 0 42px var(--neon-blue);
            }
            20%, 24%, 55% {
                text-shadow:
                    0 0 4px var(--neon-blue),
                    0 0 5px var(--neon-blue),
                    0 0 10px var(--neon-blue),
                    0 0 20px var(--neon-blue);
            }
        }
        
        .line-dec {
            background-color: var(--neon-blue) !important;
            position: relative;
            z-index: 2;
        }
        
        /* Beat Wave Animation Behind Text */
        .beat-wave-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2;
            opacity: 0.6;
            perspective: 1000px;
            transform-style: preserve-3d;
        }
        
        .beat-wave {
            width: 8px;
            height: 30px;
            margin: 0 5px;
            border-radius: 8px;
            animation: beat-animation 1.8s infinite ease-in-out;
            transform-origin: bottom;
            position: relative;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
            filter: blur(0.5px);
        }
        
        .beat-wave::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            border-radius: inherit;
            filter: blur(5px);
            opacity: 0.7;
            z-index: -1;
        }
        
        @keyframes beat-animation {
            0% {
                transform: scaleY(0.2) translateY(0);
                opacity: 0.7;
            }
            20% {
                transform: scaleY(0.5) translateY(-2px);
                opacity: 0.8;
            }
            40% {
                transform: scaleY(1) translateY(-5px);
                opacity: 1;
            }
            60% {
                transform: scaleY(0.7) translateY(-3px);
                opacity: 0.9;
            }
            80% {
                transform: scaleY(0.4) translateY(-1px);
                opacity: 0.8;
            }
            100% {
                transform: scaleY(0.2) translateY(0);
                opacity: 0.7;
            }
        }
        
        @keyframes pulse-glow {
            0%, 100% {
                filter: brightness(1) blur(0.5px);
            }
            50% {
                filter: brightness(1.5) blur(1px);
            }
        }
        
        /* Artist Card Styles */
        .artist-card {
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(121, 40, 202, 0.3);
            box-shadow: 0 10px 30px rgba(5, 217, 232, 0.2);
            margin-bottom: 40px;
            background: linear-gradient(135deg, rgba(1, 1, 43, 0.5), rgba(0, 0, 34, 0.5));
            backdrop-filter: blur(20px);
            position: relative;
            transform-style: preserve-3d;
        }
        
        .artist-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: var(--gradient-3);
            z-index: 1;
            opacity: 0.8;
        }
        
        .artist-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            padding: 1px;
            background: linear-gradient(135deg, var(--neon-pink), transparent, var(--neon-blue));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0.5;
            transition: opacity 0.4s ease;
        }
        
        .artist-card:hover::after {
            opacity: 1;
        }
        
        .artist-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 35px rgba(5, 217, 232, 0.3), 0 0 15px rgba(255, 42, 109, 0.3);
            background: linear-gradient(135deg, rgba(1, 1, 43, 0.6), rgba(0, 0, 34, 0.6));
        }
        
        .artist-image {
            height: 250px;
            object-fit: cover;
            width: 100%;
            filter: saturate(1.3) contrast(1.1);
            transition: all 0.5s ease;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
            transform: translateZ(0);
        }
        
        .artist-card:hover .artist-image {
            filter: saturate(1.5) contrast(1.2) brightness(1.1);
            transform: scale(1.05) translateZ(0);
        }
        
        .artist-image::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 30%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent);
            z-index: 1;
        }
        
        .artist-info {
            padding: 25px;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3));
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 2;
            transform: translateZ(0);
        }
        
        .artist-name {
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 0 15px rgba(255, 0, 255, 0.9);
            background: linear-gradient(to right, var(--neon-pink), var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            letter-spacing: 1.5px;
            font-size: 1.5rem;
            position: relative;
            padding-bottom: 5px;
        }
        
        .artist-name::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50%;
            height: 2px;
            background: linear-gradient(to right, var(--neon-pink), transparent);
            transition: width 0.3s ease;
        }
        
        .artist-card:hover .artist-name::after {
            width: 100%;
        }
        
        .artist-username {
            color: var(--neon-blue);
            font-size: 0.95rem;
            margin-bottom: 15px;
            opacity: 0.8;
            transition: opacity 0.3s ease;
            text-shadow: 0 0 5px rgba(5, 217, 232, 0.5);
            font-style: italic;
        }
        
        .artist-card:hover .artist-username {
            opacity: 1;
        }
        
        .artist-meta {
            display: flex;
            margin-bottom: 15px;
        }
        
        .meta-item {
            margin-right: 15px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
            background: rgba(121, 40, 202, 0.2);
            padding: 5px 10px;
            border-radius: 15px;
            display: inline-flex;
            align-items: center;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }
        
        .meta-item:hover {
            background: rgba(121, 40, 202, 0.4);
            transform: translateY(-2px);
        }
        
        .meta-item i {
            margin-right: 5px;
            color: var(--neon-pink);
            font-size: 1rem;
        }
        
        .star-rating {
            margin: 15px 0;
        }
        
        .star-rating .bi-star {
            color: rgba(255, 255, 255, 0.3);
            font-size: 1.4rem;
            margin-right: 3px;
            cursor: pointer;
        }
        
        .star-rating .bi-star-fill {
            color: #ffc107;
            font-size: 1.4rem;
            margin-right: 3px;
            cursor: pointer;
            filter: drop-shadow(0 0 3px rgba(255, 193, 7, 0.5));
        }
        
        .btn-more {
            background: linear-gradient(45deg, rgba(255, 42, 109, 0.7), rgba(5, 217, 232, 0.7));
            color: white;
            border: none;
            border-radius: 30px;
            padding: 12px 25px;
            font-weight: 600;
            width: 100%;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            z-index: 1;
            backdrop-filter: blur(5px);
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.7);
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(5, 217, 232, 0.3);
            transform: translateZ(0);
        }
        
        .btn-more::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-4);
            transition: all 0.4s;
            z-index: -1;
        }
        
        .btn-more:hover::before {
            left: 0;
        }
        
        .btn-more:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 20px rgba(255, 42, 109, 0.7);
        }
        
        .favorite-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 10;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--neon-pink);
            color: var(--neon-pink);
            font-size: 1.2rem;
            box-shadow: 0 0 15px rgba(255, 42, 109, 0.4);
            backdrop-filter: blur(4px);
        }
        
        .favorite-btn:hover {
            background: rgba(255, 42, 109, 0.2);
        }
        
        /* Card collapse content */
        .card-body {
            background: linear-gradient(135deg, rgba(1, 1, 43, 0.9), rgba(0, 0, 34, 0.9)) !important;
            color: white !important;
            border: 1px solid var(--purple) !important;
            border-radius: 8px !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3) !important;
        }
        
        /* Footer styling */
        footer {
            background: linear-gradient(to right, #000022, #010136) !important;
            border-top: 1px solid var(--purple);
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.5);
        }
        
        footer p, footer a {
            color: white !important;
        }
        
        footer a:hover {
            color: var(--neon-pink) !important;
        }
        
        /* Music Recognition Popup Styles */
        :root {
            --neon-pink: #ff2a6d;
            --neon-blue: #05d9e8;
            --dark-blue: #01012b;
            --darker-blue: #000022;
            --light-pink: #ff7bbf;
        }
        
        .music-recognition-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--neon-pink), var(--light-pink));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 0 15px var(--neon-pink);
            z-index: 999;
            border: none;
            transition: all 0.3s;
        }
        
        .music-recognition-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 25px var(--neon-pink);
        }
        
        .music-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .music-popup.active {
            display: flex;
        }
        
        .music-popup-content {
            background: rgba(1, 1, 43, 0.95);
            width: 90%;
            max-width: 600px;
            border-radius: 12px;
            padding: 30px;
            position: relative;
            border: 1px solid var(--neon-blue);
            box-shadow: 0 0 25px var(--neon-blue);
            color: white;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close-popup {
            position: absolute;
            top: 15px;
            right: 15px;
            background: transparent;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }
        
        .popup-section {
            margin-bottom: 25px;
        }
        
        /* Simple Star Rating */
        .star-rating {
            margin: 10px 0;
        }
        
        .gold-star {
            color: gold !important;
            font-size: 24px !important;
            margin: 0 2px !important;
            text-shadow: 0 0 5px rgba(255, 215, 0, 0.5) !important;
        }
        
        .star-btn {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            margin: 0 3px;
        }
        
        .popup-section h3 {
            color: var(--neon-pink);
            text-shadow: 0 0 5px var(--neon-pink);
            margin-bottom: 15px;
            font-size: 20px;
            display: flex;
            align-items: center;
        }
        
        .popup-section h3 i {
            margin-right: 10px;
        }
        
        .popup-tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(5, 217, 232, 0.3);
        }
        
        .popup-tab {
            padding: 10px 20px;
            cursor: pointer;
            color: white;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .popup-tab.active {
            color: var(--neon-blue);
            border-bottom: 3px solid var(--neon-blue);
        }
        
        .popup-tab-content {
            display: none;
        }
        
        .popup-tab-content.active {
            display: block;
        }
        
        /* Form elements */
        .popup-form-group {
            margin-bottom: 20px;
        }
        
        .popup-file-input {
            display: none;
        }
        
        .popup-file-label {
            display: inline-block;
            padding: 12px 25px;
            background: rgba(5, 217, 232, 0.2);
            border: 1px dashed var(--neon-blue);
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            width: 100%;
        }
        
        .popup-file-label:hover {
            background: rgba(5, 217, 232, 0.3);
            border-color: var(--light-pink);
        }
        
        .popup-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            border: none;
            margin: 5px;
            position: relative;
            overflow: hidden;
        }
        
        .popup-btn i {
            margin-right: 8px;
        }
        
        .popup-btn-primary {
            background: linear-gradient(45deg, var(--neon-pink), var(--light-pink));
            color: white;
            box-shadow: 0 0 10px var(--neon-pink);
        }
        
        .popup-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 20px var(--neon-pink);
        }
        
        .popup-btn-secondary {
            background: linear-gradient(45deg, var(--neon-blue), #00f7ff);
            color: var(--darker-blue);
            box-shadow: 0 0 10px var(--neon-blue);
        }
        
        .popup-btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 20px var(--neon-blue);
        }
        
        .popup-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: 0 0 10px rgba(255,255,255,0.1) !important;
        }
        
        .popup-btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 15px 0;
        }
        
        /* Recording indicator */
        .popup-recording-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            background-color: var(--neon-pink);
            border-radius: 50%;
            margin-right: 10px;
            box-shadow: 0 0 10px var(--neon-pink);
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .popup-recording-status {
            display: flex;
            align-items: center;
            margin: 15px 0;
            color: var(--neon-pink);
            font-size: 14px;
        }
        
        /* Results */
        .popup-result-container {
            background: rgba(1, 1, 43, 0.8);
            border-radius: 10px;
            padding: 20px;
            border: 1px solid var(--neon-blue);
            box-shadow: 0 0 15px var(--neon-blue);
        }
        
        .popup-song-info {
            margin-bottom: 20px;
        }
        
        .popup-song-info p {
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .popup-song-info strong {
            color: var(--neon-pink);
        }
        
        /* Audio player styling */
        audio {
            background: rgba(5, 217, 232, 0.1);
            border-radius: 30px;
        }
        
        audio::-webkit-media-controls-panel {
            background: rgba(1, 1, 43, 0.7);
        }
        
        audio::-webkit-media-controls-play-button {
            background-color: var(--neon-pink);
            border-radius: 50%;
        }
        
        .popup-platform-links {
            display: flex;
            gap: 20px;
            margin-top: 25px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .popup-platform-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 25px;
            border-radius: 30px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            min-width: 200px;
            font-weight: 500;
            font-size: 14px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: none;
        }
        
        .popup-platform-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }
        
        .popup-platform-link.spotify {
            background: linear-gradient(135deg, #1DB954, #158f3e);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .popup-platform-link.apple {
            background: linear-gradient(135deg, #FC3C44, #c81b23);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .popup-platform-logo {
            width: auto;
            height: 20px;
            margin-right: 10px;
            object-fit: contain;
        }
        
        /* Alert */
        .popup-alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            background: var(--neon-pink);
            color: white;
            box-shadow: 0 0 15px var(--neon-pink);
        }
    </style>
</head>

<body>
  <!-- ***** Preloader Start ***** -->
  <div id="js-preloader" class="js-preloader">
    <div class="preloader-inner">
      <span class="dot"></span>
      <div class="dots">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </div>
  <!-- ***** Preloader End ***** -->
  
  <!-- Particles.js Library -->
  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
  
  <!-- Particles.js Container (moved to beginning of body for proper layering) -->
  <div id="particles-js"></div>

  <!-- Include the navbar -->
  <?php include 'includes/navbar.php'; ?>
  <!-- ***** Header Area End ***** -->

  <!-- Sound Wave Animation Background -->
  <div class="sound-wave-container">
    <div class="boxContainer">
      <div class="box box1"></div>
      <div class="box box2"></div>
      <div class="box box3"></div>
      <div class="box box4"></div>
      <div class="box box5"></div>
      <div class="box box1"></div>
      <div class="box box2"></div>
      <div class="box box3"></div>
      <div class="box box4"></div>
      <div class="box box5"></div>
      <div class="box box1"></div>
      <div class="box box2"></div>
      <div class="box box3"></div>
      <div class="box box4"></div>
      <div class="box box5"></div>
      <div class="box box1"></div>
      <div class="box box2"></div>
      <div class="box box3"></div>
      <div class="box box4"></div>
      <div class="box box5"></div>
    </div>
  </div>

  <div class="page-heading normal-space">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="section-heading">
            <!-- Hexagon Grid Background -->
            <div class="hexagon-grid"></div>
            
            <!-- Horizontal Lines Effect -->
            <div class="horizontal-lines">
              <div class="h-line" style="top: 20%; animation-delay: 0s;"></div>
              <div class="h-line" style="top: 40%; animation-delay: 1s;"></div>
              <div class="h-line" style="top: 60%; animation-delay: 2s;"></div>
              <div class="h-line" style="top: 80%; animation-delay: 3s;"></div>
            </div>
            
            <!-- Circular Glow Effect -->
            <div class="glow-circle"></div>
            
            <!-- Beat Wave Animation Behind Text -->
            <div class="beat-wave-container">
              <div class="beat-wave" style="background: linear-gradient(to top, var(--neon-pink), var(--light-pink)); animation-delay: 0s; height: 40px; animation: beat-animation 1.5s infinite ease-in-out, pulse-glow 3s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--purple), #a44dff); animation-delay: 0.1s; height: 60px; animation: beat-animation 1.7s infinite ease-in-out, pulse-glow 2.8s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--magenta), #ff80ff); animation-delay: 0.2s; height: 50px; animation: beat-animation 1.3s infinite ease-in-out, pulse-glow 3.2s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--neon-blue), #80f9ff); animation-delay: 0.3s; height: 70px; animation: beat-animation 1.9s infinite ease-in-out, pulse-glow 2.5s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--cyan), #80ffff); animation-delay: 0.4s; height: 45px; animation: beat-animation 1.4s infinite ease-in-out, pulse-glow 3.5s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--teal), #80ffee); animation-delay: 0.5s; height: 65px; animation: beat-animation 1.6s infinite ease-in-out, pulse-glow 2.7s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--neon-pink), var(--light-pink)); animation-delay: 0.6s; height: 55px; animation: beat-animation 1.8s infinite ease-in-out, pulse-glow 3.3s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--purple), #a44dff); animation-delay: 0.7s; height: 75px; animation: beat-animation 1.5s infinite ease-in-out, pulse-glow 2.9s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--magenta), #ff80ff); animation-delay: 0.8s; height: 40px; animation: beat-animation 1.7s infinite ease-in-out, pulse-glow 3.1s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--neon-blue), #80f9ff); animation-delay: 0.9s; height: 60px; animation: beat-animation 1.4s infinite ease-in-out, pulse-glow 2.6s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--cyan), #80ffff); animation-delay: 1.0s; height: 50px; animation: beat-animation 1.9s infinite ease-in-out, pulse-glow 3.4s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--teal), #80ffee); animation-delay: 1.1s; height: 70px; animation: beat-animation 1.3s infinite ease-in-out, pulse-glow 2.8s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--neon-pink), var(--light-pink)); animation-delay: 1.2s; height: 45px; animation: beat-animation 1.6s infinite ease-in-out, pulse-glow 3.2s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--purple), #a44dff); animation-delay: 1.3s; height: 65px; animation: beat-animation 1.8s infinite ease-in-out, pulse-glow 2.7s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--magenta), #ff80ff); animation-delay: 1.4s; height: 55px; animation: beat-animation 1.5s infinite ease-in-out, pulse-glow 3.3s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--neon-blue), #80f9ff); animation-delay: 1.5s; height: 75px; animation: beat-animation 1.7s infinite ease-in-out, pulse-glow 2.5s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--cyan), #80ffff); animation-delay: 1.6s; height: 40px; animation: beat-animation 1.4s infinite ease-in-out, pulse-glow 3.1s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--teal), #80ffee); animation-delay: 1.7s; height: 60px; animation: beat-animation 1.9s infinite ease-in-out, pulse-glow 2.9s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--yellow), #ffff80); animation-delay: 1.8s; height: 50px; animation: beat-animation 1.6s infinite ease-in-out, pulse-glow 3.4s infinite;"></div>
              <div class="beat-wave" style="background: linear-gradient(to top, var(--orange), #ffcc80); animation-delay: 1.9s; height: 70px; animation: beat-animation 1.3s infinite ease-in-out, pulse-glow 2.6s infinite;"></div>
            </div>
            <div class="line-dec"></div>
            <h2>Discover <em class="neon-pulse">Top Music Artists</em> Here.</h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container py-5">
    <div class="row">
      <?php
      try {
          $db = config::getConnexion();
          $stmt = $db->prepare("SELECT * FROM artists");
          $stmt->execute();
          $artists = $stmt->fetchAll();

          foreach ($artists as $artist):
              // Get user rating if logged in
              $isFavorite = false;
              $stars = 0;
              
              if ($user_id) {
                  $stmt_rating = $db->prepare("SELECT r.is_favorite, r.stars
                                              FROM ratings r
                                              WHERE r.artist_id = :artist_id AND r.user_id = :user_id");
                  $stmt_rating->execute([':artist_id' => $artist['id'], ':user_id' => $user_id]);
                  $rating = $stmt_rating->fetch();
                  
                  $isFavorite = $rating['is_favorite'] ?? false;
                  $stars = $rating['stars'] ?? 0;
              }
      ?>
      <div class="col-lg-4 col-md-6 mb-4">
          <div class="artist-card <?= $isFavorite ? 'favorited' : '' ?>">
              <!-- Favorite Button -->
              <?php if ($user_id): ?>
              <form action="rate_artist.php" method="POST" class="favorite-btn">
                  <input type="hidden" name="user_id" value="<?= $user_id ?>">
                  <input type="hidden" name="artist_id" value="<?= $artist['id'] ?>">
                  <input type="hidden" name="action" value="favorite">
                  <button type="submit" class="btn p-0" style="background: none; border: none;">
                      <?php if ($isFavorite): ?>
                      <i class="fas fa-heart" style="color: gold; font-size: 24px; text-shadow: 0 0 10px rgba(255, 215, 0, 0.7);"></i>
                      <?php else: ?>
                      <i class="far fa-heart" style="color: #ff2a6d; font-size: 20px;"></i>
                      <?php endif; ?>
                  </button>
              </form>
              <?php endif; ?>
              
              <!-- Artist Image -->
              <img src="<?= htmlspecialchars($artist['image_url']) ?>" class="artist-image" alt="<?= htmlspecialchars($artist['name']) ?>">
              
              <!-- Artist Info -->
              <div class="artist-info">
                  <h4 class="artist-name"><?= htmlspecialchars($artist['name']) ?></h4>
                  <p class="artist-username">@<?= htmlspecialchars($artist['username']) ?></p>
                  
                  <div class="artist-meta">
                      <span class="meta-item"><i class="bi bi-people"></i> <?= htmlspecialchars($artist['group_name']) ?></span>
                      <span class="meta-item"><i class="bi bi-music-note"></i> <?= htmlspecialchars($artist['genre']) ?></span>
                  </div>
                  
                  <!-- Star Rating -->
                   <?php if ($user_id): ?>
                   <div class="star-rating">
                       <!-- Current user rating: <?= $stars ?> -->
                       <?php for ($i = 1; $i <= 5; $i++): ?>
                           <form action="rate_artist.php" method="POST" class="d-inline-block">
                               <input type="hidden" name="user_id" value="<?= $user_id ?>">
                               <input type="hidden" name="artist_id" value="<?= $artist['id'] ?>">
                               <input type="hidden" name="action" value="rating">
                               <input type="hidden" name="rating" value="<?= $i ?>">
                               <button type="submit" class="btn btn-link p-0" style="text-decoration: none;">
                                   <?php if ($i <= $stars): ?>
                                   <i class="fas fa-star" style="color: gold; font-size: 1.2rem; margin: 0 2px;"></i>
                                   <?php else: ?>
                                   <i class="far fa-star" style="color: gold; font-size: 1.2rem; margin: 0 2px;"></i>
                                   <?php endif; ?>
                               </button>
                           </form>
                       <?php endfor; ?>
                   </div>
                   <?php endif; ?>
                  
                  <!-- More Info -->
                  <button class="btn btn-more mt-2" type="button" data-bs-toggle="collapse" 
                          data-bs-target="#artist-<?= $artist['id'] ?>" aria-expanded="false">
                      <i class="bi bi-info-circle me-2"></i> More Info
                  </button>
                  
                  <div class="collapse mt-3" id="artist-<?= $artist['id'] ?>">
                      <div class="card card-body border-0">
                          <h5 style="color: var(--neon-blue);"><i class="bi bi-geo-alt me-2"></i> <?= htmlspecialchars($artist['country']) ?></h5>
                          <p class="mt-2"><?= nl2br(htmlspecialchars($artist['bio'])) ?></p>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <?php
          endforeach;
      } catch (PDOException $e) {
          echo "<div class='alert alert-danger col-12'>Error loading artists: " . htmlspecialchars($e->getMessage()) . "</div>";
      }
      ?>
    </div>
  </div>

  <!-- Music Recognition Button -->
  <button class="music-recognition-btn" id="musicRecognitionBtn">
    <i class="fas fa-music"></i>
  </button>
  
  <!-- Music Recognition Popup -->
  <div class="music-popup" id="musicPopup">
    <div class="music-popup-content">
      <button class="close-popup" id="closePopup">
        <i class="fas fa-times"></i>
      </button>
      
      <div class="popup-tabs">
        <button class="popup-tab active" data-tab="upload">Upload</button>
        <button class="popup-tab" data-tab="record">Record</button>
        <button class="popup-tab" data-tab="results">Results</button>
      </div>
      
      <!-- Upload Tab -->
      <div class="popup-tab-content active" id="uploadTab">
        <div class="popup-section">
          <h3><i class="fas fa-upload"></i> Upload Audio File</h3>
          <form method="POST" enctype="multipart/form-data">
            <div class="popup-form-group">
              <input type="file" name="audio_file" id="popupAudioFile" class="popup-file-input" accept="audio/*" required>
              <label for="popupAudioFile" class="popup-file-label">
                <i class="fas fa-file-audio"></i> Choose Audio File
              </label>
              <div id="popupFileName" style="margin-top: 10px; color: var(--neon-blue);"></div>
            </div>
            <button type="submit" class="popup-btn popup-btn-primary">
              <i class="fas fa-search"></i> Recognize Song
            </button>
          </form>
        </div>
      </div>
      
      <!-- Record Tab -->
      <div class="popup-tab-content" id="recordTab">
        <div class="popup-section">
          <h3><i class="fas fa-microphone"></i> Record Audio</h3>
          <form method="POST" id="popupRecordForm">
            <div class="popup-btn-group">
              <button type="button" id="popupRecordBtn" class="popup-btn popup-btn-primary">
                <i class="fas fa-circle"></i> Start Recording
              </button>
              <button type="button" id="popupStopBtn" class="popup-btn popup-btn-secondary" disabled>
                <i class="fas fa-stop"></i> Stop Recording
              </button>
            </div>
            <div id="popupRecordingStatus" class="popup-recording-status" style="display: none;">
              <span class="popup-recording-indicator"></span>
              <span class="popup-recording-text">Recording in progress...</span>
            </div>
            <input type="hidden" name="audio_data" id="popupAudioData">
            <button type="submit" id="popupSubmitBtn" class="popup-btn popup-btn-primary" disabled>
              <i class="fas fa-paper-plane"></i> Send Recording
            </button>
          </form>
        </div>
      </div>
      
      <!-- Results Tab -->
      <div class="popup-tab-content" id="resultsTab">
        <div class="popup-section">
          <?php if ($error): ?>
            <div class="popup-alert"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>
          
          <?php if ($song_data): ?>
            <div class="popup-result-container">
              <h3><i class="fas fa-music"></i> Song Identified</h3>
              <div class="popup-song-info">
                <p><strong>Title:</strong> <?= htmlspecialchars($song_data['title']) ?></p>
                <p><strong>Artist:</strong> <?= htmlspecialchars($song_data['artist']) ?></p>
                <?php if (isset($song_data['album'])): ?>
                  <p><strong>Album:</strong> <?= htmlspecialchars($song_data['album']) ?></p>
                <?php endif; ?>
              </div>
              
              <div class="popup-btn-group" style="margin: 20px 0;">
                <audio id="previewAudio" controls style="width: 100%; border-radius: 30px;">
                  <?php if (isset($song_data['spotify']['preview_url'])): ?>
                    <source src="<?= htmlspecialchars($song_data['spotify']['preview_url']) ?>" type="audio/mpeg">
                  <?php endif; ?>
                  Your browser does not support the audio element.
                </audio>
              </div>
              
              <div class="popup-platform-links">
                <?php if (isset($song_data['spotify']['external_urls']['spotify'])): ?>
                  <a href="<?= $song_data['spotify']['external_urls']['spotify'] ?>" class="popup-platform-link spotify" target="_blank">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                      <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.48.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                    </svg>
                    Listen on Spotify
                  </a>
                <?php endif; ?>
                <?php if (isset($song_data['apple_music']['url'])): ?>
                  <a href="<?= $song_data['apple_music']['url'] ?>" class="popup-platform-link apple" target="_blank">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                      <path d="M23.994 6.124c-.011-.141-.026-.283-.039-.424a4.528 4.528 0 00-.12-.75 4.281 4.281 0 00-.409-1.012c-.209-.405-.479-.754-.822-1.099-.344-.345-.69-.617-1.099-.824a4.25 4.25 0 00-1.01-.415c-.248-.061-.499-.099-.75-.12-.142-.016-.283-.029-.425-.04-.14-.013-.283-.02-.427-.027C18.609 1.367 18.326 1.36 18 1.356h-12c-.326.004-.61.011-.893.027-.143.007-.287.014-.427.027-.141.011-.282.024-.424.04-.252.021-.502.059-.752.12-.34.091-.683.223-1.01.415-.41.207-.756.479-1.1.824-.343.345-.613.694-.822 1.099a4.269 4.269 0 00-.408 1.013c-.062.248-.1.499-.12.75-.014.141-.03.283-.04.424C.013 6.267.007 6.41 0 6.554v.016c-.007.143-.013.283-.013.423v10.014c0 .14.006.28.013.423l.001.016c.007.143.013.287.027.427.011.141.026.283.04.424.02.251.058.502.12.75.081.339.212.682.408 1.013.209.405.479.754.822 1.099.344.346.69.617 1.1.824a4.27 4.27 0 001.01.415c.25.061.5.1.752.12.142.016.283.029.424.04.14.013.284.02.427.027.283.016.567.023.893.027h12c.326-.004.61-.011.893-.027.143-.007.287-.014.427-.027.141-.011.282-.024.424-.04.252-.02.502-.059.752-.12a4.27 4.27 0 001.01-.415c.41-.207.756-.478 1.1-.824.343-.345.613-.694.822-1.099a4.282 4.282 0 00.408-1.013c.062-.248.1-.499.12-.75.014-.141.03-.283.04-.424.013-.14.02-.284.027-.427.016-.283.023-.567.027-.893V7.977c-.004-.326-.011-.61-.027-.893-.007-.143-.014-.287-.027-.427-.011-.141-.026-.283-.04-.424a4.528 4.528 0 00-.12-.75 4.281 4.281 0 00-.409-1.012c-.208-.405-.479-.754-.822-1.099-.344-.345-.69-.617-1.099-.824a4.25 4.25 0 00-1.01-.415c-.248-.061-.499-.099-.75-.12-.142-.016-.283-.029-.425-.04-.14-.013-.283-.02-.427-.027-.283-.016-.567-.023-.893-.027h-12c-.326.004-.61.011-.893.027-.143.007-.287.014-.427.027zm.15 2.249a3.48 3.48 0 01.676.246c.261.124.513.283.739.504.225.221.384.474.504.739.126.271.194.536.246.675.057.161.086.251.104.339.016.09.027.215.036.358.012.145.02.311.028.488.007.169.013.345.016.539.007.28.013.601.013.937v10.017c0 .336-.006.656-.013.936-.003.195-.009.371-.016.54-.008.177-.016.342-.028.488-.009.144-.02.269-.036.358-.018.089-.047.178-.104.339-.052.14-.12.404-.246.676-.12.264-.279.517-.504.738-.226.22-.478.38-.739.504a3.392 3.392 0 01-.676.246c-.115.034-.245.063-.39.083-.126.019-.241.03-.328.037-.096.01-.198.016-.312.022-.12.006-.245.01-.38.013-.268.008-.54.012-.821.012H2.427c-.281 0-.553-.004-.821-.012-.134-.003-.26-.007-.38-.013a6.84 6.84 0 01-.312-.022c-.087-.007-.203-.018-.329-.037-.144-.02-.274-.049-.389-.083a3.43 3.43 0 01-.676-.246c-.261-.124-.513-.283-.739-.504a2.048 2.048 0 01-.504-.738c-.126-.272-.194-.536-.246-.676-.057-.16-.086-.25-.104-.339a2.637 2.637 0 01-.036-.358 11.151 11.151 0 01-.028-.488c-.007-.169-.013-.345-.016-.539-.007-.281-.013-.601-.013-.936V7.977c0-.336.006-.656.013-.936.003-.195.009-.371.016-.54.008-.177.016-.343.028-.488.009-.144.02-.269.036-.358.018-.089.047-.178.104-.339.052-.14.12-.404.246-.676.12-.264.279-.517.504-.738.226-.22.478-.38.739-.504.233-.111.452-.186.676-.246.115-.034.245-.063.39-.083.126-.019.241-.03.328-.037.096-.01.198-.016.312-.022.12-.006.245-.01.38-.013.268-.008.54-.012.821-.012h19.146c.281 0 .553.004.821.012.134.003.26.007.38.013.114.006.216.012.312.022.087.007.203.018.329.037.144.02.274.049.389.083zM8.285 7.171c0-1.004.815-1.821 1.821-1.821s1.821.817 1.821 1.821-.815 1.821-1.821 1.821-1.821-.817-1.821-1.821zm-3.141 9.324c-.005-1.825 1.235-3.091 3.047-3.091 1.084 0 1.998.506 2.569 1.401h.034c.571-.895 1.485-1.401 2.569-1.401 1.812 0 3.052 1.266 3.047 3.091-.005 1.858-1.594 3.368-3.381 4.467l-1.868 1.096c-.243.141-.5.141-.743 0l-1.868-1.096c-1.787-1.099-3.377-2.608-3.406-4.467z"/>
                    </svg>
                    Listen on Apple Music
                  </a>
                <?php endif; ?>
              </div>
            </div>
          <?php else: ?>
            <div class="popup-result-container">
              <h3><i class="fas fa-music"></i> No Results Yet</h3>
              <p>Upload or record a song to see results here.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Original Footer -->
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <p>Copyright &copy; 2022 <a href="#">Liberty</a> NFT Marketplace Co., Ltd. All rights reserved.
          &nbsp;&nbsp;
          Designed by <a title="HTML CSS Templates" rel="sponsored" href="https://templatemo.com" target="_blank">TemplateMo</a></p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>

  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/popup.js"></script>
  <script src="assets/js/custom.js"></script>
  

  
  <!-- Music Recognition Popup Script -->
  <script>
    // Music Recognition Popup Toggle
    const musicRecognitionBtn = document.getElementById('musicRecognitionBtn');
    const musicPopup = document.getElementById('musicPopup');
    const closePopup = document.getElementById('closePopup');
    
    musicRecognitionBtn.addEventListener('click', () => {
      musicPopup.classList.add('active');
    });
    
    closePopup.addEventListener('click', () => {
      musicPopup.classList.remove('active');
    });
    
    // Popup Tabs
    const popupTabs = document.querySelectorAll('.popup-tab');
    const popupTabContents = document.querySelectorAll('.popup-tab-content');
    
    popupTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        // Remove active class from all tabs
        popupTabs.forEach(t => t.classList.remove('active'));
        popupTabContents.forEach(c => c.classList.remove('active'));
        
        // Add active class to clicked tab and corresponding content
        tab.classList.add('active');
        const tabId = tab.getAttribute('data-tab') + 'Tab';
        document.getElementById(tabId).classList.add('active');
      });
    });
    
    // File Input Display
    const popupFileInput = document.getElementById('popupAudioFile');
    const popupFileNameDisplay = document.getElementById('popupFileName');
    
    popupFileInput.addEventListener('change', () => {
      if (popupFileInput.files.length > 0) {
        popupFileNameDisplay.textContent = `Selected: ${popupFileInput.files[0].name}`;
      } else {
        popupFileNameDisplay.textContent = '';
      }
    });
    
    // Audio Recording
    const popupRecordBtn = document.getElementById('popupRecordBtn');
    const popupStopBtn = document.getElementById('popupStopBtn');
    const popupSubmitBtn = document.getElementById('popupSubmitBtn');
    const popupAudioDataInput = document.getElementById('popupAudioData');
    const popupRecordingStatus = document.getElementById('popupRecordingStatus');
    
    let popupMediaRecorder;
    let popupAudioChunks = [];
    let popupWavesurfer;

    popupRecordBtn.addEventListener('click', async () => {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        popupMediaRecorder = new MediaRecorder(stream);
        popupMediaRecorder.start();
        popupAudioChunks = [];

        popupMediaRecorder.ondataavailable = e => popupAudioChunks.push(e.data);

        popupMediaRecorder.onstop = async () => {
          const blob = new Blob(popupAudioChunks, { type: 'audio/webm' });
          const reader = new FileReader();
          reader.onloadend = () => {
            popupAudioDataInput.value = reader.result;
            popupSubmitBtn.disabled = false;
          };
          reader.readAsDataURL(blob);
          stream.getTracks().forEach(track => track.stop());
          popupRecordingStatus.style.display = 'none';
        };

        popupRecordBtn.disabled = true;
        popupStopBtn.disabled = false;
        popupRecordingStatus.style.display = 'flex';
      } catch (err) {
        alert('Could not start recording: ' + err.message);
      }
    });

    popupStopBtn.addEventListener('click', () => {
      if (popupMediaRecorder && popupMediaRecorder.state !== 'inactive') {
        popupMediaRecorder.stop();
        popupRecordBtn.disabled = false;
        popupStopBtn.disabled = true;
      }
    });
    
    // Simple audio player - no additional JavaScript needed
    // HTML5 audio element handles playback automatically
    
    // Particles.js Configuration and Initialization
    document.addEventListener('DOMContentLoaded', function() {
      particlesJS('particles-js', {
        "particles": {
          "number": {
            "value": 160,
            "density": {
              "enable": true,
              "value_area": 800
            }
          },
          "color": {
            "value": "#ffffff"
          },
          "shape": {
            "type": "star",
            "stroke": {
              "width": 0,
              "color": "#000000"
            },
            "polygon": {
              "nb_sides": 5
            }
          },
          "opacity": {
            "value": 0.7,
            "random": true,
            "anim": {
              "enable": true,
              "speed": 1,
              "opacity_min": 0.1,
              "sync": false
            }
          },
          "size": {
            "value": 4,
            "random": true,
            "anim": {
              "enable": true,
              "speed": 2,
              "size_min": 0.5,
              "sync": false
            }
          },
          "line_linked": {
            "enable": false
          },
          "move": {
            "enable": true,
            "speed": 1.5,
            "direction": "bottom",
            "random": true,
            "straight": false,
            "out_mode": "out",
            "bounce": false,
            "attract": {
              "enable": false,
              "rotateX": 600,
              "rotateY": 1200
            }
          }
        },
        "interactivity": {
          "detect_on": "canvas",
          "events": {
            "onhover": {
              "enable": false,
              "mode": "repulse"
            },
            "onclick": {
              "enable": false,
              "mode": "push"
            },
            "resize": true
          },
          "modes": {
            "repulse": {
              "distance": 100,
              "duration": 0.4
            },
            "push": {
              "particles_nb": 4
            }
          }
        },
        "retina_detect": true
      });
    });
    
    // Auto-show results if available
    <?php if ($song_data): ?>
      document.addEventListener('DOMContentLoaded', function() {
        // Show the popup
        musicPopup.classList.add('active');
        // Switch to results tab
        document.querySelector('.popup-tab[data-tab="results"]').click();
      });
    <?php endif; ?>
  </script>
</body>
</html>