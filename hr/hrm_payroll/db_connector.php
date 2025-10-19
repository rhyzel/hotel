<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    $conn = null;
}
?>
