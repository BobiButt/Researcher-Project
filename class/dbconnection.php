<?php
class db {
    public $conn;

    public function __construct() {
        // Assign the connection to the class property $conn
        $this->conn = mysqli_connect('localhost', 'root', '', 'researcher');

        // Check if the connection was successful
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }
}
?>
