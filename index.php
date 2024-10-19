<?php
require_once "./class/registerClass.php";
require_once "./class/dbconnection.php";
require_once "./class/allTablesCreatClass.php";
$tableCreatClass = new tableCreation;
$myClass = new registerClass();
$db = new db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg'])) {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];



    // Assign form data to class properties
    $myClass->username = $username;
    $myClass->email = $email;
    $myClass->password = $password;
    $myClass->role = $role;
    $myClass->conn = $db->conn;
    // $myClass->file_name = $_FILES['image']['name'];
    // $myClass->file_size = $_FILES['image']['size'];
    // $myClass->file_temp = $_FILES['image']['tmp_name'];


if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['role'])) {
   // Validate form inputs
    // $checkEmpty = $myClass->checkEpmty();
    // if ($checkEmpty == 'false') {
        $emailValidation = $myClass->emailValidation();
        if ($emailValidation === true) {
            $passwordValidation = $myClass->passwordValidation();
            if ($passwordValidation === true) {
                $tableCreat = $myClass->tableCreat();
                if ($tableCreat === TRUE) {
                    // image  uploading......
                    $imageCheck = $myClass->imageCheckThenInsert();
                }
            }
        }
}
else {
    echo '<div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
             PLZ Fill all the fields!
           </div>
           <script type="text/javascript">
           setTimeout(function() {
               var alert = document.getElementById("customAlert");
               if (alert) {
                   alert.style.display = "none";
               }
           }, 3000); // 3 seconds
       </script>';
}
   
   
}





?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Font Icon -->
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">

    <!-- Main css -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/role.css">
</head>

<body>
    <section class="signup">
        <div class="container">
            <div class="signup-content">
                <div class="signup-form">
                    <h2 class="form-title">Register</h2>
                    <form method="POST" class="register-form" id="register-form">
                        <div class="form-group">
                            <label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                            <input type="text" name="username" id="name" placeholder="Username" />
                        </div>
                        <div class="form-group">
                            <label for="email"><i class="zmdi zmdi-email"></i></label>
                            <input type="email" name="email" id="email" placeholder="Your Email" />
                        </div>
                        <div class="form-group">
                            <label for="pass"><i class="zmdi zmdi-lock"></i></label>
                            <input type="password" name="password" id="pass" placeholder="Password" />
                        </div>
                        <!-- <div class="form-group">
                            <label for="re-pass"><i class="zmdi zmdi-lock-outline"></i></label>
                            <input type="password" name="re_pass" id="re_pass" placeholder="Repeat your password"/>
                        </div> -->
                        <select name="role" class="roleSelect">
                            <!-- <option value="" disabled selected>Select Role</option> -->
                            <option value="user">User</option>
                            <option value="researcher">Researcher</option>


                        </select>
                        <div class="form-group">
                            <input type="checkbox" name="agree-term" id="agree-term" class="agree-term" />
                            <label for="agree-term" class="label-agree-term"><span><span></span></span>I agree all statements in <a href="#" class="term-service">Terms of service</a></label>
                        </div>
                        <div class="form-group form-button">
                            <input type="submit" name="reg" id="signup" class="form-submit" value="Register" />
                        </div>
                    </form>
                </div>
                <div class="signup-image">
                    <figure><img src="images/signup-image.jpg" alt="sing up image"></figure>
                    <a href="./login.php" class="signup-image-link">I am already member</a>
                </div>
            </div>
        </div>
    </section>

    <!-- JS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>