<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
// Include database connection
require_once '../../class/dbconnection.php';
require_once "./class/disscusRClass.php";
$db = new db;
$userId = $_SESSION['userId'];
if (!isset($_SESSION['login']) || $_SESSION['login'] == '0') {
    header("Location: ../login.php");
    exit();
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        $_SESSION['login'] = '0';
        header("Location: ../../login.php");
        exit();
      
    }
}
$userId = $_GET['user_id']; // Get user_id from the URL
$postId = $_GET['project_id']; // Get post_id from the URL
if ($userId != $_SESSION['userId']) {
   echo "<script>alert('put wapis chal')</script>";
    exit(); // Stop executing the script if the user_id does not match the session userId
}
else{
$CheckP = "SELECT * FROM projects WHERE id = '$postId'";
    $resultCheckP = mysqli_query($db->conn, $CheckP);
    if (mysqli_num_rows($resultCheckP) == 0) {
    echo "<script>alert('put wapis chal')</script>";
    exit(); // Stop executing the script if the user_id does not match the session userId
    }

}

// Fetch all messages related to this post_id
if (isset($_FILES['fileToUpload'])) {
    echo "<pre>";
    print_r($_FILES['fileToUpload']);
    echo "</pre>";
    // The rest of your code...
}
$myClass = new discuss;
$time = $myClass->fetchTime($postId);
$colaborators = $myClass->fetchColaborators($postId);
$request = $myClass->getRequest($postId);
if (isset($_POST['requestAccept'])  && $_SERVER['REQUEST_METHOD'] == 'POST') {
$accept = $myClass->accept($_POST['request_id'],$postId,$userId);
    // echo "<script>alert('accept working')</script>";
    
// Redirect to the same page
// header("Location: " . $_SERVER['PHP_SELF']);
exit(); // Ensure no further code is executed

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        /* Chat box styling */
        .chat-container {

            /* max-width: 600px; */
            width: 50%;
            margin: 0 auto;
            margin-top: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .chat-box {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #fff;
            border-radius: 10px;
        }

        .user-list {
            width: 30%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
            margin-right: 10px;
        }

        .request-list {
            width: 30%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
        }

        /* Form styling */
        .chat-input {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .chat-input textarea {
            width: 80%;
            resize: none;
            border-radius: 10px;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f1f1f1;
        }

        .chat-input textarea:focus {
            outline: none;
            border-color: #007bff;
            background-color: #fff;
        }

        /* Send Button */
        #sendBtn {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            color: white;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #sendBtn:hover {
            background-color: #0056b3;
        }

        /* File attachment icon styling */
        .file-upload {
            position: relative;
        }

        .file-upload label {
            font-size: 24px;
            cursor: pointer;
        }

        .file-upload input {
            display: block;
        }

        /* Styling for own messages */
        .own-message {
            background-color: #d1e7dd;
            /* Light green */
            border-radius: 15px;
            padding: 10px;
            margin-bottom: 10px;
            text-align: left;
            color: #0f5132;
            border: 1px solid #0f5132;
            max-width: 70%;
            /* margin-left:; */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Styling for other messages */
        .other-message {
            background-color: #f8d7da;
            /* Light red */
            border-radius: 15px;
            padding: 10px;
            margin-bottom: 10px;
            text-align: right;
            color: #842029;
            border: 1px solid #842029;
            max-width: 70%;
            margin-right: auto;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Hover effects for messages */
        .own-message:hover,
        .other-message:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand" href="#">
                <img src="path_to_your_logo.png" alt="Logo"> <!-- Replace with your logo path -->
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto d-flex">
                    <!-- Back Button -->
                    <button class="btn btn-outline-secondary me-2" onclick="window.history.back();">Back</button>

                    <form method="POST">
                        <button class="btn btn-danger text-white mt-3" name="logout">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
<div style="display: flex; justify-content:center; width: 100%;">
    <div class="card" style="width: 25%; margin: 10px;">
        <div class="card-body">
            <h4 class="card-title" style="color: #333;">Contributed users</h4>
            <ul class="list-group list-group-flush">
                
                <?php
                if ($colaborators ->num_rows >0) {
                    # code...
                
                while ($row = mysqli_fetch_assoc($colaborators)) {
                    // echo $row['user_id'];
                    $usernameSql = "SELECT username FROM users WHERE id =  '".$row['user_id']."'";
                    $usernameResult = mysqli_query($db->conn, $usernameSql);
                    $usernameRow = mysqli_fetch_assoc($usernameResult);
                    

                 { ?>
                <li class="list-group-item"><a href="../../profileData/Profile_Card.php?user_id=<?php echo $row['user_id'] ?>" style="text-decoration: none;color:black" ><?php echo $usernameRow['username']  ?></a></li>
                <?php } }
               } else{
                    ?>
                    
                <li class="list-group-item">No contributers Yet</li>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="chat-container">
        <h2 class="text-center">Discussion Page</h2>

        <!-- Chat Box (Displays all messages) -->
        <div class="chat-box" id="chatBox">
            <!-- Messages will be dynamically loaded here -->
        </div>



        <!-- Message Input Section -->
        <!-- Message Input Section with Autocomplete -->
        <form id="chatForm" enctype="multipart/form-data" class="chat-input">
            <input type="hidden" name="action" value="send_message"> <!-- Action Field -->
            <textarea name="message" id="message" rows="1" placeholder="Type your message..." required></textarea>
            <div id="suggestions" style="display:none; position:absolute; background:white; border:1px solid #ccc; z-index:1000;"></div>
            <input type="hidden" name="user_id" id="userId" value="<?= $userId; ?>">
            <input type="hidden" name="post_id" id="postId" value="<?= $postId; ?>">
            <button type="submit" id="sendBtn" class="btn btn-primary">Send</button>
            <div class="file-upload">
                <label for="fileToUpload" style="cursor: pointer;">
                    <i class="fas fa-paperclip" style="font-size: 24px;"></i>
                </label>
                <input type="file" name="fileToUpload" id="fileToUpload" accept=".jpg,.jpeg,.png,.gif,.doc,.docx,.pdf,.ppt,.pptx,.txt" style="display: none;">
            </div>
        </form>


    </div>
    <div class="card" style="width: 25%; margin: 10px; ">
        <div class="card-body" style="max-height: 500px;">
            <?php 
            $check = "SELECT * FROM projects WHERE owner_id = '$userId'";
            $checkR = mysqli_query($db->conn,$check);
            if($checkR->num_rows > 0){
            ?>
            <h4 class="card-title" style="color: #333;">Users Request</h4>
           
            <ul class="list-group list-group-flush">
            <?php 
            if ($request->num_rows >0) {
             while($requestV = mysqli_fetch_assoc($request)){
                $username = $myClass->username($requestV['user_id']);
                ?>
                <!-- <li class="list-group-item"><?php echo $username  ?> <button class="btn btn-success ms-2">Accept</button></li> -->
                <li class="list-group-item d-flex"><a href="../../profileData/Profile_Card.php?user_id=<?php echo $requestV['user_id'] ?>" class="mt-2" style="text-decoration: none; color: black;"><?php echo $username  ?></a>
                <form action="" method="post">
                    <input type="hidden" name="request_id" value="<?= $requestV['user_id']; ?>">
                    <button type="submit" class="btn btn-success ms-2" name="requestAccept">Accept</button>
                </form>
                </li>
                <?php
             }
            }
            else{
                ?>
                <li class="list-group-item">No Request Found</li>

                <?php
            }
            ?>
               

               
               
            </ul>
            <?php } ?>
        </div>
        <div class="card-body">
                <h4 class="card-title" style="color: #333;">Remaining Time</h4>
                <p><?= date('F j, Y g:i A', strtotime($time['created_at'])) ?>, <?= $time['duration_days'] ?> days</p>
                <p>End Date: <?= date('F j, Y', strtotime($time['created_at'] . ' + ' . $time['duration_days'] . ' days')) ?></p>
                <p>Running Time: <?= date('F j, Y g:i A', strtotime($time['created_at'])) ?> - <?= date('F j, Y g:i A', strtotime('now')) ?></p>
            </div>
    </div>

    </div>

    <script>
        $(document).ready(function() {
            // Load messages via AJAX
            function loadMessages() {
                var userId = $('#userId').val();
                var postId = $('#postId').val();

                $.ajax({
                    url: 'chat_handler.php',
                    method: 'POST',
                    data: {
                        action: 'load_messages',
                        user_id: userId,
                        post_id: postId
                    },
                    success: function(response) {
                        $('#chatBox').html(response);
                        $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight); // Auto-scroll to bottom
                    }
                });
            }

            // Load chat messages initially
            loadMessages();

            // Send new message
            $('#chatForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission behavior

                var formData = new FormData(this); // Gather form data, including the message
                $.ajax({
                    url: 'chat_handler.php', // Path to your PHP script
                    method: 'POST',
                    data: formData,
                    processData: false, // Important for file uploads
                    contentType: false, // Important for file uploads
                    success: function(response) {
                        try {
                            var res = JSON.parse(response);
                            if (res.status === 'success') {
                                // Message sent successfully, clear input fields
                                $('#message').val('');
                                $('#fileToUpload').val('');
                                loadMessages(); // Reload messages to reflect the new message
                            } else {
                                alert(res.message);
                            }
                        } catch (e) {
                            alert('Error processing your request: ' + e.message);
                        }
                    },
                    error: function() {
                        alert('Failed to send message. Please try again.');
                    }
                });
            });

        });
        $(document).ready(function() {
            // Listen for typing in the message field
            $('#message').on('input', function() {
                var messageText = $(this).val();
                var lastWord = messageText.split(" ").pop(); // Get the last word typed

                if (lastWord.startsWith("@")) {
                    var searchQuery = lastWord.substring(1); // Remove the @ symbol
                    if (searchQuery.length > 0) {
                        $.ajax({
                            url: 'username_suggestions.php', // PHP script to handle autocomplete
                            method: 'POST',
                            data: {
                                query: searchQuery
                            },
                            success: function(response) {
                                $('#suggestions').html(response).show();
                            }
                        });
                    } else {
                        $('#suggestions').hide();
                    }
                } else {
                    $('#suggestions').hide();
                }
            });

            // When user clicks on a suggestion
            $(document).on('click', '.suggestion-item', function() {
                var username = $(this).text();
                var messageText = $('#message').val();
                $('#message').val(messageText.replace(/@\w*$/, '@' + username + ' ')); // Replace with selected username
                $('#suggestions').hide();
            });
        });
        // Listen for typing in the message field
        $('#message').on('input', function() {
            var messageText = $(this).val();
            var lastWord = messageText.split(" ").pop(); // Get the last word typed

            if (lastWord.startsWith("@")) {
                var searchQuery = lastWord.substring(1); // Remove the @ symbol
                if (searchQuery.length > 0) {
                    $.ajax({
                        url: 'username_suggestions.php', // PHP script to handle autocomplete
                        method: 'POST',
                        data: {
                            query: searchQuery
                        },
                        success: function(response) {
                            $('#suggestions').html(response).show();
                        }
                    });
                } else {
                    $('#suggestions').hide();
                }
            } else {
                $('#suggestions').hide();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</body>

</html>