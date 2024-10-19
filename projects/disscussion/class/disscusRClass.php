<?php
require_once "../.././class/dbconnection.php";
class discuss{

    public function __construct()
    {
        $db = new db;
        $sql = "CREATE TABLE IF NOT EXISTS project_collaborators (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    project_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    role ENUM('admin', 'member') DEFAULT 'member', -- Role in the project
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    
);
";
$member = mysqli_query($db->conn,$sql);
    }
    public function fetchColaborators($project_id)
    {
        $db = new db;
        $sql = "SELECT * FROM project_collaborators WHERE project_id = '$project_id'";
        $result = mysqli_query($db->conn,$sql);
       if ($result)
       {
       return $result;

        while ($row = mysqli_fetch_assoc($result)) {
            echo "hooooooooooooooo";
            print_r($row);
        }
       }
    }
    public function username($user_id)
    {
        $db = new db;
        $sql = "SELECT username FROM users WHERE  id = '$user_id'";

        $result = mysqli_query($db->conn,$sql);
        $row = mysqli_fetch_assoc($result);
       return $row['username'];
    }
    public function fetchTime($project_id)
    {
        $db = new db;
        $sql = "SELECT * FROM projects WHERE id  = '$project_id'";

        $result = mysqli_query($db->conn,$sql);
        $row = mysqli_fetch_assoc($result);
       return $row;
    }
    public function getRequest($project_id)
    {
        $db = new db;
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
        $sql2 = "SELECT * FROM user_requests WHERE project_id = '$project_id' AND status = 'pending'";
        $result = mysqli_query($db->conn,$sql2);
       return $result;      
    }
    public function accept($user_id,$project_id,$loginUserId){
        $db = new db;
        $sql = "UPDATE user_requests SET status = 'accepted', join_date = CURRENT_TIMESTAMP WHERE user_id = '$user_id' AND project_id = '$project_id'";
        $result = mysqli_query($db->conn,$sql);
        if($result){
            echo "success";
            $sql = "INSERT INTO project_collaborators (project_id, user_id, role) VALUES ('$project_id', '$user_id', 'member')";

        $result = mysqli_query($db->conn,$sql);
            // require_once "./disscusR.php";
header("Location:./disscusR.php?user_id=".$loginUserId."&& project_id=".$project_id."");

        }else{
            echo "fail";
        }
        
    }
}
?>