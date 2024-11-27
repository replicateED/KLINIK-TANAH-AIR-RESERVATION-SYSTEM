<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['promo_image'])) {
    include('db_connection.php');

    $imageName = basename($_FILES['promo_image']['name']);
    $targetDir = "promotion/";
    $targetFile = $targetDir . $imageName;

    // Move uploaded file to the promotion/ directory
    if (move_uploaded_file($_FILES['promo_image']['tmp_name'], $targetFile)) {
        // Insert the image name into the database
        $query = "INSERT INTO promotion (promo_image) VALUES ('$imageName')";
        if (mysqli_query($connect, $query)) {
            echo "Image uploaded and saved to database successfully.";
        } else {
            echo "Database error: " . mysqli_error($connect);
        }
    } else {
        echo "Error uploading image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="uploadPromotion.php" method="POST" enctype="multipart/form-data">
    <label for="promo_image">Upload Promo Image:</label>
    <input type="file" name="promo_image" id="promo_image" required>
    <button type="submit">Upload</button>
</form>

</body>
</html>