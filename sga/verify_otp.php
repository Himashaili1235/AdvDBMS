<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['temp_user_id'])) {
    header("Location: login.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = $_POST['otp'];
    $user_id = $_SESSION['temp_user_id'];

    $stmt = $conn->prepare("SELECT otp_code, otp_expires_at FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($otp_code, $otp_expires);
    $stmt->fetch();
    $stmt->close();

    if ($entered_otp === $otp_code && strtotime($otp_expires) > time()) {
        // ✅ Success: Log in user
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role_id'] = $_SESSION['temp_role_id'];

        // Clear OTP
        $clear = $conn->prepare("UPDATE users SET otp_code = NULL, otp_expires_at = NULL WHERE user_id = ?");
        $clear->bind_param("i", $user_id);
        $clear->execute();

        unset($_SESSION['temp_user_id'], $_SESSION['temp_role_id']);
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid or expired OTP.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 50px; }
        .container {
            width: 400px; margin: auto;
            background: white; padding: 30px;
            border-radius: 8px; box-shadow: 0 0 10px #ccc;
        }
        h2 { text-align: center; }
        input[type="text"] {
            width: 100%; padding: 10px; margin-top: 10px;
        }
        button {
            width: 100%; padding: 10px; margin-top: 20px;
            background-color: #007bff; color: white;
            border: none; border-radius: 5px;
        }
        .error { color: red; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verify OTP</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="otp" placeholder="Enter 6-digit OTP" required>
            <button type="submit">Verify OTP</button>
        </form>
    </div>
</body>
</html>
