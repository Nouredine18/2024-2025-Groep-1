<?php

include 'connect.php';
session_start();

$user_id = $_SESSION["user_id"];

// Haal bestaande adressen op voor deze gebruiker
$sqlSelect = "SELECT address_id, straat, huisnummer, postcode, stad, land FROM Adres WHERE user_id = ?";
$stmtSelect = $conn->prepare($sqlSelect);
$stmtSelect->bind_param("i", $user_id);
$stmtSelect->execute();
$resultSelect = $stmtSelect->get_result();

// Tel het aantal adressen
$addressCount = $resultSelect->num_rows;

// Initieer het bewerken van een adres
$editAddressId = null;
if (isset($_GET['edit'])) {
    $editAddressId = intval($_GET['edit']);
    $sqlEdit = "SELECT straat, huisnummer, postcode, stad, land FROM Adres WHERE user_id = ? AND address_id = ?";
    $stmtEdit = $conn->prepare($sqlEdit);
    $stmtEdit->bind_param("ii", $user_id, $editAddressId);
    $stmtEdit->execute();
    $resultEdit = $stmtEdit->get_result();
    
    if ($resultEdit->num_rows > 0) {
        $editAddress = $resultEdit->fetch_assoc();
    }
}

// Voeg een nieuw adres toe als het formulier is verzonden
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_address'])) {
    // Verkrijg ingevoerde gegevens
    $straat = trim($_POST['straat']);
    $huisnummer = trim($_POST['huisnummer']);
    $postcode = trim($_POST['postcode']);
    $stad = trim($_POST['stad']);
    $land = trim($_POST['land']);

    // Controleer of er al 3 adressen zijn
    if ($addressCount < 3) {
        // Bepaal de volgende address_id, die is gelijk aan het aantal adressen + 1
        $newAddressId = $addressCount + 1;

        // SQL voor het invoegen van het adres
        $sqlInsert = "INSERT INTO Adres (user_id, address_id, straat, huisnummer, postcode, stad, land) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("iisssss", $user_id, $newAddressId, $straat, $huisnummer, $postcode, $stad, $land);

        // Voer de insert uit en controleer op fouten
        if ($stmtInsert->execute()) {
            echo "<p style='color:green;'>Adres succesvol toegevoegd!</p>";
            // Herlaad de adressen om de nieuwe te tonen
            $stmtSelect->execute();
            $resultSelect = $stmtSelect->get_result();
            $addressCount++; // Verhoog het aantal adressen
        } else {
            echo "<p style='color:red;'>Fout bij het toevoegen van adres: " . $stmtInsert->error . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Fout: U kunt maximaal drie adressen toevoegen.</p>";
    }
}

// Adres wijzigen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_address'])) {
    $straat = trim($_POST['straat']);
    $huisnummer = trim($_POST['huisnummer']);
    $postcode = trim($_POST['postcode']);
    $stad = trim($_POST['stad']);
    $land = trim($_POST['land']);
    
    // Update het adres
    $sqlUpdate = "UPDATE Adres SET straat = ?, huisnummer = ?, postcode = ?, stad = ?, land = ? WHERE user_id = ? AND address_id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("sssssii", $straat, $huisnummer, $postcode, $stad, $land, $user_id, $editAddressId);
    
    if ($stmtUpdate->execute()) {
        echo "<p style='color:green;'>Adres succesvol gewijzigd!</p>";
        // Herlaad de adressen
        $stmtSelect->execute();
        $resultSelect = $stmtSelect->get_result();
        $editAddressId = null; // Reset editAddressId zodat het formulier niet meer zichtbaar is
    } else {
        echo "<p style='color:red;'>Fout bij het wijzigen van adres: " . $stmtUpdate->error . "</p>";
    }
}


?>

<!-- HTML structuur voor het tonen van adressen en mogelijk een nieuw adres toevoegen -->
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Adresbeheer</title>
</head>
<body>
    <style>
body {
    background-color: #001f3f; /* Donkerblauwe achtergrond */
    color: white; /* Witte tekstkleur */
    font-family: Arial, sans-serif; /* Lettertype */
    margin: 0; /* Verwijder standaard marges */
    padding: 20px; /* Voeg wat padding toe */
}

h2 {
    color: #ffdc00; /* Lichtere tint voor de koppen */
}

table {
    width: 100%; /* Zorg ervoor dat de tabel de volledige breedte gebruikt */
    border-collapse: collapse; /* Sluit tabelrandjes samen */
    margin-top: 20px; /* Voeg wat ruimte boven de tabel toe */
}

th, td {
    border: 1px solid #FFFFFF; /* Witte randen voor tabelcellen */
    padding: 10px; /* Padding binnen de cellen */
    text-align: left; /* Uitlijnen van tekst naar links */
}

