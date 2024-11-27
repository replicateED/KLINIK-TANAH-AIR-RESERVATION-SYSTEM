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

// Error message variable
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $usernameOrPhone = mysqli_real_escape_string($connect, trim($_POST['usernameOrPhone']));  // Email or Phone number
    $password = trim($_POST['password']);  // Password

    // Validate fields
    if (empty($usernameOrPhone) || empty($password)) {
        $message = "Both username/email or phone and password are required.";
    } else {
        // Query to check if the email or phone number exists in the database
        $query = "SELECT * FROM users WHERE email = '$usernameOrPhone' OR phone_number = '$usernameOrPhone'";
        $result = mysqli_query($connect, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Store session variables upon successful login
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['phone_number'] = $user['phone_number'];

                // Redirect to user dashboard or profile page
                header("Location: home.php");
                exit();
            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "No user found with this email or phone number.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="stylee.css">

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
            font-size: 20px;
            text-align: center;
            font-weight: normal;
            padding-bottom: 50px;
        }

        .field label {
            display: block;
            text-align: left; /* Center the label text */
            margin-top: 5px;
        }

    </style>
</head>
<body>
<!-- <header>
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

    </header> -->

    <div class="container">
        <div class="box form-box">
            <div class="title">User Login</div>
            <?php
            if (!empty($message)) {
                echo "<p style='color: red;'>$message</p>";
            }
            ?>
            <form action="" method="post">
                <div class="field input">
                    <label for="usernameOrPhone">Email or Phone Number</label>
                    <input type="text" name="usernameOrPhone" id="usernameOrPhone" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="links">
                    Don't have an account? <a href="userRegister.php">Sign Up Now</a>
                </div>

                <div class="field">
                    <input type="submit" class="btn" value="Log In">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
