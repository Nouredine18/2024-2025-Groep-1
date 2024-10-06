<?php
include 'connect.php'; // Inclusie van de databaseverbinding
session_start(); // Start de sessie om gebruikersinformatie te behouden

// SQL-query om producten en hun varianten op te halen
$sql = "SELECT p.artikelnr, p.naam, p.prijs, pv.variantnr, pv.kleur, pv.maat, pv.stock FROM Products p 
        JOIN ProductVariant pv ON p.artikelnr = pv.artikelnr";
$result = $conn->query($sql); // Voer de query uit en sla het resultaat op
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"> <!-- Koppel het CSS-bestand voor styling -->
    <title>Webshop</title> <!-- Titel van de pagina -->
</head>
<body>
<header>
    <div class="logo">SchoenenWijns</div> <!-- Logo van de webshop -->
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li> <!-- Link naar de homepage -->
            <li><a href="webshop.php">Shop</a></li> <!-- Link naar de webshop -->

            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="cart.php">Cart (<?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0'; ?>)</a></li> <!-- Toon het aantal artikelen in de winkelwagen -->
                <li><a href="logout.php">Logout</a></li> <!-- Link om uit te loggen -->
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="add_product.php">Voeg Product Toe</a>
                    <li><a href="manage_products.php">Beheer Producten</a></li>
                    <li><a href="active_deactivate_show_users.php">Users</a></li>
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo $_SESSION['voornaam']; ?></a></li> <!-- Welkomstbericht met de voornaam van de gebruiker -->
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li> <!-- Link om in te loggen of te registreren -->
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h2>Available Products</h2> <!-- Titel van de productsectie -->
    <div class="product-grid">
        <?php while ($row = $result->fetch_assoc()): ?> <!-- Loop door de resultaten van de query -->
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
        <?php endwhile; ?>
    </div>
</main>
</body>
</html>
