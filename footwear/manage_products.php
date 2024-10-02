<?php
include 'connect.php';
session_start();

// Controleer of de gebruiker is ingelogd als admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Verwerk verwijderverzoek
if (isset($_GET['delete'])) {
    $artikelnr = intval($_GET['delete']);
    $sql_delete = "DELETE FROM Products WHERE artikelnr = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $artikelnr);
    $stmt_delete->execute();
    header("Location: manage_products.php"); // Redirect om te voorkomen dat het verzoek opnieuw wordt uitgevoerd
    exit();
}

// Haal alle producten op
$sql_products = "SELECT * FROM Products";
$result_products = $conn->query($sql_products);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>SchoenenWijns | Beheer Producten</title>
</head>
<body>
<header>
    <div class="logo">SchoenenWijns</div>
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
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo $_SESSION['voornaam']; ?></a></li> <!-- Welkomstbericht met de voornaam van de gebruiker -->
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li> <!-- Link om in te loggen of te registreren -->
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <div class="hero">
        <h1>Beheer Producten</h1>
        <p>Hier kunt u producten bewerken of verwijderen.</p>
    </div>

    <div class="products">
        <h2>Productenlijst</h2>
        <table>
            <tr>
                <th>Artikelnummer</th>
                <th>Naam</th>
                <th>Prijs</th>
                <th>Type Schoen</th>
                <th>Acties</th>
            </tr>
            <?php while ($product = $result_products->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $product['artikelnr']; ?></td>
                    <td><?php echo htmlspecialchars($product['naam']); ?></td>
                    <td><?php echo htmlspecialchars($product['prijs']); ?></td>
                    <td><?php echo htmlspecialchars($product['type_of_shoe']); ?></td>
                    <td>
                        <a href="edit_product.php?artikelnr=<?php echo $product['artikelnr']; ?>">Bewerken</a>
                        <a href="manage_products.php?delete=<?php echo $product['artikelnr']; ?>" onclick="return confirm('Weet je zeker dat je dit product wilt verwijderen?');">Verwijderen</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>

<footer>
    <p>&copy; 2024 SchoenenWijns. Alle rechten voorbehouden.</p>
</footer>
</body>
</html>
