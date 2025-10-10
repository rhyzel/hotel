<?php
require_once 'kleishdb.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $promotion_name = htmlspecialchars(trim($_POST['promotion_name']));
    $promotion_description = htmlspecialchars(trim($_POST['promotion_description']));
    $promotion_type = htmlspecialchars(trim($_POST['promotion_type']));
    $promo_code = htmlspecialchars(trim($_POST['promo_code']));
    $start_date = htmlspecialchars(trim($_POST['start_date']));
    $end_date = htmlspecialchars(trim($_POST['end_date']));

    $sql = "INSERT INTO promotions (promotion_name, promotion_description, promotion_type, promo_code, start_date, end_date) 
            VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) { // Use $conn here
        $stmt->bind_param("ssssss", $promotion_name, $promotion_description, $promotion_type, $promo_code, $start_date, $end_date);

        if ($stmt->execute()) {
            echo "<script>alert('Promotion saved successfully!'); window.location.href='marketing.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error saving promotion: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>"; // Use $conn here
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketing Strategies</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="marketings.css">
</head>

<body>

    <div class="container">
    <h1>Marketing Tools</h1>
    <section class="promotions-grid-container">
        <div class="promo-buttons">
    <div class="create-promo-btn">
        <button id="showPromoForm" class="button create-btn">+ Create New Promotion</button>
        <a href="https://www.tiktok.com/business/" target="_blank" class="button tiktok-btn">Visit TikTok Business</a>
    </div>

    <?php
    // Fetch promotions from the database using $conn
    $result = $conn->query("SELECT * FROM promotions ORDER BY start_date DESC");
    ?>

    
</div>

    </section>
        <div class="promotions-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="promo-card">
                        <h3><?= htmlspecialchars($row['promotion_name']) ?></h3>
                        <p><?= htmlspecialchars($row['promotion_description']) ?></p>
                        <p><strong>Type:</strong> <?= htmlspecialchars($row['promotion_type']) ?></p>
                        <p><strong>Code:</strong> <?= htmlspecialchars($row['promo_code']) ?></p>
                        <p><strong>From:</strong> <?= htmlspecialchars($row['start_date']) ?><br>
                           <strong>To:</strong> <?= htmlspecialchars($row['end_date']) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-promotions-container">
                    <p>No promotions available.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Hidden popup form initially -->
    <div id="promotionFormPopup" class="popup-form">
        <div class="popup-content">
            <h2>Add New Promotion</h2>
            <form action="" method="POST" class="promotion-form">
                <label for="promotion_name">Promotion Name:</label>
                <input type="text" name="promotion_name" id="promotion_name" required>

                <label for="promotion_description">Description:</label>
                <textarea name="promotion_description" id="promotion_description" required></textarea>

                <label for="promotion_type">Promotion Type:</label>
                <input type="text" name="promotion_type" id="promotion_type" required>

                <label for="promo_code">Promo Code:</label>
                <input type="text" name="promo_code" id="promo_code" required>

                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date" required>

                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date" required>

                <button type="submit" class="button">Save Promotion</button>
            </form>
            <button id="closePopup" class="button">Close</button>
        </div>
    </div>
</div>

<script>
    // JavaScript to show the popup form when the button is clicked
    document.getElementById('showPromoForm').addEventListener('click', function() {
        document.getElementById('promotionFormPopup').style.display = 'flex';
    });

    // JavaScript to close the popup form
    document.getElementById('closePopup').addEventListener('click', function() {
        document.getElementById('promotionFormPopup').style.display = 'none';
    });
</script>

</body>
</html>
