<?php
require_once "dbconnection.php";
class readme {
    public $postId,$username,$userId,$postUserId,$followedUsername,$followerUsername,$comment;

    public function __construct()
    {
        $db = new db;

        $followTable = "CREATE TABLE IF NOT EXISTS user_follows (
            id INT AUTO_INCREMENT PRIMARY KEY,
            follower_id INT NOT NULL,
                follower_username VARCHAR(255) NOT NULL,
            followed_id INT NOT NULL,
                followed_username VARCHAR(255) NOT NULL,
        
            follow_status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
            follow_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE (follower_id, followed_id)
        )";
$followResult = mysqli_query($db->conn,$followTable);
    }
   
    public function postData()
    {
        $db = new db;
        $sql = "SELECT * FROM post WHERE post_id = '$this->postId'";

        $result = mysqli_query($db->conn , $sql);
        return  mysqli_fetch_assoc($result);
    }
    public function postUserData()
    {
        $db = new db;

        $sql2 = "SELECT id,profilePic FROM users WHERE username = '$this->username'";
        $result2 = mysqli_query($db->conn , $sql2);
        return  mysqli_fetch_assoc($result2);
    }
    public function loginUserData()
    {
        $db = new db;

        $sql2 = "SELECT username FROM users WHERE id = '$this->userId'";
        $result2 = mysqli_query($db->conn , $sql2);
        $loginUserId =  mysqli_fetch_assoc($result2);
        return $loginUserId['username'];
    }


    public function fetchUserId()
    {
        $db = new db;

        // Ensure $this->postId is not empty
       
              
        
                    // Query the users table for the ID
                    $userTable = "SELECT id FROM users WHERE username = ?";
                    $stmt2 = $db->conn->prepare($userTable);
                    
                    if ($stmt2) {
                        $stmt2->bind_param("s", $this->username);  // assuming username is a string
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
        
                        // Check if the user ID is found
                        if ($result2 && $result2->num_rows > 0) {
                            $row2 = $result2->fetch_assoc();
                            return $row2['id'];
                        } else {
                            echo "User not found for username: " . htmlspecialchars($this->username);
                        }
                    } else {
                        echo "Failed to prepare user query.";
                    }
               


    }



    public function follow()
    {
        $db = new db;
    
        // Create the user_follows table if it doesn't exist
        $followTable = "CREATE TABLE IF NOT EXISTS user_follows (
            id INT AUTO_INCREMENT PRIMARY KEY,
            follower_id INT NOT NULL,
                follower_username VARCHAR(255) NOT NULL,
            followed_id INT NOT NULL,
                followed_username VARCHAR(255) NOT NULL,
        
            follow_status ENUM('pending', 'accepted', 'rejected','None') DEFAULT 'None',
            follow_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE (follower_id, followed_id)
        )";
        $followResult = mysqli_query($db->conn, $followTable);
    
        // Check if the table creation was successful
        if ($followResult) {
            // Check if a follow request already exists
            $checkFollowQuery = "SELECT * FROM user_follows WHERE follower_id = ? AND followed_id = ?";
            $stmt = $db->conn->prepare($checkFollowQuery);
            $stmt->bind_param("ii", $this->userId, $this->postUserId); // Assuming both IDs are integers
            $stmt->execute();
            $result = $stmt->get_result();
    
            // If a follow request already exists, do nothing
            if ($result->num_rows > 0) {
                echo "Follow request already exists.";
                return; // Exit the function early
            }
    
            // Proceed to insert a new follow request
            $insertFollowQuery = "INSERT INTO user_follows (follower_id,follower_username, followed_id,followed_username, follow_status) VALUES ( ?, ?, ?, ?, 'pending')";
            $stmt = $db->conn->prepare($insertFollowQuery);
            $stmt->bind_param("isis", $this->userId,$this->followerUsername, $this->postUserId ,$this->followedUsername); // Bind parameters
            $followResult = $stmt->execute();
    
            if ($followResult) {
                echo "Following successful";
            } else {
                echo "Following failed";
            }
        } else {
            echo "Failed to create user_follows table.";
        }
    }
    
    public function unfollow()
    {
        $db = new db;
        $follow = "UPDATE user_follows SET follow_status = 'None' WHERE follower_id = '$this->userId' AND followed_id = '$this->postUserId'";
        $unFollowResult = mysqli_query($db->conn,$follow);
        if ($unFollowResult) {
            echo "UnFollowing successful";
        } else {
            echo "UnFollowing failed";
        }
    }
    public function getFollowStatus() {
        $db = new db;
        // Check if the user already reacted
        $query = "SELECT follow_status FROM user_follows WHERE follower_id =? AND followed_id =?";
        $stmt = $db->conn->prepare($query);
        $stmt->bind_param('ii', $this->userId, $this->postUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return mysqli_fetch_assoc($result)['follow_status'];
        } else {
            return "not_following";
        }
    }

    public function commentUpload()
    {
        $db = new db;

        $comment = "CREATE TABLE IF NOT EXISTS comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            from_id INT,
            `from` VARCHAR(255) NOT NULL,
            to_id INT,
            `to` VARCHAR(255) NOT NULL,
            comment TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                $creatCommentTable = mysqli_query($db->conn, $comment);
                if ($creatCommentTable) {
                    $this->comment = htmlspecialchars($this->comment, ENT_QUOTES, 'UTF-8');
                    // Prepare the insert query
                    $insertCommentQuery = "INSERT INTO comments (from_id, `from`, to_id, `to`, comment) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $db->conn->prepare($insertCommentQuery);
                
                    // Check if the statement was prepared correctly
                    if ($stmt) {
                        // Bind the parameters
                        $stmt->bind_param("isiss", $this->userId, $this->loginUserData(), $this->postUserId, $this->followedUsername, $this->comment);
                
                        // Execute the statement
                        if ($stmt->execute()) {
                            // Comment inserted successfully
                            echo json_encode(['status' => 'success', 'message' => 'Comment added successfully!']);
                        } else {
                            // Handle error during execution
                            echo json_encode(['status' => 'error', 'message' => 'Error inserting comment: ' . $stmt->error]);
                        }
                
                        // Close the statement
                        $stmt->close();
                    } else {
                        // Handle error in preparing the statement
                        echo json_encode(['status' => 'error', 'message' => 'Error preparing statement: ' . $db->conn->error]);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error creating comments table.']);
                }
                
    }

    public function getComments($as)
{
    $db = new db;
// $as = '15';
    $query = "SELECT * FROM comments WHERE to_id = ? ORDER BY created_at DESC";
    $stmt = $db->conn->prepare($query);
    $stmt->bind_param("i", $as);
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result;
}

public function report($userId,$postId,$postUsername,$reportComment)
{
    $db = new db;

                // Create a table for reports
                $report = "CREATE TABLE IF NOT EXISTS reports (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    username VARCHAR(255) NOT NULL,
                    post_id INT NOT NULL,
                    post_username VARCHAR(255) NOT NULL,
                    report_comment TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    viewed INT NOT NULL DEFAULT 0
                )CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                $creatReportTable = mysqli_query($db->conn, $report);
                if ($creatReportTable) {
                    $reportComment = htmlspecialchars($reportComment, ENT_QUOTES, 'UTF-8');
                    // Prepare the insert query
                    $insertReportQuery = "INSERT INTO reports (user_id,username, post_id,post_username, report_comment) VALUES (?, ?, ?,?,?)";
                    $stmt = $db->conn->prepare($insertReportQuery);
                
                    // Check if the statement was prepared correctly
                    if ($stmt) {
                        // Bind the parameters
                        $stmt->bind_param("isiss", $userId,$this->loginUserData(), $postId,$postUsername, $reportComment);
                
                        // Execute the statement
                        if ($stmt->execute()) {
                            // Report inserted successfully
                            echo json_encode(['status' => 'success', 'message' => 'Report added successfully!']);
                            return true;
                        } else {
                            // Handle error during execution
                            echo json_encode(['status' => 'error', 'message' => 'Error inserting report: ' . $stmt->error]);
                        }
                
                        // Close the statement
                        $stmt->close();
                    } else {
                        // Handle error in preparing the statement
                        echo json_encode(['status' => 'error', 'message' => 'Error preparing statement: ' . $db->conn->error]);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error creating reports table.']);
                }
// echo '<script>alert("report function working")</script>';
}
public function postView($userId,$postId)
{
    $db = new db;
    $sql = "CREATE TABLE IF NOT EXISTS post_views (
        view_id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        view_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_view (post_id, user_id) -- Ensures that each user can only view a post once
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $createPostViewsTable = mysqli_query($db->conn, $sql);
    if ($createPostViewsTable) {
        $checkViewQuery = "SELECT * FROM post_views WHERE post_id = ? AND user_id = ?";
$stmt = $db->conn->prepare($checkViewQuery);
$stmt->bind_param("ii", $postId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If no record exists, the user has not viewed this post yet, so increment the view count
    $updateViewStatusQuery = "UPDATE post SET view_status = view_status + 1 WHERE post_id = ?";
    $stmtUpdate = $db->conn->prepare($updateViewStatusQuery);
    $stmtUpdate->bind_param("i", $postId);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    // Also, insert a record into the post_views table to track that this user has viewed this post
    $insertViewQuery = "INSERT INTO post_views (post_id, user_id) VALUES (?, ?)";
    $stmtInsert = $db->conn->prepare($insertViewQuery);
    $stmtInsert->bind_param("ii", $postId, $userId);
    $stmtInsert->execute();
    $stmtInsert->close();
}

    } else {
        // Handle error in creating the table
        echo json_encode(['status' => 'error', 'message' => 'Error creating post views table: ' . $db->conn->error]);
    }

}
public function downloadCounts($userId,$postId)
{
    $db = new db;

    $sql = "CREATE TABLE IF NOT EXISTS post_downloads (
        download_id INT(11) AUTO_INCREMENT PRIMARY KEY,
        post_id INT(11) NOT NULL,
        user_id INT(11) NOT NULL,
        download_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_download (post_id, user_id) -- Ensures that each user can only download a post once
    )";
     $createPostDownloadTable = mysqli_query($db->conn, $sql);
     // First, check if the user has already downloaded this post
$checkDownloadQuery = "SELECT * FROM post_downloads WHERE post_id = ? AND user_id = ?";
$stmt = $db->conn->prepare($checkDownloadQuery);
$stmt->bind_param("ii", $postId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If no record exists, the user has not downloaded this post yet, so increment the download count
    $updateDownloadStatusQuery = "UPDATE post SET download_counts = download_counts + 1 WHERE post_id = ?";
    $stmtUpdate = $db->conn->prepare($updateDownloadStatusQuery);
    $stmtUpdate->bind_param("i", $postId);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    // Also, insert a record into the post_downloads table to track that this user has downloaded this post
    $insertDownloadQuery = "INSERT INTO post_downloads (post_id, user_id) VALUES (?, ?)";
    $stmtInsert = $db->conn->prepare($insertDownloadQuery);
    $stmtInsert->bind_param("ii", $postId, $userId);
    $stmtInsert->execute();
    $stmtInsert->close();
}

}
};


?>