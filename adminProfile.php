<?php
session_start(); // Start the session

// Include database connection
include('db_connection.php'); 

// Fetch the most recent background image from the database
$query = "SELECT image_path FROM bannerSet ORDER BY id DESC LIMIT 1"; // Get the most recent image
$result = mysqli_query($connect, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $backgroundImage = !empty($row['image_path']) ? $row['image_path'] : 'images/default-background.jpg';
} else {
    $backgroundImage = 'images/default-background.jpg'; // Default image if query fails
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Profile - WheelsOnRent</title>

    <link rel="stylesheet" href="stylee.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"/>

    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"/>

    <link
      rel="stylesheet"
      href="https://unpkg.com/boxicons@latest/css/boxicons.min.css"
    />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('<?php echo $backgroundImage; ?>'); /* Dynamically set background */
            background-size: cover;
            background-position: center;
            height: 100vh;
        }

        section {
          padding: 5% 10%;
          background-color: transparent;
        }

        /* Adjustments for the profile section and buttons */
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        
    }

    .profileSection {
      background-color: rgba(255, 255, 255, 0.8);
      padding: 3% 5%;
      max-width: 85%;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .profileSection h1 {
        color: #000;
        text-align: center;
        margin-bottom: 20px;
        font-weight: normal;
        text-shadow: 2px 2px 10px rgba(255, 255, 255, 0.7);
    }

    /* Shared styles for navigation and navigationEdit */
    /* Shared styles for both navigation sections */
    .navigation {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }

    /* Common button styles for both sections */
    a.btn, a.btn-edit {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 10px;
        padding: 15px 30px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 16px;
        text-align: center;
        width: 200px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    /* Navigation buttons - default section */
    .navigation a {
        background-color: #333;
        color: #fff;
    }

    .navigation a:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    /* Navigation Edit buttons - new section with different color scheme */
    .navigationEdit a {
        background-color: #ffc70b; /* Blue background */
        color: #333;
    }

    .navigationEdit a:hover {
        background-color: #0056b3; /* Darker blue on hover */
        transform: scale(1.05);
        color: white;
    }



      </style>

  </head>
  <body>
    <div class="container">
    <div class="box form-box">
        
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
    <section class="profileSection">
      <div class="container">
        <h1>Welcome, Admin</h1>
        <div class="navigation">
            <a href="appointmentsView.php" class="btn">View appointments</a>
            <a href="adminRegister.php" class="btn">Add new admin</a>
            <a href="uploadBackground.php" class="btn">Upload site background</a>
            <a href="doctorsUpload.php" class="btn">Add new doctor</a>
            <a href="uploadService.php" class="btn">Add new service</a>
            <a href="uploadPanels.php" class="btn">Add new panel</a>
            <!-- <a href="uploadLocation.php" class="btn">Add new location</a> -->
            <a href="uploadPromotion.php" class="btn"> Add new promotion</a>
            
        </div>
        <div>

        <div class="navigationEdit">
            <a href="editAdmin.php" class="btn-edit">Edit Admin</a>
            <a href="editDoctor.php" class="btn-edit">Edit Doctors</a>
            <a href="editPanel.php" class="btn-edit">Edit Panel</a>
            <a href="editService.php" class="btn-edit">Edit Services</a>
        </div>
    </section>
</div>
</body>
</html>
