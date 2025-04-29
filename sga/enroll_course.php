<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    echo "Access denied.";
    exit();
}

$student_id = $_SESSION['user_id'];
$course_id = $_GET['id'] ?? null;

if (!$course_id) {
    echo "Invalid course.";
    exit();
}

// Prevent duplicate enrollments
$stmt = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $insert = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
    $insert->bind_param("ii", $student_id, $course_id);
    $insert->execute();
}

header("Location: courses.php");
exit();
