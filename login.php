<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
require_once "./class/loginClass.php";



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        // Initialize an empty message
$message = '';
        $password = $_POST['password'];
        $email = $_POST['email'];
    
        $myClass = new loginClass();
        $myClass->email = $email;
        $myClass->password = $password;
    
        $loginResult = $myClass->getUserDataByEmailThenCompare(); // Store the return value
    
        // Check if loginResult is a role or error message
        if ($loginResult === 'admin') {
            header("Location: ./Admin/adminDashboard.php");
            exit(); // Ensure no further code is executed
        } elseif ($loginResult === 'researcher') {
            header("Location: ./Researcher/researcher.php");
            exit();
        } elseif ($loginResult === 'user') {
            header("Location: ./Dashboard.php");
            exit();
        } else {
            // Store the error message to display
            $message = $loginResult;
        }
    }
    else{
        echo '
        <div id="customAlert" style="display:block; position:fixed; top:10%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
            Plz fill all the fields
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
    <title>Document</title>
    <!-- Font Icon -->
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
    <!-- Main css -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <section class="sign-in">
        <div class="container">
            <div class="signin-content">
                <div class="signin-image">
                    <figure><img src="images/signin-image.jpg" alt="sing up image"></figure>
                    <a href="./index.php" class="signup-image-link">Create an account</a>
                </div>

                <div class="signin-form">
                    <h2 class="form-title">Login</h2>

                    <!-- Display the error message if login fails -->
                    <?php if (!empty($message)): ?>
    <div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
        <?php echo $message; ?>
    </div>
    <script type="text/javascript">
        setTimeout(function() {
            var alert = document.getElementById("customAlert");
            if (alert) {
                alert.style.display = "none";
            }
        }, 3000); // 3 seconds
    </script>
<?php endif; ?>


                    <form action="" method="POST" class="register-form" id="login-form">
                        <div class="form-group">
                            <label for="your_name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                            <input type="email" name="email" id="your_name" placeholder="Your Email"/>
                        </div>
                        <div class="form-group">
                            <label for=""><i class="zmdi zmdi-lock"></i></label>
                            <input type="password" name="password" id="" placeholder="Password"/>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" name="remember-me" id="remember-me" class="agree-term" />
                            <label for="remember-me" class="label-agree-term"><span><span></span></span>Remember me</label>
                        </div>
                        <div class="form-group form-button">
                            <button type="submit" name="login" id="signin" class="form-submit">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
