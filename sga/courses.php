<?php
session_start();
include 'db_config.php';

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];

$courses = $conn->query("
    SELECT c.course_id, c.course_name, c.course_code, c.professor_id, u.full_name AS professor_name
    FROM courses c
    JOIN users u ON c.professor_id = u.user_id
");

$enrolled_courses = [];
if ($role_id == 3) {
    $result = $conn->query("SELECT course_id FROM enrollments WHERE student_id = $user_id");
    while ($row = $result->fetch_assoc()) {
        $enrolled_courses[] = $row['course_id'];
    }
}

if ($role_id == 1 && isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: courses.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Courses - Student Grading System</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('https://images.unsplash.com/photo-1588072432836-e10032774350') no-repeat center center fixed;
            background-size: cover;
        }

        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.75);
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 1100px;
            margin: 80px auto;
            background: rgba(255, 255, 255, 0.96);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 14px #444;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .actions {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 8px 14px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            margin: 5px;
            display: inline-block;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #b02a37;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 10px;
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
    <h2>Course List</h2>

    <div class="actions">
        <a href="dashboard.php" class="btn">← Back to Dashboard</a>
        <?php if ($role_id == 1): ?>
            <a href="create_course.php" class="btn">+ Create Course</a>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Code</th>
                <th>Professor</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $courses->fetch_assoc()): ?>
            <tr>
                <td data-label="ID"><?= $row['course_id'] ?></td>
                <td data-label="Name"><?= htmlspecialchars($row['course_name']) ?></td>
                <td data-label="Code"><?= htmlspecialchars($row['course_code']) ?></td>
                <td data-label="Professor"><?= htmlspecialchars($row['professor_name']) ?></td>
                <td data-label="Actions">
                    <?php if ($role_id == 1): ?>
                        <a href="edit_course.php?id=<?= $row['course_id'] ?>" class="btn">Edit</a>
                        <a href="?delete=<?= $row['course_id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this course?')">Delete</a>
                        <a href="enroll_student.php?course_id=<?= $row['course_id'] ?>" class="btn">Enroll Student</a>
                    <?php elseif ($role_id == 2 && $row['professor_id'] == $user_id): ?>
                        <a href="edit_course.php?id=<?= $row['course_id'] ?>" class="btn">Edit</a>
                    <?php elseif ($role_id == 3): ?>
                        <?php if (in_array($row['course_id'], $enrolled_courses)): ?>
                            <a href="drop_course.php?id=<?= $row['course_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to drop this course?')">Drop</a>
                        <?php else: ?>
                            <a href="enroll_course.php?id=<?= $row['course_id'] ?>" class="btn">Enroll</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
