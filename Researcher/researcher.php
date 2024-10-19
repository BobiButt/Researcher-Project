<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
$userId = $_SESSION['userId'];
if (!isset($_SESSION['login']) || $_SESSION['login'] == '0') {  // Check if the login status is not set or is '0'
    header("Location: ../login.php");
    exit();
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        $_SESSION['login'] = '0';  // Set the session variable to '0'
        header("Location: ../login.php");
        exit();
    }
}
require_once "../class/researcherClass.php";
$myClass = new researcherClass;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {

    // Assign form inputs
    $myClass->title = $_POST['title'];
    $myClass->description = $_POST['description'];
    $myClass->category = $_POST['category'];
    // Pass the uploaded file to the post method
    $myClass->post($_FILES['fileToUpload']);
    header("Location: " . $_SERVER['PHP_SELF']);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uploadR'])) {

    // Assign form inputs
    $myClass->title = $_POST['title'];
    $myClass->description = $_POST['description'];
    $myClass->time = $_POST['time'];
    // Pass the uploaded file to the post method
    $myClass->postR($_FILES['fileToUpload']);
    header("Location: " . $_SERVER['PHP_SELF']);
}
$userFollowRequest = $myClass->followRequest($userId);
// $UFR = mysqli_fetch_assoc($userFollowRequest);
$userFollowRequestAccept = $myClass->followRequestAccept($userId);

if (isset($_POST['action'])) {
    $follower_id = $_POST['follower_id'];
    $followed_id = $_POST['followed_id'];

    // Check if the action is "accept" or "reject"
    if ($_POST['action'] == 'accept') {
        // Call a function or write code to handle the acceptance logic
        // Example: Update the database to accept the follow request
        // $status = 1; // Accepted status
        // Update the status in the database
        $myClass->followAccept($follower_id, $followed_id);
        // echo "Follow request accepted!";
    } elseif ($_POST['action'] == 'reject') {
        // Call a function or write code to handle the rejection logic
        // Example: Update the database to reject the follow request
        // $status = 0; // Rejected status
        // Update the status in the database
        $myClass->followReject($follower_id, $followed_id);
        echo "Follow request rejected!";
    } elseif ($_POST['action'] == 'delete') {
        // Call a function or write code to handle the rejection logic
        // Example: Update the database to reject the follow request
        // $status = 0; // Rejected status
        // Update the status in the database
        $myClass->followDelete($follower_id, $followed_id);
        // echo "Follow request Deleted!";
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Researcher Webpage</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }

        .sidebar {
            margin-top: 7%;
            border-radius: 10px;
            width: 15%;
            background: #333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            left: -15%;
            transition: left 0.3s;
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
            background: #ff7e5f;
            text-align: center;
        }

        .sidebar a:hover {
            background: #feb47b;
        }

        .toggle-btn {
            position: fixed;
            top: 60px;
            left: 20px;
            padding: 10px 20px;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .navbar {
            margin-bottom: 20px;
            background-color: #343a40;
        }

        .navbar-brand {
            font-family: 'Open Sans', sans-serif;
            font-weight: 700;
            color: #ffffff !important;
        }

        .nav-link {
            color: #ffffff !important;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #343a40;
            margin-bottom: 20px;
            font-family: 'Open Sans', sans-serif;
            font-weight: 700;
        }

        .form-group label {
            font-weight: bold;
            font-family: 'Open Sans', sans-serif;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            font-family: 'Open Sans', sans-serif;
            font-weight: 700;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .list-group-item {
            font-family: 'Roboto', sans-serif;
        }
        #uploadForm.hidden {
            display: block;
        }
    </style>
</head>

<body>
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <div class="sidebar" id="sidebar">
        <a href="./researcher.php">Researcher Panel</a>

        <a href="../Dashboard.php">Dashboard</a>
        <a href="../projects/allProjects.php">All projects</a>


    </div>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark d-flex justify-content-between">
        <div>
            <a class="navbar-brand" href="#">MyProject</a>
        </div>
        <div class="" id="navbarNav">
            <form class="navbar-nav" method="POST">
                <button class="btn btn-danger py-1 px-2 text-white" aria-current="page" href="#" name="logout">Logout</button>

            </form>
            <!-- <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Notifications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Followers</a>
                </li>
            </ul> -->
        </div>
    </nav>

    <!-- Main Section -->
    <div class="container mt-5">
    <button class="btn btn-secondary" onclick="toggleF()">Upload Post Form</button>
    <form action="" method="post" enctype="multipart/form-data" id="uploadForm" class="mt-3 hidden" style="display: none;">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select class="form-control" id="category" name="category" required>
                <option value="" disabled selected>Select a category</option>
                <option value="Research">Research</option>
                <option value="Technical">Technical</option>
                <option value="Thesis">Thesis</option>
                <option value="Review">Review</option>
                <option value="Conference">Conference</option>
                <option value="Dataset">Dataset</option>
                <option value="Preprint">Preprint</option>
                <option value="Grant Proposal">Grant Proposal</option>
            </select>
        </div>
        <div class="form-group">
            <label for="file">File</label>
            <input type="file" class="form-control-file" id="file" name="fileToUpload" required>
        </div>
        <button type="submit" class="btn btn-primary" name="upload">Upload</button>
    </form>
<script>
      function toggleF() {
        const form = document.getElementById('uploadForm');
        // Toggle visibility of the form
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block'; // Show the form
        } else {
            form.style.display = 'none'; // Hide the form
        }
    }
</script>
    </div>
    <div class="container mt-5">
    <button class="btn btn-secondary" onclick="toggleFR()">Upload Research Form</button>
    <form action="" method="post" enctype="multipart/form-data" id="uploadFormR" class="mt-3 hidden" style="display: none;">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="category">TimeLine</label>
            <input type="number" class="form-control" id="time" name="time" placeholder="in days" required>
            
        </div>
        <div class="form-group">
            <label for="file">File</label>
            <input type="file" class="form-control-file" id="file" name="fileToUpload" required>
        </div>
        <button type="submit" class="btn btn-primary" name="uploadR">Upload</button>
    </form>
<script>
      function toggleFR() {
        const form = document.getElementById('uploadFormR');
        // Toggle visibility of the form
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block'; // Show the form
        } else {
            form.style.display = 'none'; // Hide the form
        }
    }
