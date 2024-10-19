<?php
require_once '../class/dbconnection.php';

$action = $_POST['action'] ?? ''; // Check if an action is provided

// Initialize DB class
$db = new db();
$sqlD = "CREATE TABLE IF NOT EXISTS discussions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    post_id INT(11) NOT NULL,
    message TEXT NOT NULL,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_discussion (user_id, post_id, created_at)
);";
$createD = mysqli_query($db->conn, $sqlD);

if ($action === 'load_messages') {
    $userId = $_POST['user_id'];
    $postId = $_POST['post_id'];

    // Fetch all messages related to this post_id, irrespective of the user (to show discussion between multiple users)
    $sql = "SELECT * FROM discussions WHERE post_id = ? ORDER BY created_at ASC";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $userNameSql = "SELECT username FROM users WHERE id = ?";
        $userNameStmt = $db->conn->prepare($userNameSql);
        $userNameStmt->bind_param("i", $row['user_id']);
        $userNameStmt->execute();
        $userNameResult = $userNameStmt->get_result();
        $userNameRow = $userNameResult->fetch_assoc();
        $userName = $userNameRow['username'] ?? 'Unknown User';
        $messageClass = ($row['user_id'] == $userId) ? 'own-message' : 'other-message'; // Class based on user
        echo '<div class="message ' . $messageClass . '">';
        echo '<strong>' . ($row['user_id'] == $userId ? 'You' : ' ' . htmlspecialchars($userName)) . ':</strong><br> ';
        echo htmlspecialchars($row['message']);
        if (!empty($row['file_path'])) {
            echo '<br><a href="'. htmlspecialchars($row['file_path']) .'" target="_blank">Download file</a>';
        }
        echo '<br><small>' . $row['created_at'] . '</small>';
        echo '</div><hr>';
    }
    
    $stmt->close();
    
} elseif ($action === 'send_message') { // When sending a new message
    $userId = $_POST['user_id'];
    $postId = $_POST['post_id'];
    $message = $_POST['message'] ?? '';
    $filePath = '';

    // Handle file upload
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] == 0) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'ppt', 'pptx', 'txt'];
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
    $sql = "INSERT INTO discussions (user_id, post_id, message, file_path) VALUES (?, ?, ?, ?)";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("iiss", $userId, $postId, $message, $filePath);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['status' => 'success']); // Send response to indicate success
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>
