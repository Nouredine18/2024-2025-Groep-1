<?php
include 'connect.php';
session_start();

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login_register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['brand_name'])) {
    $brand_name = $conn->real_escape_string($_POST['brand_name']);

    $sql = "INSERT INTO brands (brand_name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $brand_name);
    $stmt->execute();

    header("Location: add_brand.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Brand</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Add a New Brand</h1>
    <?php if (isset($_GET['success'])): ?>
        <p>Brand added successfully!</p>
    <?php endif; ?>
    <form action="add_brand.php" method="post">
        <label for="brand_name">Brand Name:</label>
        <input type="text" id="brand_name" name="brand_name" required>
        <button type="submit">Add Brand</button>
    </form>
</body>
</html>