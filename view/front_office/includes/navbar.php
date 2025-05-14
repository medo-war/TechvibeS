<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user']);
$user = $isLoggedIn ? $_SESSION['user'] : null;

// Get current page for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- ***** Header Area Start ***** -->
<header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- ***** Logo Start ***** -->
                    <a href="index.php" class="logo">
                        <img src="assets/images/logo.png" alt="Live The Music">
                    </a>
                    <!-- ***** Logo End ***** -->
                    
                    <!-- ***** Menu Start ***** -->
                    <ul class="nav">
                        <li><a href="index.php" <?php echo ($currentPage == 'index.php') ? 'class="active"' : ''; ?>>Home</a></li>
                        <li><a href="details.php?mode=artists" <?php echo ($currentPage == 'details.php' && (!isset($_GET['mode']) || $_GET['mode'] == 'artists')) ? 'class="active"' : ''; ?>>Artists</a></li>
                        <li><a href="events.php" <?php echo ($currentPage == 'events.php') ? 'class="active"' : ''; ?>>Events</a></li>
                        <li><a href="groups.php" <?php echo ($currentPage == 'groups.php') ? 'class="active"' : ''; ?>>Groups</a></li>
                        <li><a href="partners_directory.php" <?php echo ($currentPage == 'partners_directory.php') ? 'class="active"' : ''; ?>>Partners</a></li>
                        <li><a href="song_creation.php" <?php echo ($currentPage == 'song_creation.php') ? 'class="active"' : ''; ?>>Song Creation</a></li>
                    </ul>
                    
                    <?php if ($isLoggedIn): ?>
                    <!-- User Profile Icon with Dropdown -->
                    <div class="user-profile">
                        <div class="profile-icon" id="profileIcon">
                            <img src="assets/images/author.jpg" alt="Profile">
                        </div>
                        <div class="profile-dropdown" id="profileDropdown">
                            <a href="profile.php"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></a>
                            <a href="#">Settings</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Login/Register Links if not logged in -->
                    <div class="login-register">
                        <a href="welcome.php" class="btn-login">Login / Register</a>
                    </div>
                    <?php endif; ?>
                    
                    <a class='menu-trigger'>
                        <span>Menu</span>
                    </a>
                    <!-- ***** Menu End ***** -->
                </nav>
            </div>
        </div>
    </div>
</header>
<!-- ***** Header Area End ***** -->

<!-- Profile Dropdown JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Only run if profile icon exists (user is logged in)
    const profileIcon = document.getElementById('profileIcon');
    if (profileIcon) {
        profileIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            var dropdown = document.getElementById('profileDropdown');
            if (dropdown.style.display === 'block') {
                dropdown.style.animation = 'fadeOut 0.3s forwards';
                setTimeout(() => {
                    dropdown.style.display = 'none';
                }, 300);
            } else {
                dropdown.style.display = 'block';
                dropdown.style.animation = 'fadeIn 0.3s forwards';
            }
        });

        // Close dropdown when clicking elsewhere
        window.addEventListener('click', function() {
            var dropdown = document.getElementById('profileDropdown');
            if (dropdown.style.display === 'block') {
                dropdown.style.animation = 'fadeOut 0.3s forwards';
                setTimeout(() => {
                    dropdown.style.display = 'none';
                }, 300);
            }
        });

        // Prevent dropdown from closing when clicking inside it
        const profileDropdown = document.getElementById('profileDropdown');
        if (profileDropdown) {
            profileDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }
});
</script>

<!-- Profile Dropdown CSS -->
<style>
/* Style néon rouge pour l'icône de profil */
.user-profile {
    position: relative;
    display: inline-block;
    margin-left: 30px;
    z-index: 1000;
}

.profile-icon {
    cursor: pointer;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(20, 0, 0, 0.8);
    border: 1px solid #ff073a;
    box-shadow: 0 0 10px #ff073a, 0 0 20px #ff073a, 0 0 30px #ff073a;
    transition: all 0.3s ease;
    animation: neonPulseRed 1.5s infinite alternate;
}

.profile-icon:hover {
    box-shadow: 0 0 15px #ff073a, 0 0 30px #ff073a, 0 0 45px #ff073a;
    transform: scale(1.1);
}

.profile-icon img {
    width: 80%;
    height: 80%;
    border-radius: 50%;
    object-fit: cover;
    filter: drop-shadow(0 0 8px #ff073a);
}

/* Menu déroulant néon rouge */
.profile-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 65px;
    background: rgba(10, 0, 0, 0.9);
    min-width: 200px;
    border-radius: 5px;
    border: 1px solid #ff073a;
    box-shadow: 0 0 20px #ff073a;
    z-index: 1001;
    overflow: hidden;
    backdrop-filter: blur(5px);
}

.profile-dropdown a {
    color: #fff;
    padding: 12px 25px;
    text-decoration: none;
    display: block;
    font-family: 'Roboto', sans-serif;
    font-weight: 500;
    letter-spacing: 1.5px;
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(255, 7, 58, 0.3);
    text-shadow: 0 0 8px #ff073a;
    position: relative;
    overflow: hidden;
}

.profile-dropdown a:before {
    content: '';
    position: absolute;
    left: -100%;
    top: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 7, 58, 0.2), transparent);
    transition: 0.5s;
}

.profile-dropdown a:hover {
    background: rgba(255, 7, 58, 0.15);
    color: #ff5c8a;
    padding-left: 30px;
}

.profile-dropdown a:hover:before {
    left: 100%;
}

.profile-dropdown a:last-child {
    border-bottom: none;
    color: #ff8fa3;
}

/* Animations néon rouge */
@keyframes neonPulseRed {
    0% { box-shadow: 0 0 5px #ff073a, 0 0 10px #ff073a; }
    100% { box-shadow: 0 0 10px #ff073a, 0 0 20px #ff073a, 0 0 30px #ff073a; }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeOut {
    from { opacity: 1; transform: translateY(0); }
    to { opacity: 0; transform: translateY(-10px); }
}

/* Login/Register button for non-logged in users */
.login-register {
    margin-left: 30px;
}

.btn-login {
    background: rgba(20, 0, 0, 0.8);
    color: #fff;
    padding: 10px 20px;
    border-radius: 30px;
    text-decoration: none;
    border: 1px solid #ff073a;
    box-shadow: 0 0 10px #ff073a;
    transition: all 0.3s ease;
    font-weight: 500;
    letter-spacing: 1px;
    text-shadow: 0 0 8px #ff073a;
}

.btn-login:hover {
    background: rgba(255, 7, 58, 0.15);
    box-shadow: 0 0 15px #ff073a, 0 0 30px #ff073a;
    color: #ff5c8a;
}
</style>
