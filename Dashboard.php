<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
// Include the ReactionSystem class
require_once "./class/dbconnection.php";
require_once './class/dashbordClass.php';
require_once "./class/readmeClass.php";

//session user id
$userId = $_SESSION['userId'];  // get from login page
$db = new db;
$dashboardClass = new ReactionSystem;
// Debugging: Check the current value of $_SESSION['login']
"Current login status: " . $_SESSION['login'] . "<br>";  // Optional: For debugging purposes

if (!isset($_SESSION['login']) || $_SESSION['login'] == '0') {  // Check if the login status is not set or is '0'
    header("Location: login.php");
    exit();
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        $_SESSION['login'] = '0';  // Set the session variable to '0'
        header("Location: login.php");
        exit();
    }
    // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //    else if (isset($_POST['like'])) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['on_like'])) {
        $post_Id = $_POST['post_id'];
        $user_Id = $_POST['user_id'];

        $dashboardClass->post_id = $post_Id;
        $dashboardClass->reaction = 'like';
        $dashboardClass->user_id = $userId;
        // echo '<script>alert("i am brand")</script>';


        // Like button was clicked
        $dashboardClass->handleReaction();
        $updateReaction = $dashboardClass->updatePostCounts($post_Id);
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['on_dislike'])) {
        $post_Id = $_POST['post_id'];
        $user_Id = $_POST['user_id'];

        $dashboardClass->post_id = $post_Id;
        $dashboardClass->reaction = 'dislike';
        $dashboardClass->user_id = $userId;
        // echo '<script>alert("i am brand")</script>';


        // Like button was clicked
        $dashboardClass->handleReaction();
        $updateReaction = $dashboardClass->updatePostCounts($post_Id);
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['readMore'])) {
        $_SESSION['postUserName'] = $_POST['username'];
        $_SESSION['postId'] = $_POST['post_id'];
        //   header('location : ./dashboard/readme.php');
        echo '<script type="text/javascript">
            window.location.href = "./dashboard/readme.php"; // Redirect to the desired page
          </script>';
        exit; // Stop further script execution
    }


    // Get current like and dislike counts
    // $postCounts = $dashboardClass->getPostCounts();


}
//  follow button in friend search


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['on_follow'])) {
    $dashboardClass->followedId = $_POST['followed_id'];
    $follow = $dashboardClass->follow($_POST['user_id'], $_POST['user_id_name'], $_POST['followed_id'], $_POST['followedUsername']);
    // header("Location: " . $_SERVER['PHP_SELF']);
    // $followStatus = $dashboardClass->getFollowStatus();


    // echo '<script>alert("Follow working")</script>';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unFollow'])) {
    $dashboardClass->followedId = $_POST['followed_id'];
    $follow = $dashboardClass->unfollow();
    // header("Location: " . $_SERVER['PHP_SELF']);
    // $followStatus = $dashboardClass->getFollowStatus();


    // echo '<script>alert("Follow working")</script>';
}

//  query to access the info of login user
$sqlUser = "SELECT * FROM users WHERE id = $userId";
$result3 = mysqli_query($db->conn, $sqlUser);

if ($result3) {
    $user = mysqli_fetch_assoc($result3); // Fetch the result as an associative array
    $userIdName = $user['username']; // Access the 'username' field from the array
} else {
    echo "Error: " . mysqli_error($db->conn); // Optional: Show an error if the query fails
}
// get follow status
$dashboardClass->user_id = $userId;
$followStatus = $dashboardClass->getFollowStatus();


$profilePic = $dashboardClass->loginUserData($userId);
$sql = "SELECT * FROM post";
$resultPost = mysqli_query($db->conn, $sql);


$role = $dashboardClass->getRole($userId);



