<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
$userId = $_SESSION['userId'];
if (!isset($_SESSION['login']) || $_SESSION['login'] == '0') {
    header("Location: ../login.php");
    exit();
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        $_SESSION['login'] = '0';
        header("Location: ../login.php");
        exit();
    }
}
require_once "../class/dbconnection.php";
require_once "../class/readmeClass.php";
if (empty($_GET['post_ids'])) {
    $postId = $_SESSION['postId']; 
}
else{
    $postId = $_GET['post_ids'];
}
$postId;

// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Disscus'])) {
//    echo "<script>alert('discuss working')</script>";
//    header("Location: ../disscussion/disscus.php");

//     // exit();
// }

  // fetch from previous page
$db = new db;
$myClass = new readme;
$userId = $_SESSION['userId'];      // fetch from login page session (the one who open page)
$myClass->userId = $userId;
echo $myClass->postId = $postId;
$row = $myClass->postData();   // fetch all data for document and other data preview
echo $username = $row['username'];             // fetch from previous page
$myClass->username = $username;
$myClass->followedUsername = $row['username'];
// Assuming the 'filepost' contains the filename or path to the document
echo $filePath = $row['filePost']; // Update path as needed    // for document showing and for download link
$loginUserName = $myClass->loginUserData();
$myClass->followerUsername = $loginUserName;

// now use post id to fetch its owner (user) id
echo $postUserId = $myClass->fetchUserId();
$myClass->postUserId = $postUserId;

$row2 = $myClass->postUserData();   // for post owner profilepic 
$followStatus = $myClass->getFollowStatus();

//  update view status
$postView = $myClass->postView($userId,$postId);
 




// Check file extension
$fileExtension = pathinfo($row['filePost'], PATHINFO_EXTENSION);
if (isset($_POST['followButton'])) {
    echo $myClass->postId = $_POST['post_id'];
    echo $myClass->userId = $_POST['userId'];

    // Call your follow function or whatever logic you need
    //  echo "I am follow function";


    $followRequest = $myClass->follow();
}
if (isset($_POST['unFollow']) && $_SERVER['REQUEST_METHOD'] == 'post') {
    echo $myClass->postId = $_POST['post_id'];
    echo $myClass->userId = $_POST['userId'];
    $unFollowRequest = $myClass->unFollow();
}
if (isset($_POST['commentSubmit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['comment'])) {
        $myClass->comment = $_POST['comment'];
        $upload = $myClass->commentUpload();        # code...
        // Redirect to the same page after successful form submission
        header("Location: " . $_SERVER['PHP_SELF']);
    } else {
        echo "plz fill comment box";
    }
}
if (isset($_POST['sendReport']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
if (!empty($_POST['reportComment'])) {
    $reportRequest = $myClass->report($userId, $postId,$username, $_POST['reportComment']);
if ($reportRequest == true) {
    header("Location: " . $_SERVER['PHP_SELF']);
    
}
}
else{
    echo '<script>alert("Plz write any reason For reporting")</script>';
}
}
// $getComments = $myClass->getComments();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Display</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .profile-header {
            text-align: center;
            margin-top: 30px;
        }

        .profile-header img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
        }

        .follow-btn {
            margin-top: 20px;
        }

        .document-container {
            margin-top: 40px;
        }

        .comments-container {
            max-height: 300px;
            /* Adjust height as needed */
            overflow-y: auto;
            /* Enable vertical scrolling */
            border: 1px solid #ccc;
            /* Optional: border around comments */
            border-radius: 5px;
            /* Optional: rounded corners */
        }

        .comments-list::-webkit-scrollbar {
            width: 8px;
            /* Width of the scrollbar */
        }

        .comments-list::-webkit-scrollbar-thumb {
            background: #888;
            /* Color of the scrollbar thumb */
            border-radius: 10px;
            /* Rounded scrollbar thumb */
        }

        .comments-list::-webkit-scrollbar-thumb:hover {
            background: #555;
            /* Color of the scrollbar thumb on hover */
        }

        .comments-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            /* Background color of the scrollbar track */
        }
    </style>
</head>

