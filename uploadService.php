<?php
session_start();
include("header.php");

if (isset($_POST['upload'])) {
    // Include the database connection file
    include("db_connection.php");

    // Get the service information from the form
    $serviceName = mysqli_real_escape_string($connect, $_POST['serviceName']);
    $category = mysqli_real_escape_string($connect, $_POST['category']); // Get the selected category

    // Insert the service details into the services table, including category
    $query = "INSERT INTO services (service_name, category) VALUES ('$serviceName', '$category')";
    $result = mysqli_query($connect, $query); // Execute the query

    // Check if the query was successful
    if ($result) {
        echo "Service information uploaded successfully.";
    } else {
        echo "Error saving the service information to the database: " . mysqli_error($connect);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Service Information</title>

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
    <div class="container">
        <form action="uploadService.php" method="POST" class="box form-box">
            <h1 class="title">Upload Service</h1>
            <label for="category">Choose a category:</label>
            <select name="category" id="category" required>
                <option value="" disabled selected>Choose a category</option>
                <option value="General Consultation">General</option>
                <option value="Pediatrics">Pediatrics</option>
                <option value="Cardiology">Cardiology</option>
                <option value="Dermatology">Dermatology</option>
                <option value="Other">Other</option>
            </select>
            
            <label for="serviceName">Service Name:</label>
            <input type="text" name="serviceName" id="serviceName" required><br><br>

            <button type="submit" name="upload" class="btn">Upload Service Info</button>
        </form>
    </div>
</body>
</html>
