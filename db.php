<?php
$host = "localhost";
$user = "root"; // Change if needed
$password = ""; // Change if needed
$database = "movie_db";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
