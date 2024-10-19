<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}

require_once "dbconnection.php";

class researcherClass
{
    public $username, $filePost, $role, $title, $description,$category,$time;

    // Pass the form data and file to this method
    public function post($file)
    {
        $db = new db;
        $this->username = $_SESSION['username'];
        $this->role = $_SESSION['role']; // Assuming you have a role in the session as well

        // File upload logic
        if (isset($file) && $file['error'] == 0) {
            $targetDir = "../filesUpload/"; // Directory where files will be uploaded
            $fileNamee = basename($file['name']);
            $fileExt = strtolower(pathinfo($fileNamee, PATHINFO_EXTENSION)); // Get the file extension
            $fileName = uniqid() . '.' . $fileExt; // Generate unique name by appending file extension

            $targetFile = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Allow only certain file formats (you can customize this)
            $allowedTypes = ['jpg', 'png', 'pdf', 'docx', 'txt'];
            if (in_array($fileType, $allowedTypes)) {
                // Check if the file already exists
                // if (!file_exists($targetFile)) {
                // Try to move the uploaded file to the target directory
                if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                    // Store the file path
                    $this->filePost = $targetFile;

                    // Create the table if it doesn't exist
                    $sql = "CREATE TABLE IF NOT EXISTS post (
                        post_id INT(11) AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(255) NOT NULL,
                        filePost VARCHAR(255) NOT NULL,
                        role VARCHAR(50) NOT NULL,
                        title VARCHAR(255) NOT NULL,
                        category VARCHAR(255) NOT NULL,
                        description TEXT NOT NULL,
                        likes_count INT DEFAULT 0,
                         is_public VARCHAR(10) DEFAULT 'public'; -- Correct way to set default value as a string
                        dislikes_count INT DEFAULT 0,
                        view_status INT DEFAULT 0, -- New field to track views
                        download_counts INT DEFAULT 0, -- New field to track Downloads

                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )";
            

                    if ($db->conn->query($sql) === TRUE) {
                        // Insert data into the table
                        $queryInsert = "INSERT INTO post (username, filePost, role, title,category, description) VALUES (?, ?, ?, ?, ?, ?)";

                        if ($stmtInsert = $db->conn->prepare($queryInsert)) {
                            // Bind parameters (s = string)
                            $stmtInsert->bind_param("ssssss", $this->username, $this->filePost, $this->role, $this->title,$this->category, $this->description);

                            // Execute the statement
                            if ($stmtInsert->execute()) {
                                return true;
                            } else {
                                echo "Error inserting data: " . $stmtInsert->error;
                            }

                            // Close the insert statement
                            $stmtInsert->close();
                        } else {
                            echo "Error preparing the insert query: " . $db->conn->error;
                        }
                    } else {
                        echo "Error creating table: " . $db->conn->error;
                    }
                } else {
                    echo "Error uploading the file.";
                }
                // } else {
                //     echo "File already exists.";
                // }
            } else {
                echo "File type not allowed. Only JPG, PNG, PDF, DOCX, and TXT are accepted.";
            }
        } else {
            echo "No file uploaded or an error occurred.";
        }
    }
    public function postR($file)
    {
        $db = new db;
        $this->username = $_SESSION['username'];
        $this->role = $_SESSION['role']; // Assuming you have a role in the session as well
$ownerId = $_SESSION['userId'];
        // File upload logic
        if (isset($file) && $file['error'] == 0) {
            $targetDir = "../projectUpload/"; // Directory where files will be uploaded
            $fileNamee = basename($file['name']);
            $fileExt = strtolower(pathinfo($fileNamee, PATHINFO_EXTENSION)); // Get the file extension
            $fileName = uniqid() . '.' . $fileExt; // Generate unique name by appending file extension

            $targetFile = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Allow only certain file formats (you can customize this)
            $allowedTypes = ['jpg', 'png', 'pdf', 'docx', 'txt'];
            if (in_array($fileType, $allowedTypes)) {
                // Check if the file already exists
                // if (!file_exists($targetFile)) {
                // Try to move the uploaded file to the target directory
                if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                    // Store the file path
                    $this->filePost = $targetFile;

                    // Create the table if it doesn't exist
                    $sql = "CREATE TABLE projects (
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
                       

                    if ($db->conn->query($sql) === TRUE) {
                        // Insert data into the table
                        $queryInsert = "INSERT INTO projects (owner_id,owner_name, projectPost, title,duration_days, description) VALUES (?, ?, ?, ?, ?, ?)";

                        if ($stmtInsert = $db->conn->prepare($queryInsert)) {
                            // Bind parameters (s = string)
                            $stmtInsert->bind_param("isssis",$ownerId, $this->username, $this->filePost, $this->title,$this->time, $this->description);

                            // Execute the statement
                            if ($stmtInsert->execute()) {
                                return true;
                            } else {
                                echo "Error inserting data: " . $stmtInsert->error;
                            }

                            // Close the insert statement
                            $stmtInsert->close();
                        } else {
                            echo "Error preparing the insert query: " . $db->conn->error;
                        }
                    } else {
                        echo "Error creating table: " . $db->conn->error;
                    }
                } else {
                    echo "Error uploading the file.";
                }
                // } else {
                //     echo "File already exists.";
                // }
            } else {
                echo "File type not allowed. Only JPG, PNG, PDF, DOCX, and TXT are accepted.";
            }
        } else {
            echo "No file uploaded or an error occurred.";
        }
    }
    public function followRequest($userId)
    {
        $db = new db;
        $sql = "SELECT * FROM user_follows WHERE followed_id = '$userId' AND follow_status = 'pending'";
        $resultFollowRequest = $db->conn->query($sql);

        if ($resultFollowRequest->num_rows > 0) {
           
            return $resultFollowRequest;
        } else {
            
            return false;
        }
    }
    public function followRequestAccept($userId)
    {
        $db = new db;
        $sql = "SELECT * FROM user_follows WHERE followed_id = '$userId' AND follow_status = 'accepted'";
        $resultFollowRequestAccept = $db->conn->query($sql);

        if ($resultFollowRequestAccept->num_rows > 0) {
          
            return $resultFollowRequestAccept;
        } else {
           
            return false;
        }
    }
    public function followAccept($follower_id, $followed_id)
    {
        $db = new db;

        // The corrected SQL query
        $query = "UPDATE user_follows SET follow_status = ? WHERE follower_id = ? AND followed_id = ?";

        // Prepare the statement
        $stmt = mysqli_prepare($db->conn, $query);

        // Assuming $status, $follower_id, and $followed_id are your variables
        $status = 'accepted';  // For example, setting status to 1


        // Bind the parameters (status as integer 'i', follower_id as integer 'i', and followed_id as integer 'i')
        mysqli_stmt_bind_param($stmt, 'sii', $status, $follower_id, $followed_id);

        // Execute the statement
        $result = mysqli_stmt_execute($stmt);

        // Check if the query was successful
        if ($result) {
            // echo "Update successful!";
        } else {
            echo "Error updating record: " . mysqli_stmt_error($stmt);
        }
    }
    public function followReject($follower_id, $followed_id)
    {
        $db = new db;

        // The corrected SQL query
        $query = "UPDATE user_follows SET status = ? WHERE follower_id = ? AND followed_id = ?";

        // Prepare the statement
        $stmt = mysqli_prepare($db->conn, $query);

        // Assuming $status, $follower_id, and $followed_id are your variables
        $status = 'rejected';  // For example, setting status to 1


        // Bind the parameters (status as integer 'i', follower_id as integer 'i', and followed_id as integer 'i')
        mysqli_stmt_bind_param($stmt, 'sii', $status, $follower_id, $followed_id);

        // Execute the statement
        $result = mysqli_stmt_execute($stmt);

        // Check if the query was successful
        if ($result) {
            // echo "Update successful!";
        } else {
            echo "Error updating record: " . mysqli_stmt_error($stmt);
        }
    }
    public function followDelete($follower_id, $followed_id)
    {
        $db = new db;

        // The corrected SQL query
        $query = "UPDATE user_follows SET follow_status = ? WHERE follower_id = ? AND followed_id = ?";

        // Prepare the statement
        $stmt = mysqli_prepare($db->conn, $query);

        // Assuming $status, $follower_id, and $followed_id are your variables
        $status = 'rejected';  // For example, setting status to 1


        // Bind the parameters (status as integer 'i', follower_id as integer 'i', and followed_id as integer 'i')
        mysqli_stmt_bind_param($stmt, 'sii', $status, $follower_id, $followed_id);

        // Execute the statement
        $result = mysqli_stmt_execute($stmt);

        // Check if the query was successful
        if ($result) {
            // echo "Update successful!";
        } else {
            echo "Error updating record: " . mysqli_stmt_error($stmt);
        }
    }
}
