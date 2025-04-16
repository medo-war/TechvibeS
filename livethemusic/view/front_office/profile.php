<?php
session_start();
require_once('../../Controller/userController.php');

if (!isset($_SESSION['user_email'])) {
    echo "Aucune session utilisateur active.";
    exit();
}

$email = $_SESSION['user_email'];
$userController = new UserController();
$user = $userController->getUserByEmail($email);

if ($user) {
    echo '
    <div class="cyberpunk-container">
        <div class="cyberpunk-sidebar">
            <div class="cyberpunk-card">
                <div class="cyberpunk-avatar">
                    <img src="../../' . htmlspecialchars($user->getImage()) . '" alt="Profile Image">
                    <div class="glow-effect"></div>
                </div>
                <h3 class="cyberpunk-name">' . htmlspecialchars($user->getFirst_name() ). ' <span class="neon-red">' . ($user->getLast_name()) . '</span></h3>
                <p class="cyberpunk-location"><span class="neon-pulse">⟠</span> New York, USA</p>
            </div>
            
            <nav class="cyberpunk-menu">
                <ul>
                    <li class="active"><span class="neon-red">▷</span> User info</li>
                    <li><span class="neon-red">▷</span> Favorites</li>
                    <li><span class="neon-red">▷</span> Watchlist</li>
                    <li><span class="neon-red">▷</span> Setting</li>
                    <li><span class="neon-red">▷</span> Notifications</li>
                </ul>
            </nav>
        </div>
        
        <div class="cyberpunk-content">
            <div class="cyberpunk-section">
                <h2 class="section-title"><span class="neon-red"></span> Personal Information</h2>
                <div class="cyberpunk-row">
                    <label>Name</label>
                    <p class="cyberpunk-value">' . htmlspecialchars($user->getFirst_name()) . '</p>
                </div>
                <div class="cyberpunk-row">
                    <label>Full Name</label>
                    <p class="cyberpunk-value">' . htmlspecialchars($user->getLast_name()) . '</p>
                </div>
            </div>
            
            <div class="cyberpunk-section">
                <h2 class="section-title"><span class="neon-red"></span> Contact Information</h2>
                <div class="cyberpunk-row">
                    <label>Email Address</label>
                    <p class="cyberpunk-value">' . htmlspecialchars($user->getEmail()) . '</p>
                </div>
                <div class="cyberpunk-row">
                    <label>Phone Number</label>
                    <p class="cyberpunk-value">' . htmlspecialchars($user->getPhone()) . '</p>
                </div>
            </div>
            
            <div class="cyberpunk-section">
                <h2 class="section-title"><span class="neon-red"></span> Location</h2>
                <div class="cyberpunk-row">
                    <label>Location</label>
                    <p class="cyberpunk-value">New York, USA</p>
                </div>
                <div class="cyberpunk-row">
                    <label>Postal Code</label>
                    <p class="cyberpunk-value">23728167</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="cyberpunk-alert">
        <h2 class="neon-alert">NEWS FLASH</h2>
        <h3 class="neon-alert">DON\'T MISS OUT THE UPCOMING CONCERTS!</h3>
        <div class="cyberpunk-buttons">
            <button class="cyberpunk-btn">Explore Concerts</button>
            <button class="cyberpunk-btn neon-btn">Buy Your Ticket</button>
        </div>
    </div>';
} else {
    echo '<div class="cyberpunk-error">Utilisateur non trouvé.</div>';
}
?>

<style>
:root {
    --neon-red: #ff073a;
    --neon-glow: 0 0 10px #ff073a, 0 0 20px #ff073a;
    --dark-bg: #0a0a0a;
    --darker-bg: #050505;
    --light-text: #f0f0f0;
    --font-main: 'Rajdhani', 'Courier New', monospace;
}

@import url('https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;700&display=swap');

body {
    background-color: var(--dark-bg);
    color: var(--light-text);
    font-family: var(--font-main);
    margin: 0;
    padding: 20px;
    overflow-x: hidden;
}

.cyberpunk-container {
    display: flex;
    max-width: 1200px;
    margin: 0 auto;
    background: var(--darker-bg);
    border: 1px solid rgba(255,7,58,0.3);
    box-shadow: 0 0 30px rgba(255,7,58,0.1);
    position: relative;
}

.cyberpunk-container::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--neon-red), transparent);
    box-shadow: var(--neon-glow);
}

