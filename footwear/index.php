<?php
include 'connect.php';
session_start();

// Haal de cart count uit de database
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // SQL-query om het aantal artikelen in de winkelwagen op te halen
    $sql_count = "SELECT SUM(aantal) AS total_items FROM Cart WHERE user_id = ?";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $row_count = $result_count->fetch_assoc();

    // Haal het aantal artikelen op uit de database, of zet het op 0 als er geen artikelen zijn
    $_SESSION['cart_count'] = $row_count['total_items'] ? $row_count['total_items'] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">
    <title>SchoenenWijns | Home</title>
</head>
<body>
<header>
    <div class="logo">SchoenenWijns</div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="webshop.php">Shop</a></li> <!-- Link naar Webshop -->

            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="cart.php">Cart (<?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0'; ?>)</a></li> <!-- Display Cart count -->
                <li><a href="logout.php">Logout</a></li>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="active_deactivate_show_users.php">Users</a></li>
                    <li><a href="add_product.php">Voeg Product Toe</a></li>
                    <li><a href="manage_products.php">Beheer Producten</a></li> <!-- Link naar Productbeheer -->
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo htmlspecialchars($_SESSION['voornaam']); ?></a></li> <!-- Display user's voornaam -->
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <div class="hero">
        <h1>Welcome to SchoenenWijns</h1>
        <p>Only the best of the best for our PHP lovers!</p>
    </div>

    <div class="products">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <!-- Hier kun je aanbevolen producten tonen -->
            <p>No featured products available yet.</p>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2024 SchoenenWijns. All rights reserved.</p>
</footer>
</body>
</html>
