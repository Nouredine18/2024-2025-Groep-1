<?php
include 'connect.php';
session_start();

// Controleer of de gebruiker is ingelogd en admin is
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login_register.php"); // Redirect naar login als niet ingelogd
    exit();
}

// Verwerk voorraadupdates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $product_id = $_POST['product_id']; // Dit is de variantnr
    $new_stock = $_POST['new_stock'];

    // Update de voorraad in de database
    $sql_update = "UPDATE ProductVariant SET stock = ? WHERE variantnr = ?";
    $stmt_update = $conn->prepare($sql_update);
    if ($stmt_update) {
        $stmt_update->bind_param("ii", $new_stock, $product_id);
        if ($stmt_update->execute()) {
            // Succesmelding
            $success_message = "Voorraad succesvol bijgewerkt.";
        } else {
            $error_message = "Fout bij het bijwerken van de voorraad.";
        }
        $stmt_update->close();
    }
}

// Haal alle producten en hun varianten op
$sql = "SELECT pv.variantnr, p.naam, pv.kleur, pv.maat, pv.stock 
        FROM ProductVariant pv 
        JOIN Products p ON pv.artikelnr = p.artikelnr";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Beheer Voorraad</title>
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
<header>
    <div class="logo">SchoenenWijns</div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="add_product.php">Voeg Product Toe</a></li>
            <li><a href="manage_products.php">Beheer Producten</a></li>
            <li><a href="active_deactivate_show_users.php">Users</a></li> 
            <li><a href="#">Welcome, <?php echo $_SESSION['voornaam']; ?></a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Beheer Voorraad van Producten</h1>

    <?php if (isset($success_message)) : ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)) : ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Naam</th>
                <th>Kleur</th>
                <th>Maat</th>
                <th>Voorraad</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['naam']); ?></td>
                        <td><?php echo htmlspecialchars($row['kleur']); ?></td>
                        <td><?php echo htmlspecialchars($row['maat']); ?></td>
                        <td><?php echo htmlspecialchars($row['stock']); ?></td>
                        <td>
                            <form action="" method="post" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $row['variantnr']; ?>"> <!-- Zorg ervoor dat dit de juiste variantnr is -->
                                <input type="number" name="new_stock" value="<?php echo $row['stock']; ?>" min="0">
                                <input type="submit" name="update_stock" value="Bijwerken">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Geen producten beschikbaar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<footer>
    <p>&copy; 2024 SchoenenWijns. All rights reserved.</p>
</footer>
</body>
</html>
