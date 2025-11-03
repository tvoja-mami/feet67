<?php
$host = "localhost";
$user = "MySqlUser";
$password = "MyPassword123!";
$database = "school_project_db";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
