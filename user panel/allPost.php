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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #007bff;
            padding: 10px;
        }
        .navbar-brand {
            font-weight: bold;
            font-family: 'Poppins', sans-serif;
        }
        .logout-btn {
            background-color: #ff4d4d;
            color: #fff;
            border-radius: 20px;
            padding: 5px 20px;
            border: none;
            transition: background-color 0.3s ease;
        }
        .logout-btn:hover {
            background-color: #e63946;
        }
        .card {
            margin-bottom: 20px;
        }
        .post-title {
            font-weight: bold;
            font-family: 'Poppins', sans-serif;
            color: #333;
            font-size: 20px;
        }
        .stat-box {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .stat-box div {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 10px;
            width: 23%;
            text-align: center;
        }
        .stat-box div span {
            font-size: 20px;
            font-weight: bold;
            display: block;
        }
        .logo-img {
            max-width: 50px;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="logo.png" alt="Logo" class="logo-img"> My Website
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../Dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../profileData/Profile_Card.php?user_id=<?php echo $userId ?> ">Profile</a>
                </li>
                <li class="nav-item">
                <form method="POST">
                            <button class="btn-logout btn btn-danger" name="logout">Logout</button>
                        </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Posts Section -->
<div class="container mt-5">
    <h2 class="mb-4">My Posts</h2>
    <!-- Search Bar -->
    <form id="searchForm" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" id="searchTerm" placeholder="Search by title, description, views, downloads, likes, dislikes">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <!-- Posts will be displayed here -->
    <div id="posts-container">
        <!-- This will be dynamically updated by AJAX -->
        <?php
        // Display all posts initially by default (no search term provided)
        require_once "../class/dbconnection.php";

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['userId'];

        // Fetch username using the user ID
        $db = new db;
        $sqlUsername = "SELECT username FROM users WHERE id = '$userId'";
        $resultU = mysqli_query($db->conn, $sqlUsername);
        $usernameData = mysqli_fetch_assoc($resultU);
        $username = $usernameData['username'];

        // Load all posts for this user by default
        $sql = "SELECT * FROM post WHERE username = '$username'";
        $result = mysqli_query($db->conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "
                <div class='card'>
                    <div class='card-body'>
                        <h4 class='post-title'>" . $row['title'] . "</h4>
                        <p class='card-text'>" . $row['description'] . "</p>
                        <div class='stat-box'>
                            <div><span>" . $row['view_status'] . "</span> Views</div>
                            <div><span>" . $row['download_counts'] . "</span> Downloads</div>
                            <div><span>" . $row['likes_count'] . "</span> Likes</div>
                            <div><span>" . $row['dislikes_count'] . "</span> Dislikes</div>
                        </div>
                    </div>
                </div>
                ";
            }
        } else {
            echo "<div class='card'>
                    <div class='card-body'>
                        <h4 class='post-title'>No posts found</h4>
                    </div>
                  </div>";
        }
        ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- AJAX Script -->
<script>
document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent form submission

    // Get the search term
    const searchTerm = document.getElementById('searchTerm').value;

    // Create an XMLHttpRequest object
    const xhr = new XMLHttpRequest();

    // Configure it: POST-request for the URL /search.php
    xhr.open('POST', 'search.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Send the request over the network
    xhr.send('searchTerm=' + encodeURIComponent(searchTerm));

    // When the request is complete
    xhr.onload = function() {
        if (xhr.status != 200) { // HTTP error?
            console.log('Error: ' + xhr.status); // e.g. 404
            return;
        }

        // Update the posts-container with the response
        document.getElementById('posts-container').innerHTML = xhr.responseText;
    };
});
</script>

</body>
</html>