.cyberpunk-sidebar {
    width: 280px;
    background: linear-gradient(160deg, #111 0%, #000 100%);
    padding: 25px;
    border-right: 1px solid rgba(255,7,58,0.2);
    position: relative;
}

.cyberpunk-card {
    text-align: center;
    padding: 20px 0;
    margin-bottom: 30px;
    position: relative;
}

.cyberpunk-avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto 20px;
    border-radius: 50%;
    border: 2px solid var(--neon-red);
    box-shadow: var(--neon-glow);
    position: relative;
    overflow: hidden;
}

.cyberpunk-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: relative;
    z-index: 2;
}

.glow-effect {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at center, rgba(255,7,58,0.4) 0%, transparent 70%);
    z-index: 1;
    animation: rotateGlow 8s linear infinite;
}

@keyframes rotateGlow {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.cyberpunk-name {
    font-size: 1.5em;
    margin: 10px 0 5px;
    letter-spacing: 1px;
}

.cyberpunk-location {
    color: #aaa;
    font-size: 0.9em;
    margin: 0;
}

.neon-pulse {
    color: var(--neon-red);
    animation: pulse 1.5s infinite alternate;
}

.cyberpunk-menu ul {
    list-style: none;
    padding: 0;
    margin: 40px 0 0;
}

.cyberpunk-menu li {
    padding: 15px;
    margin-bottom: 5px;
    cursor: pointer;
    transition: all 0.3s;
    border-left: 3px solid transparent;
    font-size: 0.95em;
    letter-spacing: 1px;
}

.cyberpunk-menu li.active {
    border-left: 3px solid var(--neon-red);
    background: rgba(255,7,58,0.1);
    text-shadow: 0 0 10px var(--neon-red);
}

.cyberpunk-menu li:hover {
    background: rgba(255,7,58,0.05);
}

.cyberpunk-content {
    flex: 1;
    padding: 30px 40px;
}

.section-title {
    color: var(--neon-red);
    text-shadow: var(--neon-glow);
    margin: 0 0 25px;
    font-size: 1.4em;
    letter-spacing: 2px;
    position: relative;
    padding-bottom: 10px;
}

.section-title::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100px;
    height: 2px;
    background: var(--neon-red);
    box-shadow: var(--neon-glow);
}

.cyberpunk-row {
    display: flex;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px dashed rgba(255,7,58,0.2);
}

.cyberpunk-row label {
    width: 180px;
    font-weight: 500;
    color: var(--neon-red);
    letter-spacing: 1px;
}

.cyberpunk-value {
    flex: 1;
    margin: 0;
    letter-spacing: 0.5px;
}

.cyberpunk-alert {
    max-width: 1200px;
    margin: 30px auto;
    padding: 30px;
    background: rgba(10,10,10,0.7);
    border: 1px solid var(--neon-red);
    box-shadow: var(--neon-glow), inset 0 0 20px rgba(255,7,58,0.1);
    text-align: center;
    position: relative;
    overflow: hidden;
}

.cyberpunk-alert::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent 48%, rgba(255,7,58,0.1) 50%, transparent 52%);
    background-size: 5px 5px;
    pointer-events: none;
}

.neon-alert {
    color: var(--neon-red);
    text-shadow: var(--neon-glow);
    margin: 0 0 20px;
    letter-spacing: 3px;
    font-weight: 700;
}

.cyberpunk-alert h3 {
    font-size: 1.3em;
    margin: 0 0 30px;
    letter-spacing: 2px;
}

.cyberpunk-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
}

.cyberpunk-btn {
    padding: 12px 30px;
    border: none;
    border-radius: 0;
    background: transparent;
    color: var(--light-text);
    font-family: var(--font-main);
    font-weight: 700;
    letter-spacing: 2px;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
    border: 1px solid var(--neon-red);
    box-shadow: 0 0 10px rgba(255,7,58,0.3);
    text-transform: uppercase;
    font-size: 0.9em;
}

.cyberpunk-btn:hover {
    background: rgba(255,7,58,0.2);
    text-shadow: 0 0 10px var(--neon-red);
}

.neon-btn {
    background: var(--neon-red);
    color: #000;
    
    font-weight: 700;
    animation: neon-pulse 1.5s infinite alternate;
}

@keyframes neon-pulse {
    0% { box-shadow: 0 0 10px var(--neon-red); }
    100% { box-shadow: 0 0 20px var(--neon-red), 0 0 30px var(--neon-red); }
}

.cyberpunk-error {
    color: var(--neon-red);
    text-shadow: var(--neon-glow);
    text-align: center;
    padding: 50px;
    font-size: 1.2em;
    letter-spacing: 2px;
}
</style>