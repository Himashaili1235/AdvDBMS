<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];

// Fetch user info
$stmt = $conn->prepare("
    SELECT u.full_name, r.role_name
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.role_id
    WHERE u.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $role_name);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Student Grading System</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.7);
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 60px auto;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.97);
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px #444;
        }

        h2 {
            margin-bottom: 10px;
        }

        .role {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
        }

        .button-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        .button {
            padding: 12px 24px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
            min-width: 140px;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .logout {
            margin-top: 30px;
        }

        .logout a {
            background-color: #dc3545;
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($full_name) ?>!</h2>
    <p class="role">Role: <?= htmlspecialchars($role_name) ?></p>

    <div class="button-group">
        <a href="profile.php" class="button">Profile</a>
        <a href="grades.php" class="button">Grades</a>
        <a href="courses.php" class="button">Courses</a>
        <a href="enrollments.php" class="button">Enrollment</a>
        <?php if ($_SESSION['role_id'] == 1): ?>
            <a href="audit_logs.php" class="button">Audit Logs</a>
        <?php endif; ?>
    </div>

    <div class="logout">
        <a href="home.php" class="button">Logout</a>
    </div>
</div>

</body>
</html>
