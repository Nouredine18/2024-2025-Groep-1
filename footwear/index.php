<?php
include 'connect.php';
session_start();

// Haal de cart count uit de database
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // SQL-query om het aantal artikelen in de winkelwagen op te halen
    $sql_count = "SELECT SUM(aantal) AS total_items FROM Cart WHERE user_id = ?";
    $stmt_count = $conn->prepare($sql_count);
    if ($stmt_count) {
        $stmt_count->bind_param("i", $user_id);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $row_count = $result_count->fetch_assoc();

        // Haal het aantal artikelen op uit de database, of zet het op 0 als er geen artikelen zijn
        $_SESSION['cart_count'] = $row_count['total_items'] ? $row_count['total_items'] : 0;
    } else {
        // Fallback to 0 if the query fails
        $_SESSION['cart_count'] = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">
    <title>FootWear | Home</title>
</head>
<body>
<header>
    <div class="logo">FootWear | BE</div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="cart.php">Cart (<?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0'; ?>)</a></li>
                <li><a href="logout.php">Logout</a></li>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="adminPanel.php">Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo $_SESSION['voornaam']; ?></a></li>
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <div class="hero">
        <h1>Welcome to FootWear | BE</h1>
        <p>Only the best of the best for our PHP lovers!</p>
    </div>

    <div class="products">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <?php
            // SQL-query om producten en hun varianten op te halen
            $sql = "SELECT p.artikelnr, p.naam, p.prijs, pv.variantnr, pv.kleur, pv.maat, pv.stock 
                    FROM Products p 
                    JOIN ProductVariant pv ON p.artikelnr = pv.artikelnr";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0):  // Check if query returned results
                while ($row = $result->fetch_assoc()): 
            ?>
                <div class="product-item"> <!-- Productitem container -->
                    <h3><?php echo $row['naam']; ?></h3> <!-- Toon de naam van het product -->
                    <p>Price: €<?php echo $row['prijs']; ?></p> <!-- Toon de prijs van het product -->
                    <p>Color: <?php echo $row['kleur']; ?></p> <!-- Toon de kleur van het product -->
                    <p>Size: <?php echo $row['maat']; ?></p> <!-- Toon de maat van het product -->
                    <p>In stock: <?php echo $row['stock']; ?></p> <!-- Toon de beschikbare voorraad -->
                    <form action="add_to_cart.php" method="post"> <!-- Formulier om het product aan de winkelwagen toe te voegen -->
                        <input type="hidden" name="artikelnr" value="<?php echo $row['artikelnr']; ?>"> <!-- Verborgen invoerveld voor artikelnr -->
                        <input type="hidden" name="variantnr" value="<?php echo $row['variantnr']; ?>"> <!-- Verborgen invoerveld voor variantnr -->
                        <label for="aantal">Quantity:</label> <!-- Label voor de hoeveelheid -->
                        <input type="number" name="aantal" value="1" min="1" max="<?php echo $row['stock']; ?>"> <!-- Invoerveld voor hoeveelheid -->
                        <input type="submit" value="Add to Cart"> <!-- Knop om het product toe te voegen aan de winkelwagen -->
                    </form>
                </div>
            <?php 
                endwhile;
            else: 
                echo "<p>No products available at the moment.</p>";
            endif;
            ?>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2024 SchoenenWijns. All rights reserved.</p>
</footer>
</body>
</html>
