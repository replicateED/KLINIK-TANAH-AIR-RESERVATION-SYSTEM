<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

// Fetch the user ID from session
$user_id = $_SESSION['user_id'];

include('db_connection.php'); // Database connection

// Fetch the most recent background image from the database
$query = "SELECT image_path FROM bannerSet ORDER BY id DESC LIMIT 1";
$result = mysqli_query($connect, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $backgroundImage = !empty($row['image_path']) ? $row['image_path'] : 'images/default-background.jpg';
} else {
    $backgroundImage = 'images/default-background.jpg'; // Default image if query fails
}

// Fetch appointments for the logged-in user
$query = "SELECT * FROM appointment WHERE user_id = $user_id ORDER BY id DESC";
$result = mysqli_query($connect, $query);

if (!$result) {
    die("Error fetching appointments: " . mysqli_error($connect));
}

// Handle status update requests
if (isset($_GET['status']) && isset($_GET['id'])) {
    $appointmentId = intval($_GET['id']);
    $newStatus = $_GET['status']; // Either 'approve' or 'reject'
    
    // Make sure that the status is being updated correctly
    if ($newStatus === 'approve') {
        $newStatus = 'accepted'; // Update the status to accepted
    } elseif ($newStatus === 'reject') {
        $newStatus = 'rejected'; // Update the status to rejected
    } else {
        $newStatus = 'pending'; // Default status in case of unknown action
    }

    // Update the status in the database
    $updateStatusQuery = "UPDATE appointment SET status = '$newStatus' WHERE id = $appointmentId AND user_id = $user_id";
    if (mysqli_query($connect, $updateStatusQuery)) {
        echo "<script>alert('Appointment status updated successfully!'); window.location='appointmentsView.php';</script>";
    } else {
        echo "<script>alert('Error updating status!'); window.location='appointmentsView.php';</script>";
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $deleteQuery = "DELETE FROM appointment WHERE id = $deleteId AND user_id = $user_id";
    if (mysqli_query($connect, $deleteQuery)) {
        echo "<script>alert('Appointment deleted successfully!'); window.location='appointmentsView.php';</script>";
    } else {
        echo "<script>alert('Error deleting appointment!'); window.location='appointmentsView.php';</script>";
    }
}

// Handle edit request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $editId = intval($_POST['edit_id']);
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $phone_number = mysqli_real_escape_string($connect, $_POST['phone_number']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $message = mysqli_real_escape_string($connect, $_POST['message']);

    $updateQuery = "UPDATE appointment SET 
        name = ?, 
        phone_number = ?, 
        email = ?, 
        message = ? 
        WHERE id = ? AND user_id = ?";
        
    if ($stmt = mysqli_prepare($connect, $updateQuery)) {
        mysqli_stmt_bind_param($stmt, "ssssii", $name, $phone_number, $email, $message, $editId, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Appointment updated successfully!'); window.location='appointmentsView.php';</script>";
        } else {
            echo "<script>alert('Error updating appointment data!'); window.location='appointmentsView.php';</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Database error!'); window.location='appointmentsView.php';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <link rel="stylesheet" href="stylee.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('<?php echo $backgroundImage; ?>');
            background-size: cover;
            background-position: center;
            height: 100vh;
        }

        .container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    margin-top: 100px;
    width: 100%;
}

.box.form-box {
    background-color: rgba(255, 255, 255, 0.9);
    padding: 20px;
    border-radius: 10px;
    width: 90%; /* Increase the width to fit the table and buttons */
    max-width: 1000px; /* Ensure the box has a larger maximum width */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

table {
    width: 100%; /* Make table width 100% of its container */
    border-collapse: collapse;
    margin-top: 20px;
    table-layout: fixed; /* Prevent table columns from expanding too much */
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 12px;
    text-align: left;
    word-wrap: break-word; /* Ensures long text breaks into multiple lines */
}

h2 {
    text-align: center;
}

th {
    background-color: #f2f2f2;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    width: 200px;
    
}

.tablee{
    width: auto;
}

.btn {
    background-color: #e0a800;
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    margin-bottom: 4px;
    text-decoration: none;
    display: inline-block; /* Make sure the buttons are inline-block elements */
}

.btn:hover {
    background-color: #000;
}

.btn-approve {
    background-color: #40A800;
}

.btn-reject {
    background-color: #dc3545;
}

.status {
    font-weight: normal;
}

.status-pending {
    color: #dc3545;
}

.status-accepted {
    color: #40A800;
}

.status-rejected {
    color: #dc3545;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
    padding-top: 60px;
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

        /* Fix for the textarea */
        textarea {
            width: 100%;                /* Ensure the textarea takes up 100% of the container's width */
            min-width: 0;               /* Prevent the textarea from expanding beyond the container */
            max-width: 100%;            /* Ensure the textarea does not stretch beyond the container width */
            min-height: 100px;          /* Set a minimum height */
            max-height: 200px;          /* Set a maximum height */
            resize: none;               /* Disable manual resizing */
            overflow-y: auto;           /* Enable vertical scrolling if content exceeds max height */
            box-sizing: border-box;     /* Ensure padding doesn't affect the width/height */
        }
    </style>
</head>
<body>
<header>
        <a href="home.php" class="logoo"><img src="klinikimg/klinikLogo.png" alt="logo"></a>
        
        <ul class="navbar">
            <li><a href="home.php">Home</a></li>
            <li><a href="doctors.php">Doctors</a></li>
            <li class="dropdown">
                <a href="#" class="dropbtn">Services ▾</a>
                <ul class="dropdown-content">
                    <li><a href="service.php">Our Services</a></li>
                    <li><a href="appointment.php">Appointment</a></li>
                
                </ul>
            </li>
            <li><a href="panels.php">Panels</a></li>
            <li><a href="zone.php">Location</a></li>
            <li class="dropdown">
                <a href="#" class="dropbtn">Profile ▾</a>
                <ul class="dropdown-content">
                    <li><a href="userProfile.php">Profile</a></li>
                    <li><a href="userLogin.php">User Login</a></li>
                    <li><a href="adminLogin.php">Admin Login</a></li>
                </ul>
            </li>
        </ul>

        <!-- <div class="navIcon">
        
        <a href="appointment.php" class="appointmentButton">Book an Appointment</a>   
            <a href="userLogin.php" class="logoutButton"><c class='bx bx-log-out'></l></a>
            <div class="bx bx-menu" id="menuIcon"></div>
        </div> -->

    </header>

<div class="container">
    <div class="box form-box">
        <h2>Your Appointments</h2>
        <table class="tablee">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Appointment Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']); ?></td>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= htmlspecialchars($row['phone_number']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td><?= htmlspecialchars($row['message']); ?></td>
                        <td><?= htmlspecialchars($row['appointment_date']); ?></td>
                        <td class="status status-<?= strtolower($row['status']); ?>">
                            <?= htmlspecialchars($row['status']); ?>
                        </td>
                        <td class="action-buttons">
                            <a href="appointmentsView.php?status=approve&id=<?= $row['id'] ?>" class="btn btn-approve">Approve</a>
                            <a href="appointmentsView.php?status=reject&id=<?= $row['id'] ?>" class="btn btn-reject">Reject</a>
                            <a href="appointmentsView.php?delete=<?= $row['id'] ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div style="margin-top: 20px; text-align: center;">
            <a href="adminProfile.php" class="btn">Back</a>
        </div>
    </div>
</div>
</body>
</html>

