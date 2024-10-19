<?php
require_once "../class/dbconnection.php";

class edit
{
    public $username , $email, $password , $role , $id;

    public function edit()
    {
     $db = new db;
      $conn = $db->conn; 
 $old = "SELECT * FROM users WHERE  id = '$this->id'";
 $oldResult = mysqli_query($conn, $old);
 if ($oldResult) {
    $oldRow = mysqli_fetch_assoc($oldResult);
 }

    
      $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ? WHERE id = ?");
      $stmt->bind_param("ssssi", $this->username, $this->email, $this->password, $this->role, $this->id);
      
      if ($stmt->execute()) {


        // update username from all tables
        $updateComments = "UPDATE comments SET `from` = ?, `to` = ? WHERE `from` = ? OR `to` = ?";
        $stmtComments = $conn->prepare($updateComments);
        $stmtComments->bind_param("ssss", $this->username, $this->username, $oldRow['username'], $oldRow['username']);
        $stmtComments->execute();
// post table  update

        $updatePosts = "UPDATE post SET username = ? WHERE username = ?";
        $stmtPosts = $conn->prepare($updatePosts);
        $stmtPosts->bind_param("ss", $this->username, $oldRow['username']);
        $stmtPosts->execute();
        //  Reports table update
        $updateReports = "UPDATE reports SET username = ?, post_username = ? WHERE username = ? OR post_username = ?";
        $stmtReports = $conn->prepare($updateReports);
        $stmtReports->bind_param("ssss", $this->username, $this->username, $oldRow['username'], $oldRow['username']);
        $stmtReports->execute();
        // user_follows table update
        $updateFollows = "UPDATE user_follows SET follower_username = ?, followed_username = ? WHERE follower_username = ? OR followed_username = ?";
        $stmtFollows = $conn->prepare($updateFollows);
        $stmtFollows->bind_param("ssss", $this->username, $this->username, $oldRow['username'], $oldRow['username']);
        $stmtFollows->execute();










        echo '<div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
        User Updated Successfully Now go back
      </div>';
      echo '<script>setTimeout(function() {
         window.location.href = ./Admin/admin.php;
    }, 2000);</script>';
    header('location: adminDashboard.php');
    return true;
      } else {
        echo '<div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
        Error updating record : 
      </div>'. $stmt->error;
         echo " " ;
      }
      
      $stmt->close();
      
//    header('location: ./banUser.php');


 
    }
}