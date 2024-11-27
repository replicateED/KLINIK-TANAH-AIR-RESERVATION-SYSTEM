<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Include database connection
include('db_connection.php');

// Fetch user details from the database using session data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($connect, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
} else {
    // Handle the case where no user is found
    echo "User not found.";
    exit();
}

// Fetch the most recent background image
$query = "SELECT image_path FROM bannerSet ORDER BY id DESC LIMIT 1";
$result = mysqli_query($connect, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $backgroundImage = !empty($row['image_path']) ? $row['image_path'] : 'images/default-background.jpg';
} else {
    $backgroundImage = 'images/default-background.jpg';
}

// Handle email and phone number update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_email_phone'])) {
    // Sanitize input data
    $new_email = mysqli_real_escape_string($connect, $_POST['email']);
    $new_phone_number = mysqli_real_escape_string($connect, $_POST['phone_number']);

    // Update email and phone number in the database
    $updateQuery = "UPDATE users SET email = '$new_email', phone_number = '$new_phone_number' WHERE user_id = '$user_id'";
    if (mysqli_query($connect, $updateQuery)) {
        // Success message
        $update_message = "Email and Phone Number updated successfully.";
    } else {
        $update_message = "Error updating email/phone number: " . mysqli_error($connect);
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    // Sanitize and validate inputs
    $old_password = mysqli_real_escape_string($connect, $_POST['old_password']);
    $new_password = mysqli_real_escape_string($connect, $_POST['new_password']);
    $confirm_new_password = mysqli_real_escape_string($connect, $_POST['confirm_new_password']);

    // Check if the old password matches
    if (password_verify($old_password, $user['password'])) {
        // Check if new password matches the confirm password
        if ($new_password === $confirm_new_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $passwordQuery = "UPDATE users SET password = '$hashed_password' WHERE user_id = '$user_id'";
            if (mysqli_query($connect, $passwordQuery)) {
                $password_message = "Password updated successfully.";
            } else {
                $password_message = "Error updating password: " . mysqli_error($connect);
            }
        } else {
            $password_message = "New password and confirmation do not match.";
        }
    } else {
        $password_message = "Old password is incorrect.";
    }
}

// Fetch the user's appointments from the appointment table
$query = "SELECT * FROM appointment WHERE user_id = '$user_id' ORDER BY appointment_date DESC";
$appointmentsResult = mysqli_query($connect, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="stylee.css">
    <style>
        body {
            background-image: url('<?php echo $backgroundImage; ?>');
            background-size: cover;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 100px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
        }
        h1 {
            text-align: center;
        }
        .profile-info {
            margin: 20px 0;
        }
        .profile-info p {
            font-size: 18px;
        }
        .appointments {
            margin-top: 30px;
        }
        .appointment {
            background-color: rgba(255, 255, 255, 0.3);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .appointment p {
            margin: 5px 0;
        }
        .logout-btn {
            display: block;
            width: 200px;
            margin: 30px auto;
            padding: 10px;
            background-color: #ff5c5c;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background-color: #ff3d3d;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
        }
        /* Hide forms by default */
        .hidden {
            display: none;
        }
        .form-links a {
            display: inline-block;
            padding: 8px 15px;
            margin: 10px 5px;
            text-decoration: none;
            color: white;
            background-color: rgba(0, 0, 0, 0.7);
            border: 1px solid #fff;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .form-links a:hover {
            background-color: rgba(255, 255, 255, 0.3);
            border-color: #ff5c5c;
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
    <h1>Welcome, <?php echo htmlspecialchars($user['user_name']); ?>!</h1>

    <div class="profile-info">
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
    </div>

    <!-- Links to toggle forms -->
    <div class="form-links">
        <a href="#" onclick="toggleForm('profile_form')">Edit Profile</a> | 
        <a href="#" onclick="toggleForm('password_form')">Change Password</a>
    </div>

    <!-- Display the update email/phone form -->
    <?php if (isset($update_message)) { echo "<p>$update_message</p>"; } ?>
    <div id="profile_form" class="hidden">
        <h2>Update Email and Phone Number</h2>
        <form method="POST">
            <div class="form-group">
                <label for="email">New Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_number">New Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
            </div>
            <div class="form-group">
                <button type="submit" name="update_email_phone">Update Email and Phone</button>
            </div>
        </form>
    </div>

    <!-- Display the change password form -->
    <?php if (isset($password_message)) { echo "<p>$password_message</p>"; } ?>
    <div id="password_form" class="hidden">
        <h2>Change Password</h2>
        <form method="POST">
            <div class="form-group">
                <label for="old_password">Old Password:</label>
                <input type="password" id="old_password" name="old_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_new_password">Confirm New Password:</label>
                <input type="password" id="confirm_new_password" name="confirm_new_password" required>
            </div>
            <div class="form-group">
                <button type="submit" name="change_password">Change Password</button>
            </div>
        </form>
    </div>

    <!-- Display the user's appointments -->
    <div class="appointments">
        <h2>Your Appointments</h2>
        <?php if (mysqli_num_rows($appointmentsResult) > 0): ?>
            <?php while ($appointment = mysqli_fetch_assoc($appointmentsResult)): ?>
                <div class="appointment">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($appointment['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($appointment['email']); ?></p>
                    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($appointment['phone_number']); ?></p>
                    <p><strong>Appointment Date:</strong> <?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($appointment['status'])); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No appointments booked yet.</p>
        <?php endif; ?>
    </div>

    <a href="userLogin.php" class="logout-btn">Log Out</a>
</div>

<script>
    // Toggle visibility of profile and password forms
    function toggleForm(formId) {
        var form = document.getElementById(formId);
        form.classList.toggle('hidden');
    }
</script>

</body>
</html>