</script>
    </div>

    <!-- Contributions Section -->
    <div class="container mt-5 d-flex justify-content-between">
        <div class="w-50">
            <h2>Follower Requests</h2>
            <ul class="list-group">
                <?php if ($userFollowRequest == false): ?>
                    <li class="list-group-item">There is no request for now</li>
                <?php else: ?>
                    <?php
                    // Remove the line fetching a record before the loop.
                    // echo 'Total rows: ' . mysqli_num_rows($userFollowRequest);  // Check the number of rows
                    while ($UFR = mysqli_fetch_assoc($userFollowRequest)):
                    ?>
                        <li class="list-group-item">
                            Follower: <?php echo htmlspecialchars($UFR['follower_username']); ?> - ID: <?php echo htmlspecialchars($UFR['follower_id']); ?>

                            <!-- Accept and Reject Buttons -->
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="follower_id" value="<?php echo $UFR['follower_id']; ?>">
                                <input type="hidden" name="followed_id" value="<?php echo $UFR['followed_id']; ?>">

                                <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accept</button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>



        </div>
        <div class="w-50">
            <h2>Accepted Follower</h2>
            <ul class="list-group">
                <?php if ($userFollowRequestAccept == false): ?>
                    <li class="list-group-item">There is no request for now</li>
                <?php else: ?>
                    <?php
                    // Remove the line fetching a record before the loop.
                    // echo 'Total rows: ' . mysqli_num_rows($userFollowRequest);  // Check the number of rows
                    while ($UFRA = mysqli_fetch_assoc($userFollowRequestAccept)):
                    ?>
                        <li class="list-group-item">
                            Follower: <?php echo htmlspecialchars($UFRA['follower_username']); ?> - ID: <?php echo htmlspecialchars($UFRA['follower_id']); ?>

                            <!-- Accept and Reject Buttons -->
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="follower_id" value="<?php echo $UFRA['follower_id']; ?>">
                                <input type="hidden" name="followed_id" value="<?php echo $UFRA['followed_id']; ?>">

                                <!-- <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accept</button> -->
                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>


    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.style.left === '0px') {
                sidebar.style.left = '-15%';
            } else {
                sidebar.style.left = '0px';
            }
           
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>