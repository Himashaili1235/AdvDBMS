<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];

    $salt = bin2hex(random_bytes(8));
    $password_hash = hash('sha256', $password . $salt);

    $stmt = $conn->prepare("INSERT INTO Users (full_name, email, password_hash, salt, role_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $full_name, $email, $password_hash, $salt, $role_id);

    if ($stmt->execute()) {
        $success = "Registration successful! <a href='login.php' style='color: #fff; text-decoration: underline;'>Click here to login</a>";
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Student Grading System</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1607032250040-77cd56f2b7b6');
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
            max-width: 500px;
            margin: 80px auto;
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

        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #28a745;
            color: white;
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
    <h2>User Registration</h2>

    <?php if (isset($success)) echo "<div class='message success'>$success</div>"; ?>
    <?php if (isset($error)) echo "<div class='message error'>$error</div>"; ?>

    <form method="POST">
        <label>Full Name:</label>
        <input type="text" name="full_name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Role:</label>
        <select name="role_id" required>
            <option value="">Select Role</option>
            <option value="1">Admin</option>
            <option value="2">Professor</option>
            <option value="3">Student</option>
        </select>

        <input type="submit" value="Register">
    </form>

    <div class="back-link">
        <a href="home.php">← Back to Home</a>
    </div>
</div>
</body>
</html>
