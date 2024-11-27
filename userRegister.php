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
    $userName = mysqli_real_escape_string($connect, trim($_POST['userName']));
    $userEmail = mysqli_real_escape_string($connect, trim($_POST['userEmail']));
    $userPhoneNo = mysqli_real_escape_string($connect, trim($_POST['userPhoneNo']));
    $userPassword = trim($_POST['userPassword']);

    // Validate fields
    if (empty($userName)) $error[] = 'You forgot to enter your name.';
    if (empty($userEmail)) $error[] = 'You forgot to enter your email.';
    if (empty($userPhoneNo)) $error[] = 'You forgot to enter your phone number.';
    if (empty($userPassword)) $error[] = 'You forgot to enter your password.';

    // Email validation
    if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $error[] = 'Invalid email format.';
    }

    // Password validation (At least 1 uppercase, 1 lowercase, 1 number, 1 symbol, min 8 characters)
    if (!preg_match('/[A-Z]/', $userPassword)) {
        $error[] = 'Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $userPassword)) {
        $error[] = 'Password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $userPassword)) {
        $error[] = 'Password must contain at least one number.';
    }
    if (!preg_match('/[\W_]/', $userPassword)) { // Checks for any symbol or special character
        $error[] = 'Password must contain at least one special character (e.g., !@#$%^&*).';
    }
    if (strlen($userPassword) < 8) {
        $error[] = 'Password must be at least 8 characters long.';
    }

    // If no errors, proceed with registration
    if (empty($error)) {
        // Hash the password
        $hashedPassword = password_hash($userPassword, PASSWORD_BCRYPT);

        // Insert into users table
        $query = "INSERT INTO users (user_name, email, phone_number, password) 
                  VALUES ('$userName', '$userEmail', '$userPhoneNo', '$hashedPassword')";
        
        $result = mysqli_query($connect, $query);

        if ($result) {
            // Redirect to user profile or dashboard upon successful registration
            header("Location: userLogin.php");
            exit();
        } else {
            if (mysqli_errno($connect) == 1062) { // Duplicate entry error code
                $error[] = 'Email or phone number already exists. Please choose a different one.';
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
    <title>User Register</title>
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
<div class="container">
    <div class="box form-box">
    <div class="title">User Register</div>
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
                <label for="userName">Full Name</label>
                <input type="text" name="userName" id="userName" required>
            </div>
            <div class="field input">
                <label for="userEmail">Email</label>
                <input type="email" name="userEmail" id="userEmail" required>
            </div>
            <div class="field input">
                <label for="userPhoneNo">Phone Number</label>
                <input type="text" name="userPhoneNo" id="userPhoneNo" required>
            </div>
            <div class="field input">
                <label for="userPassword">Password</label>
                <input type="password" name="userPassword" id="userPassword" required>
            </div>
            <div class="field">
                <input type="submit" class="btn" value="Register">
            </div>
        </form>
    </div>
</div>
</body>
</html>
