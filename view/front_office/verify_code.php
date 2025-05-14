<?php
session_start();

// Vérification session
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_code'])) {
    header("Location: forgot_password.php");
    exit();
}

$db = new PDO('mysql:host=localhost;dbname=test;charset=utf8mb4', 'root', '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Étape 1 : Vérification du code
    if (isset($_POST['code'])) {
        if ($_POST['code'] == $_SESSION['reset_code']) {
            showPasswordForm();
        } else {
            showCodeForm("Code incorrect - Veuillez réessayer");
        }
    }
    // Étape 2 : Modification du mot de passe
    elseif (isset($_POST['new_password'])) {
        $new_password = $_POST['new_password'];
        
        // Mise à jour dans la base
        $stmt = $db->prepare("UPDATE user SET pwd = ? WHERE email = ?");
        $stmt->execute([$new_password, $_SESSION['reset_email']]);
        
        // Nettoyage session
        session_unset();
        session_destroy();
        
        showSuccessMessage();
        exit();
    }
} else {
    showCodeForm();
}

function showCodeForm($error = "") {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Vérification du code</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

            * {
                box-sizing: border-box;
            }

            body {
                background: url('https://i.pinimg.com/736x/95/71/c7/9571c799aeacc2e141f47294e1f53161.jpg') no-repeat center center/cover;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                font-family: 'Montserrat', sans-serif;
                height: 100vh;
                margin: -20px 0 50px;
            }

            h1 {
                font-weight: bold;
                margin: 0;
                color: #333;
            }

            p {
                font-size: 14px;
                font-weight: 100;
                line-height: 20px;
                letter-spacing: 0.5px;
                margin: 20px 0 30px;
                color: #666;
            }

            a {
                color: #333;
                font-size: 14px;
                text-decoration: none;
                margin: 15px 0;
            }

            button {
                border-radius: 20px;
                border: 1px solid #ff8c86;
                background-color: #ff8c86;
                color: #FFFFFF;
                font-size: 12px;
                font-weight: bold;
                padding: 12px 45px;
                letter-spacing: 1px;
                text-transform: uppercase;
                transition: transform 80ms ease-in;
                margin-top: 10px;
                cursor: pointer;
            }

            button:active {
                transform: scale(0.95);
            }

            button:focus {
                outline: none;
            }

            form {
                background-color: #FFFFFF;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                padding: 0 50px;
                height: 100%;
                text-align: center;
            }

            input {
                background-color: #eee;
                border: none;
                padding: 12px 15px;
                margin: 8px 0;
                width: 100%;
                border-radius: 5px;
            }

            .container {
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 14px 28px rgba(0,0,0,0.25), 
                            0 10px 10px rgba(0,0,0,0.22);
                position: relative;
                overflow: hidden;
                width: 450px;
                max-width: 100%;
                min-height: 400px;
            }

            .form-container {
                position: absolute;
                top: 0;
                height: 100%;
                width: 100%;
                padding: 40px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .error {
                color: #ff4757;
                background-color: #ffebee;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 15px;
                font-size: 14px;
            }

            .back-link {
                display: inline-block;
                margin-top: 20px;
                color: #ff8c86;
                font-weight: bold;
            }

            .back-link:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="form-container">
                <h1><i class="fas fa-key"></i> Vérification du code</h1>
                <p>Entrez le code à 6 chiffres que vous avez reçu par email</p>
                
                <?php if ($error) echo "<div class='error'><i class='fas fa-exclamation-circle'></i> $error</div>"; ?>
                
                <form method="post">
                    <input type="text" id="code" name="code" required placeholder="123456" maxlength="6" pattern="\d{6}">
                    <button type="submit">
                        <i class="fas fa-check"></i> Valider le code
                    </button>
                </form>
                
                <a href="forgot_password.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
}

function showPasswordForm() {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nouveau mot de passe</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

            * {
                box-sizing: border-box;
            }

            body {
                background: url('https://i.pinimg.com/736x/95/71/c7/9571c799aeacc2e141f47294e1f53161.jpg') no-repeat center center/cover;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                font-family: 'Montserrat', sans-serif;
                height: 100vh;
                margin: -20px 0 50px;
            }

            h1 {
                font-weight: bold;
                margin: 0;
                color: #333;
            }

            p {
                font-size: 14px;
                font-weight: 100;
                line-height: 20px;
                letter-spacing: 0.5px;
                margin: 20px 0 30px;
                color: #666;
            }

            button {
                border-radius: 20px;
                border: 1px solid #ff8c86;
                background-color: #ff8c86;
                color: #FFFFFF;
                font-size: 12px;
                font-weight: bold;
                padding: 12px 45px;
                letter-spacing: 1px;
                text-transform: uppercase;
                transition: transform 80ms ease-in;
                margin-top: 10px;
                cursor: pointer;
            }

            button:active {
                transform: scale(0.95);
            }

            button:focus {
                outline: none;
            }

            form {
                background-color: #FFFFFF;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                padding: 0 50px;
                height: 100%;
                text-align: center;
            }

            input {
                background-color: #eee;
                border: none;
                padding: 12px 15px;
                margin: 8px 0;
                width: 100%;
                border-radius: 5px;
            }

            .container {
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 14px 28px rgba(0,0,0,0.25), 
                            0 10px 10px rgba(0,0,0,0.22);
                position: relative;
                overflow: hidden;
                width: 450px;
                max-width: 100%;
                min-height: 400px;
            }

            .form-container {
                position: absolute;
                top: 0;
                height: 100%;
                width: 100%;
                padding: 40px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .password-rules {
                font-size: 12px;
                color: #888;
                margin-top: 5px;
                text-align: left;
                width: 100%;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="form-container">
                <h1><i class="fas fa-lock"></i> Nouveau mot de passe</h1>
                <p>Créez un nouveau mot de passe sécurisé</p>
                
                <form method="post">
                    <input type="password" id="new_password" name="new_password" required placeholder="••••••••">
                    <p class="password-rules">Minimum 8 caractères avec des chiffres et lettres</p>
                    <button type="submit">
                        <i class="fas fa-sync-alt"></i> Réinitialiser
                    </button>
                </form>
            </div>
        </div>
    </body>
    </html>
    <?php
}

function showSuccessMessage() {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mot de passe modifié</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

            * {
                box-sizing: border-box;
            }

            body {
                background: url('https://i.pinimg.com/736x/95/71/c7/9571c799aeacc2e141f47294e1f53161.jpg') no-repeat center center/cover;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                font-family: 'Montserrat', sans-serif;
                height: 100vh;
                margin: -20px 0 50px;
            }

            h1 {
                font-weight: bold;
                margin: 0;
                color: #333;
            }

            p {
                font-size: 14px;
                font-weight: 100;
                line-height: 20px;
                letter-spacing: 0.5px;
                margin: 20px 0 30px;
                color: #666;
            }

            button {
                border-radius: 20px;
                border: 1px solid #ff8c86;
                background-color: #ff8c86;
                color: #FFFFFF;
                font-size: 12px;
                font-weight: bold;
                padding: 12px 45px;
                letter-spacing: 1px;
                text-transform: uppercase;
                transition: transform 80ms ease-in;
                margin-top: 10px;
                cursor: pointer;
            }

            .container {
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 14px 28px rgba(0,0,0,0.25), 
                            0 10px 10px rgba(0,0,0,0.22);
                position: relative;
                overflow: hidden;
                width: 450px;
                max-width: 100%;
                min-height: 400px;
                text-align: center;
                padding: 40px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .success-message {
                color: #28a745;
                background-color: #e8f5e9;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
                font-size: 14px;
            }

            .login-link {
                display: inline-block;
                margin-top: 20px;
                color: #ff8c86;
                font-weight: bold;
            }

            .login-link:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="success-message">
                <i class="fas fa-check-circle"></i> Votre mot de passe a été modifié avec succès!
            </div>
            
            <a href="login.php" class="login-link">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </a>
        </div>
        
        <script>
            setTimeout(function() {
                window.location.href = "login.php";
            }, 3000);
        </script>
    </body>
    </html>
    <?php
}
?>