<?php
include 'connect.php'; // Verbinding maken met de database
session_start(); // Start de sessie
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="cart.css">
    <title>SchoenenWijns | Home</title>
</head>
<body>
<header>
    <div class="logo">SchoenenWijns</div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="webshop.php">Shop</a></li> <!-- Link naar de webshop -->

            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="cart.php">Cart (<?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0'; ?>)</a></li> <!-- Weergave van het aantal artikelen in de winkelwagentje -->
                <li><a href="logout.php">Logout</a></li>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="add_product.php">Voeg Product Toe</a></li> 
                    <li><a href="manage_products.php">Beheer Producten</a></li>
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo $_SESSION['voornaam']; ?></a></li> <!-- Welkomstbericht met de voornaam van de gebruiker -->
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main>
    <!-- Formulier voor het toevoegen van een nieuw product -->
    <form action="add_product.php" method="POST">
        <label for="naam">Naam:</label>
        <input type="text" name="naam" id="naam" required>

        <label for="prijs">Prijs:</label>
        <input type="number" step="0.01" name="prijs" id="prijs" required>

        <label for="type_of_shoe">Type Schoen:</label>
        <input type="text" name="type_of_shoe" id="type_of_shoe" required>

        <h3>Productvariant:</h3>
        <label for="kleur">Kleur:</label>
        <input type="text" name="kleur" id="kleur" required>

        <label for="maat">Maat:</label>
        <input type="number" name="maat" id="maat" required>

        <label for="stock">Stock:</label>
        <input type="number" name="stock" id="stock" required>

        <button type="submit">Product Toevoegen</button> <!-- Verzenden van het formulier -->
    </form>
</main>
</body>
</html>

<?php
// Controleer of de gebruiker een admin is
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login_register.php"); // Als de gebruiker geen admin is, doorverwijzen naar login
    exit();
}

// Controleer of de formuliergegevens zijn ingesteld
if (isset($_POST['naam'], $_POST['prijs'], $_POST['type_of_shoe'], $_POST['kleur'], $_POST['maat'], $_POST['stock'])) {
    // Sanitize en wijs invoerwaarden toe
    $naam = $_POST['naam']; // Naam van het product
    $prijs = floatval($_POST['prijs']); // Prijs van het product
    $type_of_shoe = $_POST['type_of_shoe']; // Type schoen
    $kleur = $_POST['kleur']; // Kleur van de variant
    $maat = intval($_POST['maat']); // Maat van de variant
    $stock = intval($_POST['stock']); // Voorraad van de variant

    // Voeg product toe aan de Products-tabel
    $insert_product_sql = "INSERT INTO Products (naam, prijs, type_of_shoe) VALUES (?, ?, ?)";
    $stmt_product = $conn->prepare($insert_product_sql);
    $stmt_product->bind_param("sds", $naam, $prijs, $type_of_shoe);
    if ($stmt_product->execute()) {
        // Verkrijg het laatst ingevoegde artikelnr
        $artikelnr = $stmt_product->insert_id;

        // Voeg productvariant toe aan de ProductVariant-tabel
        $insert_variant_sql = "INSERT INTO ProductVariant (artikelnr, variantnr, kleur, maat, stock, bought_counter) VALUES (?, ?, ?, ?, ?, ?)";
        // Aangenomen dat variantnr begint bij 1, pas deze logica aan indien nodig
        $variantnr = 1; // Dit kan worden aangepast voor meerdere varianten
        $bought_counter = 0; // Aanvankelijke gekocht teller

        $stmt_variant = $conn->prepare($insert_variant_sql);
        $stmt_variant->bind_param("iissii", $artikelnr, $variantnr, $kleur, $maat, $stock, $bought_counter);
        if ($stmt_variant->execute()) {
            echo "Product en variant succesvol toegevoegd!"; // Bevestiging dat het product is toegevoegd
        } else {
            echo "Fout bij het toevoegen van de productvariant: " . $stmt_variant->error; // Foutmelding bij variant
        }
    } else {
        echo "Fout bij het toevoegen van het product: " . $stmt_product->error; // Foutmelding bij product
    }

    // Sluit de verklaringen
    $stmt_product->close();
    $stmt_variant->close();
} else {
    echo "Ongeldige invoer."; // Foutmelding voor ongeldige invoer
}

// Sluit de verbinding
$conn->close();
?>

