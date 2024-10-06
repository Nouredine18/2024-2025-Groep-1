<?php
// Verbind met de database en start de sessie
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

// Controleer of de gebruiker is ingelogd, zo niet, stuur door naar de login pagina
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

// Haal de user_id op uit de sessie
$user_id = $_SESSION['user_id'];

// SQL-query om de producten en hun details uit de winkelwagen op te halen
$sql = "SELECT p.naam, pv.kleur, pv.maat, c.aantal, p.prijs, c.artikelnr, c.variantnr 
        FROM Cart c 
        JOIN ProductVariant pv ON c.artikelnr = pv.artikelnr AND c.variantnr = pv.variantnr
        JOIN Products p ON c.artikelnr = p.artikelnr 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql); // Bereid de SQL-query voor
$stmt->bind_param("i", $user_id); // Koppel de user_id als parameter
$stmt->execute(); // Voer de query uit
$result = $stmt->get_result(); // Haal het resultaat van de query op
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">
    <title>Shopping Cart</title>
</head>
<body>
<header>
    <div class="logo">SchoenenWijns</div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="webshop.php">Shop</a></li> <!-- Link naar Webshop -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="cart.php">Cart (<?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0'; ?>)</a></li>
                <li><a href="logout.php">Logout</a></li>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="add_product.php">Voeg Product Toe</a>
                    <li><a href="manage_products.php">Beheer Producten</a></li>
                    <li><a href="active_deactivate_show_users.php">Users</a></li>
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo $_SESSION['voornaam']; ?></a></li>
                
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h2>Your Cart</h2>
    <div class="cart-items">
        <?php if ($result->num_rows > 0): ?>
            <form action="update_cart.php" method="POST">
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    $total_price = 0; // Variabele om de totale prijs bij te houden
                    while ($row = $result->fetch_assoc()):
                        $item_total = $row['aantal'] * $row['prijs']; // Bereken de totale prijs per artikel
                        $total_price += $item_total; // Voeg de prijs toe aan de totaalprijs
                    ?>
                    <tr>
                        <td><?php echo $row['naam']; ?></td>
                        <td><?php echo $row['kleur']; ?></td>
                        <td><?php echo $row['maat']; ?></td>
                        <td>
                            <input type="number" name="quantities[<?php echo $row['artikelnr']; ?>][<?php echo $row['variantnr']; ?>]" value="<?php echo $row['aantal']; ?>" min="1" max="100">
                        </td>
                        <td>€<?php echo number_format($row['prijs'], 2); ?></td>
                        <td>€<?php echo number_format($item_total, 2); ?></td>
                        <td>
                            <button type="submit" name="remove" value="<?php echo $row['artikelnr'] . '-' . $row['variantnr']; ?>">Remove</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="5" class="total">Total Price:</td>
                        <td class="total">€<?php echo number_format($total_price, 2); ?></td>
                        <td></td>
                    </tr>
                </table>
                <button type="submit" name="update_cart">Update Cart</button>
            </form>
        <?php else: ?>
            <p class="empty-cart">Your cart is empty.</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
