<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT full_name, email, role_id, created_at FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $user_role_id, $created_at);
$stmt->fetch();
$stmt->close();

$role_name = match ($user_role_id) {
    1 => 'Admin',
    2 => 'Professor',
    3 => 'Student',
    default => 'Unknown'
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Student Grading System</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background-image: url('https://images.unsplash.com/photo-1553877522-43269d4ea984');
            background-size: cover;
            background-position: center;
            height: 100%;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.7);
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 500px;
            margin: 80px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.96);
            border-radius: 10px;
            box-shadow: 0 0 12px #333;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .info {
            font-size: 16px;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .info strong {
            color: #007bff;
        }

        .buttons {
            margin-top: 25px;
            text-align: center;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            margin: 10px 8px 0 8px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .admin-btn {
            background-color: #28a745;
        }

        .admin-btn:hover {
            background-color: #1e7e34;
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<div class="container">
    <h2>My Profile</h2>
    <div class="info"><strong>Full Name:</strong> <?= htmlspecialchars($full_name) ?></div>
    <div class="info"><strong>Email:</strong> <?= htmlspecialchars($email) ?></div>
    <div class="info"><strong>Role:</strong> <?= $role_name ?></div>
    <div class="info"><strong>Joined On:</strong> <?= $created_at ?></div>

    <div class="buttons">
        <?php if ($user_role_id == 1): ?>
            <a href="users.php" class="btn admin-btn">Manage Users</a>
        <?php endif; ?>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
