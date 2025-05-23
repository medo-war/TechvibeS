<?php
session_start();
require_once('../../Controller/userController.php');

if (!isset($_SESSION['user'])) {
    echo "Aucune session utilisateur active.";
    exit();
}

$email = $_SESSION['user']['email'];
$userController = new UserController();
$user = $userController->getUserByEmail($email);
if (isset($_POST['delete_account'])) {
    if ($userController->deleteUser($user->getId())) {
        session_destroy();
        header('Location: welcome.php'); // Crée cette page ou redirige vers l'accueil
        exit();
    } else {
        $error = "Échec de la suppression du compte.";
    }
}


if ($user) {
    echo '
    <div class="musicpunk-container">
    <a href="index.php" class="musicpunk-back-btn">
        <span class="neon-arrow">↻</span> BACK TO SITE
    </a>
        <div class="musicpunk-sidebar">
            <div class="musicpunk-card">
                <div class="musicpunk-avatar">
                    <img src="/livethemusic/'.htmlspecialchars($user->getImage()).'" alt="Profile Image">
                    <div class="audio-wave"></div>
                    <div class="vinyl-overlay"></div>
                </div>
                <h3 class="musicpunk-name">' . htmlspecialchars($user->getFirst_name()). ' <span class="neon-pink">' . htmlspecialchars($user->getLast_name()). '</span></h3>
                <div class="musicpunk-role">
                    <span class="equalizer-bars">
                        <span></span><span></span><span></span><span></span>
                    </span>
                    <span class="role-tag neon-purple">'.htmlspecialchars(ucfirst($user->getRole())).'</span>
                    <span class="equalizer-bars">
                        <span></span><span></span><span></span><span></span>
                    </span>
                </div>
                <p class="musicpunk-location"><span class="music-note">♪</span> New York, USA</p>
            </div>
            
          <nav class="musicpunk-menu">
            <ul>
                <li class="active"><span class="neon-cyan">▶</span> User info</li>
                <li><span class="neon-cyan">▶</span> Edit Profile</li>
                <li><a href="logout.php" class="musicpunk-logout"><span class="neon-cyan">▶</span> Logout</a></li>
                <li>
                   <form method="POST" onsubmit="return confirm("Are you sure you want to permanently delete your account? This cannot be undone!");">
    <button type="submit" onclick="confirmDelete()" name="delete_account" class="musicpunk-delete-btn">
        <span class="neon-red">⚠</span> Delete Account
    </button>
</form>
                </li>
            </ul>
        </nav>
    </div>
        
        <div class="musicpunk-content">
            <div class="musicpunk-section">
                <h2 class="section-title"><span class="neon-cyan">♫</span> Personal Information</h2>
                <div class="musicpunk-row">
                    <label><span class="music-icon">♩</span> Name</label>
                    <p class="musicpunk-value">' . htmlspecialchars($user->getFirst_name()). '</p>
                </div>
                <div class="musicpunk-row">
                    <label><span class="music-icon">♩</span> Full Name</label>
                    <p class="musicpunk-value">' . htmlspecialchars($user->getLast_name()). '</p>
                </div>
            </div>
            
            <div class="musicpunk-section">
                <h2 class="section-title"><span class="neon-cyan">✉</span> Contact Information</h2>
                <div class="musicpunk-row">
                    <label><span class="music-icon">♫</span> Email</label>
                    <p class="musicpunk-value">' . htmlspecialchars($user->getEmail()). '</p>
                </div>
                <div class="musicpunk-row">
                    <label><span class="music-icon">♫</span> Phone</label>
                    <p class="musicpunk-value">' . htmlspecialchars($user->getPhone()). '</p>
                </div>
            </div>

            
            <div class="musicpunk-section">
                <h2 class="section-title"><span class="neon-cyan">⟠</span> Location</h2>
                <div class="musicpunk-row">
                    <label><span class="music-icon">♬</span> City</label>
                    <p class="musicpunk-value">New York, USA</p>
                </div>
                <div class="musicpunk-row">
                    <label><span class="music-icon">♬</span> ZIP Code</label>
                    <p class="musicpunk-value">23728167</p>
                </div>
            </div>
         <!-- Bouton CD Vinyle -->
    <div class="vinyl-btn-container">
        <div class="vinyl-btn" id="vinylBtn" title="Créer un album IA">
        <a href="test.php">
        
            <div class="vinyl-label">
                <span class="vinyl-text">IA</span>
                <div class="vinyl-hole"></div>
            </div>
            <div class="vinyl-ring"></div>
            <div class="vinyl-glow"></div>
        </div>
        <div class="vinyl-tooltip">Générer un Album IA</div>
    </div>
</div>
        </div>
    </div>';
} else {
    echo '<div class="musicpunk-error">USER NOT FOUND</div>';
}

