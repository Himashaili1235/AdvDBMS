<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo "Access denied.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $grade = $_POST['grade'];

    // Check if a grade already exists for this student-course
    $check = $conn->prepare("SELECT * FROM grades WHERE student_id = ? AND course_id = ?");
    $check->bind_param("ii", $student_id, $course_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Grade already exists for this student in this course.";
    } else {
        $stmt = $conn->prepare("INSERT INTO grades (student_id, course_id, encrypted_grade) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $student_id, $course_id, $grade);
        if ($stmt->execute()) {
            $success = "Grade assigned successfully!";
        } else {
            $error = "Error assigning grade: " . $stmt->error;
        }
    }
}

// Fetch students and courses
$students = $conn->query("SELECT user_id, full_name FROM users WHERE role_id = 3");
$courses = $conn->query("SELECT course_id, course_name, course_code FROM courses");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Grade</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f9f9f9; }
        .container {
            background: white;
            padding: 30px;
            max-width: 500px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        label { font-weight: bold; display: block; margin-top: 15px; }
        select, input { width: 100%; padding: 10px; margin-top: 5px; }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .message { text-align: center; color: green; }
        .error { text-align: center; color: red; }
        a { display: block; text-align: center; margin-top: 20px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h2>Assign Grade to Student</h2>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='message'>$success</p>"; ?>

    <form method="POST">
        <label>Select Student:</label>
        <select name="student_id" required>
            <option value="">-- Select Student --</option>
            <?php while ($s = $students->fetch_assoc()): ?>
                <option value="<?= $s['user_id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Select Course:</label>
        <select name="course_id" required>
            <option value="">-- Select Course --</option>
            <?php while ($c = $courses->fetch_assoc()): ?>
                <option value="<?= $c['course_id'] ?>"><?= htmlspecialchars($c['course_name']) ?> (<?= $c['course_code'] ?>)</option>
            <?php endwhile; ?>
        </select>

        <label>Select Grade:</label>
        <select name="grade" required>
            <option value="">-- Select Grade --</option>
            <option value="A+">A+</option>
            <option value="A">A</option>
            <option value="B+">B+</option>
            <option value="B">B</option>
            <option value="C+">C+</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="F">F</option>
        </select>

        <button type="submit">Create Grade</button>
    </form>

    <a href="grades.php">← Back to Grades</a>
</div>

</body>
</html>
