<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo "<h2 style='color:red; text-align:center;'>Access denied.</h2>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];

    $salt = bin2hex(random_bytes(8));
    $password_hash = hash('sha256', $password . $salt);

    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash, salt, role_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $full_name, $email, $password_hash, $salt, $role_id);

    if ($stmt->execute()) {
        $success = "✅ User created successfully.";
    } else {
        $error = "❌ Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create User - Admin Panel</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('https://images.unsplash.com/photo-1581091226825-e270406d31b0') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.75);
            position: fixed;
            top: 0; left: 0;
            height: 100%; width: 100%;
            z-index: 0;
        }

        .form-box {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.97);
            max-width: 500px;
            margin: 80px auto;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .btn {
            margin-top: 25px;
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #218838;
        }

        .back {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .back:hover {
            text-decoration: underline;
        }

        .success {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        @media (max-width: 600px) {
            .form-box {
                margin: 40px 20px;
                padding: 30px;
            }
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<div class="form-box">
    <h2>Create New User</h2>

    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <label>Full Name:</label>
        <input type="text" name="full_name" placeholder="Enter full name" required>

        <label>Email:</label>
        <input type="email" name="email" placeholder="Enter email address" required>

        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter password" required>

        <label>Role:</label>
        <select name="role_id" required>
            <option value="">-- Select Role --</option>
            <option value="1">Admin</option>
            <option value="2">Professor</option>
            <option value="3">Student</option>
        </select>

        <button type="submit" class="btn">Create User</button>
    </form>

    <a href="users.php" class="back">← Back to User Management</a>
</div>

</body>
</html>
