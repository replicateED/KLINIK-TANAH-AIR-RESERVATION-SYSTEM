<?php
session_start(); // Start the session

// Check if the necessary data is available in the session
if(isset($_SESSION['booking_id']) && isset($_SESSION['user_id']) && isset($_SESSION['car_id']) && isset($_SESSION['booking_date']) && isset($_SESSION['duration']) && isset($_SESSION['status']) && isset($_SESSION['total_amount'])) {
    // Assign the session variables to local variables
    $booking_id = $_SESSION['booking_id'];
    $user_id = $_SESSION['user_id'];
    $car_id = $_SESSION['car_id'];
    $booking_date = $_SESSION['booking_date'];
    $duration = $_SESSION['duration'];
    $status = $_SESSION['status'];
    $total_amount = $_SESSION['total_amount'];
} else {
    // Redirect the user if session data is not available
    header("Location: error_page.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <!-- Include any CSS stylesheets and necessary files here -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .receipt {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        h1, h2 {
            text-align: center;
        }
        .receipt-details {
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }
        .receipt-details p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <h1>Payment Receipt</h1>
        <div class="receipt-details">
            <h2>Booking Details</h2>
            <p><strong>Booking ID:</strong> <?php echo $booking_id; ?></p>
            <p><strong>User ID:</strong> <?php echo $user_id; ?></p>
            <p><strong>Car ID:</strong> <?php echo $car_id; ?></p>
            <p><strong>Booking Date:</strong> <?php echo $booking_date; ?></p>
            <p><strong>Duration:</strong> <?php echo $duration; ?> days</p>
            <p><strong>Status:</strong> <?php echo $status; ?></p>
            <p><strong>Total Amount Paid:</strong> RM<?php echo $total_amount; ?></p>
        </div>
        <p style="text-align: center; margin-top: 20px;">Thank you for your payment!</p>
    </div>
</body>
</html>
