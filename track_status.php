<?php
// Importeer de databaseverbinding
include 'connect.php';

// Check of het formulier is ingediend
if (isset($_POST['check_status'])) {
    // Haal het ingevoerde bestelling_id op
    $bestelling_id = $_POST['bestelling_id'];

    // Query om de status op te halen
    $sql = "SELECT status FROM bestelling WHERE bestelling_id = :bestelling_id";

    // Bereid de query voor
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['bestelling_id' => $bestelling_id]);

    // Haal het resultaat op
    $bestelling = $stmt->fetch(PDO::FETCH_ASSOC);

    // Controleer of de bestelling is gevonden
    if ($bestelling) {
        echo "De status van bestelling #$bestelling_id is: " . $bestelling['status'];
    } else {
        echo "Bestelling niet gevonden.";
    }
}
?>

<!-- HTML Formulier om de status te volgen -->
<!DOCTYPE html>
<html>
<head>
    <title>Volg je bestelling</title>
</head>
<body>
    <h2>Volg de status van je bestelling</h2>
    <form method="POST" action="track_status.php">
        <label for="bestelling_id">Bestelling ID:</label>
        <input type="text" id="bestelling_id" name="bestelling_id" required><br><br>

        <input type="submit" name="check_status" value="Controleer Status">
    </form>
</body>
</html>
