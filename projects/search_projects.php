<?php
require_once "../class/dbconnection.php";
$db = new db;
session_start();
$userId = $_SESSION['userId'];

$searchQuery = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($db->conn, $_GET['search']);
    // Modify the query to search for title or creation date
    $searchQuery = " AND (title LIKE '%$searchTerm%' OR created_at LIKE '%$searchTerm%')";
}

// Fetch all projects based on the search term (if any)
$projectsQuery = "SELECT * FROM projects WHERE owner_id = '$userId' $searchQuery";
$projectsResult = mysqli_query($db->conn, $projectsQuery);

if (mysqli_num_rows($projectsResult) > 0) {
    while ($project = mysqli_fetch_assoc($projectsResult)) {
        // Convert created_at to a DateTime object
        $createdAt = new DateTime($project['created_at']);
        
        // Add the duration_days to created_at to calculate the expiration date
        $expirationDate = clone $createdAt;  // Clone to avoid modifying the original date
        $expirationDate->modify('+' . $project['duration_days'] . ' days');
        
        // Get the current date
        $currentDate = new DateTime();
        
        // Calculate the remaining time (interval between current date and expiration date)
        $remainingTime = $currentDate->diff($expirationDate);
        
        // Check if the project has expired
        if ($currentDate > $expirationDate) {
            $remainingText = "Expired";
        } else {
            // Format the remaining time in days
            $remainingText = $remainingTime->days . ' days remaining';
        }

        echo '
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($project['title']) . '</h5>
                    <p class="card-text text-truncate">' . htmlspecialchars($project['description']) . '</p>
                    <p class="card-text"><strong>Expires in: </strong>' . htmlspecialchars($project['duration_days']) . ' days</p>
                    <p class="card-text"><strong>Created on: </strong>' . htmlspecialchars($project['created_at']) . '</p>
                    <p class="card-text"><strong>Remaining Time: </strong>' . $remainingText . '</p>
                    <a href="./disscussion/disscusR.php?project_id=' . htmlspecialchars($project['id']) . '&user_id=' . $userId . '" class="btn btn-primary">View Disscussion</a>
                </div>
            </div>
        </div>
        ';
    }
} else {
    echo '<p class="text-center">No projects found.</p>';
}
?>
