<?php
session_start();
include 'db_config.php';

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];

// ✅ Admin Grade Delete
if ($role_id == 1 && isset($_GET['delete'])) {
    $grade_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM grades WHERE grade_id = ?");
    $stmt->bind_param("i", $grade_id);
    $stmt->execute();
    header("Location: grades.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Management - Student Grading System</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background-image: url('https://images.unsplash.com/photo-1581090700227-1e8a1f417373');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.7);
            position: absolute;
            top: 0; left: 0;
            height: 100%; width: 100%;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.95);
            max-width: 1100px;
            margin: 70px auto;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 12px #444;
        }

        h2, h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 8px 14px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin: 5px;
            display: inline-block;
        }

        .btn-purple {
            background-color: #6c63ff;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .actions {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
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
            .container {
                margin: 20px;
                padding: 20px;
            }

            table, thead, tbody, th, td, tr {
                display: block;
                width: 100%;
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
    <h2>Grade Management</h2>

    <div class="actions">
        <a href="dashboard.php" class="btn">← Back to Dashboard</a>
        <?php if ($role_id == 2 || $role_id == 1): ?>
            <a href="assign_grade.php" class="btn">+ Assign Grade</a>
        <?php endif; ?>
        <?php if ($role_id == 1): ?>
            <a href="manage_grade_options.php" class="btn btn-purple">Manage Grade Labels</a>
        <?php endif; ?>
    </div>

    <?php
    if ($role_id == 3) {
        $stmt = $conn->prepare("
            SELECT c.course_name, c.course_code, g.encrypted_grade
            FROM grades g
            JOIN courses c ON g.course_id = c.course_id
            WHERE g.student_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<h3>Your Grades</h3>";
        echo "<table><tr><th>Course</th><th>Code</th><th>Grade</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td data-label='Course'>{$row['course_name']}</td>
                    <td data-label='Code'>{$row['course_code']}</td>
                    <td data-label='Grade'>{$row['encrypted_grade']}</td>
                  </tr>";
        }
        echo "</table>";
    }

    elseif ($role_id == 2) {
        $result = $conn->query("
            SELECT g.grade_id, g.student_id, s.full_name AS student_name, c.course_id, c.course_name, c.course_code, g.encrypted_grade, c.professor_id
            FROM grades g
            JOIN users s ON g.student_id = s.user_id
            JOIN courses c ON g.course_id = c.course_id
            ORDER BY c.course_name
        ");

        echo "<h3>All Grades (Editable for Your Courses)</h3>";
        echo "<table><tr><th>Course</th><th>Code</th><th>Student</th><th>Grade</th><th>Action</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td data-label='Course'>{$row['course_name']}</td>
                    <td data-label='Code'>{$row['course_code']}</td>
                    <td data-label='Student'>{$row['student_name']}</td>
                    <td data-label='Grade'>{$row['encrypted_grade']}</td>";
            if ($row['professor_id'] == $user_id) {
                echo "<td data-label='Action'><a class='btn' href='update_grade.php?id={$row['grade_id']}'>Update</a></td>";
            } else {
                echo "<td data-label='Action' style='color: gray;'>Read-only</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    elseif ($role_id == 1) {
        $result = $conn->query("
            SELECT g.grade_id, s.full_name AS student_name, s.email, c.course_name, c.course_code, p.full_name AS professor_name, g.encrypted_grade
            FROM grades g
            JOIN users s ON g.student_id = s.user_id
            JOIN courses c ON g.course_id = c.course_id
            JOIN users p ON c.professor_id = p.user_id
            ORDER BY c.course_name
        ");

        echo "<h3>All Grades (Full Control)</h3>";
        echo "<table><tr><th>Course</th><th>Code</th><th>Professor</th><th>Student</th><th>Email</th><th>Grade</th><th>Action</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td data-label='Course'>{$row['course_name']}</td>
                    <td data-label='Code'>{$row['course_code']}</td>
                    <td data-label='Professor'>{$row['professor_name']}</td>
                    <td data-label='Student'>{$row['student_name']}</td>
                    <td data-label='Email'>{$row['email']}</td>
                    <td data-label='Grade'>{$row['encrypted_grade']}</td>
                    <td data-label='Action'>
                        <a class='btn' href='update_grade.php?id={$row['grade_id']}'>Update</a>
                        <a class='btn btn-danger' href='grades.php?delete={$row['grade_id']}' onclick=\"return confirm('Are you sure you want to delete this grade?')\">Delete</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
    }
    ?>
</div>

</body>
</html>
