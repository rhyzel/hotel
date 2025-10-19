<?php
include __DIR__ . '/../db.php';

$staff_query = "SELECT staff_id, first_name, last_name 
                FROM staff 
                WHERE department_name = 'Housekeeping' 
                AND employment_status = 'Active'";
$staff_result = $conn->query($staff_query);

$supply_query = "SELECT item_id, item, category 
                 FROM inventory 
                 WHERE category IN (
                    'Hotel Supplies',
                    'Cleaning & Sanitation',
                    'Utility Products',
                    'Office Supplies',
                    'Toiletries',
                    'Laundry & Linen'
                 )";
$supply_result = $conn->query($supply_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Supply</title>
    <link rel="stylesheet" href="request_supply.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
        <div class="request-container">
            <h1><i class="fas fa-box-open"></i> Request Housekeeping Supply</h1>

            <form action="process_request.php" method="POST" class="request-form">
                <div class="form-group">
                    <label for="requested_by">Requested By</label>
                    <select id="requested_by" name="requested_by" required>
                        <option value="">-- Select Housekeeping Staff --</option>
                        <?php
                        if ($staff_result && $staff_result->num_rows > 0) {
                            while ($row = $staff_result->fetch_assoc()) {
                                echo "<option value='{$row['staff_id']}'>{$row['first_name']} {$row['last_name']}</option>";
                            }
                        } else {
                            echo "<option disabled>No housekeeping staff found</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="item">Select Item</label>
                    <select id="item" name="item" required>
                        <option value="">-- Select Supply Item --</option>
                        <?php
                        if ($supply_result && $supply_result->num_rows > 0) {
                            while ($row = $supply_result->fetch_assoc()) {
                                echo "<option value='{$row['item_id']}'>{$row['item']} ({$row['category']})</option>";
                            }
                        } else {
                            echo "<option disabled>No available supplies</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="1" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Submit Request</button>
                    <button type="button" class="btn-cancel" onclick="window.location.href='housekeeping_inventory.php'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
