<?php
session_start();
// Clear HRM session keys
unset($_SESSION['hrm_logged_in']);
unset($_SESSION['hrm_user']);
session_destroy();
header('Location: login.php');
exit;
