<?php
// NOG NIET KLAAR! --VAN NOUREDINE--
// Verbind met de database
include 'connect.php'; 
session_start(); // Start de sessie

// Controleer of de gebruiker een admin is
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    // Als de gebruiker geen admin is, omleiden naar inlogpagina
    header("Location: login_register.php"); 
    exit(); // Stop verdere uitvoering van het script
}

$uploadOk = 1; // Variabele om bij te houden of de upload succesvol is
$target_dir = "directory/"; // De map waar geüploade bestanden worden opgeslagen

// Controleer of het formulier is ingediend
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Controleer of er een bestand is geüpload en dat er geen fouten zijn
    if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
        $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION)); // Haal de bestandsextensie op en maak deze klein

        // Controleer of het geüploade bestand een afbeelding is
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            echo "Bestand is een afbeelding - " . htmlspecialchars($check["mime"]) . ".";
            $uploadOk = 1; // Zet uploadOk op 1 om aan te geven dat upload kan doorgaan
        } else {
            echo "Bestand is geen afbeelding.";
            $uploadOk = 0; // Zet uploadOk op 0 om upload te blokkeren
        }

        // Toestaan van bepaalde bestandsformaten
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) { // Controleer op toegestane types
            echo "Sorry, alleen JPG, JPEG, PNG, GIF & WEBP bestanden zijn toegestaan.";
            $uploadOk = 0; // Zet uploadOk op 0 om upload te blokkeren
        }

        // Controleer of uploadOk is ingesteld op 0 door een fout
        if ($uploadOk == 0) {
            echo "Sorry, uw bestand is niet geüpload.";
        } else {
            // Genereer een unieke bestandsnaam om overschrijving te voorkomen
            $unique_filename = uniqid('', true) . '.' . $imageFileType; // Unieke bestandsnaam genereren
            $target_file = $target_dir . $unique_filename; // Volledige pad voor het bestand

            // Probeer het bestand te uploaden
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "Het bestand " . htmlspecialchars(basename($unique_filename)) . " is geüpload.";

                // Controleer of de formuliergegevens zijn ingesteld
                if (isset($_POST['naam'], $_POST['prijs'], $_POST['type_of_shoe'], $_POST['kleur'], $_POST['maat'], $_POST['stock'])) {
                    // Sanitize en wijs invoerwaarden toe
                    $naam = htmlspecialchars($_POST['naam']); // Productnaam
                    $prijs = floatval($_POST['prijs']); // Productprijs, omgezet naar een float
                    $type_of_shoe = htmlspecialchars($_POST['type_of_shoe']); // Type schoen
                    $kleur = htmlspecialchars($_POST['kleur']); // Kleur variant
                    $maat = intval($_POST['maat']); // Maat variant, omgezet naar een integer
                    $stock = intval($_POST['stock']); // Voorraad variant, omgezet naar een integer

                    // Voeg product toe aan de Products tabel
                    $insert_product_sql = "INSERT INTO Products (naam, prijs, type_of_shoe, directory) VALUES (?, ?, ?, ?)";
                    $stmt_product = $conn->prepare($insert_product_sql); // Voorbereiden van de SQL-instructie
                    $stmt_product->bind_param("sdss", $naam, $prijs, $type_of_shoe, $unique_filename); // Binden van parameters aan de instructie

                    if ($stmt_product->execute()) {
                        // Verkrijg het laatst ingevoerde productnummer
                        $artikelnr = $stmt_product->insert_id;

                        // Voeg productvariant toe aan de ProductVariant tabel
                        $insert_variant_sql = "INSERT INTO ProductVariant (artikelnr, variantnr, kleur, maat, stock, bought_counter) VALUES (?, ?, ?, ?, ?, ?)";
                        $variantnr = 1; // Dit kan worden aangepast voor meerdere varianten
                        $bought_counter = 0; // Aanvankelijke waarde van de verkochte teller

                        $stmt_variant = $conn->prepare($insert_variant_sql); // Voorbereiden van de SQL-instructie voor de variant
                        $stmt_variant->bind_param("iissii", $artikelnr, $variantnr, $kleur, $maat, $stock, $bought_counter); // Binden van parameters aan de instructie
                        
                        if ($stmt_variant->execute()) {
                            // Als de variant succesvol is toegevoegd
                            echo "Product en variant zijn succesvol toegevoegd!"; // Bevestiging dat product is toegevoegd
                        } else {
                            // Fout bij het toevoegen van de productvariant
                            echo "Fout bij het toevoegen van de productvariant: " . $stmt_variant->error; 
                        }

                        // Sluit de variant statement
                        $stmt_variant->close();
                    } else {
                        // Fout bij het toevoegen van het product
                        echo "Fout bij het toevoegen van het product: " . $stmt_product->error; 
                    }

                    // Sluit de product statement
                    $stmt_product->close();
                } else {
                    // Ongeldige invoer
                    echo "Ongeldige invoer."; 
                }
            } else {
                // Fout bij het uploaden van het bestand
                echo "Sorry, er was een fout bij het uploaden van uw bestand.";
            }
        }
    } else {
        // Fout bij het uploaden van het bestand
        echo "Bestand upload fout. Probeer het opnieuw. Foutcode: " . $_FILES["fileToUpload"]["error"];
    }
}

// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css"> <!-- CSS-bestand voor stijl -->
    <link rel="stylesheet" href="css/cart.css"> <!-- CSS-bestand voor winkelwagentje stijl -->
    <title>SchoenenWijns | Voeg Product Toe</title> <!-- Titel van de pagina -->
    <style>
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
        }

        .logo {
            font-size: 1.5em;
            font-weight: bold;
            text-align: center;
        }

        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        nav ul li {
            display: inline;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            transition: background-color 0.3s;
        }

        nav ul li a:hover {
            background-color: #007bff;
        }

        main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        footer p {
            margin: 0;
            padding: 10px 0;
        }

        .social-media {
            margin-top: 10px;
        }

        .social-media h3 {
            margin-bottom: 10px;
        }

        .social-media ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-media ul li {
            display: inline;
        }

        .social-media ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 1.2em;
            transition: color 0.3s;
        }

        .social-media ul li a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">SchoenenWijns</div> <!-- Logo van de website -->
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li> <!-- Link naar de startpagina -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">Logout</a></li> <!-- Link voor uitloggen -->
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="add_product.php">Voeg Product Toe</a></li> <!-- Link naar product toevoegen -->
                    <li><a href="manage_products.php">Beheer Producten</a></li> <!-- Link naar producten beheren -->
                    <li><a href="active_deactivate_show_users.php">Users</a></li> <!-- Link naar gebruikers beheren -->
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo htmlspecialchars($_SESSION['voornaam']); ?></a></li> <!-- Welkombericht voor de ingelogde gebruiker -->
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li> <!-- Link naar inloggen of registreren -->
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main>
    <!-- Formulier voor het toevoegen van een nieuw product -->
    <form action="add_product.php" method="POST" enctype="multipart/form-data"> <!-- Formulier voor bestand upload -->
        <label for="naam">Naam:</label>
        <input type="text" name="naam" id="naam" required> <!-- Invoerveld voor productnaam -->

        <label for="prijs">Prijs:</label>
        <input type="number" step="0.01" name="prijs" id="prijs" required> <!-- Invoerveld voor productprijs -->

        <label for="type_of_shoe">Type Schoen:</label>
        <input type="text" name="type_of_shoe" id="type_of_shoe" required> <!-- Invoerveld voor type schoen -->

        <h3>Productvariant:</h3> <!-- Sectie voor productvariant -->
        <label for="kleur">Kleur:</label>
        <input type="text" name="kleur" id="kleur" required> <!-- Invoerveld voor kleur -->

        <label for="maat">Maat:</label>
        <input type="number" name="maat" id="maat" required> <!-- Invoerveld voor maat -->

        <label for="stock">Stock:</label>
        <input type="number" name="stock" id="stock" required> <!-- Invoerveld voor voorraad -->

        <label for="fileToUpload">Afbeelding:</label>
        <input type="file" name="fileToUpload" id="fileToUpload" required><br><br><br> <!-- Invoerveld voor bestand upload -->
        <button type="submit" name="submit">Product Toevoegen</button> <!-- Knop om het formulier in te dienen -->
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