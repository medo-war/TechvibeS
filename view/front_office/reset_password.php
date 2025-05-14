<?php
session_start();
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['new_password'])) {
    $newPwd = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $db = new PDO('mysql:host=localhost;dbname=music;charset=utf8mb4', 'root', '');
    $stmt = $db->prepare("UPDATE user SET pwd = ? WHERE email = ?");
    $stmt->execute([$newPwd, $_SESSION['reset_email']]);

    // Nettoyage
    unset($_SESSION['reset_email'], $_SESSION['reset_code']);
    header("Location: /livethemusic/view/front_office/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nouveau mot de passe</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: url('https://i.pinimg.com/736x/95/71/c7/9571c799aeacc2e141f47294e1f53161.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
            position: relative;
            overflow: hidden;
            width: 400px;
            max-width: 100%;
            min-height: 400px;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        form {
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 30px;
            height: 100%;
            text-align: center;
            width: 100%;
        }

        h2 {
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        input {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
            border-radius: 4px;
            font-size: 14px;
            color: #333;
        }

        input:focus {
            outline: none;
            border-color: #ff6b6b;
        }

        button {
            border-radius: 20px;
            border: 1px solid #ff6b6b;
            background-color: #ff6b6b;
            color: #FFFFFF;
            font-size: 13px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            margin-top: 15px;
            cursor: pointer;
        }

        button:hover {
            background-color: #ff5252;
            transform: translateY(-2px);
        }

        button:active {
            transform: scale(0.95);
        }

        button:focus {
            outline: none;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
                min-height: 350px;
            }

            form {
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form method="post">
                <h2>Réinitialisez votre mot de passe</h2>
                <input type="password" name="new_password" placeholder="Nouveau mot de passe" required />
                <button type="submit">Réinitialiser</button>
            </form>
        </div>
    </div>
</body>
</html>