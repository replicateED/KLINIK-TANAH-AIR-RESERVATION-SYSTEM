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

// Handle the form submission for editing an admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editAdmin'])) {
    // Get the form data
    $username = $_POST['username'];
    $adminName = $_POST['adminName'];
    $adminEmail = $_POST['adminEmail'];
    $adminPhoneNo = $_POST['adminPhoneNo'];

    // Update query to modify admin data
    $updateQuery = "UPDATE admin SET adminName = ?, adminEmail = ?, adminPhoneNo = ? WHERE adminUsername = ?";

    // Prepare and execute the query
    if ($stmt = mysqli_prepare($connect, $updateQuery)) {
        mysqli_stmt_bind_param($stmt, "ssss", $adminName, $adminEmail, $adminPhoneNo, $username);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Admin updated successfully!'); window.location='editAdmin.php';</script>";
        } else {
            echo "<script>alert('Error updating admin data!'); window.location='editAdmin.php';</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Database error!'); window.location='editAdmin.php';</script>";
    }
}

// Fetch all admin data from the database
$query = "SELECT * FROM admin";
$result = mysqli_query($connect, $query);

// Handle delete functionality
if (isset($_GET['delete_username'])) {
    $delete_username = $_GET['delete_username'];
    
    // Delete admin data from the database
    $deleteQuery = "DELETE FROM admin WHERE adminUsername = '$delete_username'";
    if (mysqli_query($connect, $deleteQuery)) {
        echo "<script>alert('Admin deleted successfully!'); window.location='editAdmin.php';</script>";
    } else {
        echo "<script>alert('Error deleting admin!'); window.location='editAdmin.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admins</title>
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
            padding: 20px;
            margin-top: 100px;
            width: 100%;
        }

        .box.form-box {
            background-color: rgba(255, 255, 255);
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 900px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn {
            background-color: #e0a800;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #000;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        /* Modal Styles */
        .modal {
            display: none; 
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<header>
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
</header>

<div class="container">
    <div class="box form-box">
        <h2>Edit Admins</h2>

        <!-- Admin data table -->
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone No</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display all admin data
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['adminUsername']}</td>
                            <td>{$row['adminName']}</td>
                            <td>{$row['adminEmail']}</td>
                            <td>{$row['adminPhoneNo']}</td>
                            <td class='action-buttons'>
                                <button class='btn' onclick='openModal(\"{$row['adminUsername']}\", \"{$row['adminName']}\", \"{$row['adminEmail']}\", \"{$row['adminPhoneNo']}\")'>Edit</button>
                                <a href='editAdmin.php?delete_username={$row['adminUsername']}' class='btn'>Delete</a>
                            </td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
        <!-- Back Button -->
        <div style="margin-top: 20px; text-align: center;">
            <a href="adminProfile.php" class="btn">Back</a>
        </div>
    </div>
</div>

<!-- Modal for Editing Admin -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Edit Admin Details</h3>
        <form action="editAdmin.php" method="POST">
            <input type="hidden" id="username" name="username">
            <label for="adminName">Full Name:</label>
            <input type="text" id="adminName" name="adminName" required><br><br>
            <label for="adminEmail">Email:</label>
            <input type="email" id="adminEmail" name="adminEmail" required><br><br>
            <label for="adminPhoneNo">Phone No:</label>
            <input type="text" id="adminPhoneNo" name="adminPhoneNo" required><br><br>
            <button type="submit" name="editAdmin" class="btn">Save Changes</button>
        </form>
    </div>
</div>

<script>
    // Function to open the modal with pre-filled data
    function openModal(username, name, email, phoneNo) {
        document.getElementById('username').value = username;
        document.getElementById('adminName').value = name;
        document.getElementById('adminEmail').value = email;
        document.getElementById('adminPhoneNo').value = phoneNo;

        document.getElementById('editModal').style.display = 'block'; // Show modal
    }

    // Function to close the modal
    function closeModal() {
        document.getElementById('editModal').style.display = 'none'; // Hide modal
    }

    // Close modal when clicked outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('editModal')) {
            closeModal();
        }
    }
</script>

</body>
</html>
