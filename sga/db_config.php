<?php
$servername = "localhost";
$username = "root";
$password = ""; // Leave empty for default XAMPP setup
$dbname = "sga"; // or "studentgrade", depending on what you named it

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