<body class="bg-dark text-white">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">Navbar Logo</a>
        </div>
        <div class="d-flex">
            <a href="../disscussion/disscus.php?post_id=<?php echo $postId ?>&&user_id=<?php echo $userId ?>" class="btn btn-success text-white me-4">Disscussion</a>
        <?php
        $checkDiscussionQuery = "SELECT * FROM discussions WHERE post_id = ?";
        $stmt = $db->conn->prepare($checkDiscussionQuery);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "The discussion has been started";
        }
        $stmt->close();
        ?>
        </div>
        <!-- <form method="POST">
            <button class="btn btn-success text-white me-4" name="Disscus">Disscussion</button>
        </form> -->
        <form method="POST">
            <button class="btn btn-danger text-white mt-3" name="logout">Logout</button>
        </form>
    </nav>

    <div class="container mt-5">
        <!-- Profile Section -->
        <div class="profile-header">
            <a href="../profileData/Profile_Card.php?user_id=<?php echo $row2['id'] ?>">
                <img src="<?php echo $row2['profilePic']; ?>" class="rounded-circle" alt="User Picture">
            </a>


            <h1 class="mt-3"><?php echo $row['username'] ?></h1>
            <!-- form for follow  -->
            <form action="" method="post">
                <!-- Hidden fields to ensure postUserId and userId are passed when form is submitted -->
                <input type="hidden" name="post_id" value="<?php echo $postUserId; ?>">
                <input type="hidden" name="username" value="<?php echo $username; ?>">
                <input type="hidden" name="userId" value="<?php echo $userId; ?>">

                <button type="submit" id="follow_button" name="followButton" class="btn btn-primary" style="<?php if ($followStatus == 'accepted') { ?> display: none; <?php } ?>" <?php if ($followStatus == 'pending') { ?>disabled<?php } ?>>
                    <?php
                    if ($followStatus == 'rejected' || $followStatus == 'None' || $followStatus == 'not_following') {
                        echo "Follow";
                    } elseif ($followStatus == 'pending') {
                        echo "Pending";
                    } ?>
                </button>

                <button type="submit" class="btn btn-danger m-auto" style="<?php if ($followStatus == 'accepted') { ?> display: block; <?php } else { ?> display: none <?php } ?>" name="unFollow">
                    Unfollow
                </button>
            </form>
        </div>

        <!-- Document Title, Description, and Upload Date -->
        <div class="text-center mt-3">
            <div class="d-flex justify-content-center align-items-center flex-wrap mb-3">
                <h2><?php echo $row['title'] ?></h2>
                <span class="pt-2 ps-2">(uploaded on <?php echo $row['created_at'] ?>)</span>
            </div>
            <p><?php echo $row['description'] ?></p>
        </div>

        <!-- Document Display Section -->
        <div class="card mt-4">
            <div class="card-body document-container" style="color: black; overflow-y: scroll; max-height: 400px;">
                <?php
                // Sanitize the file extension
                $fileExtension = strtolower(pathinfo($row['filePost'], PATHINFO_EXTENSION));

                if ($fileExtension == 'pdf') { ?>
                    <!-- Display PDF document -->
                    <iframe class="bg-dark" src="<?php echo htmlspecialchars($row['filePost']); ?>" width="100%" height="600px"></iframe>
                <?php } elseif ($fileExtension == 'docx' || $fileExtension == 'doc') { ?>
                    <!-- Display Word document -->
                    <object data="<?php echo htmlspecialchars($row['filePost']); ?>" type="application/vnd.openxmlformats-officedocument.wordprocessingml.document" width="100%" height="600px">
                        <p>Your browser does not support Word files. <a href="<?php echo htmlspecialchars($row['filePost']); ?>">Download the document</a> to view it.</p>
                    </object>
                <?php } elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) { ?>
                    <!-- Display Image -->
                    <img src="<?php echo htmlspecialchars($row['filePost']); ?>" alt="Document Image" width="100%">
                <?php } else { ?>
                    <!-- Unsupported File Type -->
                    <p>Unsupported file type</p>
                <?php } ?>
            </div>

        </div>
        <div class="container mt-5">
            <h2>Comments</h2>
            <div class="comments-container">
                <div class="comments-list">
                    <?php
                    // Fetching comments
                    $comments = $myClass->getComments($postUserId);

                    if ($comments->num_rows > 0): ?>
                        <ul class="list-group">
                            <?php while ($comment = $comments->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    <strong><?php echo htmlspecialchars($comment['from']); ?></strong>:
                                    <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                                    <small class="text-muted"><?php echo date('Y-m-d H:i:s', strtotime($comment['created_at'])); ?></small>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p> No comments yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div>
            <div class="container mt-5">
                <h2>Leave a Comment</h2>
                <form id="commentForm" method="POST">
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" name="commentSubmit">Submit Comment</button>
                </form>
            </div>
        </div>
        <form method="POST" id="reportForm" class="border border-light w-25 p-3 bg-dark rounded" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 99; display: none;">
            <span class="fw-bold text-right bg-danger text-white p-2 mb-2" onclick="reportFade()" style="cursor: pointer;border-radius:5px; float: right;">X</span>
            <h3 class="text-center">Report Form</h3>
            <textarea name="reportComment" id="" placeholder="Write about Content" class="form-control"></textarea>
            <button type="submit" class="btn btn-danger text-white mt-2" name="sendReport">Submit</button>
        </form>

        <!-- Download Button -->
        <div class="text-center mt-4">
            <!-- <a href="<?php echo htmlspecialchars($row['filePost']); ?>" class="btn btn-success" download target="_blank">Download Document</a> -->
            <a href="<?php echo htmlspecialchars($row['filePost']); ?>" class="btn btn-success" download target="_blank" onclick="trackDownload(<?php echo $userId; ?>, <?php echo $row['post_id']; ?>)">Download Document</a>

            <button class="btn btn-warning " onclick="report()">Report</button>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function report() {
            var rform = document.getElementById("reportForm");
            rform.style.display = "block"; // Shows the form
        }

        function reportFade() {
            var form = document.getElementById("reportForm");
            form.style.display = "none"; // Hides the form
        }
    </script>
<script>
function trackDownload(userId, postId) {
    // Create an XMLHttpRequest object
    var xhr = new XMLHttpRequest();
    
    // Define the type of request, file, and whether it's asynchronous
    xhr.open("POST", "track_download.php", true);

    // Set request headers
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Send the data to PHP
    xhr.send("user_id=" + userId + "&post_id=" + postId);

    // Optionally handle the server response (you can add custom logic here)
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log("Download count updated successfully.");
        }
    };
}
</script>

</body>

</html>