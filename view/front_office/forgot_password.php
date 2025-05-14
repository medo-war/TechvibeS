<?php
session_start();

require '../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $code = rand(100000, 999999);

    $db = new PDO('mysql:host=localhost;dbname=test;charset=utf8mb4', 'root', '');
    $stmt = $db->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_code'] = $code;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'peonycovers05@gmail.com';
            $mail->Password = 'iggt ezwb tdak xkpm';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('peonycovers05@gmail.com', 'LiveTheMusic');
            $mail->addAddress($email);
            $mail->Subject = "Votre code de réinitialisation";
            $mail->Body = "Bonjour,\n\nVotre code de réinitialisation est : $code\n\nMerci.";

            $mail->send();
            header("Location: verify_code.php");
            exit();
        } catch (Exception $e) {
            $error = "Erreur lors de l'envoi du mail : " . $mail->ErrorInfo;
        }
    } else {
        $error = "Aucun utilisateur trouvé avec cet email.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', sans-serif;
        }
        
        body {
            background: url('https://i.pinimg.com/736x/95/71/c7/9571c799aeacc2e141f47294e1f53161.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            margin: -20px 0 50px;
        }
        
        .forgot-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
            width: 400px;
            max-width: 100%;
            min-height: 480px;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        h1 {
            font-weight: bold;
            margin: 0 0 20px;
            color: #333;
        }
        
        .subtitle {
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 0 0 30px;
            color: #666;
        }
        
        form {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-size: 13px;
            font-weight: 600;
        }
        
        input {
            background-color: #eee;
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
            border-radius: 5px;
            font-size: 13px;
        }
        
        button {
            border-radius: 20px;
            border: 1px solid #ff8c86;
            background-color: #ff8c86;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
        }
        
        button:hover {
            background-color: #ff7b75;
        }
        
        button:active {
            transform: scale(0.95);
        }
        
        .error {
            color: #ff4757;
            margin: 15px 0;
            font-size: 13px;
        }
        
        .options {
            margin-top: 30px;
            font-size: 13px;
        }
        
        .options a {
            color: #ff8c86;
            text-decoration: none;
            font-weight: 600;
        }
        
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
    </style>
</head>
<body>
    <div class="forgot-container">
        <h1>Hello, Friend!</h1>
        <p class="subtitle">Enter your email to reset your password</p>
        
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        
        <form method="post">
            <div class="form-group">
                <label>EMAIL ADDRESS</label>
                <input type="email" name="email" required placeholder="Your email address">
            </div>
            
            <button type="submit">SEND RESET CODE</button>
        
        </form>
        
        <div class="options">
            Remember your password? <a href="login.php">Sign in</a>
        </div>
    </div>
</body>
</html>