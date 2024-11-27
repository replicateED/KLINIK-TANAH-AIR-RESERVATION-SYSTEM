<?php
session_start();
include("header.php");

// Include the database connection file
include("db_connection.php");

if (isset($_POST['upload'])) {
    // Get the location information from the form
    $locationName = mysqli_real_escape_string($connect, $_POST['locationName']);
    $address = mysqli_real_escape_string($connect, $_POST['address']);
    $mapLink = mysqli_real_escape_string($connect, $_POST['mapLink']); // This will be the Google Maps URL

    // Generate the map embed code using the mapLink
    $mapEmbedCode = generateMapEmbedCode($mapLink);

    // Insert the location information into the database
    $query = "INSERT INTO locations (location_name, address, map_embed_code) VALUES ('$locationName', '$address', '$mapEmbedCode')";
    $result = mysqli_query($connect, $query); // Execute the query

    // Check if the query was successful
    if ($result) {
        echo "Location information uploaded successfully.";
    } else {
        echo "Error saving the location information to the database: " . mysqli_error($connect);
    }
}

// Function to generate the Google Maps embed iframe code
function generateMapEmbedCode($mapLink) {
    // Generate the embed code based on the provided map link
    return '<iframe src="' . htmlspecialchars($mapLink) . '" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Location</title>
    <link rel="stylesheet" href="stylee.css">
</head>
<body>
    <div class="container">
        <form action="uploadLocation.php" method="POST" class="box form-box">
            <h1 class="title">Upload Location</h1>
            
            <!-- Location Name Input -->
            <label for="locationName">Location Name:</label>
            <input type="text" name="locationName" id="locationName" required><br><br>

            <!-- Address Input -->
            <label for="address">Address:</label>
            <input type="text" name="address" id="address" required><br><br>

            <!-- Google Maps URL Input -->
            <label for="mapLink">Google Map Embed Code:</label>
            <input type="text" name="mapLink" id="mapLink" required><br><br>

            <!-- Submit Button -->
            <button type="submit" name="upload" class="btn">Upload Location</button>
        </form>
    </div>
</body>
</html>
