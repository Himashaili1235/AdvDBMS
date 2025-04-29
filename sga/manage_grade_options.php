<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo "<h2 style='color:red; text-align:center;'>Access denied. Admins only.</h2>";
    exit();
}

$admin_id = $_SESSION['user_id'];

// Add label
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_label'])) {
    $label = strtoupper(trim($_POST['new_label']));
    $check = $conn->prepare("SELECT * FROM GradeOptions WHERE grade_label = ?");
    $check->bind_param("s", $label);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Grade label already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO GradeOptions (grade_label) VALUES (?)");
        $stmt->bind_param("s", $label);
        if ($stmt->execute()) {
            $message = "Grade label added.";
            $log = $conn->prepare("INSERT INTO AuditLogs (user_id, action, table_name, record_id) VALUES (?, ?, ?, 0)");
            $action = "Created grade label: '$label'";
            $table = "GradeOptions";
            $log->bind_param("iss", $admin_id, $action, $table);
            $log->execute();
        }
    }
}

// Delete label
if (isset($_GET['delete'])) {
    $labelToDelete = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM GradeOptions WHERE grade_label = ?");
    $stmt->bind_param("s", $labelToDelete);
    if ($stmt->execute()) {
        $message = "Grade label deleted.";
        $log = $conn->prepare("INSERT INTO AuditLogs (user_id, action, table_name, record_id) VALUES (?, ?, ?, 0)");
        $action = "Deleted grade label: '$labelToDelete'";
        $table = "GradeOptions";
        $log->bind_param("iss", $admin_id, $action, $table);
        $log->execute();
    }
}

// Update label
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_old'], $_POST['update_new'])) {
    $oldLabel = $_POST['update_old'];
    $newLabel = strtoupper(trim($_POST['update_new']));
    $stmt = $conn->prepare("UPDATE GradeOptions SET grade_label = ? WHERE grade_label = ?");
    $stmt->bind_param("ss", $newLabel, $oldLabel);
    if ($stmt->execute()) {
        $message = "Grade label updated.";
        $log = $conn->prepare("INSERT INTO AuditLogs (user_id, action, table_name, record_id) VALUES (?, ?, ?, 0)");
        $action = "Updated grade label: '$oldLabel' to '$newLabel'";
        $table = "GradeOptions";
        $log->bind_param("iss", $admin_id, $action, $table);
        $log->execute();
    }
}

// Get labels
$labels = $conn->query("SELECT * FROM GradeOptions ORDER BY grade_label ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Grade Labels</title>
    <style>
        body, html {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://images.unsplash.com/photo-1571260899304-425eee4c7efc') no-repeat center center fixed;
            background-size: cover;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.6);
            position: absolute;
            top: 0; left: 0;
            height: 100%;
            width: 100%;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 700px;
            margin: 80px auto;
            background: rgba(255, 255, 255, 0.96);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 60%;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            padding: 10px 18px;
            margin-left: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            color: white;
            text-decoration: none;
        }

        .btn-danger:hover {
            background-color: #a71d2a;
        }

        .update-form input[type="text"] {
            width: 120px;
            padding: 5px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                display: none;
            }

            td {
                position: relative;
                padding-left: 50%;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                font-weight: bold;
            }

            form {
                flex-direction: column;
                align-items: stretch;
            }

            input[type="text"], button {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>

<div class="overlay"></div>
<div class="container">
    <h2>Manage Grade Labels</h2>

    <?php if (isset($message)) echo "<div class='message'>$message</div>"; ?>

    <!-- Add Form -->
    <form method="POST">
        <input type="text" name="new_label" placeholder="e.g., A, B+" required>
        <button type="submit">Add Grade</button>
    </form>

    <table>
        <tr><th>Grade Label</th><th>Update</th><th>Delete</th></tr>
        <?php while ($row = $labels->fetch_assoc()): ?>
            <tr>
                <td data-label="Label"><?= htmlspecialchars($row['grade_label']) ?></td>
                <td data-label="Update">
                    <form method="POST" class="update-form">
                        <input type="hidden" name="update_old" value="<?= $row['grade_label'] ?>">
                        <input type="text" name="update_new" placeholder="New label" required>
                        <button type="submit">Update</button>
                    </form>
                </td>
                <td data-label="Delete">
                    <a class="btn-danger" href="?delete=<?= urlencode($row['grade_label']) ?>" onclick="return confirm('Delete this grade label?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div class="back-link">
        <a href="grades.php">← Back to Grades</a>
    </div>
</div>

</body>
</html>
