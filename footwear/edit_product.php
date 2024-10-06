<?php
include 'connect.php';
session_start();

// Controleer of de gebruiker is ingelogd als admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Haal product op voor bewerking
if (isset($_GET['artikelnr'])) {
    $artikelnr = intval($_GET['artikelnr']);
    
    // Haal het product op uit de Products tabel
    $sql_product = "SELECT * FROM Products WHERE artikelnr = ?";
    $stmt_product = $conn->prepare($sql_product);
    $stmt_product->bind_param("i", $artikelnr);
    $stmt_product->execute();
    $result_product = $stmt_product->get_result();
    $product = $result_product->fetch_assoc();

    // Haal productvariant(en) op
    $sql_variant = "SELECT * FROM ProductVariant WHERE artikelnr = ?";
    $stmt_variant = $conn->prepare($sql_variant);
    $stmt_variant->bind_param("i", $artikelnr);
    $stmt_variant->execute();
    $result_variant = $stmt_variant->get_result();
    $variants = $result_variant->fetch_all(MYSQLI_ASSOC); // Haal alle varianten op
}

// Verwerk het update-verzoek
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = $_POST['naam'];
    $prijs = $_POST['prijs'];
    $type_of_shoe = $_POST['type_of_shoe'];

    // Update de productinformatie
    $sql_update_product = "UPDATE Products SET naam = ?, prijs = ?, type_of_shoe = ? WHERE artikelnr = ?";
    $stmt_update_product = $conn->prepare($sql_update_product);
    $stmt_update_product->bind_param("sdsi", $naam, $prijs, $type_of_shoe, $artikelnr);
    $stmt_update_product->execute();

    // Update de productvarianten
    foreach ($_POST['variant'] as $variantnr => $variant_data) {
        $kleur = $variant_data['kleur'];
        $maat = $variant_data['maat'];
        $stock = intval($variant_data['stock']);

        $sql_update_variant = "UPDATE ProductVariant SET kleur = ?, maat = ?, stock = ? WHERE artikelnr = ? AND variantnr = ?";
        $stmt_update_variant = $conn->prepare($sql_update_variant);
        $stmt_update_variant->bind_param("siiii", $kleur, $maat, $stock, $artikelnr, $variantnr);
        $stmt_update_variant->execute();
    }

    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>SchoenenWijns | Product Bewerken</title>
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
        <h1>Bewerken van Product</h1>
    </div>

    <div class="edit-product">
        <form method="POST">
            <label for="naam">Naam:</label>
            <input type="text" name="naam" id="naam" value="<?php echo htmlspecialchars($product['naam']); ?>" required>

            <label for="prijs">Prijs:</label>
            <input type="number" step="0.01" name="prijs" id="prijs" value="<?php echo htmlspecialchars($product['prijs']); ?>" required>

            <label for="type_of_shoe">Type Schoen:</label>
            <input type="text" name="type_of_shoe" id="type_of_shoe" value="<?php echo htmlspecialchars($product['type_of_shoe']); ?>" required>

            <h2>Product Varianten</h2>
            <?php foreach ($variants as $variant): ?>
                <div class="variant">
                    <h3>Variant <?php echo htmlspecialchars($variant['variantnr']); ?></h3>
                    <label for="kleur_<?php echo $variant['variantnr']; ?>">Kleur:</label>
                    <input type="text" name="variant[<?php echo $variant['variantnr']; ?>][kleur]" id="kleur_<?php echo $variant['variantnr']; ?>" value="<?php echo htmlspecialchars($variant['kleur']); ?>" required>

                    <label for="maat_<?php echo $variant['variantnr']; ?>">Maat:</label>
                    <input type="number" name="variant[<?php echo $variant['variantnr']; ?>][maat]" id="maat_<?php echo $variant['variantnr']; ?>" value="<?php echo htmlspecialchars($variant['maat']); ?>" required>

                    <label for="stock_<?php echo $variant['variantnr']; ?>">Voorraad:</label>
                    <input type="number" name="variant[<?php echo $variant['variantnr']; ?>][stock]" id="stock_<?php echo $variant['variantnr']; ?>" value="<?php echo htmlspecialchars($variant['stock']); ?>" required>
                </div>
            <?php endforeach; ?>

            <button type="submit">Bijwerken</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2024 SchoenenWijns. Alle rechten voorbehouden.</p>
</footer>
</body>
</html>
