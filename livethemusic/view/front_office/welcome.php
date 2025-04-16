
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Platform - Welcome</title>
    <link rel="stylesheet" href="welcome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <h2>Hello, Friend!</h2>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
        <form action="gestion_user.php" method="post" enctype="multipart/form-data">
                <h1>Create Account</h1>
                <div class="social-container">
                    <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                    <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <span>or use your email for registration</span>
                
                <!-- Champ pour uploader une image de profil -->
                
                <div class="profile-upload-container">
    <div class="profile-image-preview" id="profileImagePreview">
        <div class="upload-instructions">
            <span>Drop files to upload or</span>
        </div>
        <input type="file" id="profile-picture" name="profile_picture" accept="image/*" class="file-input">
    </div>
    
</div>
                
                <div class="name-fields">
                    <input type="text" name="first_name" placeholder="First Name" required class="half-width" />
                    <input type="text" name="last_name" placeholder="Last Name" required class="half-width" />
                </div>
                
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="pwd" placeholder="Password" required />
                <input type="tel" name="phone" placeholder="Phone (10 digits)" pattern="[0-9]{10}" title="10 digit phone number" required />
                
                <select name="role" required class="role-select">
                    <option value="" disabled selected>Select Role</option>
                    <option value="user">Music Fan</option>
                    <option value="artist">Artist</option>
                    <option value="admin">Admin</option>
                </select>
                
                <button type="submit" class="signup-btn">Sign Up</button>
            </form>
        </div>
        
        <!-- Sign In Container (inchangé) -->
        <div class="form-container sign-in-container">
            <form action="#">
                <h1>Sign in</h1>
                <div class="social-container">
                    <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                    <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <span>or use your account</span>
                <input type="email" placeholder="Email" required />
                <input type="password" placeholder="Password" required />
                <a href="#" class="forgot-password">Forgot your password?</a>
                <button type="submit">Sign In</button>
            </form>
        </div>

        <!-- Overlay (inchangé) -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
    <footer></footer>
    <script src="welcome.js"></script>
</body>
</html>