<?php
session_start();
// Si déjà connecté, redirige vers l'accueil
if (isset($_SESSION['user'])) {
    header('Location: /livethemusic/view/front_office/index.php');
    exit();
}
if (isset($_SESSION['login_error'])) {
    echo '<div class="error-message" style="color:red;text-align:center;margin:20px;">'.htmlspecialchars($_SESSION['login_error']).'</div>';
    unset($_SESSION['login_error']);
}
// Configuration Face++
define('FACE_API_KEY', 'PUMCJCLjjKUak0j_-O7noKqzY3TB4-AB');
define('FACE_API_SECRET', 'DQxi3F1lj4eBlDtkVwFCTBM2o_yaF5TQ');

// Connexion à la base de données
$db = new PDO('mysql:host=localhost;dbname=test;charset=utf8mb4', 'root', ''); // Adaptez les identifiants
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Traitement de la reconnaissance faciale
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['face_data'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $faceData = $_POST['face_data'];
    
    try {
        // Récupérer l'utilisateur
        $stmt = $db->prepare("SELECT id, image FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && !empty($user['image'])) {
            $storedImagePath = __DIR__ . '/../../uploads/users/' . basename($user['image']); // Chemin relatif
            // Vérification de l'existence du fichier
if (!file_exists($storedImagePath)) {
    error_log("ERREUR: Fichier image introuvable à l'emplacement: $storedImagePath");
    echo "<!-- ERREUR: Fichier image introuvable -->";
}
            
            // Sauvegarder l'image capturée temporairement
            $tempPath = __DIR__ . '/temp_face_capture.jpg';
            file_put_contents($tempPath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $faceData)));
            
            // Comparer les visages avec Face++
            $result = compareFaces($storedImagePath, $tempPath);
            
            // Nettoyer et répondre
            unlink($tempPath);
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        } else {
            echo json_encode(['error' => 'User not found or no profile image']);
            exit;
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database error']);
        exit;
    }
}

function compareFaces($image1Path, $image2Path) {
    if (!file_exists($image1Path)) {
        return ['error' => 'Stored image not found'];
    }
    
    $url = 'https://api-us.faceplusplus.com/facepp/v3/compare';
    $postFields = [
        'api_key' => FACE_API_KEY,
        'api_secret' => FACE_API_SECRET,
        'image_file1' => new CURLFile($image1Path),
        'image_file2' => new CURLFile($image2Path)
    ];
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => $error];
    }
    
    return json_decode($response, true) ?: ['error' => 'Invalid API response'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Platform - Welcome</title>
    <link rel="stylesheet" href="welcome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
 <style>
        .faceid-btn {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 13px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
            width: 100%;
        }
        
        .faceid-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .btn-capture {
            background: #FF4B2B;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-cancel {
            background: #ccc;
            color: black;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }
        #faceIdModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
         #faceProcessing {
            display: none;
            margin-top: 10px;
            color: #FF4B2B;
        }
    </style>
    

<body>
    <h2>Hello, Friend!</h2>
    <div class="container" id="container">
        <!-- Sign Up Container (inchangé) -->
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
        
        <!-- Sign In Container (avec ajout du Face ID) -->
        <div class="form-container sign-in-container">
            <form action="/livethemusic/view/front_office/login.php" method="post" id="loginForm">
                <h1>Sign in</h1>
                <div class="social-container">
                    <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                    <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <span>or use your account</span>
                <input type="email" name="email" placeholder="Email" id="loginEmail" required />
                <input type="password" name="password" placeholder="Password" id="loginPassword" required />
                <a href="forgot_password.php" class="forgot-password">Forgot your password?</a>
                <button type="submit">Sign In</button>
                
                
   <!-- Modifiez la section Face ID comme ceci : -->
    <div class="faceid-option" style="margin-top: 15px; text-align: center;">
        <p style="margin-bottom: 10px;">Or sign in with Face ID</p>
                 <button type="button" id="faceIdBtn"class="faceid-btn">
                <i class="fas fa-fingerprint"></i> USE FACE ID
            </button>
        <div id="faceProcessing">Processing face recognition...</div>
    </div>

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

    <!-- Modal pour la caméra -->
    <div id="faceIdModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 10px; text-align: center;">
            <h2>Face Recognition</h2>
            <video id="faceIdVideo" width="400" height="300" autoplay style="background: black;"></video>
            <div style="margin-top: 15px;">
                <button id="captureFaceBtn" style="background: #FF4B2B; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                    Capture
                </button>
                <button id="cancelFaceId" style="background: #ccc; color: black; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <footer></footer>
    <script src="welcome.js"></script>
    
    <!-- Script pour le Face ID -->
<script>
        document.getElementById('faceIdBtn').addEventListener('click', function() {
            const modal = document.getElementById('faceIdModal');
            const video = document.getElementById('faceIdVideo');
            const processing = document.getElementById('faceProcessing');
            
            modal.style.display = 'flex';
            processing.style.display = 'none';
            
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => {
                    alert("Camera error: " + err.message);
                    modal.style.display = 'none';
                });
        });

        document.getElementById('captureFaceBtn').addEventListener('click', async function() {
            const video = document.getElementById('faceIdVideo');
            const canvas = document.createElement('canvas');
            const processing = document.getElementById('faceProcessing');
            const email = document.getElementById('loginEmail').value;
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Afficher le traitement en cours
            processing.style.display = 'block';
            
            // Convertir en base64
            const imageData = canvas.toDataURL('image/jpeg');
            
            try {
                // Envoyer au serveur pour comparaison
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'email': email,
                        'face_data': imageData
                    })
                });
                
                const result = await response.json();
                
                if (result.error_message) {
                    throw new Error(result.error_message);
                }
                
                // Seuil de confiance (ajustable)
                if (result.confidence >= 70) {
                    alert("✅ Face recognized! Logging you in...");
                    document.getElementById('loginForm').submit();
                } else {
                    alert("❌ Face not recognized. Please try again."+result.confidence);
                }
            } catch (error) {
                console.error("Error:", error);
                alert("Face recognition failed: " + error.message);
            } finally {
                processing.style.display = 'none';
                document.getElementById('faceIdModal').style.display = 'none';
                if (video.srcObject) {
                    video.srcObject.getTracks().forEach(track => track.stop());
                }
            }
        });
    </script>
</body>
</html>