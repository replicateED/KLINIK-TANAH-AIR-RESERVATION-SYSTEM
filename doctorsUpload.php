<?php
session_start();
include("header.php");

if (isset($_POST['upload'])) {
    // Include the database connection file
    include("db_connection.php");

    // Get the doctor's information from the form
    $doctorName = mysqli_real_escape_string($connect, $_POST['doctorName']);
    $doctorQualification = mysqli_real_escape_string($connect, $_POST['doctorQualification']);
    $doctorGraduatedFrom = mysqli_real_escape_string($connect, $_POST['doctorGraduatedFrom']);

    // Get the uploaded file and its temporary location
    if (isset($_FILES['doctor_photo']) && $_FILES['doctor_photo']['error'] == 0) {
        $fileName = $_FILES['doctor_photo']['name'];
        $fileTmpName = $_FILES['doctor_photo']['tmp_name'];
        $fileSize = $_FILES['doctor_photo']['size'];
        $fileError = $_FILES['doctor_photo']['error'];
        $fileType = $_FILES['doctor_photo']['type'];

        // Check if the file is a valid image
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($fileExt, $allowed)) {
            // Create a unique file name to avoid name conflicts
            $newFileName = uniqid('', true) . '.' . $fileExt;

            // Set the upload directory
            $uploadDir = 'doctorPhotos/';
            $fileDestination = $uploadDir . $newFileName;

            // Move the file to the specified directory
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                // File has been successfully uploaded
                // Now, insert the doctor's information and image path into the database

                // Insert the doctor's details into the doctors table
                $query = "INSERT INTO doctors (name, qualification, graduated_of, photo) VALUES ('$doctorName', '$doctorQualification', '$doctorGraduatedFrom', '$fileDestination')";
                $result = mysqli_query($connect, $query); // Execute the query

                // Check if the query was successful
                if ($result) {
                    echo "Doctor information and photo uploaded successfully.";
                } else {
                    echo "Error saving the doctor information to the database: " . mysqli_error($connect);
                }
            } else {
                echo "Failed to upload the photo.";
            }
        } else {
            echo "Invalid file type. Please upload a JPG, JPEG, PNG, or GIF image.";
        }
    } else {
        echo "No photo uploaded or there was an error uploading the photo.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Doctor Information</title>

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
    <h1>Upload Doctor Information</h1>
    <form action="doctorsUpload.php" method="POST" enctype="multipart/form-data">
        <label for="doctorName">Doctor's Full Name:</label>
        <input type="text" name="doctorName" id="doctorName" required><br><br>

        <label for="doctorQualification">Qualification:</label>
        <input type="text" name="doctorQualification" id="doctorQualification" required><br><br>

        <label for="doctorGraduatedFrom">Graduated from:</label>
        <input type="text" name="doctorGraduatedFrom" id="doctorGraduatedFrom" required><br><br>

        <label for="doctor_photo">Upload Photo:</label>
        <input type="file" name="doctor_photo" id="doctor_photo" required><br><br>

        <button type="submit" name="upload">Upload Doctor Info</button>
    </form>
</body>
</html>
