<?php
session_start();
include 'db_config.php';

// ✅ Check if user is logged in and is Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    echo "<h2 style='color:red; text-align:center;'>Access denied. Admins only.</h2>";
    exit();
}

// ✅ Handle delete request (excluding self)
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    if ($delete_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
    }
}

// ✅ Fetch all users with their role names using JOIN
$result = $conn->query("
    SELECT u.user_id, u.full_name, u.email, u.created_at, r.role_name
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.role_id
    ORDER BY u.user_id ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin Panel</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background-image: url('https://images.unsplash.com/photo-1532619675605-1ede6c2d73f7');
            background-size: cover;
            background-position: center;
            height: 100%;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.75);
            position: fixed;
            top: 0; left: 0;
            height: 100%;
            width: 100%;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 1100px;
            margin: 60px auto;
            background: rgba(255, 255, 255, 0.96);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .btn {
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            margin: 5px 0;
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

        .btn-success {
            background-color: #28a745;
        }

        .btn-success:hover {
            background-color: #1e7e34;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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

        .you-tag {
            color: gray;
            font-size: 13px;
            font-style: italic;
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
    <h2>Manage Users</h2>

    <div class="top-actions">
        <a href="dashboard.php" class="btn">← Back to Dashboard</a>
        <a href="register_user.php" class="btn btn-success">+ Create New User</a>
    </div>

    <table>
        <thead>
        <tr>
            <th>User ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td data-label="User ID"><?= $row['user_id'] ?></td>
                <td data-label="Full Name"><?= htmlspecialchars($row['full_name']) ?></td>
                <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
                <td data-label="Role"><?= htmlspecialchars($row['role_name'] ?? 'Unknown') ?></td>
                <td data-label="Created At"><?= $row['created_at'] ?></td>
                <td data-label="Actions">
                    <a href="edit_user.php?id=<?= $row['user_id'] ?>" class="btn">Edit</a>
                    <?php if ($row['user_id'] != $_SESSION['user_id']): ?>
                        <a href="users.php?delete=<?= $row['user_id'] ?>" class="btn btn-danger"
                           onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    <?php else: ?>
                        <span class="you-tag">(You)</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
