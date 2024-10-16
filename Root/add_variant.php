<?php
session_start();
include 'connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Variant Toevoegen</title>
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
        <h2>Voeg Varianten Toe aan Bestaand Product</h2>
        <form action="add_variant.php" method="POST" enctype="multipart/form-data"> <!-- Formulier voor bestand upload -->
            <label for="existing_product">Bestaand Product:</label>
            <select name="existing_product" id="existing_product" required>
                <?php
                // Verbind met de database
                include 'connect.php';

                // Haal bestaande producten op uit de database
                $bestaanProduct = "SELECT artikelnr, naam FROM Products";
                $result = $conn->query($bestaanProduct);

                // Controleer of er resultaten zijn
                if ($result->num_rows > 0) {
                    // Loop door de resultaten en voeg ze toe aan de dropdown
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['artikelnr']) . '">' . htmlspecialchars($row['naam']) . '</option>';
                    }
                } else {
                    echo '<option value="">Geen producten beschikbaar</option>';
                }
                ?>
            </select>

            <label for="variant_color">Kleur:</label>
            <select id="variant_color" name="variant_color" required>
                <option value="black">Black</option>
                <option value="grey">Grey</option>
                <option value="white">White</option>
                <option value="green">Green</option>
            </select>

            <label for="size_from">Maat (van):</label>
            <input type="number" id="size_from" name="size_from" required>

            <label for="size_to">Maat (tot):</label>
            <input type="number" id="size_to" name="size_to" required>

            <label for="variant_stock">Voorraad:</label>
            <input type="number" id="variant_stock" name="variant_stock" required>

            <label for="variant_image">Afbeeldingen (exact 3):</label>
            <input type="file" id="variant_image" name="variant_image[]" accept="image/*" multiple required>

            <button type="submit" name="add_variant">Voeg Variant Toe</button>
        </form>

        <?php
        // Verwerk het indienen van het variantformulier
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_variant'])) {
            $existing_product = intval($_POST['existing_product']);
            $variant_color = htmlspecialchars($_POST['variant_color']);
            $size_from = intval($_POST['size_from']);
            $size_to = intval($_POST['size_to']);
            $variant_stock = intval($_POST['variant_stock']);
            $variant_images = $_FILES['variant_image'];

            // Controleer of er exact 3 afbeeldingen zijn geüpload
            if (count($variant_images['name']) !== 3) {
                echo "Je moet exact 3 afbeeldingen uploaden.";
                exit;
            }

            // Verwerk de afbeelding upload
            $target_dir = "variant_directory/";
            $uploaded_files = [];
            $uploadOk = 1;

            // Controleer of de directory bestaat, zo niet, maak deze aan
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            for ($i = 0; $i < count($variant_images['name']); $i++) {
                $target_file = $target_dir . basename($variant_images["name"][$i]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Controleer of het bestand een afbeelding is
                $check = getimagesize($variant_images["tmp_name"][$i]);
                if ($check !== false) {
                    $uploadOk = 1;
                } else {
                    echo "Bestand is geen afbeelding.";
                    $uploadOk = 0;
                }

                // Controleer of het bestand al bestaat
                if (file_exists($target_file)) {
                    echo "Sorry, bestand bestaat al.";
                    $uploadOk = 0;
                }

                // Controleer bestandsgrootte
                if ($variant_images["size"][$i] > 500000) {
                    echo "Sorry, je bestand is te groot.";
                    $uploadOk = 0;
                }

                // Alleen bepaalde bestandstypen toestaan
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    echo "Sorry, alleen JPG, JPEG, PNG & GIF bestanden zijn toegestaan.";
                    $uploadOk = 0;
                }

                // Controleer of $uploadOk op 0 is gezet door een fout
                if ($uploadOk == 0) {
                    echo "Sorry, je bestand is niet geüpload.";
                // Probeer het bestand te uploaden
                } else {
                    if (move_uploaded_file($variant_images["tmp_name"][$i], $target_file)) {
                        $uploaded_files[] = $target_file;
                    } else {
                        echo "Sorry, er was een fout bij het uploaden van je bestand.";
                    }
                }
            }

            if ($uploadOk == 1 && !empty($uploaded_files)) {
                $images_string = implode(" | ", $uploaded_files);

                // Voeg de varianten toe aan de database voor elke maat in het bereik
                for ($size = $size_from; $size <= $size_to; $size++) {
                    // Generate a unique variantnr
                    $variantnr_result = $conn->query("SELECT MAX(variantnr) AS max_variantnr FROM ProductVariant");
                    $variantnr_row = $variantnr_result->fetch_assoc();
                    $variantnr = $variantnr_row['max_variantnr'] + 1;

                    $sql_variant = "INSERT INTO ProductVariant (artikelnr, variantnr, kleur, maat, stock, variant_directory) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt_variant = $conn->prepare($sql_variant);
                    $stmt_variant->bind_param("iissis", $existing_product, $variantnr, $variant_color, $size, $variant_stock, $images_string);

                    if ($stmt_variant->execute()) {
                        echo "<p>Variant met maat $size succesvol toegevoegd!</p>";
                    } else {
                        echo "<p>Er is een fout opgetreden bij het toevoegen van de variant met maat $size: " . $stmt_variant->error . "</p>";
                    }

                    $stmt_variant->close();
                }
            }
        }

        $conn->close();
        ?>
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