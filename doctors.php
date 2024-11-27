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

// Fetch the most recent background image from the database
$query = "SELECT image_path FROM bannerSet ORDER BY id DESC LIMIT 1"; // Get the most recent image
$result = mysqli_query($connect, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $backgroundImage = !empty($row['image_path']) ? $row['image_path'] : 'images/default-background.jpg';
} else {
    $backgroundImage = 'images/default-background.jpg'; // Default image if query fails
}

$message = ""; // To store error messages

// Fetch doctors' data
$query = "SELECT * FROM doctors";
$result = mysqli_query($connect, $query);

// Initialize an empty array for doctors
$doctors = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $doctors[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors</title>
    <!------CSS------>
    <link rel="stylesheet" href="stylee.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

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

        .container {
            background: linear-gradient(to bottom, #FFF4D7, #fff);
            padding: 20px;
            margin-top: 0;
            text-align: center; /* Center the text */
            max-width: 100%; /* Set the max width for the content */
            margin-left: auto; /* Center the container horizontally */
            margin-right: auto; /* Center the container horizontally */
        }

        .container h1 {
            margin-top: 80px;
            font-family: 'Poppins', sans-serif;
            color: black;
            font-size: 30px;
            text-align: center;
            margin-bottom: 30px;
            font-weight: normal;
        }

        .container p {
            font-size: 20px;
            color: black;
            max-width: 1000px; /* Max width for paragraph */
            margin: 0 auto; /* Centers the paragraph horizontally */
            line-height: 1.5; /* Improve readability */
            margin-bottom: 100px;
        }

        .card-container {
            display: flex;
            flex-direction: column; /* Changed from row to column to stack cards vertically */
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
            align-items: center; /* Centers the cards horizontally */
        }

        .card {
            background-color: #fff;
            border: 1px solid #ccc;
            
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 500px; /* Increased width of the card */
            overflow: hidden;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card img {
            display: block;  /* Makes the image a block element */
            margin: 0 auto;  /* Centers the image horizontally */
            width: 200px;    /* Keeps the image width as is */
            height: auto;    /* Keeps the aspect ratio of the image intact */
            object-fit: cover;  /* Ensures the image covers the space properly */
            max-height: 200px;
            border-radius: 50%;
            margin-top: 20px;
        }

        .card-content {
            padding: 15px;
            text-align: center;
        }

        .card-content h3 {
            margin: 10px 0;
            font-size: 20px;
            color: #333;
        }

        .card-content p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            color: white;
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
            <h1>Our Doctors</h1>
            
        </div>
    </div>

<div class="container">
    <h1>Meet the Passionate Experts Behind Our Care</h1>
    <p>Klinik Tanah Air is home to a team of doctors who are committed to excellence in healthcare. 
        With their ongoing dedication to learning, they provide our patients with the most advanced 
        and compassionate care available.</p>
    <div class="card-container">
        <?php if (!empty($doctors)): ?>
            <?php foreach ($doctors as $doctor): ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($doctor['photo']); ?>" alt="Doctor Photo">
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($doctor['name']); ?></h3>
                        <p><strong>Qualification:</strong> <?php echo htmlspecialchars($doctor['qualification']); ?></p>
                        <p><strong>Graduated from:</strong> <?php echo htmlspecialchars($doctor['graduated_of']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">No doctors found.</p>
        <?php endif; ?>
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
