<?php
require_once "./class/dbconnection.php";
class registerClass{

    public $username, $email, $password, $role,$conn, $file_name, $file_size,$file_temp;
    public function checkEpmty()
    {
        if(empty($this->username) && empty($this->email) && empty($this->password) && empty($this->role) && empty($this->imageName)){
            return 'true';
          //   echo '<div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
          //   PLZ Fill all the fields!
          // </div>';
        }else{
            return 'false';
      //       echo '<div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
      //   Register Successfull!
      // </div>';
     

        }
    }
    public function passwordValidation()
    {
        if(strlen($this->password)<8){
            echo '<div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
        Password must contain at least 8 characters!
      </div>';
    }
    else {
        return true;
    }
}
public function emailValidation()
{
    if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            echo '<div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
        Invalid Email!
      </div>';
    }
    else {
        return true;
    }
}



public function tableCreat()
{
  $sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    profilePic VARCHAR(255) DEFAULT NULL,
    coverPic VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,            -- Field to store user bio
    location VARCHAR(255) DEFAULT NULL, -- Field to store user location
    website VARCHAR(255) DEFAULT NULL,  -- Field to store user website
    caption TEXT DEFAULT NULL,         -- Field to store profile caption
    affiliations TEXT DEFAULT NULL,    -- Field to store user affiliations (JSON or comma-separated)
    researchInterest TEXT DEFAULT NULL, -- Field to store user research interests (JSON or comma-separated)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ban_until DATETIME NULL            -- Field to store ban expiration date
)";

$result = mysqli_query($this->conn, $sql);

   if($result){
    return true;
    }else{
    echo "Error creating table: ". mysqli_error($this->conn);
    return false;
    }
}

public function imageCheckThenInsert()
{
    // $permited  = array('jpg', 'jpeg', 'png', 'gif');//data type of the image we will define here
                   
    // $div = explode('.', $this->file_name);//miage.jpg [0]=>image [1] jpg
    // print_r(value: $div); 2 last one
    // $file_ext = strtolower(end($div));
    // echo time();
    // $unique_image = substr(md5(time()), 0, 10) . '.' . $file_ext;
    // $uploaded_image = "uploads/" . $unique_image;
  //   $uploaded_image = $unique_image;
  // $upload_dir = "profilePic/"; // Folder to store images
  // $uploaded_image = $upload_dir . $unique_image; // Full path to store the image
    // if (empty($this->file_name)) {
        // echo "<span class='error'>Please Select any Image !</span>";
      //   echo '<div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
      //   Please Select any Image
      // </div>';
        
    // } elseif ($this->file_size > 1048567) {
    //     echo "<span class='error'>Image Size should be less then 1MB!
    //  </span>";
  //    echo '<div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
  //    Image Size should be less then 1MB!
  //  </div>';
    // } elseif (in_array($file_ext, $permited) === false) {
        // echo "<span class='error'>You can upload only:-"
        //     . implode(', ', $permited) . "</span>";

    //         echo '<div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
    //         You can upload only:- '. implode(', ', $permited) .'
    //       </div>';
    // } else {
        // move_uploaded_file($this->file_temp, $uploaded_image);
        $sql = "SELECT * FROM users WHERE email = '$this->email'";
        $result = mysqli_query($this->conn, $sql);
        if (mysqli_num_rows($result) > 0) {
           echo '<div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
           Email Already Exist
         </div>';
        } else {
        $query = "INSERT INTO users (username, email, password, role)
    
    VALUES('$this->username','$this->email','$this->password','$this->role')";
        // $inserted_rows = $db->insert($query);
        $inserted_rows = mysqli_query($this->conn,$query);
        if ($inserted_rows) {
    //         echo "<span class='success'>Image Inserted Successfully.
    //  </span>";
    echo '<div id="customAlert" style="display:block; position:fixed; top:20%; left:50%; transform:translate(-50%, -50%); background-color:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:5px; z-index:1000;">
    Register successfull
    </div>';
    echo'<script>
    setTimeout(() => {
    window.location.href = "./login.php";
    }, 2000);
    </script>';
        } else {
            // echo "<span class='error'>Image Not Inserted !</span>";
          //  echo' <div class="d-flex justify-content-center w-100">
          //   <span class="fields">Unknown Error (check your internet connection)
          //   <button href="" class="btn border border-black" onclick="fade2()">X</button></span>
          //   </div>
          //   ';
            echo 'Query failed: ' . mysqli_error($this->conn);
        }
    }
    
    }
}



// echo '<script>
// document.getElementById("customAlert").style.display = "block";
// setTimeout(function() {
//   window.location.href = "otherfile.php";
// }, 2000);
// </script>';
?>
