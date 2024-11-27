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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = []; // Initialize error array

    // Sanitize and validate inputs
    $adminUsername = mysqli_real_escape_string($connect, trim($_POST['adminUsername']));
    $adminName = mysqli_real_escape_string($connect, trim($_POST['adminName']));
    $adminEmail = mysqli_real_escape_string($connect, trim($_POST['adminEmail']));
    $adminPhoneNo = mysqli_real_escape_string($connect, trim($_POST['adminPhoneNo']));
    $adminPassword = trim($_POST['adminPassword']);

    // Validate fields
    if (empty($adminUsername)) $error[] = 'You forgot to enter your username.';
    if (empty($adminName)) $error[] = 'You forgot to enter your name.';
    if (empty($adminEmail)) $error[] = 'You forgot to enter your email.';
    if (empty($adminPhoneNo)) $error[] = 'You forgot to enter your phone number.';
    if (empty($adminPassword)) $error[] = 'You forgot to enter your password.';

    // Email validation
    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        $error[] = 'Invalid email format.';
    }

    // Password validation (At least 1 uppercase, 1 lowercase, 1 number, 1 symbol, min 8 characters)
    if (!preg_match('/[A-Z]/', $adminPassword)) {
        $error[] = 'Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $adminPassword)) {
        $error[] = 'Password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $adminPassword)) {
        $error[] = 'Password must contain at least one number.';
    }
    if (!preg_match('/[\W_]/', $adminPassword)) { // Checks for any symbol or special character
        $error[] = 'Password must contain at least one special character (e.g., !@#$%^&*).';
    }
    if (strlen($adminPassword) < 8) {
        $error[] = 'Password must be at least 8 characters long.';
    }

    // If no errors, proceed with registration
    if (empty($error)) {
        // Hash the password
        $hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);

        // Insert into database
        $query = "INSERT INTO admin (adminUsername, adminPassword, adminName, adminPhoneNo, adminEmail) 
                  VALUES ('$adminUsername', '$hashedPassword', '$adminName', '$adminPhoneNo', '$adminEmail')";
        
        $result = mysqli_query($connect, $query);

        if ($result) {
            // Redirect to profile on successful registration
            header("Location: adminProfile.php");
            exit();
        } else {
            if (mysqli_errno($connect) == 1062) { // Duplicate entry error code
                $error[] = 'Username already exists. Please choose a different one.';
            } else {
                $error[] = 'System error. Please try again later.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
            background-image: url('<?php echo $backgroundImage; ?>'); /* Dynamically set background */
            background-size: cover;
            background-position: center;
            height: 100vh;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            padding-top: 80px;
        }
        .box.form-box {
            background-color: rgba(255, 255, 255);
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .field input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #e0a800;
            border: none;
            color: white;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #000;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            color: #e0a800;
            text-decoration: none;
        }

        .title {
            text-align: center;
            font-weight: normal;
            padding-bottom: 30px;
            font-size: 20px;
        }

        .field label {
            display: block;
            text-align: left; /* Center the label text */
            margin-top: 5px;
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
    <div class="title">Admin Register</div>
        <?php
        // Display errors, if any
        if (!empty($error)) {
            echo '<ul>';
            foreach ($error as $msg) {
                echo "<li style='color: red;'>$msg</li>";
            }
            echo '</ul>';
        }
        ?>

        <form action="" method="post">
            <div class="field input">
                <label for="adminUsername">Username</label>
                <input type="text" name="adminUsername" id="adminUsername" required>
            </div>
            <div class="field input">
                <label for="adminName">Full Name</label>
                <input type="text" name="adminName" id="adminName" required>
            </div>
            <div class="field input">
                <label for="adminEmail">Email</label>
                <input type="email" name="adminEmail" id="adminEmail" required>
            </div>
            <div class="field input">
                <label for="adminPhoneNo">Phone Number</label>
                <input type="text" name="adminPhoneNo" id="adminPhoneNo" required>
            </div>
            <div class="field input">
                <label for="adminPassword">Password</label>
                <input type="password" name="adminPassword" id="adminPassword" required>
            </div>
            <div class="field">
                <input type="submit" class="btn" value="Register">
            </div>
            <div style="margin-top: 20px; text-align: center;">
            <a href="adminProfile.php" class="btn">Back</a>
        </div>
        </form>
    </div>
</div>
</body>
</html>
