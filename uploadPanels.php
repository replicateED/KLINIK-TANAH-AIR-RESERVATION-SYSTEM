<?php
session_start();
include("header.php");

if (isset($_POST['upload'])) {
    // Include the database connection file
    include("db_connection.php");

    // Get the panel information from the form
    $panelName = mysqli_real_escape_string($connect, $_POST['panelName']);

    // Handle file upload for logo
    if (isset($_FILES['panelLogo']) && $_FILES['panelLogo']['error'] == 0) {
        $logoName = $_FILES['panelLogo']['name'];
        $logoTmpName = $_FILES['panelLogo']['tmp_name'];
        $logoSize = $_FILES['panelLogo']['size'];
        $logoType = $_FILES['panelLogo']['type'];
        $logoExtension = pathinfo($logoName, PATHINFO_EXTENSION);
        
        // Define allowed file types and size limit (1MB in this case)
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $maxSize = 1048576; // 1MB in bytes

        // Check if the file is valid
        if (in_array(strtolower($logoExtension), $allowedTypes) && $logoSize <= $maxSize) {
            $uploadDir = "panels/"; // Update the directory to 'panels/'
            $uploadFile = $uploadDir . basename($logoName);

            // Try to move the uploaded logo file to the desired directory
            if (move_uploaded_file($logoTmpName, $uploadFile)) {
                // Insert the panel information into the database (without category)
                $query = "INSERT INTO panels (panel_name, logo) VALUES ('$panelName', '$uploadFile')";
                $result = mysqli_query($connect, $query); // Execute the query

                // Check if the query was successful
                if ($result) {
                    echo "Panel information uploaded successfully.";
                } else {
                    echo "Error saving the panel information to the database: " . mysqli_error($connect);
                }
            } else {
                echo "Error uploading the logo file.";
            }
        } else {
            echo "Invalid file type or file size exceeded.";
        }
    } else {
        echo "Please upload a logo.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Panel Information</title>

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
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .field input, .field select {
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
            font-weight: bold;
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
    <div class="container">
        <form action="uploadPanels.php" method="POST" class="box form-box" enctype="multipart/form-data">
            <h1 class="title">Upload Panel</h1>
            
            <!-- Panel Name Input -->
            <label for="panelName">Panel Name:</label>
            <input type="text" name="panelName" id="panelName" required><br><br>

            <!-- Logo Upload -->
            <label for="panelLogo">Upload Logo:</label>
            <input type="file" name="panelLogo" id="panelLogo" accept="image/*" required><br><br>

            <!-- Submit Button -->
            <button type="submit" name="upload" class="btn">Upload Panel Info</button>
        </form>
    </div>
</body>
</html>
