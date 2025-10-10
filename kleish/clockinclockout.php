<?php
include 'kleishdb.php';  // Ensure your database connection is included
session_start();

// 1. Ensure employee is logged in
if (!isset($_SESSION['employee_id'])) {
    die("Employee not logged in.");
}

$employee_id = $_SESSION['employee_id'];
$current_time = date('Y-m-d H:i:s'); // Get current date and time

// 2. Check if action is 'clock_in' or 'clock_out'
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // 3. Clock In Logic
    if ($action == 'clock_in') {
        // Check if employee has already clocked in today
        $check_in_query = "SELECT * FROM attendance WHERE employee_id = '$employee_id' AND date = CURDATE()";
        $check_in_result = mysqli_query($conn, $check_in_query);

        if (mysqli_num_rows($check_in_result) > 0) {
            // Employee has already clocked in, show a message (or redirect)
            echo "You have already clocked in today.";
        } else {
            // Insert clock in time
            $insert_query = "INSERT INTO attendance (employee_id, date, check_in) VALUES ('$employee_id', CURDATE(), '$current_time')";
            $insert_result = mysqli_query($conn, $insert_query);

            if ($insert_result) {
                echo "You have successfully clocked in.";
            } else {
                echo "Error clocking in: " . mysqli_error($conn);
            }
        }
    }

    // 4. Clock Out Logic
    if ($action == 'clock_out') {
        // Check if employee has clocked in today
        $check_out_query = "SELECT * FROM attendance WHERE employee_id = '$employee_id' AND date = CURDATE() AND check_in IS NOT NULL";
        $check_out_result = mysqli_query($conn, $check_out_query);

        if (mysqli_num_rows($check_out_result) == 0) {
            // Employee hasn't clocked in, show a message (or redirect)
            echo "You cannot clock out without clocking in first.";
        } else {
            // Update clock out time
            $update_query = "UPDATE attendance SET check_out = '$current_time' WHERE employee_id = '$employee_id' AND date = CURDATE()";
            $update_result = mysqli_query($conn, $update_query);

            if ($update_result) {
                echo "You have successfully clocked out.";
            } else {
                echo "Error clocking out: " . mysqli_error($conn);
            }
        }
    }
} else {
    echo "Invalid action.";
}


// Close database connection
mysqli_close($conn);
?>

