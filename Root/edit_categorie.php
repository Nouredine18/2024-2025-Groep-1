<?php
include 'connect.php';

// Controleer of de categorie_id is doorgegeven via de GET-parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Categorie ID is niet opgegeven.";
    exit;
}

$categorie_id = $_GET['id'];

// Als de pagina is verzonden met een POST-verzoek
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verkrijg de nieuwe categorie naam
    $categorie_naam = trim($_POST['categorie']);
    $categorie_id = intval($_POST["categorie_id"]); // Use intval to ensure it's an integer

    // SQL-query om de categorie bij te werken
    $sql = "UPDATE Categorie SET categorie = ? WHERE categorie_id = ?";

    // Bereid de statement voor
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("si", $categorie_naam, $categorie_id);

        // Voer de query uit
        if ($stmt->execute()) {
            // Controleer hoeveel rijen zijn bijgewerkt
            if ($stmt->affected_rows > 0) {
                // Redirect naar de overzichtspagina na succesvol bijwerken
                header("Location: add_wijzig_categorie.php");
                exit();
            } else {
                echo "Geen wijzigingen aangebracht of categorie niet gevonden.";
            }
        } else {
            echo "Fout bij het bijwerken van de categorie: " . $stmt->error;
        }

        // Sluit de statement
        $stmt->close();
    } else {
        echo "Fout bij het voorbereiden van de SQL-query: " . $conn->error;
    }
}

// SQL-query om de huidige categorie gegevens op te halen
$sql = "SELECT * FROM Categorie WHERE categorie_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $categorie_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $categorie = $row['categorie'];
    } else {
        echo "Geen categorie gevonden.";
        exit;
    }
    // Sluit de statement
    $stmt->close();
} else {
    echo "Fout bij het voorbereiden van de SQL-query: " . $conn->error;
}

// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorie Bewerken</title>
</head>
<body>
    <style>body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background-color: #f9f9f9;
}

h1 {
    color: #333;
}

form {
    margin-bottom: 20px;
}

input[type="text"] {
    padding: 10px;
    margin-right: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

input[type="submit"] {
    padding: 10px 15px;
    background-color: #5cb85c;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #4cae4c;
}

.error {
    color: red;
    font-weight: bold;
}
</style>
    <h1>Categorie Bewerken</h1>
    <form action="edit_categorie.php?id=<?php echo $categorie_id; ?>" method="post">
        <label for="categorie">Categorie naam:</label>
        <input type="text" name="categorie" id="categorie" value="<?php echo htmlspecialchars($categorie); ?>" required>
        <input type="hidden" name="categorie_id" value="<?php echo htmlspecialchars($categorie_id); ?>">
        <input type="submit" value="Bijwerken">
    </form>
    <a href="add_wijzig_categorie.php">Terug naar overzicht</a>
</body>
</html>