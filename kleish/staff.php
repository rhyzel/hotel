<?php
// Include the database connection
require_once 'kleishdb.php';

// Check if the database connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Employee List</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="stafff.css">
    <style>
 
    </style>
</head>

<body>

    <h2>Employee Directory</h2>

    <button class="add-employee-btn" onclick="openCreateEmployee()">+ Add Employee</button>

    <input type="text" id="employeeSearch" placeholder="Search employee..." style="margin-bottom: 15px; padding: 8px; width: 300px; font-size: 14px; border: 1px solid #ccc; border-radius: 5px;">

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Position</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Birthday</th>
                <th>Hire Date</th>
                <th>Salary</th>
                <th>Emergency Contact</th>
                <th>Admin</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM employee WHERE is_admin = 1";
            $result = mysqli_query($conn, $query);

            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    $count = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $emergencyName = !empty($row['emergency_contact_name']) ? htmlspecialchars($row['emergency_contact_name']) : 'N/A';
                        $emergencyPhone = !empty($row['emergency_contact_number']) ? htmlspecialchars($row['emergency_contact_number']) : 'N/A';

                        echo "<tr>";
                        echo "<td>" . $count++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['employee_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['position']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['mobile_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['birthdate']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['hire_date']) . "</td>";
                        echo "<td>₱" . number_format($row['salary'], 2) . "</td>";
                        echo "<td>" . $emergencyName . " (" . $emergencyPhone . ")</td>";
                        echo "<td><span class='admin-badge'>Admin</span></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' style='text-align:center;'>No admin found 🥲</td></tr>";
                }
            } else {
                echo "<tr><td colspan='11' style='text-align:center;'>Error fetching data: " . htmlspecialchars(mysqli_error($conn)) . "</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        document.getElementById('employeeSearch').addEventListener('keyup', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        function openCreateEmployee() {
            window.location.href = "create_employee.php";
        }
    </script>

</body>
</html>
