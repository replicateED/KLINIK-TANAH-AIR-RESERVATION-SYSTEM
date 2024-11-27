<?php
session_start();
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

// Handle form submission for editing a panel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editPanel'])) {
    $panelID = $_POST['panelID'];
    $panelName = $_POST['panel_name'];

    // Handle file upload
    if (!empty($_FILES['panel_logo']['name'])) {
        $fileName = $_FILES['panel_logo']['name'];
        $fileTmpName = $_FILES['panel_logo']['tmp_name'];
        $fileError = $_FILES['panel_logo']['error'];

        $targetDir = "panels/";
        $newFileName = "panels_" . time() . "_" . basename($fileName);
        $targetFile = $targetDir . $newFileName;

        if ($fileError === 0) {
            if (move_uploaded_file($fileTmpName, $targetFile)) {
                $updateQuery = "UPDATE panels SET panel_name = ?, logo = ? WHERE id = ?";
                if ($stmt = mysqli_prepare($connect, $updateQuery)) {
                    mysqli_stmt_bind_param($stmt, "ssi", $panelName, $targetFile, $panelID);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "<script>alert('Panel updated successfully!'); window.location='editPanel.php';</script>";
                    } else {
                        echo "<script>alert('Error updating panel data!');</script>";
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                echo "<script>alert('Error uploading logo!');</script>";
            }
        } else {
            echo "<script>alert('File upload error!');</script>";
        }
    } else {
        $updateQuery = "UPDATE panels SET panel_name = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($connect, $updateQuery)) {
            mysqli_stmt_bind_param($stmt, "si", $panelName, $panelID);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Panel updated successfully!'); window.location='editPanel.php';</script>";
            } else {
                echo "<script>alert('Error updating panel data!');</script>";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle delete functionality
if (isset($_GET['delete_id'])) {
    $deleteID = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM panels WHERE id = ?";
    if ($stmt = mysqli_prepare($connect, $deleteQuery)) {
        mysqli_stmt_bind_param($stmt, "i", $deleteID);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Panel deleted successfully!'); window.location='editPanel.php';</script>";
        } else {
            echo "<script>alert('Error deleting panel!');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all panels
$query = "SELECT * FROM panels";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Panels</title>
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
        <h2>Edit Panels</h2>
        <table>
            <thead>
                <tr>
                    <th>Panel Name</th>
                    <th>Logo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['panel_name']) ?></td>
                        <td><img src="<?= htmlspecialchars($row['logo']) ?>" alt="Panel Logo" style="max-height: 50px;"></td>
                        <td class="action-buttons">
                            <button class="btn" onclick="openModal('<?= $row['id'] ?>', '<?= addslashes($row['panel_name']) ?>', '<?= addslashes($row['logo']) ?>')">Edit</button>
                            <a href="editPanel.php?delete_id=<?= $row['id'] ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
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
        <h3>Edit Panel</h3>
        <form method="POST" action="editPanel.php" enctype="multipart/form-data">
            <input type="hidden" id="panelID" name="panelID">
            <label for="panel_name">Panel Name:</label>
            <input type="text" id="panel_name" name="panel_name" required><br>
            <label for="panel_logo">Panel Logo:</label>
            <input type="file" id="panel_logo" name="panel_logo"><br><br>
            <button type="submit" name="editPanel" class="btn">Save Changes</button>
        </form>
    </div>
</div>

<script>
    function openModal(id, name, logo) {
        document.getElementById('panelID').value = id;
        document.getElementById('panel_name').value = name;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>
</body>
</html>
