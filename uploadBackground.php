<?php
session_start();
include("header.php");

if (isset($_POST['upload'])) {
    // Include the database connection file
    include("db_connection.php");

    // Get the uploaded file and its temporary location
    if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] == 0) {
        $fileName = $_FILES['background_image']['name'];
        $fileTmpName = $_FILES['background_image']['tmp_name'];
        $fileSize = $_FILES['background_image']['size'];
        $fileError = $_FILES['background_image']['error'];
        $fileType = $_FILES['background_image']['type'];

        // Check if the file is a valid image
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($fileExt, $allowed)) {
            // Create a unique file name to avoid name conflicts
            $newFileName = uniqid('', true) . '.' . $fileExt;

            // Set the upload directory
            $uploadDir = 'backgroundUploads/';
            $fileDestination = $uploadDir . $newFileName;

            // Move the file to the specified directory
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                // File has been successfully uploaded
                // Now, insert the file path into the database

                // Insert the image path into the bannerSet table (no need to specify `id`)
                $query = "INSERT INTO bannerSet (image_path) VALUES ('$fileDestination')";
                $result = mysqli_query($connect, $query); // Execute the query

                // Check if the query was successful
                if ($result) {
                    echo "Image uploaded and path saved to database successfully.";
                } else {
                    echo "Error saving the image path to the database: " . mysqli_error($connect);
                }
            } else {
                echo "Failed to upload the image.";
            }
        } else {
            echo "Invalid file type. Please upload a JPG, JPEG, PNG, or GIF image.";
        }
    } else {
        echo "No file uploaded or there was an error uploading the file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Background Image</title>
</head>
<body>
    <h1>Upload Background Image</h1>
    <form action="uploadBackground.php" method="POST" enctype="multipart/form-data">
        <label for="background_image">Select an image:</label>
        <input type="file" name="background_image" id="background_image" required>
        <button type="submit" name="upload">Upload</button>
    </form>
</body>
</html>
