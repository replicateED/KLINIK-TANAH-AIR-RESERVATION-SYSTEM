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

if (!$result) {
    die("Query failed: " . mysqli_error($connect));
}

$row = mysqli_fetch_assoc($result);

if ($row) {
    $backgroundImage = $row['image_path'] ? $row['image_path'] : 'images/default-background.jpg';
} else {
    $backgroundImage = 'images/default-background.jpg';
}

// Fetch the services grouped by category from the database
$query = "SELECT category, service_name FROM services ORDER BY FIELD(category, 'General', 'Pediatrics', 'Cardiology', 'Dermatology', 'Other'), service_name";
$servicesResult = mysqli_query($connect, $query);

if (!$servicesResult) {
    die("Error fetching services: " . mysqli_error($connect));
}

// Process the results into a grouped array
$servicesByCategory = [];
while ($service = mysqli_fetch_assoc($servicesResult)) {
    $servicesByCategory[$service['category']][] = $service['service_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Klinik Tanah Air</title>
    <link rel="stylesheet" href="stylee.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
            <h1>Our Services</h1>
        </div>
    </div>

    <!-- Services Section -->
    <div class="page-section">
    <h1>Services Provided</h1>
    <p>At Klinik Tanah Air, we offer a wide range of medical services to ensure your health and well-being. 
        Our dedicated team of healthcare professionals is committed to providing high-quality care across 
        various specialties. Here are the services we provide:</p>

        <div class="services-container">
            <?php
                foreach ($servicesByCategory as $category => $services) {
                    echo '<div class="category-group">';
                    echo '<h2 class="category-title">' . htmlspecialchars($category) . '</h2>';
                    echo '<ul class="service-list">';
                    foreach ($services as $service) {
                        echo '<li>' . htmlspecialchars($service) . '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
            ?>
        </div>
    </div>

    <style>
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

        .page-section {
            padding: 20px;
            background: linear-gradient(to bottom, #FFF4D7, #fff);

        }

        .page-section h1 {
            font-family: 'Poppins', sans-serif;
            color: black;
            font-size: 30px;
            text-align: center;
            margin-top: 80px;
            margin-bottom: 30px;
            font-weight: normal;
        }

        .page-section p {
            font-size: 20px;
            color: black;
            max-width: 1000px; /* Max width for paragraph */
            margin: 0 auto; /* Centers the paragraph horizontally */
            line-height: 1.5; /* Improve readability */
            margin-bottom: 100px;
            text-align: center;
        }

        .services-container {
            display: flex;
            flex-direction: column; /* Stack categories vertically */
            gap: 40px; /* Add spacing between the categories */
            margin-left: 300px;
            margin-right: 300px;
        }

        .category-group {
            padding: 20px;
            background-color: #DFE0CB;
            background: linear-gradient(to bottom, #fff, #F6F7E1); Gradient from top to bottom
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .category-title {
            font-size: 40px;
            font-weight: normal;
            margin-bottom: 30px;
        }

        .service-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .service-list li {
            margin-bottom: 5px;
        }

        @media (max-width: 768px) {
            .category-group {
                width: 100%; /* Full width for smaller screens */
            }
        }
    </style>

<script>
  window.addEventListener("scroll", function() {
    const header = document.querySelector("header");
    header.classList.toggle("sticky", window.scrollY > 0);
  });
</script>

</body>
</html>
