<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
require_once "../class/dbconnection.php";
$db = new db;
$userId = $_SESSION['userId'];

if (!isset($_SESSION['login']) || $_SESSION['login'] == '0') {  // Check if the login status is not set or is '0'
    header("Location: ../login.php");
    exit();
} else {
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
    <title>Responsive UI</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Logo</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../Researcher/researcher.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../profileData/Profile_Card.php?user_id=<?php echo $userId; ?>">Profile</a>
                </li>
                <li class="nav-item">
                    <form class="navbar-nav" method="POST">
                        <button class="btn btn-danger py-1 px-2 text-white" name="logout">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" name="search" id="searchInput" placeholder="Search by Title or Date" aria-label="Search" oninput="searchProjects()">
        </form>
    </nav>

    <!-- Main Content Section: Projects Display -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Your Collaborative Projects</h2>
        
        <!-- Projects Container -->
        <div class="row" id="projectsContainer">
            <!-- Projects will be loaded here via AJAX -->
        </div>
    </div>

    <script>
        function searchProjects() {
            let searchTerm = document.getElementById('searchInput').value;

            // Create an AJAX request
            let xhr = new XMLHttpRequest();
            xhr.open('GET', 'search_projects.php?search=' + searchTerm, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Update the projects container with the returned HTML
                    document.getElementById('projectsContainer').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Trigger an initial search to load all projects on page load
        window.onload = searchProjects;
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
