<?php
require_once "./class/dbconnection.php";
class ReactionSystem
{
    public $user_id, $post_id, $reaction ,$followedId;
    // private $conn;

    // Constructor to initialize MySQL connection
    public function __construct()
    {


        // Create tables if they don't exist
        $this->createTables();
    }

    // Create tables for posts and reactions
    private function createTables()
    {
        $db = new db;
        $sql = "CREATE TABLE IF NOT EXISTS post (
            post_id INT(11) AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            filePost VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            likes_count INT DEFAULT 0,
        dislikes_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        // Create post_reactions table
        $createReactionsTable = "CREATE TABLE IF NOT EXISTS post_reactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            post_id INT NOT NULL,
            reaction_type ENUM('like', 'dislike') NOT NULL            
       )";
        $creatPost = mysqli_query($db->conn, $sql);

        // $this->conn->query($createReactionsTable);
        $creat = mysqli_query($db->conn, $createReactionsTable);
    }

    // Handle like or dislike reactions
    public function handleReaction()
    {
        $db = new db;

        // Check if the user already reacted
        $query = "SELECT * FROM post_reactions WHERE user_id = ? AND post_id = ?";
        $stmt = $db->conn->prepare($query);
        $stmt->bind_param('ii', $this->user_id, $this->post_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the existing reaction
            $query = "UPDATE post_reactions SET reaction_type = ? WHERE user_id = ? AND post_id = ?";
            $stmt = $db->conn->prepare($query);
            $stmt->bind_param('sii', $this->reaction, $this->user_id, $this->post_id);
            $stmt->execute();
        } else {
            // Insert a new reaction
            $query = "INSERT INTO post_reactions (user_id, post_id, reaction_type) VALUES (?, ?, ?)";
            $stmt = $db->conn->prepare($query);
            $stmt->bind_param('iis', $this->user_id, $this->post_id, $this->reaction);
            $stmt->execute();
        }

        // Update the likes and dislikes count
        // $this->updatePostCounts($this->post_id);
    }

    // Update the like and dislike counts in the posts table
    public function updatePostCounts()
    {
        $db = new db;

        $updateLikes = "UPDATE post
                        SET likes_count = (SELECT COUNT(*) FROM post_reactions WHERE post_id = ? AND reaction_type = 'like'),
                            dislikes_count = (SELECT COUNT(*) FROM post_reactions WHERE post_id = ? AND reaction_type = 'dislike')
                        WHERE post_id = ?";
        $stmt = $db->conn->prepare($updateLikes);
        $stmt->bind_param('iii', $this->post_id, $this->post_id, $this->post_id);
        $stmt->execute();
    }

    // Get the like and dislike counts for a specific post
    public function getPostCounts()
    {
        $db = new db;

        $query = "SELECT likes_count, dislikes_count FROM post WHERE post_id = ?";
        $stmt = $db->conn->prepare($query);
        $stmt->bind_param('i', $this->post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function loginUserData($loginUserId)
    {
        $db = new db;

        $sql = "SELECT profilePic FROM users WHERE id = '$loginUserId' ";
        $result = mysqli_query($db->conn, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return $row;
        } else {
            echo "cannot get profilePic";
        }
    }
    public function getRole($userId)
    {
        $db = new db;

        $query = "SELECT role FROM users WHERE id = ?";
        $stmt = $db->conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->bind_result($role);
        $stmt->fetch();
        $stmt->close();
        return $role;
    }
    //  follow functions for get info and send info aboyut follow in friend search dashboard
    public function follow($user_id, $followerUsername,$followedId,$followedUsername)
    {
        $db = new db;
    
        // Create the user_follows table if it doesn't exist
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
        $followResult = mysqli_query($db->conn, $followTable);
    
        // Check if the table creation was successful
        if ($followResult) {
            // Check if a follow request already exists
            $checkFollowQuery = "SELECT * FROM user_follows WHERE follower_id = ? AND followed_id = ?";
            $stmt = $db->conn->prepare($checkFollowQuery);
            $stmt->bind_param("ii", $user_id, $followedId); // Assuming both IDs are integers
            $stmt->execute();
            $result = $stmt->get_result();
    
            // If a follow request already exists, do nothing
            if ($result->num_rows > 0) {
                // echo "Follow request already exists.";
    echo '<script>alert("Follow request already exist")</script>';

                return; // Exit the function early
            }
    
            // Proceed to insert a new follow request
            $insertFollowQuery = "INSERT INTO user_follows (follower_id,follower_username, followed_id,followed_username, follow_status) VALUES ( ?, ?, ?, ?, 'pending')";
            $stmt = $db->conn->prepare($insertFollowQuery);
            $stmt->bind_param("isis", $user_id,$followerUsername, $followedId ,$followedUsername); // Bind parameters
            $followResult = $stmt->execute();
    
            if ($followResult) {
                // echo "Following successful";
    echo '<script>alert("Follow successfull")</script>';

            } else {
                // echo "Following failed";
    echo '<script>alert("Follow Failed")</script>';

            }
        } else {
            echo "Failed to create user_follows table.";
        }
    }
    
    public function unfollow()
    {
        $db = new db;
        $follow = "UPDATE user_follows SET follow_status = 'none' WHERE follower_id = '$this->user_id' AND followed_id = '$this->followedId'";
        $unFollowResult = mysqli_query($db->conn,$follow);
        if ($unFollowResult) {
            // echo "UnFollowing successful";
    echo '<script>alert("UNFollow Successfull")</script>';

        } else {
            // echo "UnFollowing failed";
    echo '<script>alert("UNFollow Failed")</script>';

        }
    }
    // public function getFollowStatus() {
    //     $db = new db;
    //     // Check if the user already reacted
    //     $query = "SELECT follow_status FROM user_follows WHERE follower_id =? AND followed_id =?";
    //     $stmt = $db->conn->prepare($query);
    //     $stmt->bind_param('ii', $this->user_id, $this->followedId);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     if ($result->num_rows > 0) {
    //         return mysqli_fetch_assoc($result)['follow_status'];
    //     } else {
    //         // return "not_following";
    //         echo "Error preparing statement: " . $db->conn->error;
    //     }
    // }
    public function getFollowStatus() {
        $db = new db;
        // Check if the user already reacted
        $query = "SELECT follow_status FROM user_follows WHERE follower_id = ? AND followed_id = ?";
        $stmt = $db->conn->prepare($query);
        
        if ($stmt === false) {
            // If the statement couldn't be prepared, echo the error
            echo "Error preparing statement: " . $db->conn->error;
            return null;
        }
       
    
        $stmt->bind_param('ii', $this->user_id, $this->followedId);
        $stmt->execute();
        
        // Fetch the result
        $result = $stmt->get_result();
        
        if ($result === false) {
            // If there is an error in the execution, echo the error
            echo "Error executing query: " . $stmt->error;
            return null;
        }
    
        // Check if there is a follow status
        if ($result->num_rows > 0) {
            // Fetch the follow_status correctly
            return $result->fetch_assoc()['follow_status'];
        } else {
            // Echo an error message if no record is found
            echo "No follow record found.";
            return "not_following";
        }
    }
    
}
