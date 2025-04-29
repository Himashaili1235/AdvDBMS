<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo "Access denied.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];
    $professor_id = $_POST['professor_id'];

    $stmt = $conn->prepare("INSERT INTO courses (course_name, course_code, professor_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $course_name, $course_code, $professor_id);
    if ($stmt->execute()) {
        header("Location: courses.php");
        exit();
    } else {
        $error = "Error: " . $stmt->error;
    }
}

// Get professors to assign
$professors = $conn->query("SELECT user_id, full_name FROM users WHERE role_id = 2");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Course</title>
</head>
<body>
<h2>Create New Course</h2>

<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    <label>Course Name:</label><br>
    <input type="text" name="course_name" required><br><br>

    <label>Course Code:</label><br>
    <input type="text" name="course_code" required><br><br>

    <label>Assign Professor:</label><br>
    <select name="professor_id" required>
        <option value="">-- Select Professor --</option>
        <?php while ($p = $professors->fetch_assoc()): ?>
            <option value="<?= $p['user_id'] ?>"><?= htmlspecialchars($p['full_name']) ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <button type="submit">Create Course</button>
</form>

<p><a href="courses.php">← Back to Courses</a></p>
</body>
</html>
