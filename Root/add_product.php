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

    // Prepare the SQL statement to insert the product into the database
    $sql = "INSERT INTO Products (naam, product_information, prijs, type_of_shoe) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $product_name, $description, $price, $type_of_shoe);

    // Execute the statement and check if the product was added successfully
    if ($stmt->execute()) {
        echo "<p>Product succesvol toegevoegd!</p>";
    } else {
        echo "<p>Er is een fout opgetreden bij het toevoegen van het product: " . $stmt->error . "</p>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Toevoegen</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/add_product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<header>
    <div class="logo">SchoenenWijns</div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">Logout</a></li>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="add_product.php">Voeg Product Toe</a></li>
                    <li><a href="manage_products.php">Beheer Producten</a></li>
                    <li><a href="active_deactivate_show_users.php">Users</a></li>
                    <li><a href="add_product.php">Product Toevoegen</a></li>
                    <li><a href="add_variant.php">Variant Toevoegen</a></li>
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo htmlspecialchars($_SESSION['voornaam']); ?></a></li>
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h2>Product Toevoegen</h2>
    <form action="add_product.php" method="post">
        <label for="product_name">Productnaam:</label>
        <input type="text" id="product_name" name="product_name" required>

        <label for="description">Beschrijving:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="price">Prijs:</label>
        <input type="number" id="price" name="price" step="0.01" required>

        <label for="type_of_shoe">Categorie:</label>
        <input type="text" id="type_of_shoe" name="type_of_shoe" required>

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