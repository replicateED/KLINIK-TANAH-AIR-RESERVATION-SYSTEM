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

// Form submission logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Fetch form data
  $name = mysqli_real_escape_string($connect, $_POST['name']);
  $phone_number = mysqli_real_escape_string($connect, $_POST['phone_number']);
  $email = mysqli_real_escape_string($connect, $_POST['email']);
  $message = mysqli_real_escape_string($connect, $_POST['message']);
  
  // Convert date format from DD/MM/YYYY to YYYY-MM-DD
  $appointment_date = mysqli_real_escape_string($connect, $_POST['appointment_date']);
  $appointment_date = date('Y-m-d', strtotime(str_replace('/', '-', $appointment_date)));

  // Insert data into the database, including user_id
  $query = "INSERT INTO appointment (name, phone_number, email, message, appointment_date, status, user_id) 
  VALUES ('$name', '$phone_number', '$email', '$message', '$appointment_date', 'pending', '$user_id')";

  if (mysqli_query($connect, $query)) {
    // Redirect or handle success
    header("Location: appointmentDetails.php?id=" . mysqli_insert_id($connect));
    exit();
  } else {
    echo "Error: " . mysqli_error($connect);
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klinik Tanah Air</title>
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



    <style>
      /* General Styles */
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


      /* Banner Section */
      .page-banner {
        /* margin-top: 80px; Adjust based on the navbar height */
            position: relative;
            height: 400px; /* Adjust height for the banner */
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

      .banner-section {
        position: relative;
        z-index: 10;
        text-align: center;
        color: #fff;
      }

      /* Page Section */
      .page-section {
        /* margin-top: 100px; Adjust margin to move the form down */
        padding: 20px;
      }

      /* Form Styling */
      .contact-form {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 50px;
      }

      .contact-form label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
      }

      .contact-form input,
      .contact-form textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        font-family: Arial, sans-serif;
      }

      .contact-form .row {
        display: flex;
        gap: 50px; /* Space between columns */
      }

      .contact-form .row > div {
        flex: 1; /* Make columns take equal space */
      }

      .contact-form button {
        padding: 10px 20px;
        background-color: #ffc513;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
      }

      .contact-form button:hover {
        background-color: #e0a800;
      }

      /* Heading Styling */
      h1 {
        margin-top: 30px;
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
    <div
  class="page-banner"
  style="background-image: url('<?php echo $backgroundImage; ?>');"
>
  <div class="banner-section">
    <div>
      <h1>Book an appointment.</h1>
    </div>
  </div>
</div>

    <!-- Contact Form Section -->
    <div class="page-section">
      <h1>Get in Touch</h1>
      <form class="contact-form" method="POST" action="">
    <div class="row">
        <div>
            <label for="fullName">Name</label>
            <input type="text" id="name" name="name" placeholder="Full name.." required />
        </div>
        <div>
            <label for="phoneNum">Phone Number</label>
            <input type="text" id="phoneNum" name="phone_number" placeholder="Phone number" required />
        </div>
    </div>
    <div>
        <label for="emailAddress">Email</label>
        <input type="email" id="emailAddress" name="email" placeholder="Email" required />
    </div>
    <div>
        <label for="message">How can we help you?</label>
        <textarea id="message" name="message" rows="8" placeholder="Your message" required></textarea>
    </div>
    <div>
        <label for="appointmentDate">Appointment Date</label>
        <input 
            type="date" 
            id="appointmentDate" 
            name="appointment_date" 
            required 
            pattern="\d{2}/\d{2}/\d{4}" 
        />
    </div>
    <button type="submit">Submit</button>
    </form>

    </div>
    <script>
  window.addEventListener("scroll", function() {
    const header = document.querySelector("header");
    header.classList.toggle("sticky", window.scrollY > 0);
  });
</script>
  </body>
</html>