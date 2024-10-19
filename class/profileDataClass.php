<?php
require_once "../class/dbconnection.php";

class profile
{
    public $userId;

    public function fetch()
    {
        $db = new db;
        $sql = "SELECT * FROM users WHERE id = '$this->userId'";
        $result = $db->conn->query($sql);
        return $result->fetch_assoc();
    }

    public function uploadProfileData($bio, $location, $caption, $website, $profilePic, $coverPic, $userId, $affiliations, $researchInterest)
{
    // Database connection (Assuming you have the connection setup)
    $db = new db;

    // Helper function to handle image upload
    function handleImageUpload($file, $folder)
    {
        $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 1048576;

        // Ensure that the file is not null
        if (!isset($file['name']) || !$file['name']) {
            return ['error' => 'No file uploaded.'];
        }

        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileTmpName = $file['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Check if the file extension is allowed
        if (!in_array($fileExt, $allowedFormats)) {
            return ['error' => 'Invalid file format. Only jpg, jpeg, png, gif formats are allowed.'];
        }

        // Check file size (must not exceed 1MB)
        if ($fileSize > $maxFileSize) {
            return ['error' => 'File size must not exceed 1MB.'];
        }

        // Move file to the specified folder
        $newFileName = uniqid() . '.' . $fileExt;
        $filePath = $folder . '/' . $newFileName;
        if (move_uploaded_file($fileTmpName, $filePath)) {
            return ['success' => $filePath]; // Return the file path on success
        } else {
            return ['error' => 'Failed to upload the file.'];
        }
    }

    // Fetch current data of the user to check if fields already have data
    $sqlFetch = "SELECT bio, location, caption, website, profilePic, coverPic, affiliations, researchInterest FROM users WHERE id = ?";
    if ($stmtFetch = $db->conn->prepare($sqlFetch)) {
        $stmtFetch->bind_param("i", $userId);
        $stmtFetch->execute();
        $stmtFetch->bind_result($currentBio, $currentLocation, $currentCaption, $currentWebsite, $currentProfilePic, $currentCoverPic, $currentAffiliations, $currentResearchInterest);
        $stmtFetch->fetch();
        $stmtFetch->close();
    }

    // Handle profile picture upload
    if ($profilePic['name']) {
        $profilePicResult = handleImageUpload($profilePic, '../profilePic'); // Assuming 'profilePic' folder exists
        if (isset($profilePicResult['error'])) {
            return ['error' => $profilePicResult['error']];
        }
        $profilePicPath = $profilePicResult['success'];
    } else {
        $profilePicPath = $currentProfilePic;
    }

    // Handle cover picture upload
    if ($coverPic['name']) {
        $coverPicResult = handleImageUpload($coverPic, '../coverPic'); // Assuming 'coverPic' folder exists
        if (isset($coverPicResult['error'])) {
            return ['error' => $coverPicResult['error']];
        }
        $coverPicPath = $coverPicResult['success'];
    } else {
        $coverPicPath = $currentCoverPic;
    }

    // Keep old values if no new data is provided
    $bio = !empty($bio) ? $bio : $currentBio;
    $location = !empty($location) ? $location : $currentLocation;
    $caption = !empty($caption) ? $caption : $currentCaption;
    $website = !empty($website) ? $website : $currentWebsite;
    $researchInterest = !empty($researchInterest) ? $researchInterest : $currentResearchInterest;
    $affiliations = !empty($affiliations) ? implode(',', $affiliations) : $currentAffiliations; // Convert array to string

    // Update user data in the users table
    $sqlUpdate = "UPDATE users 
                  SET bio = ?, location = ?, caption = ?, website = ?, profilePic = ?, coverPic = ?, affiliations = ?, researchInterest = ? 
                  WHERE id = ?";

    if ($stmtUpdate = $db->conn->prepare($sqlUpdate)) {
        $stmtUpdate->bind_param("ssssssssi", $bio, $location, $caption, $website, $profilePicPath, $coverPicPath, $affiliations, $researchInterest, $userId);
        if ($stmtUpdate->execute()) {
            return ['success' => 'Profile updated successfully.'];
        } else {
            return ['error' => 'Failed to update profile.'];
        }
    } else {
        return ['error' => 'Failed to prepare statement.'];
    }
}

    public function countFollowers($followedId)
    {
        // how many person followed him
        $db = new db;
        $query = "SELECT COUNT(*) AS total_followers FROM user_follows WHERE followed_id = ? AND follow_status = 'accepted'";

        $stmt = $db->conn->prepare($query);
        $stmt->bind_param("i", $followedId); // Bind followed_id as an integer
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['total_followers']; // Return the count
        } else {
            return 0; // No followers found
        }
    }
    public function countFollowed($followedId)
    {
        //how much he followed
        $db = new db;
        $query = "SELECT COUNT(*) AS total_followers FROM user_follows WHERE follower_id = ? AND follow_status = 'accepted'";

        $stmt = $db->conn->prepare($query);
        $stmt->bind_param("i", $followedId); // Bind followed_id as an integer
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['total_followers']; // Return the count
        } else {
            return 0; // No followers found
        }
    }
    public function totalPost($followedId)
    {
        //how much he followed
        $db = new db;
        $sql = "SELECT username FROM users WHERE id = ?";

        $stmt = $db->conn->prepare($sql);
        $stmt->bind_param("i", $followedId); // Bind followed_id as an integer
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $rowName = $result->fetch_assoc();
            echo $rowName['username'];
            $query = "SELECT COUNT(*) AS total_posts FROM post WHERE username = ?";

            $stmt = $db->conn->prepare($query);
            $stmt->bind_param("i", $rowName['username']); // Bind followed_id as an integer
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $rowtotal = $result->fetch_assoc();
                return $rowtotal['total_posts']; // Return the count
            } else {
                return 0; // No followers found
            }
        } else {
            return 00000000; // No followers found
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
}
