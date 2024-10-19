<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
echo $id = $_SESSION['id'];
require_once "../class/editUserClass.php";
$myClass = new edit;
if (isset($_POST['update'])) {
    $myClass->username = $_POST['username'];
    $myClass->email = $_POST['email'];
    $myClass->password = $_POST['password'];
    $myClass->role = $_POST['role'];
    $myClass->id = $id;

    $edit = $myClass->edit();
    if ($edit == true) {
        unset($_SESSION['id']);
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../img/register/register\ 5.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            justify-content: start;
            
            align-items: center;
            min-height: 100vh;
            min-width: 100vw;
            width: 100vw;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .registration-form {
            background: linear-gradient(to right, rgba(255, 255, 255, 0.9), rgba(0, 123, 255, 0.9));
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            margin:auto;
        }
        .registration-form h2 {
            margin-bottom: 20px;
            font-weight: 500;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        .registration-form input, .registration-form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .registration-form button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .registration-form button:hover {
            background-color: #45a049;
        }
        .loginLink{
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            margin-left: 5px;
            border-radius: 5px;
            background-color: red;
        }
    </style>
</head>
<body>
    <div class="registration-form">
        <h2>EDIT</h2>
        <form action="" method="POST" enctype="multipart/form-data" onsubmit='return confirm("Are you sure you want to Update this users DATA?");'>
            <input type="text" name="username" placeholder="Username" >
            <input type="email" name="email" placeholder="Email" >
            <input type="password" name="password" placeholder="Password" >
            <!-- <label for="">Profile Pic</label> -->
            <input type="file" name="image" placeholder="Profile Pic" disabled>
            <small>For Profile Pic (use jpg , jpeg and png extension only)</small>

            <select name="role" >
                <!-- <option value="" disabled selected>Select Role</option> -->
                <option value="user">User</option>
                <option value="researcher">Researcher</option>
                

            </select>
            <button type="submit" name="update">Update</button>
            <div style="margin-top: 10px;">
         </div>
        </form>
    </div>
</body>
</html>
