<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}

require_once "../class/dbconnection.php";

// Check connection
$myClass = new db;
$db = $myClass->conn;

// Fetch users with role 'user'
$sql = "SELECT id, username, role FROM users WHERE ban_until IS NOT NULL";
$result = $db->query($sql);

// Handle delete request

 if (isset($_POST['unBan_id'])) {
    $unBan = $_POST['unBan_id'];
   $sql_unBan = " UPDATE users SET ban_until = NULL WHERE id = '$unBan'";
   $resultBan = mysqli_query($db,$sql_unBan);
   header('location: ./banUser.php');

 }

$db->close();
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
            <a href="./admin.php">Admin Dashboard</a>
            <a href="./allUser.php">All Users</a>
            <a href="./allResearcher.php">All Researchers</a>
            <a href="../Dashboard.php">Dashboard</a>

        </div>
        <div class="container">
            <h1>Admin (Ban List)</h1>
            <ul>
                <?php
               
                // $result1 = $db->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <li>
                            <span class='user-name'><?= htmlspecialchars($row["username"]) ?></span>
                            <span class='user-name'>(<?= htmlspecialchars($row["role"]) ?>)</span>

                            <div class='user-actions'>
                                <!-- Edit form -->
                                
                                <form action='' method='POST' onsubmit='return confirm("Are you sure you want to UNBAN this user?");'>
                                    <input type='hidden' name='unBan_id' value='<?= $row["id"] ?>'>
                                    <button type='submit' class='delete-btn'>UNBAN</button>
                                </form>
                            </div>
                        </li>
                        <?php
                    }
                } else {
                    echo "<li>No results found</li>";
                }

                // $db->close();
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
