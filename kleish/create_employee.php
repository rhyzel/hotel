<?php
session_start();
require_once 'kleishdb.php';

// Fake email function for demo/testing (logs to a file)
function sendEmail($toEmail, $subject, $body) {
    $logFile = 'email_logs.txt';
    $logData = "To: $toEmail\nSubject: $subject\nBody: $body\n\n";
    file_put_contents($logFile, $logData, FILE_APPEND);
    echo "<p><strong>📧 Fake Email Sent! (Logged to email_logs.txt)</strong></p>";
    echo "<p>To: $toEmail</p>";
    echo "<p>Subject: $subject</p>";
    echo "<p>Body: $body</p>";
}

function sanitize_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_account'])) {
    $first_name = sanitize_input($_POST['first_name']);
    $last_name = sanitize_input($_POST['last_name']);
    $position = sanitize_input($_POST['position']);
    $email = sanitize_input($_POST['email']);
    $mobile_number = sanitize_input($_POST['mobile_number']);
    $birthdate = sanitize_input($_POST['birthdate']);
    $hire_date = sanitize_input($_POST['hire_date']);
    $salary = sanitize_input($_POST['salary']);
    $emergency_contact_name = sanitize_input($_POST['emergency_contact_name']);
    $emergency_contact_number = sanitize_input($_POST['emergency_contact_number']);

    // Optional fields
    $role = isset($_POST['role']) ? sanitize_input($_POST['role']) : null;
    $address = isset($_POST['address']) ? sanitize_input($_POST['address']) : null;
    $background = isset($_POST['background']) ? sanitize_input($_POST['background']) : null;
    $is_admin = 1;
  

    // Check for duplicate email or mobile_number
    $check_duplicate_sql = "SELECT COUNT(*) FROM employee WHERE email = ? OR mobile_number = ?";
    $stmt = $conn->prepare($check_duplicate_sql);

    if (!$stmt) {
        die("<p>❌ Prepare failed: " . htmlspecialchars($conn->error) . "</p>");
    }

    $stmt->bind_param("ss", $email, $mobile_number);
    $stmt->execute();
    $stmt->bind_result($duplicate_count);
    $stmt->fetch();
    $stmt->close();

    if ($duplicate_count > 0) {
        echo "<script>alert('⚠️ Email or mobile number already exists in the system.'); window.history.back();</script>";
        exit;
    }

    // Generate temporary password
    $temp_password = bin2hex(random_bytes(6)); // 12-char temp password
    $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
    $employee_id = generateEmployeeId();

    $sql = "INSERT INTO employee (
        employee_id, last_name, first_name, position, role, email, mobile_number, address, birthdate,
        hire_date, salary, emergency_contact_name, emergency_contact_number, is_admin, created_at,
        password, background
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?, ?)";
    

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("<p>❌ Prepare failed: " . htmlspecialchars($conn->error) . "</p>");
    }

    $stmt->bind_param(
        "sssssssssssdssis",    
        $employee_id,
        $last_name,
        $first_name,
        $position,
        $role,
        $email,
        $mobile_number,
        $address,
        $birthdate,
        $hire_date,
        $salary,
        $emergency_contact_name,
        $emergency_contact_number,
        $is_admin,
        $hashed_password,
        $background
    );
    

    if ($stmt->execute()) {
        echo "
        <script type='text/javascript'>
            alert('New employee added!\\nEmployee ID: $employee_id\\nTemporary Password: $temp_password');
            window.location.href = 'create_employee.php';
        </script>";
    } else {
        echo "<p>❌ Error: Could not execute query. " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
}

// Function to generate a unique employee ID
function generateEmployeeId() {
    global $conn;
    do {
        $employee_id = str_pad(rand(100000000, 999999999), 9, "0", STR_PAD_LEFT);
        $check_sql = "SELECT COUNT(*) FROM employee WHERE employee_id = ?";
        $stmt = $conn->prepare($check_sql);

        if (!$stmt) {
            die("<p>❌ Error generating ID: " . htmlspecialchars($conn->error) . "</p>");
        }

        $stmt->bind_param("s", $employee_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0);
    return $employee_id;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="create_employees.css">
</head>
<body>
    <div>
    </div>

    <h2>+ Add New Employee</h2>

    <form method="POST" action="">

        <div class="input-container">
            <input type="text" name="first_name" id="first_name" required>
            <label for="first_name">First Name</label>
        </div>

        <div class="input-container">
            <input type="text" name="last_name" id="last_name" required>
            <label for="last_name">Last Name</label>
        </div>

        <div class="input-container">
            <input type="text" name="position" id="position" required>
            <label for="position">Position</label>
        </div>

        <div class="input-container">
            <input type="email" name="email" id="email" required>
            <label for="email">Email Address</label>
        </div>

        <div class="input-container">
            <input type="text" name="mobile_number" id="mobile_number" required>
            <label for="mobile_number">Phone Number</label>
        </div>

        <div class="input-container">
            <input type="text" name="address" id="address" required>
            <label for="address">Address</label>
        </div>

        <div class="input-container">
            <input type="text" name="background" id="background" required>
            <label for="background">Background</label>
        </div>

        <div class="input-container">
            <input type="date" name="birthdate" id="birthdate" required>
            <label for="birthdate">Birthday</label>
        </div>

        <div class="input-container">
            <input type="text" name="role" id="role">
            <label for="role">Role (Optional)</label>
        </div>

        <div class="input-container">
            <input type="date" name="hire_date" id="hire_date" required>
            <label for="hire_date">Hire Date</label>
        </div>

        <div class="input-container">
            <input type="number" name="salary" id="salary" required step="0.01">
            <label for="salary">Salary (₱)</label>
        </div>

        <div class="input-container">
            <input type="text" name="emergency_contact_name" id="emergency_contact_name" required>
            <label for="emergency_contact_name">Emergency Contact Name</label>
        </div>

        <div class="input-container">
            <input type="text" name="emergency_contact_number" id="emergency_contact_number" required>
            <label for="emergency_contact_number">Emergency Contact Phone</label>
        </div>

        <button type="submit" name="create_account">Create Admin Account</button>
    </form>
</body>
</html>
