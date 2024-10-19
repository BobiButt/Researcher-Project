<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
// Database connection
$userId = $_SESSION['userId']; // get from login page
include('../class/dbconnection.php'); // Ensure you include your database connection file
$db = new db;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['on_like'])) {
    $post_Id = $_POST['post_id'];
    $user_Id = $_POST['user_id'];

    $dashboardClass->post_id = $post_Id;
    $dashboardClass->reaction = 'like';
    $dashboardClass->user_id = $userId;
    // echo '<script>alert("i am brand")</script>';


    // Like button was clicked
    $dashboardClass->handleReaction();
    $updateReaction = $dashboardClass->updatePostCounts($post_Id);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['on_dislike'])) {
    $post_Id = $_POST['post_id'];
    $user_Id = $_POST['user_id'];

    $dashboardClass->post_id = $post_Id;
    $dashboardClass->reaction = 'dislike';
    $dashboardClass->user_id = $userId;
    // echo '<script>alert("i am brand")</script>';


    // Like button was clicked
    $dashboardClass->handleReaction();
    $updateReaction = $dashboardClass->updatePostCounts($post_Id);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['readMore'])) {
    $_SESSION['postUserName'] = $_POST['username'];
    $_SESSION['postId'] = $_POST['post_id'];
    //   header('location : ./dashboard/readme.php');
    echo '<script type="text/javascript">
        window.location.href = "./dashboard/readme.php"; // Redirect to the desired page
      </script>';
    exit; // Stop further script execution
}


if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];

    // Escape special characters for SQL query
    $search = mysqli_real_escape_string($db->conn, $search);

    // Query to search posts and users
    $searchQuery = "
        SELECT post.*, users.username
        FROM post
        JOIN users ON post.username = users.username
        WHERE post.title LIKE '%$search%'
        OR users.username LIKE '%$search%'
        OR post.category LIKE '%$search%'
        OR post.created_at LIKE '%$search%'
    ";

    $searchResult = mysqli_query($db->conn, $searchQuery);
    
    if (mysqli_num_rows($searchResult) > 0) {
        while ($row = mysqli_fetch_assoc($searchResult)) {
            // Output the card structure
            echo '<div class="card">';
            echo '<div class="title">' . htmlspecialchars($row['title']) . '</div>';
            echo '<div class="username">by ' . htmlspecialchars($row['username']) . '</div>';
            echo '<div class="category">' . htmlspecialchars($row['category']) . '</div>';
            echo '<div class="created-at">' . htmlspecialchars($row['created_at']) . '</div>';
            echo '<div style="display: flex; justify-content: space-between;">';
            echo '<form action="" method="post">';
            echo '<input type="hidden" name="username" value="' . htmlspecialchars($row['username']) . '">';
            echo '<input type="hidden" name="post_id" value="' . htmlspecialchars($row['post_id']) . '">';
            echo '<button name="readMore" class="read_more">Read More</button>';
            echo '</form>';
            echo '<a href="./filesUpload/' . htmlspecialchars($row['filePost']) . '" class="readme-link">Download File</a>';
            echo '</div>';
            echo '<form action="" method="post">';
            echo '<div class="buttons">';
            echo '<input type="hidden" name="post_id" value="' . htmlspecialchars($row['post_id']) . '">';
            echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($userId) . '">';
            echo '<button id="like" name="on_like" type="submit">Like</button>';
            echo '<button id="dislike" name="on_dislike" type="submit">Dislike</button>';
            echo '</div>';
            echo '</form>';
            echo '<div style="display: flex; justify-content: space-between;">';
            echo '<small>Total likes: ' . htmlspecialchars($row['likes_count']) . '</small>';
            echo '<small>Total dislikes: ' . htmlspecialchars($row['dislikes_count']) . '</small>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No results found.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <script>
         document.addEventListener('DOMContentLoaded', function() {
            // Select all like and dislike buttons
            const likeButtons = document.querySelectorAll('.like');
            const dislikeButtons = document.querySelectorAll('.dislike');

            // Add event listener for like buttons
            likeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Disable like button, enable dislike button
                    this.style.backgroundColor = 'black';
                    this.disabled = true;

                    const dislikeButton = this.closest('.buttons').querySelector('.dislike');
                    dislikeButton.style.backgroundColor = '#f44336';
                    dislikeButton.disabled = false;
                });
            });

            // Add event listener for dislike buttons
            dislikeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Disable dislike button, enable like button
                    this.style.backgroundColor = 'black';
                    this.disabled = true;

                    const likeButton = this.closest('.buttons').querySelector('.like');
                    likeButton.style.backgroundColor = '#4CAF50';
                    likeButton.disabled = false;
                });
            });
        });
    </script>
</body>
</html>
