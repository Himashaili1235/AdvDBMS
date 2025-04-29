<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo "<h2 style='color:red; text-align:center;'>Access denied.</h2>";
    exit();
}

// Handle individual delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $log_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM AuditLogs WHERE log_id = ?");
    $stmt->bind_param("i", $log_id);
    $stmt->execute();
}

// Handle delete all
if (isset($_GET['delete_all']) && $_GET['delete_all'] === 'true') {
    $conn->query("DELETE FROM AuditLogs");
}

$thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));

$query = $conn->prepare("
    SELECT a.*, u.full_name, u.email 
    FROM AuditLogs a
    JOIN Users u ON a.user_id = u.user_id
    WHERE a.timestamp >= ?
    ORDER BY a.timestamp DESC
");
$query->bind_param("s", $thirtyDaysAgo);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Logs - Admin Panel</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1'); /* ✨ NEW Professional Campus Background */
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.75);
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
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
            color: #333;
        }

        .actions {
            text-align: center;
            margin-bottom: 25px;
        }

        .btn {
            padding: 8px 16px;
            margin: 6px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 15px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #a71d2a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
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
    <h2>Audit Logs (Last 30 Days)</h2>

    <div class="actions">
        <a href="dashboard.php" class="btn">← Back to Dashboard</a>
        <a href="?delete_all=true" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete ALL logs?')">🗑️ Delete All Logs</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Action</th>
                <th>Table</th>
                <th>Record ID</th>
                <th>Timestamp</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($log = $result->fetch_assoc()): ?>
                <tr>
                    <td data-label="User"><?= htmlspecialchars($log['full_name']) ?></td>
                    <td data-label="Email"><?= htmlspecialchars($log['email']) ?></td>
                    <td data-label="Action"><?= htmlspecialchars($log['action']) ?></td>
                    <td data-label="Table"><?= htmlspecialchars($log['table_name']) ?></td>
                    <td data-label="Record ID"><?= $log['record_id'] ?></td>
                    <td data-label="Timestamp"><?= $log['timestamp'] ?></td>
                    <td data-label="Delete">
                        <a href="?delete=<?= $log['log_id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this log?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
