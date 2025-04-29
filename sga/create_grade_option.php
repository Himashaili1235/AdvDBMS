<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo "Access denied.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade_label = strtoupper(trim($_POST['grade_label']));

    // Check if grade already exists
    $check = $conn->prepare("SELECT * FROM GradeOptions WHERE grade_label = ?");
    $check->bind_param("s", $grade_label);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "This grade already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO GradeOptions (grade_label) VALUES (?)");
        $stmt->bind_param("s", $grade_label);
        if ($stmt->execute()) {
            $success = "Grade option added successfully.";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}

// Fetch current grade options
$grades = $conn->query("SELECT * FROM GradeOptions ORDER BY grade_label ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Grade Option</title>
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
        .message { text-align: center; color: green; }
        .error { color: red; }
        label { font-weight: bold; display: block; margin-top: 15px; }
        input { width: 100%; padding: 10px; margin-top: 5px; }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
        }
        ul { padding-left: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Create Grade Option</h2>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='message'>$success</p>"; ?>

    <form method="POST">
        <label>New Grade Label:</label>
        <input type="text" name="grade_label" placeholder="e.g., A, B+, C" required>
        <button type="submit">Add Grade Option</button>
    </form>

    <h3>Available Grades:</h3>
    <ul>
        <?php while ($g = $grades->fetch_assoc()): ?>
            <li><?= htmlspecialchars($g['grade_label']) ?></li>
        <?php endwhile; ?>
    </ul>

    <p><a href="grades.php">← Back to Grades</a></p>
</div>

</body>
</html>
