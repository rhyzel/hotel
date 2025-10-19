<?php
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = $_POST['requested_by'];
    $item_id = $_POST['item'];
    $quantity = $_POST['quantity'];

    if (!empty($staff_id) && !empty($item_id) && !empty($quantity)) {
        $stmt = $conn->prepare("INSERT INTO inventory_supplies (staff_id, item_id, quantity, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->bind_param("sii", $staff_id, $item_id, $quantity);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Supply request submitted successfully!');
                    window.location.href = 'housekeeping_inventory.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error submitting request. Please try again.');
                    window.location.href = 'request_supply.php';
                  </script>";
        }

        $stmt->close();
    } else {
        echo "<script>
                alert('Please fill in all fields.');
                window.location.href = 'request_supply.php';
              </script>";
    }
}

$conn->close();
?>
