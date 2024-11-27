<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: userLogin.php");
    exit();
}

// Fetch the user ID from session
$user_id = $_SESSION['user_id']; 
// Fetch the most recent background image from the database
include('db_connection.php');

$query = "SELECT image_path FROM bannerSet ORDER BY id DESC LIMIT 1"; // Get the most recent image
$result = mysqli_query($connect, $query);

// Check if the query is successful
if (!$result) {
    die("Query failed: " . mysqli_error($connect));
}

$row = mysqli_fetch_assoc($result);

if ($row) {
    // Use the image path if it exists
    $backgroundImage = $row['image_path'] ? $row['image_path'] : 'images/default-background.jpg';
} else {
    // If no result is returned, fall back to default image
    $backgroundImage = 'images/default-background.jpg';
}

// Check if the ID is passed in the URL
if (isset($_GET['id'])) {
    $appointment_id = mysqli_real_escape_string($connect, $_GET['id']);
    
    // Fetch the appointment details from the database
    $query = "SELECT * FROM appointment WHERE id = '$appointment_id'";
    $result = mysqli_query($connect, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($connect));
    }

    $appointment = mysqli_fetch_assoc($result);

    if ($appointment) {
        // Store the appointment data for display
        $name = $appointment['name'];
        $phone_number = $appointment['phone_number'];
        $email = $appointment['email'];
        $message = $appointment['message'];
        $created_at = $appointment['created_at'];
    } else {
        echo "No appointment found with that ID.";
        exit();
    }
} else {
    echo "No appointment ID provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details</title>
    <link rel="stylesheet" href="stylee.css"> <!-- Link to your CSS file -->


    <style>
      /* General Styles */
      body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
      }

      .page-banner {
            height: 500px;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
        }

      .banner-section {
        position: relative;
        z-index: 10;
        text-align: center;
        color: #fff;
      }
/* Appointment Details Box Styling */
.appointment-details {
    max-width: 800px;
    margin: 80px auto; /* Center the box horizontally */
    padding: 20px;
    background-color: #fff;
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
}

.appointment-details h1 {
    font-size: 28px;
    text-align: center;
    margin-bottom: 70px;
    color: #333;
}

.appointment-details p {
    font-size: 18px;
    margin: 10px 0;
    color: #555;
    padding-left: 20px;
}

.appointment-details strong {
    color: #333;
}

      /* Heading Styling */
      h1 {
        font-size: 24px;
        text-align: center;
        margin-bottom: 20px;
        font-family: "Poppins";
        font-weight: normal;
      }
    </style>


</head>
<body>

<header>
        <a href="home.php" class="logoo"><img src="klinikimg/klinikLogo.png" alt="logo"></a>
        
        <ul class="navbar">
            <li><a href="home.php">Home</a></li>
            <li><a href="doctors.php">Doctors</a></li>
            <li><a href="service.php">Services</a></li>
            <li><a href="panels.php">Panels</a></li>
            <li><a href="zone.php">Location</a></li>
            <li class="dropdown">
                <a href="#" class="dropbtn">Log in / Sign up ▾</a>
                <ul class="dropdown-content">
                    <li><a href="userLogin.php">User Login</a></li>
                    <li><a href="adminLogin.php">Admin Login</a></li>
                </ul>
            </li>
            <li class="btnLogout">
                <a href="userLogin.php">Log Out</a>
            </li>
        </ul>

        <!-- <div class="navIcon">
        
        <a href="appointment.php" class="appointmentButton">Book an Appointment</a>   
            <a href="userLogin.php" class="logoutButton"><c class='bx bx-log-out'></l></a>
            <div class="bx bx-menu" id="menuIcon"></div>
        </div> -->

    </header>
    <!-- Banner -->
    <div
  class="page-banner"
  style="background-image: url('<?php echo $backgroundImage; ?>');"
>
  <div class="banner-section">
    <div>
      <h1>Appointments</h1>
    </div>
  </div>
</div>

    <div class="appointment-details">
        <h1>Appointment Information</h1>
        <div>
            <p><strong>Name:</strong> <?php echo $name; ?></p>
            <p><strong>Phone Number:</strong> <?php echo $phone_number; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>Notes:</strong> <?php echo nl2br($message); ?></p>
            <p><strong>Submitted At:</strong> <?php echo $created_at; ?></p>
        </div>
    </div>
    <script>
  window.addEventListener("scroll", function() {
    const header = document.querySelector("header");
    header.classList.toggle("sticky", window.scrollY > 0);
  });
</script>
</body>
</html>
