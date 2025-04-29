<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo "Access denied.";
    exit();
}

$course_id = $_GET['course_id'] ?? null;

if (!$course_id) {
    echo "Invalid course ID.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];

    // Prevent duplicate enrollment
    $check = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
    $check->bind_param("ii", $student_id, $course_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $student_id, $course_id);
        $stmt->execute();
        $success = "Student enrolled successfully!";
    } else {
        $error = "Student is already enrolled in this course.";
    }
}

// Get all students
$students = $conn->query("SELECT user_id, full_name FROM users WHERE role_id = 3");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enroll Student</title>
</head>
<body>
<h2>Enroll a Student in Course ID <?= $course_id ?></h2>

<?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    <label>Select Student:</label><br>
    <select name="student_id" required>
        <option value="">-- Select a Student --</option>
        <?php while ($s = $students->fetch_assoc()): ?>
            <option value="<?= $s['user_id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
        <?php endwhile; ?>
    </select><br><br>
    <button type="submit">Enroll</button>
</form>

<p><a href="courses.php">Back to Courses</a></p>
</body>
</html>
