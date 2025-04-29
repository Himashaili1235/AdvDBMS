<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_config.php';

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password_input = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password_hash, salt, role_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $password_hash, $salt, $role_id);
        $stmt->fetch();

        $hashed_input = hash('sha256', $password_input . $salt);

        if ($hashed_input === $password_hash) {
            $otp = rand(100000, 999999);
            $expires_at = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            $update = $conn->prepare("UPDATE users SET otp_code = ?, otp_expires_at = ? WHERE user_id = ?");
            $update->bind_param("ssi", $otp, $expires_at, $user_id);
            $update->execute();

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'sandbox.smtp.mailtrap.io';
                $mail->SMTPAuth = true;
                $mail->Username = '4c5a4daba810b2';
                $mail->Password = 'f086b0006265bc';
                $mail->Port = 2525;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

                $mail->setFrom('noreply@sga.local', 'SGS System');
                $mail->addAddress($email);
                $mail->Subject = 'Your OTP for Secure Login';
                $mail->Body = "Your OTP is: $otp\nThis code will expire in 10 minutes.";

                $mail->send();

                $_SESSION['temp_user_id'] = $user_id;
                $_SESSION['temp_role_id'] = $role_id;

                header("Location: verify_otp.php");
                exit();
            } catch (Exception $e) {
                $error = "Failed to send OTP email. Mailer Error: " . $mail->ErrorInfo;
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Student Grading System</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1571260899304-425eee4c7efc');
            background-size: cover;
            background-position: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.6);
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        .container {
            position: relative;
            z-index: 2;
            max-width: 400px;
            margin: 100px auto;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #444;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }

        .error {
            background-color: #dc3545;
            color: white;
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="overlay"></div>
<div class="container">
    <h2>Login</h2>

    <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>

    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <div class="back-link">
        <a href="home.php">← Back to Home</a>
    </div>
</div>
</body>
</html>
