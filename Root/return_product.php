<?php
// Verbinding met de database
include 'connect.php';
session_start();

// Controleer of de klant is ingelogd
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php"); // Als niet ingelogd, doorverwijzen naar loginpagina
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verzamelen van de formuliergegevens
    $order_id = $_POST['order_id'];
    $product_id = $_POST['product_id'];
    $return_reason = $_POST['return_reason'];
    $user_id = $_SESSION['user_id']; // Gebruikers-ID van de ingelogde klant
    $return_status = 'pending'; // Initialiseer de status als 'pending'
    $request_date = date('Y-m-d H:i:s'); // Huidige datum en tijd

    // SQL-query om het retour in de database in te voeren
    $sql = "INSERT INTO returns (order_id, user_id, product_id, return_reason, return_status, request_date)
            VALUES (?, ?, ?, ?, ?, ?)";

    // Voorbereiden en uitvoeren van de query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiisss", $order_id, $user_id, $product_id, $return_reason, $return_status, $request_date);

    if ($stmt->execute()) {
        echo "Retouraanvraag is succesvol ingediend.";
    } else {
        echo "Er is een fout opgetreden: " . $stmt->error;
    }
}
?>

<?php include 'header.php'; ?>
<br><br>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retour Aanvragen</title>
    <style>
        body {
            font-family: 'Oswald', Arial, sans-serif;
            margin: 20px;
        }
        form {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        label {
            font-size: 1.1em;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h1>Retour Aanvragen</h1>

<form method="POST" action="return_product.php">
    <label for="order_id">Factuurnummer:</label>
    <input type="number" id="order_id" name="order_id" required>
    
    <label for="product_id">Artikelnummer:</label>
    <input type="number" id="product_id" name="product_id" required>
    
    <label for="return_reason">Reden voor Retour:</label>
    <textarea id="return_reason" name="return_reason" rows="4" required></textarea>
    
    <button type="submit">Retour aanvragen</button>
</form>

<a href="index.php" class="back-button">Terug naar de homepage</a>
<br><br>    
<?php include 'footer.php'; ?>
</body>
</html>

<?php
// Sluit de databaseverbinding
$conn->close();
?>