th {
    background-color: #0056b3; /* Donkerblauwe achtergrond voor tabelkoppen */
}

tr:nth-child(even) {
    background-color: #003366; /* Iets lichtere donkerblauwe achtergrond voor even rijen */
}

a {
    color: #ffffff; /* Witte kleur voor links */
    text-decoration: none; /* Verwijder onderlijning van links */
}

a:hover {
    color: #ffdc00; /* Lichtgele kleur bij hover op links */
}

form {
    border: 1px solid #0056b3; /* Donkerblauwe rand voor formulieren */
    padding: 20px; /* Padding binnen het formulier */
    background-color: #001f3f; /* Donkerblauwe achtergrond voor het formulier */
}

label {
    display: block; /* Maak labels blokken zodat ze altijd op een nieuwe regel staan */
    margin: 10px 0 5px; /* Voeg wat marge toe aan labels */
}

input[type="text"] {
    width: 100%; /* Volledige breedte voor invoervelden */
    padding: 10px; /* Padding binnen invoervelden */
    margin-bottom: 10px; /* Voeg ruimte onder het invoerveld toe */
    border: 1px solid #ffffff; /* Witte rand voor invoervelden */
    background-color: #003366; /* Lichtere donkerblauwe achtergrond voor invoervelden */
    color: white; /* Witte tekstkleur in invoervelden */
}

input[type="submit"] {
    background-color: #0056b3; /* Donkerblauwe achtergrond voor knoppen */
    color: white; /* Witte tekstkleur voor knoppen */
    padding: 10px 20px; /* Padding voor knoppen */
    border: none; /* Geen rand voor knoppen */
    cursor: pointer; /* Cursor verandert naar handje bij hover */
}

input[type="submit"]:hover {
    background-color: #003f7f; /* Iets lichtere tint bij hover op knoppen */
}
</style>
    <h2>Bestaande Adressen</h2>
    <table border="1">
        <tr>
            <th>Adres ID</th>
            <th>Straat</th>
            <th>Huisnummer</th>
            <th>Postcode</th>
            <th>Stad</th>
            <th>Land</th>
            <th>Acties</th>
        </tr>
        <?php while ($address = $resultSelect->fetch_assoc()): ?>
        <tr>
            <td><?php echo $address['address_id']; ?></td>
            <td><?php echo $address['straat']; ?></td>
            <td><?php echo $address['huisnummer']; ?></td>
            <td><?php echo $address['postcode']; ?></td>
            <td><?php echo $address['stad']; ?></td>
            <td><?php echo $address['land']; ?></td>
            <td>
                <a href="?edit=<?php echo $address['address_id']; ?>">Bewerken</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <?php if ($addressCount < 3): ?>
        <h2>Voeg een nieuw adres toe</h2>
        <form method="post" action="">
            <label for="straat">Straat:</label>
            <input type="text" name="straat" required><br>
            <label for="huisnummer">Huisnummer:</label>
            <input type="text" name="huisnummer" required><br>
            <label for="postcode">Postcode:</label>
            <input type="text" name="postcode" required><br>
            <label for="stad">Stad:</label>
            <input type="text" name="stad" required><br>
            <label for="land">Land:</label>
            <input type="text" name="land" required><br>
            <input type="submit" name="add_address" value="Adres Toevoegen">
        </form>
    <?php else: ?>
        <p>U heeft al het maximale aantal adressen toegevoegd.</p>
    <?php endif; ?>

    <?php if ($editAddressId !== null): ?>
        <h2>Adres Wijzigen</h2>
        <form method="post" action="">
            <input type="hidden" name="address_id" value="<?php echo htmlspecialchars($editAddressId); ?>">
            <label for="straat">Straat:</label>
            <input type="text" name="straat" value="<?php echo htmlspecialchars($editAddress['straat']); ?>" required><br>
            <label for="huisnummer">Huisnummer:</label>
            <input type="text" name="huisnummer" value="<?php echo htmlspecialchars($editAddress['huisnummer']); ?>" required><br>
            <label for="postcode">Postcode:</label>
            <input type="text" name="postcode" value="<?php echo htmlspecialchars($editAddress['postcode']); ?>" required><br>
            <label for="stad">Stad:</label>
            <input type="text" name="stad" value="<?php echo htmlspecialchars($editAddress['stad']); ?>" required><br>
            <label for="land">Land:</label>
            <input type="text" name="land" value="<?php echo htmlspecialchars($editAddress['land']); ?>" required><br>
            <input type="submit" name="edit_address" value="Adres Wijzigen">
        </form>
    <?php endif; ?>
</body>
</html>