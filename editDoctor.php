<?php
session_start();

// Include database connection
include('db_connection.php');

// Fetch the most recent background image from the database
$query = "SELECT image_path FROM bannerSet ORDER BY id DESC LIMIT 1";
$result = mysqli_query($connect, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $backgroundImage = !empty($row['image_path']) ? $row['image_path'] : 'images/default-background.jpg';
} else {
    $backgroundImage = 'images/default-background.jpg'; // Default image if query fails
}

// Handle form submission for editing a doctor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editDoctor'])) {
    $doctorID = $_POST['doctorID'];
    $name = $_POST['name'];
    $qualification = $_POST['qualification'];
    $graduatedOf = $_POST['graduated_of'];

    // Update doctor data
    $updateQuery = "UPDATE doctors SET name = ?, qualification = ?, graduated_of = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($connect, $updateQuery)) {
        mysqli_stmt_bind_param($stmt, "sssi", $name, $qualification, $graduatedOf, $doctorID);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Doctor updated successfully!'); window.location='editDoctor.php';</script>";
        } else {
            echo "<script>alert('Error updating doctor data!'); window.location='editDoctor.php';</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Database error!'); window.location='editDoctor.php';</script>";
    }
}

// Handle delete functionality
if (isset($_GET['delete_id'])) {
    $deleteID = $_GET['delete_id'];

    // Delete doctor data
    $deleteQuery = "DELETE FROM doctors WHERE id = ?";
    if ($stmt = mysqli_prepare($connect, $deleteQuery)) {
        mysqli_stmt_bind_param($stmt, "i", $deleteID);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Doctor deleted successfully!'); window.location='editDoctor.php';</script>";
        } else {
            echo "<script>alert('Error deleting doctor!'); window.location='editDoctor.php';</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all doctor data
$query = "SELECT * FROM doctors";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctors</title>
    <link rel="stylesheet" href="stylee.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        <h2>Edit Doctors</h2>
        <!-- Doctor Data Table -->
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Qualification</th>
                    <th>Graduated Of</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['qualification']) ?></td>
                        <td><?= htmlspecialchars($row['graduated_of']) ?></td>
                        <td class="action-buttons">
                            <button class="btn" onclick="openModal('<?= $row['id'] ?>', '<?= addslashes($row['name']) ?>', '<?= addslashes($row['qualification']) ?>', '<?= addslashes($row['graduated_of']) ?>')">Edit</button>
                            <a href="editDoctor.php?delete_id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this doctor?')">Delete</a>
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

<!-- Modal for Editing Doctor -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Edit Doctor Details</h3>
        <form method="POST" action="editDoctor.php">
            <input type="hidden" id="doctorID" name="doctorID">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br><br>
            <label for="qualification">Qualification:</label>
            <input type="text" id="qualification" name="qualification" required><br><br>
            <label for="graduated_of">Graduated Of:</label>
            <input type="text" id="graduated_of" name="graduated_of" required><br><br>
            <button type="submit" name="editDoctor" class="btn">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openModal(id, name, qualification, graduatedOf) {
    document.getElementById("doctorID").value = id;
    document.getElementById("name").value = name;
    document.getElementById("qualification").value = qualification;
    document.getElementById("graduated_of").value = graduatedOf;
    document.getElementById("editModal").style.display = "block";
}

function closeModal() {
    document.getElementById("editModal").style.display = "none";
}
</script>

</body>
</html>
