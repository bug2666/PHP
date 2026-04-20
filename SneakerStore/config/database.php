<?php
$servername = "localhost";
$username = "pxzwksde_test";
$password = "KYDKsGH47WpFGU4zjcyb";
$dbname = "pxzwksde_test";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
