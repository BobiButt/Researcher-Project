<?php
// Assuming your session is already started elsewhere
require '../class/dbconnection.php'; // Database connection
require '../class/readmeClass.php'; // Load your class file where downloadCounts is defined

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id']) && isset($_POST['post_id'])) {
    $userId = intval($_POST['user_id']);
    $postId = intval($_POST['post_id']);
    
    // Instantiate your class
    $myClass = new readme(); 

    // Call your function to increment the download count
    $downloadCounts = $myClass->downloadCounts($userId, $postId);

    echo "Download count updated"; // Optionally send a response back to JS
}
?>
