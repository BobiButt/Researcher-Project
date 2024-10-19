<?php

ob_start(); // Start output buffering
require_once '../../class/dbconnection.php';

$action = $_POST['action'] ?? '';

// Initialize DB class
$db = new db();
// Create tables if not exists...


$sqlD = "CREATE TABLE IF NOT EXISTS projectDiscussions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    post_id INT(11) NOT NULL,
    message TEXT NOT NULL,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_discussion (user_id, post_id, created_at)
);";
$createD = mysqli_query($db->conn, $sqlD);
$tagNotify = "CREATE TABLE IF NOT EXISTS notifications (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL, -- The user being notified
    sender_id INT(11),        -- The user who sent the tag
    post_id INT(11) NOT NULL, -- The post in which the user is tagged
    message VARCHAR(255) NOT NULL, -- The notification message
     link VARCHAR(255), -- Optional link to the discussion
    is_read TINYINT(1) DEFAULT 0,  -- 0 for unread, 1 for read
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";
$createTagNotify = mysqli_query($db->conn, $tagNotify);

if ($action === 'load_messages') {
    // Load messages logic...
    $userId = $_POST['user_id'];
    $postId = $_POST['post_id'];

    // Fetch all messages related to this post_id, irrespective of the user (to show discussion between multiple users)
    $sql = "SELECT * FROM projectDiscussions WHERE post_id = ? ORDER BY created_at ASC";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();

    $messagesHtml = ''; // Initialize variable to hold messages HTML
    while ($row = $result->fetch_assoc()) {
        $userNameSql = "SELECT username FROM users WHERE id = ?";
        $userNameStmt = $db->conn->prepare($userNameSql);
        $userNameStmt->bind_param("i", $row['user_id']);
        $userNameStmt->execute();
        $userNameResult = $userNameStmt->get_result();
        $userNameRow = $userNameResult->fetch_assoc();
        $userName = $userNameRow['username'] ?? 'Unknown User';
        $messageClass = ($row['user_id'] == $userId) ? 'own-message' : 'other-message'; // Class based on user
        $messagesHtml .= '<div class="message ' . $messageClass . '">';
        $messagesHtml .= '<strong>' . ($row['user_id'] == $userId ? 'You' : ' ' . htmlspecialchars($userName)) . ':</strong><br> ';
        $messagesHtml .= htmlspecialchars($row['message']);
        if (!empty($row['file_path'])) {
            $messagesHtml .= '<br><a href="' . htmlspecialchars($row['file_path']) . '" target="_blank">Download file</a>';
        }
        $messagesHtml .= '<br><small>' . $row['created_at'] . '</small>';
        $messagesHtml .= '</div><hr>';
    }
    echo $messagesHtml; // Output the messages HTML
} elseif ($action === 'send_message') {
    // Message sending logic...
    $userId = $_POST['user_id'];
    $postId = $_POST['post_id'];
    $message = $_POST['message'] ?? '';
    $filePath = '';

    // Detect and notify tagged users
    if (preg_match_all('/@(\w+)/', $message, $matches)) {
        $taggedUsers = $matches[1];
        foreach ($taggedUsers as $taggedUser) {
            // Notify logic...
            // Get the user ID for the tagged user
            $sql = "SELECT id FROM users WHERE username = ?";
            $stmt = $db->conn->prepare($sql);
            $stmt->bind_param("s", $taggedUser);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $userRow = $result->fetch_assoc();
                $taggedUserId = $userRow['id'];

                // Insert a notification for the tagged user
                $notificationMessage = "You were mentioned in a post by @" . $taggedUser;
                $sql = "INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)";
                $stmt = $db->conn->prepare($sql);
                $notificationLink = 'discussion_page.php?post_id=' . $postId; // Link to the discussion
                $stmt->bind_param("iss", $taggedUserId, $notificationMessage, $notificationLink);
                $stmt->execute();
            }
        }
    }

    // Handle file upload...
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] == 0) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'ppt', 'pptx', 'txt','zip'];
        $fileInfo = pathinfo($_FILES['fileToUpload']['name']);
        $fileExtension = strtolower($fileInfo['extension']);

        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadDir = './uploads/';
            $filePath = $uploadDir . uniqid() . '_' . basename($_FILES['fileToUpload']['name']);

            if (!move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $filePath)) {
                $filePath = ''; // Reset file path if upload fails
            }
        }
    }

    // Insert message into discussions table
    try {
        // Insert message into discussions table
        $message = str_replace(array(',', '_'), array('&#44;', '&#95;'), $message);
        $sql = "INSERT INTO projectDiscussions (user_id, post_id, message, file_path) VALUES (?, ?, ?, ?)";
        $stmt = $db->conn->prepare($sql);
        $stmt->bind_param("iiss", $userId, $postId, $message, $filePath);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

}
else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

ob_end_flush(); // Flush the output buffer