?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1AnmQN02U6rBTOytX1CmqeGTVRlhD9xkaFZpLmg39Vf5c4+eoho" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="./css/dashboard.css">


    <script>
        function searchCards() {
            const searchInput = document.getElementById('search-bar').value.toLowerCase();
            const cards = document.getElementsByClassName('card');

            for (let i = 0; i < cards.length; i++) {
                const titleElement = cards[i].getElementsByClassName('title')[0];
                const usernameElement = cards[i].getElementsByClassName('username')[0];
                const categoryElement = cards[i].getElementsByClassName('category')[0];
                const createdAtElement = cards[i].getElementsByClassName('created-at')[0];

                // Check if elements exist before trying to access their innerText
                const title = titleElement ? titleElement.innerText.toLowerCase() : '';
                const username = usernameElement ? usernameElement.innerText.toLowerCase() : '';
                const category = categoryElement ? categoryElement.innerText.toLowerCase() : '';
                const createdAt = createdAtElement ? createdAtElement.innerText.toLowerCase() : '';

                // Debugging output
                console.log("Title:", title);
                console.log("Username:", username);
                console.log("Category:", category);
                console.log("Created At:", createdAt);
                console.log("Search Input:", searchInput);

                // Show the card if any field includes the search input
                if (title.includes(searchInput) || username.includes(searchInput) || category.includes(searchInput) || createdAt.includes(searchInput)) {
                    cards[i].style.display = ''; // Show the card
                } else {
                    cards[i].style.display = 'none'; // Hide the card
                }
            }
        }


        document.addEventListener('DOMContentLoaded', function() {
            // Select all like and dislike buttons
            const likeButtons = document.querySelectorAll('.like');
            const dislikeButtons = document.querySelectorAll('.dislike');

            // Add event listener for like buttons
            likeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Disable like button, enable dislike button
                    this.style.backgroundColor = 'black';
                    this.disabled = true;

                    const dislikeButton = this.closest('.buttons').querySelector('.dislike');
                    dislikeButton.style.backgroundColor = '#f44336';
                    dislikeButton.disabled = false;
                });
            });

            // Add event listener for dislike buttons
            dislikeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Disable dislike button, enable like button
                    this.style.backgroundColor = 'black';
                    this.disabled = true;

                    const likeButton = this.closest('.buttons').querySelector('.like');
                    likeButton.style.backgroundColor = '#4CAF50';
                    likeButton.disabled = false;
                });
            });
        });
    </script>
</head>

<body>
<button class="toggle-btn" onclick="toggleSidebar()">☰</button>
<!-- Sidebar HTML -->
<div class="sidebar" id="sidebar">
    <?php if ($role === 'researcher') : ?>
        <a href="./Researcher/researcher.php">Researcher Panel</a>
        <a href="#">Dashboard</a>
        <a href="./profileData/Profile_Card.php?user_id=<?php echo $userId ?>">Account Info</a>
        <a href="./projects/onGoingProjects.php">OnGoing Projects</a>
        <a href="./projects/joinProjects.php">Join Projects</a>



    <?php elseif ($role === 'user') : ?>
        <a href="./user panel/userPanel.php">User Panel</a>
        <a href="#">Dashboard</a>
        <a href="./profileData/Profile_Card.php?user_id=<?php echo $userId ?>">Account Info</a>
        <a href="./projects/onGoingProjects.php">OnGoing Projects</a>
        <a href="./projects/joinProjects.php">Join Projects</a>


    <?php elseif ($role === 'admin') : ?>
        <a href="./Admin/adminDashboard.php">Admin Panel</a>
        <a href="#">Dashboard</a>
        <a href="./profileData/Profile_Card.php?user_id=<?php echo $userId ?>">Account Information</a>
        <a href="./Admin/banUser.php">Ban User</a>
        <a href="./Admin/allUser.php">All Users</a>
        <a href="./Admin/allResearcher.php">All Researchers</a>
        <a href="./projects/onGoingProjects.php">OnGoing Projects</a>
        <a href="./projects/joinProjects.php">Join Projects</a>


    <?php else : ?>
        <a href="#">Unknown Role</a>
    <?php endif; ?>
