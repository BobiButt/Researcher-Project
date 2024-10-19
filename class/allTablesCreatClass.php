<?php
require_once "dbconnection.php";
class tableCreation
{
    public function __construct()
    {
        $db = new db;

        // users table

        $user = "CREATE TABLE IF NOT EXISTS users (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL,
            profilePic VARCHAR(255) DEFAULT NULL,
            coverPic VARCHAR(255) DEFAULT NULL,
            bio TEXT DEFAULT NULL,            -- Field to store user bio
            location VARCHAR(255) DEFAULT NULL, -- Field to store user location
            website VARCHAR(255) DEFAULT NULL,  -- Field to store user website
            caption TEXT DEFAULT NULL,         -- Field to store profile caption
            affiliations TEXT DEFAULT NULL,    -- Field to store user affiliations (JSON or comma-separated)
            researchInterest TEXT DEFAULT NULL, -- Field to store user research interests (JSON or comma-separated)
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ban_until DATETIME NULL            -- Field to store ban expiration date
        )";
        
        $userResult = mysqli_query($db->conn, $user);
        
        // post table
        $post = "CREATE TABLE IF NOT EXISTS post (
        post_id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        filePost VARCHAR(255) NOT NULL,
        role VARCHAR(50) NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        likes_count INT DEFAULT 0,
       is_public VARCHAR(10) DEFAULT 'public'; -- Correct way to set default value as a string

dislikes_count INT DEFAULT 0,
view_status INT DEFAULT 0, -- New field to track views
download_counts INT DEFAULT 0, -- New field to track Downloads
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
        $postResult = mysqli_query($db->conn, $post);

        // post reaction table

        $createReactionsTable = "CREATE TABLE IF NOT EXISTS post_reactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        post_id INT NOT NULL,
        reaction_type ENUM('like', 'dislike') NOT NULL            
   )";
        $creatPost = mysqli_query($db->conn, $createReactionsTable);

        // folllow table

        $follow = "CREATE TABLE IF NOT EXISTS user_follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
        follower_username VARCHAR(255) NOT NULL,
    followed_id INT NOT NULL,
        followed_username VARCHAR(255) NOT NULL,

    follow_status ENUM('pending', 'accepted', 'rejected','None') DEFAULT 'None',
    follow_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (follower_id, followed_id)
)";
        $followResult = mysqli_query($db->conn, $follow);

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


                $sqlPostView = "CREATE TABLE IF NOT EXISTS post_views (
                    view_id INT AUTO_INCREMENT PRIMARY KEY,
                    post_id INT NOT NULL,
                    user_id INT NOT NULL,
                    view_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_view (post_id, user_id) -- Ensures that each user can only view a post once
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                $createPostViewsTable = mysqli_query($db->conn, $sqlPostView);

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

                $projectTable = "CREATE TABLE projects (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NOT NULL,
                    projectPost VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    duration_days INT NOT NULL,  -- Number of days for project duration
                    owner_id INT,
                    owner_name VARCHAR(50),
                    FOREIGN KEY (owner_id) REFERENCES users(id)
                )";
                $createProject = mysqli_query($db->conn, $projectTable);

                $tagNotify = "CREATE TABLE notifications (
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

                $memberSql = "CREATE TABLE IF NOT EXISTS project_collaborators (
                    id INT(11) AUTO_INCREMENT PRIMARY KEY,
                    project_id INT(11) NOT NULL,
                    user_id INT(11) NOT NULL,
                    role ENUM('admin', 'member') DEFAULT 'member', -- Role in the project
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    
                );
                ";
                $member = mysqli_query($db->conn,$memberSql);

    }
}
