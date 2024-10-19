<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}

// Debugging: Check the current value of $_SESSION['login']
// echo "Current login status: " . $_SESSION['login'] . "<br>";   Optional: For debugging purposes

if ($_SESSION['login'] == '0') {  // Use == for comparison
    header("Location: login.php");
    exit();
}else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        $_SESSION['login'] = '0';  // Set the session variable to '0'
        header("Location: ../login.php");
        exit();
    }
}
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <style>
           /* @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap'); */
           @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap');
        body {
            /* font-family: Arial, sans-serif; */
            /* font-family: 'Roboto', sans-serif; */
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #333;
            margin: 0;
            padding: 0;
            /* display: flex; */
            color:white;
        }
        main{
            /* display: flex; */
            transition: margin-left 0.3s ease;
        }
        h1{
            color: white;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #333;
            padding: 10px 20px;
            color: white;
        }
        .navbar h1 {
            margin: 0;
        }
        .navbar form {
            margin: 0;
        }
        .sidebar {
            margin-top: 5%;
            border-radius: 10px;
            width: 15%;
            background: #333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            left: -15%;
            transition: left 0.3s;
        }
        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
            background: #ff7e5f;
            text-align: center;
        }
        .sidebar a:hover {
            background: #feb47b;
        }
        .container {
            margin-top: 5%;
            width: 100%;
            /* margin: 0; */
            padding: 20px;
            box-sizing: border-box;
        }
        .section {
            background: #333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #fff;
        }
        .form-group input[type="text"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group button {
            background: #ff7e5f;
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group button:hover {
            background: #feb47b;
        }
        .toggle-btn {
            position: fixed;
            top: 80px; /* Adjusted to be below the navbar */
            left: 20px;
            background: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .close-btn {
            display: none;
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff0000;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
    <script>
      function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.style.left === '0px') {
                sidebar.style.left = '-15%';
            } else {
                sidebar.style.left = '0px';
            }
        }

       
    </script>
</head>
<body>
    <div class="navbar">
        <h1>Welcome, Admin!</h1>
        <form method="POST">
            <button style="background-color: #4CAF50; /* Green */
                border: none;
                color: white;
                padding: 10px 20px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                cursor: pointer;
                border-radius: 12px;" type="submit" name="logout">Logout</button>
        </form>
    </div>
    <!-- <button class="toggle-btn" onclick="toggleSidebar()">Toggle Sidebar</button> -->
    <main>
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <main>
        <div class="sidebar" id="sidebar">
            <a href="./admin.php">Admin Dashboard</a>
            <a href="./allUser.php">All Users</a>
            <a href="./allResearcher.php">All Researchers</a>
            <a href="./banUser.php">Ban users</a>
            <a href="../Dashboard.php">Dashboard</a>


        </div>
    
    <div class="container">
        <h1 style="text-align:center" >Admin Panel</h1>
        <div class="section" id="upload-document">
            <h2>Upload Document</h2>
            <form method="POST" style="padding: 20px;">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description">
                </div>
                <!-- <div class="form-group">
                    <label for="image">Upload Image</label>
                    <input type="file" id="image" name="image">
                </div> -->
                <div class="form-group">
                    <label for="file">Upload File</label>
                    <input type="file" id="file" name="file">
                </div>
                <div class="form-group">
                    <button type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
    </main>
</body>
</html>