</div>

    <div class="right-sidebar">
        <div class="right-sideba">
            <button class="right-toggle-btn" onclick="toggleRightSidebar()">☰</button>
            <div id="right_content">
                <h3 style="text-align: center; font-family: 'Comic Sans MS', cursive;">Friends Panel</h3>
                <div class="find_friend">
                    <h3 style="text-align: center; font-family: 'Comic Sans MS', cursive;">Find Your Friend</h3>
                    <form action="" method="get">
                        <input type="text" name="search" placeholder="Search for users..." class="userInput">
                        <button type="submit" class="userButton">Search</button>
                    </form>
                    <div class="search-results">
                        <?php
                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                            $search = mysqli_real_escape_string($db->conn, $_GET['search']); // Sanitize input

                            // Modify the query to join post and users tables based on the username
                            $searchQuery = "
                              SELECT users.id as user_id, users.username
                              FROM users
                              WHERE users.username LIKE '%" . $search . "%'";

                            $searchResult = mysqli_query($db->conn, $searchQuery);

                            if (mysqli_num_rows($searchResult) > 0) {
                                while ($row = mysqli_fetch_assoc($searchResult)) {
                                    echo '<div class="search-result">';

                                    // Display the user's profile link using the user_id fetched from the users table
                                    echo '<a class="search-result-a" href="./profileData/Profile_Card.php?user_id=' . htmlspecialchars($row['user_id']) . '">' . htmlspecialchars($row['username']) . '</a>';

                                    // echo '<p>Title: ' . htmlspecialchars($row['title']) . '</p>'; // Display post title
                                    // echo '<p>Category: ' . htmlspecialchars($row['category']) . '</p>'; // Display post category
                                    // echo '<p>Posted on: ' . htmlspecialchars($row['created_at']) . '</p>'; // Display post date

                                    // Follow/Unfollow form
                                    echo '<form method="POST">';
                                    echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($userId) . '">';
                                    echo '<input type="hidden" name="user_id_name" value="' . htmlspecialchars($userIdName) . '">';

                                    echo '<input type="hidden" name="followedUsername" value="' . htmlspecialchars($row['username']) . '">';
                                    echo '<input type="hidden" name="followed_id" value="' . htmlspecialchars($row['user_id']) . '">';

                                    // Follow button logic
                                    echo '<button type="submit" name="on_follow" class="btn btn-primary">';
                                    if ($followStatus == 'approved') {
                                        echo 'Unfollow';
                                    } elseif ($followStatus == 'pending') {
                                        echo 'Pending';
                                    } else {
                                        echo 'Follow';
                                    }
                                    echo '</button>';

                                    echo '</form>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p>No posts found.</p>';
                            }
                        }
                        ?>
                    </div>

                </div>
                <div class="accepted-followers" style="background-color: #f9f9f9;width: 100%;margin-top:20px ; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <h3 style="color: #333; font-weight: bold;font-family: 'Comic Sans MS', cursive;">Accepted Followed</h3>
                    <?php
                    $acceptedFollowersQuery = "SELECT * FROM user_follows WHERE follower_id = ? AND follow_status = 'accepted'";
                    $stmt = $db->conn->prepare($acceptedFollowersQuery);
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="accepted-follower" style="margin-bottom: 10px; padding: 10px; border-bottom: 1px solid #ddd;"><a href="./profileData/Profile_Card.php?user_id=' . htmlspecialchars($row['followed_id']) . '" style="text-decoration: none; color: #4CAF50; font-weight: bold;">' . htmlspecialchars($row['followed_username']) . '</a></div>';
                        }
                    } else {
                        echo '<p style="color: #888;">No accepted followers.</p>';
                        // echo $userId;
                    }
                    ?>
                </div>

            </div>
        </div>


        <script>
             function toggleRightSidebar() {
            var sidebar = document.getElementById('right_content');
            sidebar.classList.toggle('show'); // Toggle the 'show' class
        }
        </script>


    </div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MyProject</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./profileData/Profile_Card.php?user_id=<?php echo $userId; ?>">
                            <img src="<?php echo (empty($profilePic) || $profilePic['profilePic'] == NULL) ? './profilePic/guest/guestUserPic.jpg' : './class/' . $profilePic['profilePic']; ?>"
                                alt="profilePic"
                                class="rounded-circle" style="height: 80px; width: 80px;">
                        </a>
                    </li>
                </ul>
                <form class="d-flex" action="" method="post">
                    <button class="btn btn-danger" name="logout" type="submit">LOGOUT</button>
                </form>
            </div>
        </div>
    </nav>

