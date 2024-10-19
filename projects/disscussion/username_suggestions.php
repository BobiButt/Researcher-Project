<?php
require_once '../../class/dbconnection.php';
$db = new db();

if (isset($_POST['query'])) {
    $searchQuery = $_POST['query'];
    
    $sql = "SELECT username FROM users WHERE username LIKE ? LIMIT 5";
    $stmt = $db->conn->prepare($sql);
    $searchTerm = $searchQuery . '%'; // Wildcard search
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = '';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $suggestions .= '<div class="suggestion-item" style="padding: 5px; cursor: pointer;">' . htmlspecialchars($row['username']) . '</div>';
        }
    } else {
        $suggestions = '<div class="suggestion-item" style="padding: 5px;">No suggestions found</div>';
    }

    echo $suggestions;
}
