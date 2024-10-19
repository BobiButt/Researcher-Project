<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
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
require_once "../class/profileDataClass.php";
$userId = $_SESSION['userId']; // get from login page
$postUserId = $_GET['user_id'];
$myClass = new profile;
$myClass->userId = $postUserId;
$fetch = $myClass->fetch();
$countFollowers = $myClass->countFollowers($fetch['id']);
$countFollowed = $myClass->countFollowed($fetch['id']);
$totalPost = $myClass->totalPost($postUserId);
$role = $myClass->getRole($userId);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>

    <!-- Bootstrap 5.0 CSS inclusion -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- FontAwesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Custom CSS for styling -->
    <style>
        body {
            background-color: #f9f9f9;
            /* color: #333; */
        }

        .sidebar {
            margin-top: 15%;
            border-radius: 10px;
            width: 15%;
            background: #333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            left: -15%;
            transition: left 0.3s;
            z-index: 5;
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
            top: 150px;
            left: 20px;
            padding: 10px 20px;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .profile-header {
            background-image: url('<?php echo $fetch['coverPic'] ?>');
            background-size: cover;
            background-position: center;
            height: 300px;
            position: relative;
        }

        .profile-img {
            position: absolute;
            left: 50%;
            top: 85%;
            transform: translate(-50%, -50%);
            border: 5px solid #fff;
        }

        .profile-info h2 {
            margin-top: 60px;
        }

        .edit-button {
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            padding: 10px 20px;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }

        .card {
            /* background-color: rgba(255, 255, 255, 0.9); Semi-transparent white */
            background-color: rgba(0, 0, 0, 0.7);
        }
    </style>
</head>

<body class="bg-dark text-white">
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <!-- Sidebar HTML -->
    <div class="sidebar" id="sidebar">
        <?php if ($role === 'researcher') : ?>
            <!-- If the role is researcher, display this -->
            <a href="../Researcher/researcher.php">Researcher Panel</a>
            <a href="../Dashboard.php">Dashboard</a>
            <a href="#">Account Info</a>

        <?php elseif ($role === 'user') : ?>
            <!-- If the role is user, display this -->
            <a href="../user panel/userPanel.php">User Panel</a>
            <a href="../Dashboard.php">Dashboard</a>
            <a href="#">Account Info</a>
        <?php elseif ($role === 'admin') : ?>
            <!-- If the role is admin, display this -->
            <a href="./Admin/adminDashboard.php">Admin Panel</a>
            <a href="../Dashboard.php">Dashboard</a>
            <a href="#">Account Information</a>
            <a href="../Admin/banUser.php">Ban User</a>
            <a href="../Admin/allUser.php">All Users</a>
            <a href="../Admin/allResearcher.php">All Researchers</a>
        <?php else : ?>
            <!-- Optional: You can handle any other roles here -->
            <a href="#">Unknown Role</a>
        <?php endif; ?>
    </div>
    <nav class="navbar navbar-expand-sm navbar-light bg-dark text-white">
        <div class="container-fluid d-flex">
            <div>
                <a class="navbar-brand text-white" href="#">MyProject</a>
            </div>

            <form class="navbar-nav" method="POST">
                <button class="btn btn-danger py-1 px-2 text-white" aria-current="page" href="#" name="logout">Logout</button>

            </form>

        </div>
    </nav>
    <div class="container mt-5">
        <!-- Profile Header Section -->
        <div class="profile-header rounded">
            <img src="<?php echo (!empty($fetch['profilePic'])) ? htmlspecialchars($fetch['profilePic']) : '../profilePic/guest/guestUserPic.jpg'; ?>"
                alt="Profile"
                class="profile-img rounded-circle"
                width="150"
                height="150">

        </div>

        <!-- Profile Information -->
        <div class="row mt-5">
            <div class="col-md-8 ">
                <div class="profile-info ">
                    <h2><?php echo $fetch['username'] ?></h2>
                    <p class="text-muted">
                        <?php if ($fetch['bio'] == NULL) {
                            echo "Hi there! I'm [Name], a passionate [profession/role] with a love for [specific interests or skills].
          With a background in [mention relevant experience or education], I thrive on [what you enjoy doing in your profession, e.g., solving complex problems, creating beautiful designs, etc.].";
                        } else {
                            echo $fetch['bio'];
                        } ?>

                    <p><strong>Location:</strong><?php if ($fetch['location'] == NULL) {
                                                        echo "New York, NY, USA";
                                                    } else {
                                                        echo $fetch['location'];
                                                    } ?></p>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="./profileCardEdit.php" class="btn edit-button rounded-circle w-25" style="<?php if ($fetch['id'] == $userId) {  ?> display: block; <?php
                                                                                                                                                        } else { ?>display: none;<?php } ?>">

                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>

        <!-- Website and Contact Information -->
        <div class="row mt-4">
            <div class="col-md-6">
                <p><strong>Website:</strong> <a href="<?php if ($fetch['website'] == NULL) {
                                                            echo "https://www.google.com/";
                                                        } else {
                                                            echo $fetch['website'];
                                                        } ?>" class="text-primary text-decoration-none text-white " target='blank'><?php if ($fetch['website'] == NULL) {
                                                                                                                                        echo "https://www.google.com/";
                                                                                                                                    } else {
                                                                                                                                        echo $fetch['website'];
                                                                                                                                    } ?></a></p>
                <p><strong>Email:</strong> <?php echo $fetch['email'] ?></p>
                <p><strong>Affiliations : </strong><?php if ($fetch['affiliations'] == NULL) {
                                                    echo "Not any";
                                                } else {
                                                    echo $fetch['affiliations'];
                                                } ?></p>
                <p><strong>Research Interest : </strong><?php if ($fetch['researchInterest'] == NULL) {
                                                    echo "Not any";
                                                } else {
                                                    echo $fetch['researchInterest'];
                                                } ?></p>


            </div>
        </div>

        <!-- Followers, Following, and Posts Information -->
        <div class="row mt-4 text-center">
            <div class="col-md-4">
                <div class="card shadow-sm rounded p-3" style="background-color: rgba(0, 0, 0, 0.7);">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-friends fa-2x text-primary"></i>
                        <p class="ms-2 mb-0 text-white"><strong>Followers:</strong> <?php echo $countFollowers; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm rounded p-3" style="background-color: rgba(0, 0, 0, 0.7);">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-plus fa-2x text-success"></i>
                        <p class="ms-2 mb-0 text-white"><strong>Following:</strong> <?php echo $countFollowed; ?></p>
                    </div>
                </div>
            </div>
            <!-- Uncomment for posts if needed -->
            <!-- <div class="col-md-4">
        <div class="card shadow-sm rounded p-3" style="background-color: rgba(0, 0, 0, 0.7);">
            <div class="d-flex align-items-center justify-content-center">
                <i class="fas fa-pencil-alt fa-2x text-info"></i>
                <p class="ml-2 mb-0 text-white"><strong>Posts:</strong> <?php echo $totalPost; ?></p>
            </div>
        </div>
    </div> -->
        </div>



        <!-- Caption Section -->
        <div class="row mt-4">
            <div class="col-12">
                <p class="fst-italic text-muted">‘Breathtaking views that are worth the hike! #GrandCanyon #Adventure’</p>
            </div>
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
    <!-- Bootstrap 5.0 JS inclusion (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl2Vv1kL5GBOvGAZ4hEY8YzxmG6gThbgiWd2xEgxkIbNLRKw2PflJVRJaG7" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhG+Ylv0pI7LW0J8kGdm4D4Gp2fPzv9ZL+3IJ2LqD2eBz/e60uamddB1Qf+p" crossorigin="anonymous"></script>

</body>

</html>