<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
echo $userId = $_SESSION['userId'];

include('../class/profileDataClass.php'); // Include the file with the function
$myClass = new profile;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Edit'])) {
    $bio = $_POST['bio'];
    $location = $_POST['location'];
    $caption = $_POST['caption'];
    $website = $_POST['website'];
    $affiliations = $_POST['affiliations']; // This will be an array
    $researchInterest = $_POST['researchInterest']; // Get the research interest input
    $profilePic = $_FILES['profilePic'];
    $coverPic = $_FILES['coverPic'];

    // Pass affiliations and research interest to the function
    $result = $myClass->uploadProfileData($bio, $location, $caption, $website, $profilePic, $coverPic, $userId, $affiliations, $researchInterest);

    if (isset($result['error'])) {
        echo $result['error'];
    } else {
        echo $result['success'];
        echo "<script type='text/javascript'>window.location.href = 'Profile_Card.php?user_id=" . $userId . "';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Bootstrap 5.0 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>PROFILE EDIT FORM</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <!-- Username -->
                            <div class="mb-3">
                                <!-- Bio -->
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Tell us something about yourself"></textarea>

                            </div>





                            <!-- Location -->
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="Enter your location">
                            </div>

                            <!-- Caption -->
                            <div class="mb-3">
                                <label for="caption" class="form-label">Caption</label>
                                <input type="text" class="form-control" id="caption" name="caption" placeholder="Enter a caption">
                            </div>
                            <!-- website -->
                            <div class="mb-3">
                                <label for="caption" class="form-label">Website</label>
                                <input type="text" class="form-control" id="caption" name="website" placeholder="Enter a caption">
                            </div>
                            <div class="mb-3">
                                <label for="affiliation" class="form-label">Affiliation(s)</label>
                                <div id="affiliation-container">
                                    <input type="text" class="form-control mb-2" id="affiliation" name="affiliations[]" placeholder="Enter your affiliation">
                                </div>
                                <button type="button" class="btn btn-primary" onclick="addAffiliation()">Add More</button>
                            </div>
                            <div class="mb-3">
                                <label for="profilePic" class="form-label">Result Interest</label>
                                <input type="text" class="form-control" id="profilePic" name="researchInterest">
                            </div>

                            <!-- Profile Picture -->
                            <div class="mb-3">
                                <label for="profilePic" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="profilePic" name="profilePic" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="profilePic" class="form-label">Cover Picture</label>
                                <input type="file" class="form-control" id="coverPic" name="coverPic" accept="image/*">
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" name="Edit">Edit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.0 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js"></script>
    <script>
        function addAffiliation() {
            const container = document.getElementById('affiliation-container');
            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.className = 'form-control mb-2';
            newInput.name = 'affiliations[]'; // This allows multiple values to be stored in an array
            newInput.placeholder = 'Enter another affiliation';
            container.appendChild(newInput);
        }
    </script>
</body>

</html>