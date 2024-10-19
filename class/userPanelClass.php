<?php
require_once "../class/dbconnection.php";
class userPanel{

public $filePost;
    public function post($username,$role,$title,$category,$description,$PublicStatus,$file)
    {
        $db = new db;
        $username = $_SESSION['username'];
        $role = $_SESSION['role']; // Assuming you have a role in the session as well

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
                    $filePost = $targetFile;

                    // Create the table if it doesn't exist
                    $sql = "
                    CREATE TABLE IF NOT EXISTS user_posts (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(255) NOT NULL,
                        role VARCHAR(50) NOT NULL,
                        title VARCHAR(255) NOT NULL,
                        category VARCHAR(100) NOT NULL,
                        description TEXT NOT NULL,
                        public_status ENUM('Public', 'Private') NOT NULL DEFAULT 'Public', -- Set default value here properly
                        file_path VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )";
                    
                    if (!$db->conn->query($sql)) {
                        echo "Error creating table: " . $db->conn->error;
                    }
                

                    if ($db->conn->query($sql) === TRUE) {
                        // Insert data into the table
                        $queryInsert = "INSERT INTO post (username, filePost, role, title,category, description,is_public) VALUES (?, ?, ?, ?, ?, ?, ?)";

                        if ($stmtInsert = $db->conn->prepare($queryInsert)) {
                            // Bind parameters (s = string)
                            $stmtInsert->bind_param("sssssss", $username, $filePost, $role, $title,$category, $description,$PublicStatus);

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
}

