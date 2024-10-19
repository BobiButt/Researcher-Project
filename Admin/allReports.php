<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
require_once "../class/dbconnection.php";
$db = new db;

// Search functionality
if (isset($_POST['search'])) {
    $searchTerm = $_POST['searchTerm'];
    $sql = "SELECT * FROM reports WHERE report_comment LIKE '%$searchTerm%' OR post_id LIKE '%$searchTerm%' OR user_id LIKE '%$searchTerm%'";
    $result = mysqli_query($db->conn, $sql);
} else {
    $sql = "SELECT * FROM reports";
    $result = mysqli_query($db->conn, $sql);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details</title>
    <!-- Bootstrap 5.0.2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">MyWebsite</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./adminDashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../profileData/Profile_Card.php?user_id=">Profile</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 mt-5" style="max-height: 400px; overflow-y: scroll;">
                <div class="card shadow-lg">
                    <div class="card-header bg-dark text-white text-center">
                        <h2>All Reports</h2>
                    </div>
                    <div class="card-body">
                        <!-- Search Bar -->
                        <form action="" method="post" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="searchTerm" class="form-control" placeholder="Search...">
                                <button type="submit" name="search" class="btn btn-primary">Search</button>
                            </div>
                        </form>
                        <!-- Post ID -->
                        <ul style="list-style: none;">
                            <?php
                            $num = 1;
                            if ($result->num_rows > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                   <a href="./reports.php?post_ids=<?php echo $row['post_id']; ?>&userId=<?php echo $row['user_id']; ?>" 
                   style="font-weight: bold; color:gray; text-decoration: none;padding:5px 0px">
                   <li style="font-weight: bold; font-size: large;"><?php echo $num?>) <?php echo $row['username']; ?> reported his <?php echo $row['post_id']; ?> post that <?php echo $row['report_comment']; ?></li>
                   
                </a>
                                    <?php
                                    $num ++;
                                }
                            } else {
                                ?>
                                <li>No reports found.</li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5" style="position:absolute; bottom: 0; width: 100%;">
        <div class="container">
            <p>&copy; 2024 MyWebsite. All Rights Reserved.</p>
            <p><a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms of Service</a></p>
        </div>
    </footer>

    <!-- Bootstrap 5.0.2 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
