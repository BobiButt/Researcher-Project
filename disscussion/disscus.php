<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
$userId = $_SESSION['userId'];
if (!isset($_SESSION['login']) || $_SESSION['login'] == '0') {
    header("Location: ../login.php");
    exit();
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        $_SESSION['login'] = '0';
        header("Location: ../login.php");
        exit();
    }
}
$userId = $_GET['user_id']; // Get user_id from the URL

$postId = $_GET['post_id']; // Get post_id from the URL

// Include database connection
require_once '../class/dbconnection.php';
$db = new db;
// Fetch all messages related to this post_id
if (isset($_FILES['fileToUpload'])) {
    echo "<pre>";
    print_r($_FILES['fileToUpload']);
    echo "</pre>";
    // The rest of your code...
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

            max-width: 600px;
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
            background-color: #d1e7dd; /* Light green */
            border-radius: 15px;
            padding: 10px;
            margin-bottom: 10px;
            text-align:left;
            color: #0f5132;
            border: 1px solid #0f5132;
            max-width: 70%;
            /* margin-left:; */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Styling for other messages */
        .other-message {
            background-color: #f8d7da; /* Light red */
            border-radius: 15px;
            padding: 10px;
            margin-bottom: 10px;
            text-align:right;
            color: #842029;
            border: 1px solid #842029;
            max-width: 70%;
            margin-right: auto;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Hover effects for messages */
        .own-message:hover, .other-message:hover {
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
        </form></div>
        </div>
    </div>
</nav>

<div class="chat-container">
    <h2 class="text-center">Discussion Page</h2>

    <!-- Chat Box (Displays all messages) -->
    <div class="chat-box" id="chatBox">
        <!-- Messages will be dynamically loaded here -->
    </div>

    <!-- Message Input Section -->
    <form id="chatForm" enctype="multipart/form-data" class="chat-input">
        <textarea name="message" id="message" rows="1" placeholder="Type your message..." required></textarea>
        <input type="hidden" name="user_id" id="userId" value="<?= $userId; ?>">
        <input type="hidden" name="post_id" id="postId" value="<?= $postId; ?>">

        <!-- Send Button -->
        <button type="submit" id="sendBtn" class="btn btn-primary">Send</button>

        <!-- File Attachment Icon -->
        <div class="file-upload">
    <label for="fileToUpload" style="cursor: pointer;">
        <i class="fas fa-paperclip" style="font-size: 24px;"></i>
    </label>
    <input type="file" name="fileToUpload" id="fileToUpload" accept=".jpg,.jpeg,.png,.gif,.doc,.docx,.pdf,.ppt,.pptx,.txt" style="display: none;">
</div>
    </form>
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
            data: { action: 'load_messages', user_id: userId, post_id: postId },
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
        e.preventDefault();

        var formData = new FormData(this); // Get form data
        formData.append('action', 'send_message'); // Append action as 'send_message'

        $.ajax({
            url: 'chat_handler.php', // The PHP handler
            method: 'POST',
            data: formData,
            contentType: false, // Required for FormData
            processData: false, // Required for FormData
            success: function(response) {
                var res = JSON.parse(response);
                if (res.status === 'success') {
                    $('#message').val(''); // Clear message input
                    $('#fileToUpload').val(''); // Clear file input
                    loadMessages(); // Reload messages
                } else {
                    alert('Error sending message');
                }
            }
        });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</body>
</html>
