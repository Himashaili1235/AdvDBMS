<?php
session_start();
include 'db_config.php';

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollment Information</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('https://images.unsplash.com/photo-1596495577886-d920f1fb7238') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.7);
            position: fixed;
            top: 0; left: 0;
            height: 100%;
            width: 100%;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 1100px;
            margin: 80px auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px #444;
        }

        h2, h3 {
            text-align: center;
            margin-bottom: 25px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-bottom: 30px;
        }

        .btn {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        td:last-child {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                display: none;
            }

            td {
                position: relative;
                padding-left: 50%;
                border: none;
                border-bottom: 1px solid #ddd;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                font-weight: bold;
                color: #007bff;
            }
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<div class="container">
    <h2>Enrollment Information</h2>
    <div class="back-link">
        <a href="dashboard.php" class="btn">← Back to Dashboard</a>
    </div>

    <?php
    // Student View
    if ($role_id == 3) {
        $stmt = $conn->prepare("
            SELECT c.course_name, c.course_code, u.full_name AS professor
            FROM enrollments e
            JOIN courses c ON e.course_id = c.course_id
            JOIN users u ON c.professor_id = u.user_id
            WHERE e.student_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<h3>Your Enrolled Courses</h3>";
        echo "<table><tr><th>Course</th><th>Code</th><th>Professor</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td data-label='Course'>{$row['course_name']}</td>
                    <td data-label='Code'>{$row['course_code']}</td>
                    <td data-label='Professor'>{$row['professor']}</td>
                  </tr>";
        }
        echo "</table>";
    }

    // Professor View
    elseif ($role_id == 2) {
        $stmt = $conn->prepare("
            SELECT c.course_name, c.course_code, s.full_name AS student_name, s.email
            FROM enrollments e
            JOIN courses c ON e.course_id = c.course_id
            JOIN users s ON e.student_id = s.user_id
            WHERE c.professor_id = ?
            ORDER BY c.course_name
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<h3>Students Enrolled in Your Courses</h3>";
        echo "<table><tr><th>Course</th><th>Code</th><th>Student</th><th>Email</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td data-label='Course'>{$row['course_name']}</td>
                    <td data-label='Code'>{$row['course_code']}</td>
                    <td data-label='Student'>{$row['student_name']}</td>
                    <td data-label='Email'>{$row['email']}</td>
                  </tr>";
        }
        echo "</table>";
    }

    // Admin View
    elseif ($role_id == 1) {
        $result = $conn->query("
            SELECT c.course_name, c.course_code, s.full_name AS student_name, s.email, p.full_name AS professor
            FROM enrollments e
            JOIN courses c ON e.course_id = c.course_id
            JOIN users s ON e.student_id = s.user_id
            JOIN users p ON c.professor_id = p.user_id
            ORDER BY c.course_name
        ");

        echo "<h3>All Enrollments</h3>";
        echo "<table><tr><th>Course</th><th>Code</th><th>Professor</th><th>Student</th><th>Email</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td data-label='Course'>{$row['course_name']}</td>
                    <td data-label='Code'>{$row['course_code']}</td>
                    <td data-label='Professor'>{$row['professor']}</td>
                    <td data-label='Student'>{$row['student_name']}</td>
                    <td data-label='Email'>{$row['email']}</td>
                  </tr>";
        }
        echo "</table>";
    }
    ?>
</div>

</body>
</html>
