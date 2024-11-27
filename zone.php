<?php
session_start();
include("header.php");

// Include the database connection file
include("db_connection.php");

// Fetch the most recent background image from the database
include('db_connection.php'); 

$query = "SELECT image_path FROM bannerSet ORDER BY id DESC LIMIT 1"; // Get the most recent image
$result = mysqli_query($connect, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connect));
}

$row = mysqli_fetch_assoc($result);

if ($row) {
    $backgroundImage = $row['image_path'] ? $row['image_path'] : 'images/default-background.jpg';
} else {
    $backgroundImage = 'images/default-background.jpg';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WheelsOnRent</title>
    <!------CSS------>
    <link rel="stylesheet" href="stylee.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" 
    rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!----BOX ICON-----> 
    <link rel="stylesheet"
    href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
</head>
<body>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        height: 100vh;
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
    
    .page-banner h1{
        font-weight: normal;
    }

    section {
        margin-top: 200px;
        padding-top: 700px;
        height: 800px;
        
    }

    .topCenterText {
        color: #043272;
    }
    .topCenterText h2 {
        text-align: center;
        color: black;
        font-size: 25px;
        text-transform: capitalize;
        font-weight: 700;
        margin-bottom: 60px;
        margin-top: -60px;
    }

    .locUpdate img {
        margin-top: 40px;
        width: 380px;
        height: auto;
        border-radius: 5px;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
    }

    /* Style for the iframe container */
    .iframe-container {
        display: flex;
        justify-content: space-between; /* Space out text and map */
        align-items: center; /* Vertically align the items */
        height: 80vh;
        padding-right: 60px;
        margin-top: 0;
        background: linear-gradient(to bottom, #FFF4D7, #fff);
    }

    /* Style for the left section (location text) */
    .location-text {
        flex: 1; /* Make the text section flexible */
        padding: 20px;
        max-width: 400px; /* Limit the width */
        margin-left: 60px;
    }

    .location-text h3 {
        font-size: 30px;
        font-weight: normal;
        color: black;
        margin-bottom: 20px;
    }

    .location-text p {
        font-size: 16px;
        color: #333;
        line-height: 1.5;
    }

    /* Style for the iframe */
    iframe {
        flex: 2; /* Make the map section take up more space */
        width: 100%;
        max-width: 900px;
        height: 450px;
        border: none;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }
</style>
    
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

<!-- Banner -->
<div class="page-banner" style="background-image: url('<?php echo $backgroundImage; ?>');">
    <div class="banner-section">
        <h1>Our Location</h1>
    </div>
</div>

<div class="container">
    <!-- Google Maps Embed with "Visit Us" Text -->
    <div class="iframe-container">
        <!-- Left side: Location address -->
        <div class="location-text">
            <h3>Visit Us</h3>
            <p>
                Klinik Tanah Air 24 Jam<br>
                Desa Sri Hartamas, Malaysia<br>
                Plaza Prismaville, 1, Jalan 19/70a, Desa Sri Hartamas, 50480 Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur
            </p>
        </div>
        
        <!-- Right side: Google Maps Embed -->
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d443.63886301623734!2d101.6418253634459!3d3.162474008784163!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc49d22817de95%3A0x866df0dbd0ae1fe2!2sKlinik%20Tanah%20Air%2024%20Jam%20(Tanah%20Air%20Clinic%2024%20Hours)%20%2C%20Desa%20Sri%20Hartamas!5e0!3m2!1sen!2smy!4v1731945052832!5m2!1sen!2smy" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</div>

<script src="java.js"></script>
</body>
</html>
