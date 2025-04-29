<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Grading System</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1588072432836-e10032774350');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.6);
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 0;
        }

        header {
            position: relative;
            z-index: 2;
            width: 100%;
            padding: 20px 40px;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        nav a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
            font-weight: 500;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .container {
            position: relative;
            z-index: 2;
            height: 90%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .btn {
            padding: 12px 30px;
            font-size: 16px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            header {
                flex-direction: column;
                text-align: center;
            }

            nav {
                margin-top: 10px;
            }

            h1 {
                font-size: 2em;
            }

            .btn {
                width: 80%;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<header>
    <div class="logo">
        <img src="https://cdn-icons-png.flaticon.com/512/1670/1670042.png" alt="Logo">
        Student Grading System
    </div>
    <nav>
        <a href="login.php">Sign In</a>
        <a href="register.php">Sign Up</a>
    </nav>
</header>

<div class="container">
    <h1>Welcome to the Student Grading Portal</h1>
    <a href="login.php" class="btn">Sign In</a>
    <a href="register.php" class="btn">Sign Up</a>
</div>

</body>
</html>
