<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
require_once "../class/dbconnection.php";
$db = new db;
$postId = $_GET['post_ids'];
$reporterId = $_GET['userId'];
$sql = "SELECT * FROM post WHERE post_id = $postId";
$result = mysqli_query($db->conn, $sql);
// reports queries====================
// $reportComment = "SELECT * FROM reports WHERE user_id ="'. $row2['id'].'";

$view = "UPDATE reports SET viewed = 1 WHERE post_id = $postId";

$viewResult = mysqli_query($db->conn, $view);
if ($result ->num_rows >0) {
    $row = mysqli_fetch_assoc($result);
    $sql2 = "SELECT * FROM users WHERE username = '" . $row['username'] . "'";
    $result1 = mysqli_query($db->conn, $sql2);
   
}
$query = "SELECT * FROM users WHERE id = $reporterId";
$reporterResult = mysqli_query($db->conn, $query);

if ($reporterResult) {
    $reporterRow = mysqli_fetch_assoc($reporterResult);
    
}
$reportComment = "SELECT * FROM reports WHERE user_id = $reporterId";
$reportCommentR = mysqli_query($db->conn,$reportComment);
if ($reportCommentR) {
    $RCR = mysqli_fetch_assoc($reportCommentR);
}
else{
    echo "Cant find report comment";
}

if (isset($_POST['deletePost'])) {
    $sqlDelete = "DELETE FROM post WHERE post_id = $postId";
    $resultDelete = mysqli_query($db->conn, $sqlDelete);
    if ($resultDelete) {
        header("Location: ./adminDashboard.php");
        exit();
    } else {
        echo "Failed to delete post. Please try again.";
    }
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
                        <a class="nav-link" href="../profileData/Profile_Card.php?user_id=<?php echo $_SESSION['userId']?>">Profile</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php  
                 if (isset($result1) && $result1->num_rows > 0) {

                    $row2 = mysqli_fetch_assoc($result1);
                  
                    ?>
                    
                    <div class="card shadow-lg">
                    <div class="card-header bg-dark text-white text-center">
                        <h2>Post Details</h2>
                    </div>
                    <div class="card-body">
                        <!-- Post ID -->
                        <h3 class="card-title">Post ID: <span class="text-info"><?php echo $row['post_id']; ?></span></h3>

                        <!-- User Info -->
                        <div class="d-flex align-items-center my-3">
                           <a href="../profileData/Profile_Card.php?user_id=<?php echo $row2['id'] ?>"> <img src="<?php echo $row2['profilePic']; ?>" alt="user image" class="rounded-circle profile-image"></a>
                            <div>
                                <h4>Posted by: <span class="text-primary"><?php echo $row['username']; ?></span></h4>
                                <p class="text-muted">Posted on: <?php echo $row['created_at']; ?></p>
                            </div>
                        </div>

                        <!-- Post Caption (uncomment if needed) -->
                        <!-- <p><strong>Caption:</strong> <?php echo $row['caption']; ?></p> -->

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="../dashboard/readme.php?post_ids=<?php echo $row['post_id']?>" class="btn btn-warning">View Post</a>
                            <form action="" method="post">
                                <input type="hidden" name="post_id" value="<?php echo $row['post_id']; ?>">
                                <!-- <input type="submit" value="Delete_Post" class="btn btn-danger"> -->
                                <button type="submit" class="btn btn-danger" name="deletePost">Delete Post</button>
                            </form>
                        </div>
                    </div>
                </div>
                    <?php
                }
                else {
                     ?>  <h2 class="text-center text-danger">No Posts Found! Or maybe Deleted already</h2> 
                    <?php

                }
                ?>
                
            </div>
            <div class="col-lg-8 mt-5">
                <div class="card shadow-lg">
                    <div class="card-header bg-dark text-white text-center">
                        <h2>Report Details</h2>
                    </div>
                    <div class="card-body">
                        <!-- Post ID -->
                        <h3 class="card-title">Reporter ID: <span class="text-info"><?php echo $reporterRow['id']; ?></span></h3>

                        <!-- User Info -->
                        <div class="d-flex align-items-center my-3">
                        <a href="../profileData/Profile_Card.php?user_id=<?php echo $reporterRow['id'] ?>">  <img src="<?php echo $reporterRow['profilePic']; ?>" alt="user image" class="rounded-circle profile-image"></a>
                            <div>
                                <h4>Reported by: <span class="text-primary"><?php echo $reporterRow['username']; ?></span></h4>
                                <p class="text-muted">Reported on: <?php echo $reporterRow['created_at']; ?></p>
                            </div>
                        </div>
                        <div class="report-comment mt-4">
                            <h4>Report Comment:</h4>
                            <p><?php echo $RCR['report_comment']; ?></p>
                        </div>

                        <!-- Post Caption (uncomment if needed) -->
                        <!-- <p><strong>Caption:</strong> <?php echo $row['caption']; ?></p> -->

                        <!-- Action Buttons -->
                        <!-- <div class="d-flex justify-content-between mt-4">
                            <a href="editPost.php?post_ids=<?php echo $row['post_id']; ?>" class="btn btn-warning">Edit Post</a>
                            <form action="deletePost.php" method="post">
                                <input type="hidden" name="post_id" value="<?php echo $row['post_id']; ?>">
                                <input type="submit" value="Delete Post" class="btn btn-danger">
                            </form>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5" style="position:relative; bottom: 0; width: 100%;">
        <div class="container">
            <p>&copy; 2024 MyWebsite. All Rights Reserved.</p>
            <p><a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms of Service</a></p>
        </div>
    </footer>

    <!-- Bootstrap 5.0.2 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
