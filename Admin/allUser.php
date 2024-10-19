<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "researcher";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users with role 'user'
$sql = "SELECT id, username FROM users WHERE role = 'user' && ban_until IS NULL";
$result = $conn->query($sql);

// Handle delete request
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_sql = "DELETE FROM users WHERE id = ?";
    
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully.'); window.location.href='';</script>";
    } else {
        echo "Error deleting user: " . $stmt->error;
    }
    $stmt->close();
}
if (isset($_POST['id'])) {
    $_SESSION['id'] = $_POST['id'];
    header('location: ./editUser.php');
 }
 if (isset($_POST['ban_id'])) {
    $ban = $_POST['ban_id'];
   $sql_ban = " UPDATE users SET ban_until = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE id = '$ban'";
   $resultBan = mysqli_query($conn,$sql_ban);
 }

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin User List</title>
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        main{
            display: flex;
        }
        .sidebar {
            margin-top: 5%;
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
        .container {
            width: 80%;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background: #f9f9f9;
            margin: 10px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        li:nth-child(odd) {
            background: #e9e9e9;
        }
        .user-name {
            font-size: 18px;
            color: #333;
        }
        .user-actions {
            display: flex;
            gap: 10px;
        }
        .user-actions form {
            display: inline; /* Makes the form display inline with other elements */
        }
        .user-actions button {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
        .toggle-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <main>
        <div class="sidebar" id="sidebar">
            <a href="adminDashboard.php">Admin Dashboard</a>
            <a href="#all-users">All Users</a>
            <a href="./allResearcher.php">All Researchers</a>
            <a href="./banUser.php">Ban users</a>
            <a href="../Dashboard.php">Dashboard</a>


        </div>
        <div class="container">
            <h1>Admin (User List)</h1>
            <ul>
                <?php
                // Reopen connection to fetch users again
                $conn = new mysqli('localhost', 'root', '', 'researcher');
                $result1 = $conn->query($sql);

                if ($result1->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result1->fetch_assoc()) {
                        ?>
                        <li>
                            <span class='user-name'><?= htmlspecialchars($row["username"]) ?></span>
                            <div class='user-actions'>
                                <!-- Edit form -->
                                <form action='' method='POST'>
                                    <input type='hidden' name='id' value='<?= $row["id"] ?>'>
                                    <button type='submit' class='edit-btn'>Edit</button>
                                </form>

                                <!-- Delete form -->
                                <form action='' method='POST' onsubmit='return confirm("Are you sure you want to delete this user?");'>
                                    <input type='hidden' name='delete_id' value='<?= $row["id"] ?>'>
                                    <button type='submit' class='delete-btn'>Delete</button>
                                </form>
                                <form action='' method='POST' onsubmit='return confirm("Are you sure you want to Ban this user?");'>
                                    <input type='hidden' name='ban_id' value='<?= $row["id"] ?>'>
                                    <button type='submit' class='delete-btn'>Ban</button>
                                </form>
                            </div>
                        </li>
                        <?php
                    }
                } else {
                    echo "<li>No results found</li>";
                }

                $conn->close();
                ?>
            </ul>
        </div>
    </main>
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
</body>
</html>
