<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session if none exists
}
require_once "./class/dbconnection.php";

class loginClass
{
    public $email, $password;

    public function getUserDataByEmailThenCompare()
    {
        $db = new db(); // Create a new database connection

        // SQL query to select all fields where email matches
        $queryCheck = "SELECT * FROM users WHERE email = ?";

        // Prepare the statement to check for existing user
        if ($stmtCheck = $db->conn->prepare($queryCheck)) {
            // Bind the email parameter to the query
            $stmtCheck->bind_param("s", $this->email);

            // Execute the statement
            if ($stmtCheck->execute()) {
                // Fetch the result into an associative array
                $result = $stmtCheck->get_result();

                // Check if any rows are returned
                if ($result->num_rows > 0) {
                    // Fetch data as an associative array
                    $userData = $result->fetch_assoc();

                    // Compare password (you should use password hashing in a real application)
                    if ($this->password == $userData['password']) {

                        // Check if the user is banned
                        if ($userData['ban_until'] == NULL || new DateTime() > new DateTime($userData['ban_until'])) {
                            $_SESSION['email'] = $this->email; 
                            $_SESSION['userId'] = $userData['id'];
                            $_SESSION['username'] = $userData['username'];
                            $_SESSION['role'] = $userData['role'];
                            $_SESSION['login'] = 1;
                            $_SESSION['image'] = $userData['profilePic'];

                            return $userData['role'];
                        } else {
                            // Display ban message
                            return "Your account is banned until " . htmlspecialchars($userData['ban_until']);
                        }
                    } else {
                        // Invalid password
                        return "Invalid email or password.";
                    }
                } else {
                    // No user found with the provided email
                    return "No account found with this email.";
                }
            } else {
                // Handle errors in query execution
                return "Error executing query: " . $stmtCheck->error;
            }

            // Close the prepared statement
            $stmtCheck->close();
        } else {
            // Handle errors in query preparation
            return "Error preparing query: " . $db->conn->error;
        }
    }
}
