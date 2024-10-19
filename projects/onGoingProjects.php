<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
require_once "../class/dbconnection.php";
$db = new db;

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($db->conn, $_GET['search']);
    $query = "SELECT title, owner_name, created_at, id FROM projects WHERE title LIKE '%$searchTerm%' OR owner_name LIKE '%$searchTerm%' OR created_at LIKE '%$searchTerm%'";
} else {
    $query = "SELECT title, owner_name, created_at , id FROM projects";
}
$userId = $_SESSION['userId'];
$result = mysqli_query($db->conn, $query);

$projects = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $projects[] = $row;
    }
}

if(isset($_POST['requestJoin']) && $_SERVER['REQUEST_METHOD'] == 'POST')
{
    $sql1 = "CREATE TABLE IF NOT EXISTS user_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        project_id INT,
        status ENUM('none', 'pending', 'rejected', 'accepted') DEFAULT 'none',
        request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        join_date TIMESTAMP NULL -- This will be populated when the request is accepted
     
    );
    
    ";
            $request = mysqli_query($db->conn,$sql1);
$projectId = $_POST['projecId'];
$sql = "INSERT INTO user_requests (user_id, project_id, status, request_date) VALUES (?, ?, 'pending', CURRENT_TIMESTAMP)";
    $stmt = mysqli_prepare($db->conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $userId, $projectId);
    mysqli_stmt_execute($stmt);
   
    
// Redirect to the same page
header("Location: " . $_SERVER['PHP_SELF']);
exit(); // Ensure no further code is executed

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    *{
        padding: 0px;
        margin: 0px;
        box-sizing: border-box;
    }
    .container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 10px; /* Added border-radius for a more rounded look */
    }

    .card {
        margin-bottom: 20px;
        border-radius: 10px; /* Added border-radius for a more rounded look */
    }

    .card-body {
        padding: 20px;
    }

    .card-title {
        font-weight: bold;
        margin-bottom: 10px;
        font-family: 'Arial', sans-serif; /* Changed font family for a more modern look */
        font-size: 18px; /* Increased font size for better readability */
    }

    .card-text {
        color: #666;
        font-family: 'Arial', sans-serif; /* Changed font family for a more modern look */
        font-size: 14px; /* Adjusted font size for better readability */
    }
    #projectsContainer{
        border: 2px solid #007bff; /* Changed border color to a more vibrant blue */
        width: 60%; /* Increased width for better visibility */
        margin-top: 20px; /* Increased margin-top for better spacing */
        border-radius: 25px; /* Increased border-radius for a more rounded look */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Added box-shadow for depth */
    }
</style>
<body style="background-color: #666;">
    
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="../Dashboard.php">Dashboard</a>
    <a class="navbar-brand" href="../profileData/Profile_Card.php?user_id=<?php echo $userId ?>">Profile</a>
  </div>
</nav>

<div class="container">
    <div>
    <h2 class="text-center mb-4" style="font-family: 'Comic Sans MS', cursive; color: #007bff;text-align: center;">On Going Projects</h2>
    <form action="" method="get" style="display: flex; justify-content: center;">
        <div class="mb-4 d-flex">
            <input type="text" name="search" id="searchInput" placeholder="Search by Title, Owner Name, or Date" class="form-control" style="border-radius: 20px; padding: 10px; font-size: 16px;">
            <button type="submit" class="btn btn-primary ms-2">Search</button>
        </div>
    </form>
    <div id="projectsContainer">
        <div class="row">
            <?php foreach ($projects as $project) { ?>
                <div class="m-2" style="display: flex; justify-content: space-between;">
                    <div class=" shadow-sm w-100" style="border-radius: 10px;display:flex">
                        <div class="card-body w-100">
                            <h5 class="card-title" style="font-family: 'Arial', sans-serif; font-size: 18px; font-weight: bold;"><?php echo htmlspecialchars($project['title']); ?></h5>
                            <p class="card-text" style="font-family: 'Arial', sans-serif; font-size: 14px; color: #6B7280;">Owner: <?php echo htmlspecialchars($project['owner_name']); ?></p>
                            <p class="card-text" style="font-family: 'Arial', sans-serif; font-size: 14px; color: #6B7280;">Posted on: <?php echo htmlspecialchars($project['created_at']); ?></p>
                        </div>
                        <form action="" method="POST" style="margin: 40px 10px 0px 0px;">
                        <input type="hidden" name="projecId" value="<?php echo htmlspecialchars($project['id']) ?>">
                        <?php
                        $userId = $_SESSION['userId'];
                        $projectId = $project['id'];
                        $db = new db;
                        $query = "SELECT status FROM user_requests WHERE user_id = '$userId' AND project_id = '$projectId'";
                        $result = mysqli_query($db->conn, $query);
                        $rew = mysqli_fetch_assoc($result);
                        if ($rew ['status'] == 'accepted') {
                            echo '<button disabled class="btn btn-warning" style="border-radius: 20px; padding: 10px; font-size: 16px; background-color: #007bff; border: 1px solid blue; color: white; font-weight: bold;">Request Accepted</button>';
                        }elseif($rew ['status'] == 'pending'){
                            echo '<button disabled class="btn btn-warning" style="border-radius: 20px; padding: 10px; font-size: 16px; background-color: #007bff; border: 1px solid blue; color: white; font-weight: bold;">Pending</button>';
                        } else {
                            echo '<button  class="btn btn-success" name="requestJoin" style="border-radius: 20px; padding: 10px; font-size: 16px; background-color: #007bff; border: 1px solid blue; color: white; font-weight: bold;">Request to join</button>';
                        }
                        ?>
                    </form>
                    </div>
                    
                </div>
                <!-- <button disabled></button> -->
            <?php } ?>
        </div>
    </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const projectsContainer = document.getElementById('projectsContainer');

    searchInput.addEventListener('input', function() {
        const searchTerm = searchInput.value.trim();
        if (searchTerm !== '') {
            fetch(`search_projects.php?search=${searchTerm}`)
                .then(response => response.text())
                .then(data => projectsContainer.innerHTML = data);
        } else {
            projectsContainer.innerHTML = '';
            // Re-fetch all projects when search input is empty
            fetch('search_projects.php')
                .then(response => response.text())
                .then(data => projectsContainer.innerHTML = data);
        }
    });
</script>
</body>
</html>

