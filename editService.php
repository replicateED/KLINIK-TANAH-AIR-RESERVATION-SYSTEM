<?php
session_start();
include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

// Fetch the most recent background image from the database
$query = "SELECT image_path FROM bannerSet ORDER BY id DESC LIMIT 1";
$result = mysqli_query($connect, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $backgroundImage = !empty($row['image_path']) ? $row['image_path'] : 'images/default-background.jpg';
} else {
    $backgroundImage = 'images/default-background.jpg';
}

// Handle form submission for editing a service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editService'])) {
    $serviceID = $_POST['serviceID'];
    $serviceName = $_POST['service_name'];
    $serviceCategory = $_POST['service_category'];

    $updateQuery = "UPDATE services SET service_name = ?, category = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($connect, $updateQuery)) {
        mysqli_stmt_bind_param($stmt, "ssi", $serviceName, $serviceCategory, $serviceID);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Service updated successfully!'); window.location='editService.php';</script>";
        } else {
            echo "<script>alert('Error updating service data!');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle delete functionality
if (isset($_GET['delete_id'])) {
    $deleteID = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM services WHERE id = ?";
    if ($stmt = mysqli_prepare($connect, $deleteQuery)) {
        mysqli_stmt_bind_param($stmt, "i", $deleteID);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Service deleted successfully!'); window.location='editService.php';</script>";
        } else {
            echo "<script>alert('Error deleting service!');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all services
$query = "SELECT * FROM services ORDER BY FIELD(category, 'General', 'Pediatrics', 'Cardiology', 'Dermatology', 'Other'), service_name";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Services</title>
    <link rel="stylesheet" href="stylee.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
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

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
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
        <h2>Edit Services</h2>
        <table>
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['service_name']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td class="action-buttons">
                            <button class="btn" onclick="openModal('<?= $row['id'] ?>', '<?= addslashes($row['service_name']) ?>', '<?= addslashes($row['category']) ?>')">Edit</button>
                            <a href="editService.php?delete_id=<?= $row['id'] ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div style="margin-top: 20px; text-align: center;">
            <a href="adminProfile.php" class="btn">Back</a>
        </div>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Edit Service</h3>
        <form method="POST" action="editService.php">
            <input type="hidden" id="serviceID" name="serviceID">
            <label for="service_name">Service Name:</label>
            <input type="text" id="service_name" name="service_name" required><br>
            <label for="service_category">Category:</label>
            <select id="service_category" name="service_category" required>
                <option value="General">General</option>
                <option value="Pediatrics">Pediatrics</option>
                <option value="Cardiology">Cardiology</option>
                <option value="Dermatology">Dermatology</option>
                <option value="Other">Other</option>
            </select><br><br>
            <button type="submit" name="editService" class="btn">Save Changes</button>
        </form>
    </div>
</div>

<script>
    function openModal(id, name, category) {
        document.getElementById('serviceID').value = id;
        document.getElementById('service_name').value = name;
        document.getElementById('service_category').value = category;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>
</body>
</html>