?>

<style>
:root {
    --neon-pink: #ff2a6d;
    --neon-purple: #d300f5;
    --neon-cyan: #05d9e8;
    --neon-blue: #0055ff;
    --dark-bg: #0d0221;
    --darker-bg: #050218;
    --light-text: #f0f0f0;
    --font-main: 'Orbitron', 'Rajdhani', sans-serif;
}

@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Rajdhani:wght@400;700&display=swap');

body {
    background: radial-gradient(circle at center, #1a0535 0%, #0d0221 100%);
    color: var(--light-text);
    font-family: var(--font-main);
    margin: 0;
    padding: 0;
    min-height: 100vh;
    overflow-x: hidden;
}

.musicpunk-container {
    display: flex;
    max-width: 1400px;
    margin: 0 auto;
    background: rgba(13, 2, 33, 0.8);
    border: 1px solid var(--neon-purple);
    box-shadow: 0 0 30px rgba(211, 0, 245, 0.2),
                inset 0 0 20px rgba(211, 0, 245, 0.1);
    backdrop-filter: blur(5px);
    position: relative;
    overflow: hidden;
}

.musicpunk-container::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--neon-cyan), transparent);
    box-shadow: 0 0 10px var(--neon-cyan);
}

.musicpunk-sidebar {
    width: 300px;
    background: linear-gradient(160deg, #1a0535 0%, #0d0221 100%);
    padding: 30px;
    border-right: 1px solid rgba(5, 217, 232, 0.3);
    position: relative;
    z-index: 1;
}

.musicpunk-card {
    text-align: center;
    padding: 60px 0;
    margin-bottom: 40px;
    position: relative;
}

.musicpunk-avatar {
    width: 150px;
    height: 150px;
    margin: 0 auto 25px;
    border-radius: 50%;
    border: 3px solid var(--neon-pink);
    box-shadow: 0 0 15px var(--neon-pink),
                inset 0 0 15px var(--neon-pink);
    position: relative;
    overflow: hidden;
}

.musicpunk-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: relative;
    z-index: 2;
}

.audio-wave {
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 100%;
    height: 20px;
    background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0,5 Q25,10 50,5 T100,5" fill="none" stroke="%23ff2a6d" stroke-width="1"/></svg>') repeat-x;
    background-size: 100px 20px;
    animation: wave-animation 1s linear infinite;
    z-index: 3;
}

.vinyl-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, transparent 60%, rgba(0,0,0,0.7) 100%);
    border-radius: 50%;
    z-index: 1;
}

.musicpunk-name {
    font-size: 1.8em;
    margin: 15px 0 10px;
    letter-spacing: 2px;
    text-shadow: 0 0 10px var(--neon-pink);
}

.neon-pink {
    color: var(--neon-pink);
    text-shadow: 0 0 10px var(--neon-pink);
}

.musicpunk-role {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin: 20px 0;
}

.equalizer-bars {
    display: flex;
    align-items: flex-end;
    height: 20px;
    gap: 3px;
}

.equalizer-bars span {
    display: inline-block;
    width: 4px;
    background: var(--neon-cyan);
    animation: equalize 1.4s infinite ease-in-out;
}

.equalizer-bars span:nth-child(1) { height: 30%; animation-delay: 0.1s; }
.equalizer-bars span:nth-child(2) { height: 60%; animation-delay: 0.3s; }
.equalizer-bars span:nth-child(3) { height: 40%; animation-delay: 0.5s; }
.equalizer-bars span:nth-child(4) { height: 80%; animation-delay: 0.2s; }

.role-tag {
    padding: 5px 15px;
    border-radius: 20px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-size: 0.9em;
    background: rgba(211, 0, 245, 0.1);
    border: 1px solid var(--neon-purple);
    box-shadow: 0 0 10px rgba(211, 0, 245, 0.3);
}

.musicpunk-location {
    font-size: 1em;
    margin: 15px 0 0;
    letter-spacing: 1px;
}

