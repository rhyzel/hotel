<?php
// logout.php

session_start(); // Start the session

// Destroy all session variables
$_SESSION = array();

// Destroy the session itself
session_destroy();

// Optional: Redirect to login page
header("Location: index.php");
exit;
?>
