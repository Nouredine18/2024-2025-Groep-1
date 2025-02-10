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