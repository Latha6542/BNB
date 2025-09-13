<?php
// config/config.php

// Database connection settings
$host = "localhost";      // Your database host, usually localhost
$user = "root";           // Your database username
$pass = "";               // Your database password
$dbname = "fundflow";     // Your database name

// Create MySQLi connection
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
