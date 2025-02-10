<?php
session_start();
include 'connect.php';

// Ensure the script is only executed if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $product_name = htmlspecialchars($_POST['product_name']);
    $description = htmlspecialchars($_POST['description']);
    $price = floatval($_POST['price']);
    $type_of_shoe = htmlspecialchars($_POST['type_of_shoe']);

    // Handle file upload
    $target_dir = "directory/";
    $target_file = $target_dir . basename($_FILES["product_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["product_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<p>File is not an image.</p>";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "<p>Sorry, file already exists.</p>";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["product_image"]["size"] > 500000) {
        echo "<p>Sorry, your file is too large.</p>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "<p>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<p>Sorry, your file was not uploaded.</p>";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // Prepare the SQL statement to insert the product into the database
            $sql = "INSERT INTO Products (naam, product_information, prijs, type_of_shoe, directory) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdss", $product_name, $description, $price, $type_of_shoe, $target_file);

            // Execute the statement and check if the product was added successfully
            if ($stmt->execute()) {
                echo "<p>Product succesvol toegevoegd!</p>";
            } else {
                echo "<p>Er is een fout opgetreden bij het toevoegen van het product: " . $stmt->error . "</p>";
            }

            // Close the statement and connection
            $stmt->close();
            $conn->close();
        } else {
            echo "<p>Sorry, there was an error uploading your file.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Toevoegen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<main>
    <h2>Product Toevoegen</h2>
    <form action="add_product.php" method="post" enctype="multipart/form-data">
        <label for="product_name">Productnaam:</label>
        <input type="text" id="product_name" name="product_name" required>

        <label for="description">Beschrijving:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="price">Prijs:</label>
        <input type="number" id="price" name="price" step="0.01" required>

        <label for="type_of_shoe">Categorie:</label>
        <input type="text" id="type_of_shoe" name="type_of_shoe" required>

        <label for="product_image">Productafbeelding:</label>
        <input type="file" id="product_image" name="product_image" accept="image/*" required>

        <button type="submit">Product Toevoegen</button>
    </form>
</main>

<footer>
    <p>&copy; 2024 FootWear. Alle rechten voorbehouden.</p>

    <div class="social-media">
        <h3>Volg ons op:</h3>
        <ul>
            <li><a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-f"></i> Facebook</a></li>
            <li><a href="https://www.twitter.com" target="_blank"><i class="fab fa-twitter"></i> Twitter</a></li>
            <li><a href="https://www.instagram.com" target="_blank"><i class="fab fa-instagram"></i> Instagram</a></li>
            <li><a href="https://www.linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i> LinkedIn</a></li>
        </ul>
    </div>
</footer>
</body>
</html>