<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f9f9f9;
            color: #333;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #333;
        }

        nav ul li {
            float: left;
        }

        nav ul li a {
            display: block;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-size: 28px; 
            color: white;
        }

        nav ul li a:hover {
            background-color: #111;
        }

        nav ul li.right {
            float: right;
            margin-left: 20px;
        }

        nav ul li.right a {
            background-color: blue;
            color: white;
            border-radius: 20px;
            padding: 10px 20px;
            text-decoration: none;
        }

        nav ul li.right a:hover {
            background-color: #0056b3; 
        }

        nav ul li.right a.logout {
            background-color: red;
        }

        .container {
            max-width: 800px; 
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #444;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin: 10px 0;
            font-size: 20px;
        }

        strong {
            color: #007BFF;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    require 'db_connection.php';
    
    if ($_SESSION['role'] != 'user') {
        header("Location: login.php");
        exit();
    }
    
    $values = [
                "Gabriel Imanullah Putra Pribowo" => "00000100492",
                "Samgar Sammy Napitupulu" => "00000109993",
                "Muhammad Thomas Pangukir" => "00000109875",
                "Rafael Gading Samoda" => "00000090472"
            ];
    ?>
    <nav>
        <ul>
            <li><a href="about_us.php">About Us</a></li>
            <li class="right"><a href="logout.php" class="logout">Logout</a></li>
            <li class="right"><a href="user_profile.php">User Profile</a></li>
            <li class="right"><a href="user_dashboard.php">Event Browsing</a></li>
            <li class="right"><a href="registered_events.php">Event Registration</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>About Us</h1>
        <section>
            <h2>Member Kelompok 9</h2>
            <ul>
                <?php foreach ($values as $key => $value): ?>
                    <li><strong><?php echo $key; ?></strong> (<?php echo $value; ?>)</li>
                <?php endforeach; ?>
            </ul>
        </section>
    </div>
</body>
</html>