.music-note {
    color: var(--neon-cyan);
    margin-right: 8px;
    animation: bounce 2s infinite;
}

.musicpunk-menu ul {
    list-style: none;
    padding: 0;
    margin: 50px 0 0;
}

.musicpunk-menu li {
    padding: 15px 20px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.3s;
    border-left: 3px solid transparent;
    font-size: 1em;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
}

.musicpunk-menu li.active {
    border-left: 3px solid var(--neon-cyan);
    background: rgba(5, 217, 232, 0.1);
    text-shadow: 0 0 10px var(--neon-cyan);
}

.musicpunk-menu li:hover {
    background: rgba(5, 217, 232, 0.05);
}

.musicpunk-content {
    flex: 1;
    padding: 60px 50px;
    position: relative;
}

.section-title {
    color: var(--neon-cyan);
    text-shadow: 0 0 10px var(--neon-cyan);
    margin: 0 0 30px;
    font-size: 1.6em;
    letter-spacing: 3px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.musicpunk-row {
    display: flex;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px dashed rgba(5, 217, 232, 0.3);
    align-items: center;
}

.musicpunk-row label {
    width: 200px;
    font-weight: 500;
    color: var(--neon-pink);
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.music-icon {
    color: var(--neon-purple);
    font-size: 1.2em;
    animation: pulse 1.5s infinite alternate;
}

.musicpunk-value {
    flex: 1;
    margin: 0;
    letter-spacing: 1px;
    font-size: 1.1em;
}

/* Animations */
@keyframes wave-animation {
    0% { background-position-x: 0; }
    100% { background-position-x: 100px; }
}

@keyframes equalize {
    0%, 100% { transform: scaleY(0.3); }
    50% { transform: scaleY(1); }
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

@keyframes pulse {
    0% { opacity: 0.6; transform: scale(0.95); }
    100% { opacity: 1; transform: scale(1.1); }
}

@keyframes vinyl-rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.musicpunk-error {
    color: var(--neon-pink);
    text-shadow: 0 0 10px var(--neon-pink);
    text-align: center;
    padding: 100px;
    font-size: 1.5em;
    letter-spacing: 3px;
    text-transform: uppercase;
}

/* Effets globaux */
body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 30%, rgba(255, 42, 109, 0.1) 0%, transparent 20%),
        radial-gradient(circle at 80% 70%, rgba(5, 217, 232, 0.1) 0%, transparent 20%);
    pointer-events: none;
    z-index: -1;
}
.musicpunk-back-btn {
    position: absolute;
    top: 20px;
    left: 20px;
    padding: 10px 20px;
    background: rgba(5, 217, 232, 0.1);
    border: 1px solid var(--neon-cyan);
    color: var(--neon-cyan);
    text-decoration: none;
    font-size: 0.9em;
    letter-spacing: 1px;
    z-index: 100;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.musicpunk-back-btn:hover {
    background: rgba(5, 217, 232, 0.2);
    text-shadow: 0 0 10px var(--neon-cyan);
}

.neon-arrow {
    animation: spin-pulse 2s infinite;
}

@keyframes spin-pulse {
    0% { transform: rotate(0deg); opacity: 0.7; }
    50% { transform: rotate(180deg); opacity: 1; }
    100% { transform: rotate(360deg); opacity: 0.7; }
}

.musicpunk-logout {
    color: inherit;
    text-decoration: none;
    display: block;
    width: 100%;
}

.musicpunk-delete-btn {
    background: transparent;
    border: none;
    color: #ff073a;
    font-family: inherit;
    font-size: inherit;
    cursor: pointer;
    padding: 15px 20px;
    width: 100%;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
}

.musicpunk-delete-btn:hover {
    background: rgba(255, 7, 58, 0.1);
    text-shadow: 0 0 10px #ff073a;
}

.musicpunk-menu li form {
    width: 100%;
}

/* Animation pour le bouton delete */
@keyframes warning-pulse {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}

.neon-red {
    color: #ff073a;
    text-shadow: 0 0 5px #ff073a;
    animation: warning-pulse 1.5s infinite;
}
/* Styles pour le bouton vinyle */
.vinyl-btn-container {
    position: fixed;
    bottom: 40px;
    right: 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 100;
}

.vinyl-btn {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2c3e50 0%, #1a1a2e 100%);
    border: 2px solid #8e44ad;
    box-shadow: 
        0 0 15px rgba(142, 68, 173, 0.6),
        inset 0 0 20px rgba(0,0,0,0.8);
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
    animation: vinyl-float 3s ease-in-out infinite;
}

.vinyl-btn:hover {
    transform: scale(1.1) rotate(10deg);
    box-shadow: 
        0 0 25px rgba(142, 68, 173, 0.8),
        inset 0 0 20px rgba(0,0,0,0.8);
}

.vinyl-label {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #8e44ad, #9b59b6);
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: inset 0 0 10px rgba(0,0,0,0.5);
}

.vinyl-text {
    color: white;
    font-weight: bold;
    font-size: 14px;
    text-shadow: 0 0 5px rgba(0,0,0,0.5);
}

.vinyl-hole {
    position: absolute;
    width: 10px;
    height: 10px;
    background: #1a1a2e;
    border-radius: 50%;
    border: 1px solid #333;
}

.vinyl-ring {
    position: absolute;
    top: 5px;
    left: 5px;
    right: 5px;
    bottom: 5px;
    border: 1px dashed rgba(255,255,255,0.3);
    border-radius: 50%;
    animation: vinyl-spin 10s linear infinite;
}

.vinyl-glow {
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(142, 68, 173, 0.4) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s;
}

.vinyl-btn:hover .vinyl-glow {
    opacity: 1;
}

.vinyl-tooltip {
    position: absolute;
    bottom: -30px;
    background: rgba(30, 30, 60, 0.9);
    color: #9b59b6;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s;
    pointer-events: none;
    border: 1px solid #8e44ad;
    box-shadow: 0 0 10px rgba(142, 68, 173, 0.5);
}

.vinyl-btn:hover + .vinyl-tooltip {
    opacity: 1;
    transform: translateY(0);
}

/* Animations */
@keyframes vinyl-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes vinyl-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Style pour le popup d'album IA */
.album-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    z-index: 1000;
    display: none;
    justify-content: center;
    align-items: center;
}

.album-popup-content {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    padding: 30px;
    border-radius: 15px;
    width: 80%;
    max-width: 600px;
    border: 1px solid #8e44ad;
    box-shadow: 0 0 30px rgba(142, 68, 173, 0.6);
    position: relative;
}

.close-popup {
    position: absolute;
    top: 15px;
    right: 25px;
    font-size: 28px;
    color: #fff;
    cursor: pointer;
    transition: all 0.3s;
}

.close-popup:hover {
    color: #8e44ad;
    transform: scale(1.2);
}

</style>
<script>
// Confirmation pour la suppression
document.querySelector('.musicpunk-delete-btn').addEventListener('click', function(e) {
    if (!confirm('Are you absolutely sure? This will permanently delete your account and all data!')) {
        e.preventDefault();
    }
});
// Script pour le bouton vinyle
document.getElementById('vinylBtn').addEventListener('click', function() {
    // Créer et afficher le popup
    const popup = document.createElement('div');
    popup.className = 'album-popup';
    popup.innerHTML = `
        <div class="album-popup-content">
            <span class="close-popup">&times;</span>
            <h2 style="color: #9b59b6; text-align: center; margin-bottom: 30px;">
                <i class="fas fa-robot"></i> Générateur d'Album IA
            </h2>
            <div style="text-align: center;">
                <p style="color: #a1a1ff; margin-bottom: 30px;">
                    Créez une pochette d'album unique avec notre IA!
                </p>
                <a href="/album-generator.php" style="
                    background: #8e44ad;
                    color: white;
                    padding: 12px 25px;
                    border-radius: 5px;
                    text-decoration: none;
                    display: inline-block;
                    transition: all 0.3s;
                ">Commencer</a>
            </div>
        </div>
    `;
    
    document.body.appendChild(popup);
    popup.style.display = 'flex';
    
    // Gestion de la fermeture
    popup.querySelector('.close-popup').addEventListener('click', function() {
        popup.style.animation = 'fadeOut 0.3s forwards';
        setTimeout(() => {
            popup.remove();
        }, 300);
    });
});

// Animation d'entrée
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
function confirmDelete() {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone!')) {
        // Si l'utilisateur confirme, rediriger vers le script de suppression
        window.location.href = 'delete_account.php';
    }
}
</script>
