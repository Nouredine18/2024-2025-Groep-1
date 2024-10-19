
<?php
// Verbinden met de database
include 'connect.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $discount = $_POST['discount'];
    $expiration_date = $_POST['expiration_date'];

    // Valideren van invoer
    if (empty($code) || empty($discount)) {
        echo "Alle velden zijn verplicht.";
    } else {
        // Kortingscode toevoegen aan de database
        $stmt = $conn->prepare("INSERT INTO discount_codes (code, discount_percentage, expiration_date) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $code, $discount, $expiration_date);

        if ($stmt->execute()) {
            echo "Kortingscode succesvol toegevoegd!";
        } else {
            echo "Fout bij het toevoegen van de kortingscode.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kortingscode toevoegen</title>
</head>
<body>
    <h1>Voeg een nieuwe kortingscode toe</h1>
    <form method="POST" action="">
        <label for="code">Kortingscode:</label><br>
        <input type="text" id="code" name="code" required><br><br>
        
        <label for="discount">Kortingspercentage (%):</label><br>
        <input type="number" step="0.01" id="discount" name="discount" required><br><br>

        <label for="expiration_date">Vervaldatum:</label><br>
        <input type="date" id="expiration_date" name="expiration_date"><br><br>

        <button type="submit">Voeg toe</button>
    </form>
</body>
</html>
