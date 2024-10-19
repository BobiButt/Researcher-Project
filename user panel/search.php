<?php
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

// Search functionality
if (isset($_POST['searchTerm'])) {
    $searchTerm = $_POST['searchTerm'];
    $sql = "SELECT * FROM post WHERE username = '$username' AND (title LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%' OR view_status LIKE '%$searchTerm%' OR download_counts LIKE '%$searchTerm%' OR likes_count LIKE '%$searchTerm%' OR dislikes_count LIKE '%$searchTerm%')";
} else {
    $sql = "SELECT * FROM post WHERE username = '$username'";
}

$result = mysqli_query($db->conn, $sql);

// Check if any posts are found
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