<div style="display: flex; justify-content: center; align-items: center; margin-top: 50px;">
    <div style="width: 50%; background: linear-gradient(135deg, #2C3E50, #4A69BD);border-radius: 50px;">
    <input type="text" id="search-bar" class="search-bar" placeholder="Search... (by category, title, username , posted date" onkeyup="searchCards()">
</div></div>
    <div class="dashboard" style="color: black;">
        <!-- <h1>afdasdas</h1> -->
        <?php
        if (mysqli_num_rows($resultPost) > 0) {


            while ($row = mysqli_fetch_assoc($resultPost)) {



                // Check if 'username' exists in the row
                $username = isset($row['username']) ? $row['username'] : 'Unknown User'; // Fallback if username is not set
                $postId = $row['post_id'];
                $userPic = isset($row['userPic']) ? $row['userPic'] : 'default_user.jpg'; // Get the user's profile picture or default image if it's not set
                //   
                $like_status = "SELECT * FROM post_reactions WHERE user_id = '$userId' AND post_id = '" . $row['post_id'] . "'";

                $status = mysqli_query($db->conn, $like_status);
                $result2 = mysqli_fetch_assoc($status);

        ?>
                <div class="card">
                    <div class="title"><?php echo htmlspecialchars($row['title']); ?></div>
                    <div class="username">by <?php echo htmlspecialchars($username); ?></div>
                    <div class="category">Category : <?php echo htmlspecialchars($row['category']); ?></div> <!-- Added category -->
                    <div class="created-at"><?php echo htmlspecialchars($row['created_at']); ?></div> <!-- Added created_at -->

                    <div style="display: flex; justify-content: space-between;">
                        <form action="" method="post">
                            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>"> <!-- Submit username -->
                            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($row['post_id']); ?>"> <!-- Submit post ID -->
                            <button name="readMore" class="read_more">Read More</button>
                        </form>
                        <a href="./filesUpload/<?php echo htmlspecialchars($row['filePost']); ?>" class="readme-link">Download File</a>
                    </div>

                    <form action="" method="post">
                        <div class="buttons">
                            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($postId); ?>">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                            <button id="like" name="on_like" type="submit" style="<?php if ($result2['reaction_type'] == 'like') { ?>background-color: green; <?php } ?>">
                                <i class="fas fa-thumbs-up"></i> Like
                            </button>
                            <button id="dislike" onclick="dislikebutton()" type="submit" name="on_dislike" style="<?php if ($result2['reaction_type'] == 'dislike') { ?>background-color: red; <?php } ?>">
                                <i class="fas fa-thumbs-down"></i> Dislike
                            </button>
                        </div>
                    </form>

                    <div style="display: flex; justify-content: space-between;">
                        <small>Total likes: <?php echo $row['likes_count'] ?></small>
                        <small>Total dislikes: <?php echo $row['dislikes_count'] ?></small>
                    </div>
                </div>


        <?php
            }
        } else {
            echo "No posts found";
        }
        ?>


    </div>
    <!-- <div class="usersDiv"   style="display: flex; flex-direction: column;">
        <div class="searchbar-container">
            <h3 style="text-align: center; font-family: 'Comic Sans MS', cursive;">Find Your Friend</h3>
            <form action="" method="get">
                <input type="text" name="search" placeholder="Search for users..." class="userInput">
                <button type="submit" class="userButton">Search</button>
            </form>
            <div class="search-results">
                <?php
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $searchQuery = "SELECT * FROM users WHERE username LIKE '%" . $_GET['search'] . "%'";
                    $searchResult = mysqli_query($db->conn, $searchQuery);
                    if (mysqli_num_rows($searchResult) > 0) {
                        while ($row = mysqli_fetch_assoc($searchResult)) {
                            echo '<div class="search-result">';
                            echo '<a class= "search-result-a" href="./profileData/Profile_Card.php?user_id=' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['username']) . '</a>';
                            echo '<button id="follow" name="on_follow" type="submit">Follow</button>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No users found.</p>';
                    }
                }
                ?>
            </div>
        </div>

        </div> -->




    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.style.display === 'block') {
                sidebar.style.display = 'none';
            } else {
                sidebar.style.display = 'block';
            }
        }
        
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMF6qbXTgVLtXTzB6NIOIp6WYBlr49IhFAlzYkHpm7buj+5znk97oM4pHtz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>

</html>