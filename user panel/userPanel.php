<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
$userId = $_SESSION['userId'];
if (!isset($_SESSION['login']) || $_SESSION['login'] == '0') {
    header("Location: ../login.php");
    exit();
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        $_SESSION['login'] = '0';
        header("Location: ../login.php");
        exit();
    }
}
echo $userId = $_SESSION['userId'];
require_once "../class/userPanelClass.php";
require_once "../class/dbconnection.php";
$db = new db;
$myClass = new userPanel;
$sql = "SELECT username FROM users WHERE id = '$userId'";
$result = mysqli_query($db->conn, $sql);
$row = mysqli_fetch_assoc($result);
$username = $row['username'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {

    // Assign form inputs

    $myClass->post($username, 'user', $_POST['title'], $_POST['category'], $_POST['description'], $_POST['public_status'], $_FILES['fileToUpload']);
    header("Location: " . $_SERVER['PHP_SELF']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Panel</title>

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome - latest version -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ENjdO4Dr2bkBIFxQpeoYz1Hir5fm3u5c57/V53u5I1h5iY5v2EnKtQ" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/userpanel.css">

    <!-- Background image for the entire page -->
    <style>
        * {
            padding: 0px;
            margin: 0px;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: url('../images/black.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .navbar-nav {
            display: flex;
            justify-content: space-between;
            margin: 0px;
        }

        .navbar-nav .nav-item {
            margin-right: 20px;
        }

        .navbar-nav .nav-link {
            color: white;
            font-weight: 600;
        }

        .btn-logout {
            background-color: #ff5e57;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: -250px;
            height: 100%;
            width: 250px;
            background: rgba(0, 0, 0, 0.9);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease-in-out;
            padding-top: 60px;
        }

        .toggle-btn {
            position: absolute;
            top: 100px;
            left: 260px;
            background-color: #007bff;
            border: none;
            font-size: 25px;
            color: white;
            cursor: pointer;
            padding: 2px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .toggle-btn:hover {
            background-color: #0056b3;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .sidebar a:hover {
            background-color: #f0f0f0;
            border-radius: 5px;
            color: black;
        }

        .container {
            margin-top: 80px;
            color: white;
            padding: 0px 10%;
        }

        .text-center {
            color: #000000;
            text-align: center;
            margin-bottom: 5px;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            color: black;
        }

        .search-friend,
        .chat-section,
        .followed-users {
            margin-bottom: 20px;
        }

        .followed-user-ul {
            list-style: none;
        }

        .form-control {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .friends-list {
            display: none;
            /* Initially hidden */
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            width: 300px;
            background-color: rgba(0, 0, 0, 0.9);
            padding: 20px;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            z-index: 1000;
            /* Make sure it's on top */
        }

        .friends-list ul {
            list-style-type: none;
            padding: 0;
        }

        .friends-list ul li {
            margin-bottom: 10px;
            color: white;
        }

        .close-btn {
            background-color: #ff5e57;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            float: right;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="padding: 0px;">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarNav" style="padding: 0px;">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item" style="display: flex; align-items: center; padding: 0px; color: white;margin-top: 5px;">
                        <a href="" style="padding: 0px 15px 0px 0px; margin: 0px;"> <img src="../profilePic/guest/guestUserPic.jpg" alt="" height="80px" width="80px" style="border-radius: 100%;"></a><span>UserName</span>
                    </li>
                    <li class="nav-item" style="margin-top: 20px;">
                        <form method="POST">
                            <button class="btn-logout" name="logout">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <a href="../Dashboard.php">Dashboard</a>
        <a href="#">Profile</a>
        <a href="./allPost.php">All Posts</a>
        <a href="#" onclick="showFriends()">Friends</a>
        <a href="#">Notifications</a>
    </div>

    <!-- Friends List Div -->
    <div class="friends-list" id="friendsList">
        <h3>My Friends</h3>
        <ul>
            <li>John Doe</li>
            <li>Jane Smith</li>
            <li>Robert Brown</li>
        </ul>
        <button class="close-btn" onclick="hideFriends()">Close</button>
    </div>

    <!-- Main Content -->
   

        <!-- Search Friend -->
        <!-- Main Content -->
        <div class="container">
            <h1 class="text-center">Welcome to Your User Panel</h1>

            <!-- User Engagement Statistics -->
            <div class="card engagement-stats" style="max-width: 600px; margin: auto;margin-bottom: 20px; padding: 30px; background: linear-gradient(135deg, #ffffff, #e9ecef); border-radius: 20px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);">
                <h3 style="text-align: center; font-family: 'Poppins', sans-serif; color: #333; font-size: 24px; margin-bottom: 20px;">Upload Document</h3>

                <form action="" method="post" enctype="multipart/form-data" style="background-color: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); ">
                    <div class="form-group">
                        <label for="title" style="font-weight: bold; font-family: 'Poppins', sans-serif; color: #333; font-size: 16px;">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required style="padding: 15px; border-radius: 10px; border: 1px solid #ccc; box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05); transition: box-shadow 0.3s ease;">
                    </div>

                    <div class="form-group">
                        <label for="description" style="font-weight: bold; font-family: 'Poppins', sans-serif; color: #333; font-size: 16px;">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required style="padding: 15px; border-radius: 10px; border: 1px solid #ccc; box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05); transition: box-shadow 0.3s ease;"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="category" style="font-weight: bold; font-family: 'Poppins', sans-serif; color: #333; font-size: 16px;">Category</label>
                        <select class="form-control" id="category" name="category" required style="padding: 15px; border-radius: 10px; border: 1px solid #ccc; box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05); transition: box-shadow 0.3s ease;">
                            <option value="" disabled selected>Select a category</option>
                            <option value="Research">Research</option>
                            <option value="Technical">Technical</option>
                            <option value="Thesis">Thesis</option>
                            <option value="Review">Review</option>
                            <option value="Conference">Conference</option>
                            <option value="Dataset">Dataset</option>
                            <option value="Preprint">Preprint</option>
                            <option value="Grant Proposal">Grant Proposal</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="file" style="font-weight: bold; font-family: 'Poppins', sans-serif; color: #333; font-size: 16px;">File</label>
                        <input type="file" class="form-control-file" id="file" name="fileToUpload" required style="padding: 15px; border-radius: 10px; border: 1px solid #ccc; box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05); transition: box-shadow 0.3s ease;">
                    </div>

                    <div class="form-group">
                        <label style="font-weight: bold; font-family: 'Poppins', sans-serif; color: #333; font-size: 16px;">Visibility</label>
                        <select class="form-control" id="visibility" name="public_status" required style="padding: 15px; border-radius: 10px; border: 1px solid #ccc; box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05); transition: box-shadow 0.3s ease;">
                            <option value="" disabled selected>Select visibility</option>
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" name="upload" style="background-color: #007bff; border-color: #007bff; font-family: 'Poppins', sans-serif; font-weight: 700; padding: 12px 30px; border-radius: 50px; width: 100%; transition: background-color 0.3s ease, box-shadow 0.3s ease;">
                        Upload
                    </button>
                </form>

                <!-- Hover Effect -->
                <style>
                    .form-control,
                    .form-control-file,
                    .form-control:focus {
                        outline: none;
                        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1), 0 4px 15px rgba(0, 0, 0, 0.05);
                    }

                    .btn:hover {
                        background-color: #0056b3;
                        box-shadow: 0 6px 20px rgba(0, 91, 187, 0.4);
                    }

                    .btn {
                        transition: background-color 0.3s ease, box-shadow 0.3s ease;
                    }

                    .card {
                        background-color: #f8f9fa;
                        padding: 30px;
                        border-radius: 20px;
                        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
                    }

                    .form-group {
                        margin-bottom: 20px;
                    }

                    h3 {
                        font-weight: bold;
                        font-family: 'Poppins', sans-serif;
                        color: #333;
                    }
                </style>
            </div>


            <!-- Download Counts Section -->
            <div class="card download-counts">
                <h3>Download Counts</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Document Name</th>
                            <th>Download Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Research Paper 1</td>
                            <td>50</td> <!-- Example data -->
                        </tr>
                        <tr>
                            <td>Research Paper 2</td>
                            <td>30</td> <!-- Example data -->
                        </tr>
                        <tr>
                            <td>Whitepaper</td>
                            <td>40</td> <!-- Example data -->
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Citation Counts Section -->
            <div class="card citation-counts">
                <h3>Citation Counts</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Document Name</th>
                            <th>Citation Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Research Paper 1</td>
                            <td>25</td> <!-- Example data -->
                        </tr>
                        <tr>
                            <td>Research Paper 2</td>
                            <td>45</td> <!-- Example data -->
                        </tr>
                        <tr>
                            <td>Case Study</td>
                            <td>15</td> <!-- Example data -->
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- User Engagement (Views, Clicks) Section -->
            <div class="card user-engagement">
                <h3>User Engagement</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Document Name</th>
                            <th>Views</th>
                            <th>Clicks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Research Paper 1</td>
                            <td>500</td> <!-- Example data -->
                            <td>250</td> <!-- Example data -->
                        </tr>
                        <tr>
                            <td>Research Paper 2</td>
                            <td>400</td> <!-- Example data -->
                            <td>150</td> <!-- Example data -->
                        </tr>
                        <tr>
                            <td>Whitepaper</td>
                            <td>600</td> <!-- Example data -->
                            <td>300</td> <!-- Example data -->
                        </tr>
                    </tbody>
                </table>
            </div>
     

    </div>

    <!-- JavaScript files -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.style.left = (sidebar.style.left === "-250px") ? "0" : "-250px";
        }

        function showFriends() {
            const friendsList = document.getElementById("friendsList");
            friendsList.style.display = "block";
        }

        function hideFriends() {
            const friendsList = document.getElementById("friendsList");
            friendsList.style.display = "none";
        }
    </script>
</body>

</html>