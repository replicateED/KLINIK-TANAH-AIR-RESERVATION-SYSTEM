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
include("db_connection.php");

// Fetch all panels from the database
$query = "SELECT * FROM panels";
$result = mysqli_query($connect, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Panels found in the database
    $panels = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    // No panels found
    $panels = [];
}

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
    <title>Available Panels</title>
    <link rel="stylesheet" href="stylee.css">
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


        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(to bottom, #FFF4D7, #fff);
            padding-bottom: 100px;
        }

        .panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #fff;
            border-radius: 8px;
            padding: 15px;
            width: 200px;
            margin: 15px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-align: center;
        }

        .panel:hover {
            transform: translateY(-5px);
        }

        .panel img {
            max-width: 100%;
            max-height: 100px;
            object-fit: contain;
            transition: opacity 0.3s ease;
        }

        .panel .panel-info {
            display: none;
            margin-top: 10px;
            font-size: 16px;
            color: #333;
        }

        .panel:hover .panel-info {
            display: block;
        }

        .title {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 40px;
            width: 100%;
        }

        /* Optional: Sticky navbar on scroll */
        window.addEventListener("scroll", function() {
            const header = document.querySelector("header");
            header.classList.toggle("sticky", window.scrollY > 0);
        });
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

    <!-- Banner -->
    <div class="page-banner" style="background-image: url('<?php echo $backgroundImage; ?>');">
        <div class="banner-section">
            <h1>Available Panels</h1>
        </div>
    </div>

    <div class="container">
        <h1 class="title"></h1>

        <?php
        if (empty($panels)) {
            echo "<p>No panels found.</p>";
        } else {
            // Loop through the panels and display them
            foreach ($panels as $panel) {
                echo "
                    <div class='panel'>
                        <img src='" . $panel['logo'] . "' alt='Panel Logo'>
                        <div class='panel-info'>
                            <h3>" . $panel['panel_name'] . "</h3>
                        </div>
                    </div>
                ";
            }
        }
        ?>
    </div>

    <script>
  window.addEventListener("scroll", function() {
    const header = document.querySelector("header");
    header.classList.toggle("sticky", window.scrollY > 0);
  });
</script>

</body>
</html>
