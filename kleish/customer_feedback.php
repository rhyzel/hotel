<?php
// Start session to handle user login or admin details
session_start();
include_once 'kleishdb.php'; // Database connection file

// Check if user is logged in as admin
if (!isset($_SESSION['employee_id'])) {
    header("Location: customers.php"); // Redirect to login page if not logged in
    exit();
}

$name = $_SESSION['employee_id']; // Get admin name

// Fetching feedback data
$feedbackQuery = "SELECT * FROM customer_feedback ORDER BY date_submitted DESC"; // Fetch all feedback entries
$feedbackResult = mysqli_query($conn, $feedbackQuery);

// Check if the feedback query was successful
if (!$feedbackResult) {
    $errorMessage = "Error fetching customer feedback: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Feedback - Kleish Collection</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="customer_feedback.css"> 
</head>
<body>

<div class="container">
    <h1>Customer Feedback</h1>

    <?php if (isset($successMessage)): ?>
        <div class="message success"><?php echo $successMessage; ?></div>
    <?php elseif (isset($errorMessage)): ?>
        <div class="message error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>


        <div class="feedback-list">
            <?php if ($feedbackResult && mysqli_num_rows($feedbackResult) > 0): ?>
                <?php while ($feedbackRow = mysqli_fetch_assoc($feedbackResult)): ?>
                    <div class="feedback-entry">
                        <p><strong><?php echo $feedbackRow['customer_name']; ?></strong></p>
                        <p><em>⭐⭐⭐⭐⭐</em> - <?php echo $feedbackRow['feedback']; ?></p>
                        <p><strong>Items Purchased:</strong> <?php echo $feedbackRow['items_purchased']; ?></p>
                        <p><strong>Category:</strong> <?php echo $feedbackRow['category']; ?></p>
                        <p><em>Submitted on: <?php echo $feedbackRow['date_submitted']; ?></em></p>

                
                        <div class="feedback-photos">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if (!empty($feedbackRow["photo_$i"])): ?>
                                    <img src="uploads/<?php echo $feedbackRow["photo_$i"]; ?>" alt="Customer Photo" class="feedback-photo">
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No feedback found</p>
            <?php endif; ?>
        </div>
    </section>

</div>


</body>
</html>
