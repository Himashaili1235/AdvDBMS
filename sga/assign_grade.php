<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
    echo "<h2 style='color:red; text-align:center;'>Access denied.</h2>";
    exit();
}

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $grade_label = $_POST['grade_label'];

    // Professors can only assign grades for their courses
    if ($role_id == 2) {
        $check = $conn->prepare("SELECT * FROM courses WHERE course_id = ? AND professor_id = ?");
        $check->bind_param("ii", $course_id, $user_id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows === 0) {
            $error = "You can only assign grades for your own courses.";
        }
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO grades (student_id, course_id, encrypted_grade) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $student_id, $course_id, $grade_label);
        if ($stmt->execute()) {
            $new_grade_id = $stmt->insert_id;

            $log_stmt = $conn->prepare("INSERT INTO AuditLogs (user_id, action, table_name, record_id) VALUES (?, ?, ?, ?)");
            $action = "Assigned grade '$grade_label' to student_id=$student_id in course_id=$course_id";
            $table_name = "Grades";
            $log_stmt->bind_param("issi", $user_id, $action, $table_name, $new_grade_id);
            $log_stmt->execute();

            $success = "✅ Grade assigned successfully.";
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
    }
}

// Admin: See all courses, all students
// Professor: See their own courses and enrolled students only
if ($role_id == 1) {
    $courses = $conn->query("
        SELECT c.course_id, c.course_name
        FROM courses c
        ORDER BY c.course_name
    ");

    $students = $conn->query("
        SELECT DISTINCT u.user_id, u.full_name
        FROM users u
        JOIN enrollments e ON u.user_id = e.student_id
        ORDER BY u.full_name
    ");
} else {
    $courses = $conn->query("SELECT * FROM courses WHERE professor_id = $user_id");

    $students = $conn->query("
        SELECT DISTINCT u.user_id, u.full_name
        FROM enrollments e
        JOIN users u ON u.user_id = e.student_id
        JOIN courses c ON c.course_id = e.course_id
        WHERE c.professor_id = $user_id
    ");
}

$grades = $conn->query("SELECT grade_label FROM GradeOptions ORDER BY grade_label");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Grade - <?= $role_id == 1 ? "Admin" : "Professor" ?> Panel</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('https://images.unsplash.com/photo-1600195077076-4369e4c179ae') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.7);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .form-container {
            position: relative;
            z-index: 1;
            max-width: 500px;
            margin: 100px auto;
            padding: 35px;
            background: rgba(255, 255, 255, 0.96);
            border-radius: 12px;
            box-shadow: 0 0 14px #444;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            font-weight: bold;
            color: #007bff;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .form-container {
                margin: 40px 20px;
                padding: 25px;
            }

            button {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<div class="form-container">
    <h2>Assign Grade to Student</h2>

    <?php if (isset($error)) echo "<div class='message error'>$error</div>"; ?>
    <?php if (isset($success)) echo "<div class='message success'>$success</div>"; ?>

    <form method="POST">
        <label>Student:</label>
        <select name="student_id" required>
            <option value="">-- Select Student --</option>
            <?php while ($s = $students->fetch_assoc()): ?>
                <option value="<?= $s['user_id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Course:</label>
        <select name="course_id" required>
            <option value="">-- Select Course --</option>
            <?php while ($c = $courses->fetch_assoc()): ?>
                <option value="<?= $c['course_id'] ?>"><?= htmlspecialchars($c['course_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Grade:</label>
        <select name="grade_label" required>
            <option value="">-- Select Grade --</option>
            <?php while ($g = $grades->fetch_assoc()): ?>
                <option value="<?= $g['grade_label'] ?>"><?= $g['grade_label'] ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Assign Grade</button>
    </form>

    <a class="back-link" href="grades.php">← Back to Grades</a>
</div>

</body>
</html>
