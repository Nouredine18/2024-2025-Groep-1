<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <style>body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background-color: #f9f9f9;
}

h2, h3 {
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

input[type="submit"].delete-button {
    background-color: #d9534f; /* Rood voor verwijderen */
}

input[type="submit"]:hover {
    background-color: #4cae4c;
}

input[type="submit"].delete-button:hover {
    background-color: #c9302c; /* Donkerder rood voor verwijderen */
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background-color: #f2f2f2;
}

.success {
    color: green;
    font-weight: bold;
}

.error {
    color: red;
    font-weight: bold;
}
</style>
</body>
</html>
<?php

include 'connect.php';
session_start();

// Controleer of het formulier is verzonden voor toevoegen of verwijderen van een categorie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete'])) {
        $categorie_id = $_POST['categorie_id'];

        $sql = "DELETE FROM Categorie WHERE categorie_id = '$categorie_id'";
        
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color:green;'>Categorie succesvol verwijderd.</p>";
        } else {
            echo "<p style='color:red;'>Fout bij het verwijderen van de categorie: " . $conn->error . "</p>";
        }
    } elseif (isset($_POST['add'])) {
        $categorie = trim($_POST['categorie']);

        if (!empty($categorie)) {
            $sqlAdd = "INSERT INTO Categorie (categorie) VALUES (?)";
            $stmtAdd = $conn->prepare($sqlAdd);
            $stmtAdd->bind_param("s", $categorie);

            if ($stmtAdd->execute()) {
                echo "<p style='color:green;'>Categorie succesvol toegevoegd.</p>";
            } else {
                echo "<p style='color:red;'>Fout bij het toevoegen van de categorie: " . $stmtAdd->error . "</p>";
            }
        } else {
            echo "<p style='color:red;'>Categorie mag niet leeg zijn.</p>";
        }
    }
} 

// Selecteer alle categorieën om te tonen
$sql = "SELECT * FROM Categorie";
$result = $conn->query($sql);

echo '<h2>Categorie Beheer</h2>';

// Formulier voor het toevoegen van een categorie
echo '<h3>Voeg een nieuwe categorie toe</h3>';
echo '<form action="" method="post">
        <label for="categorie">Categorie Naam:</label>
        <input type="text" name="categorie" required>
        <input type="submit" name="add" value="Toevoegen">
      </form>';

echo '<h3>Bestaande Categorieën</h3>';
echo '<table>
    <thead>
        <th>Categorie</th>
        <th>Acties</th>
    </thead>
    <tbody>';

while ($row = $result->fetch_assoc()) {
    echo '<tr>
        <td>' . htmlspecialchars($row["categorie"]) . '</td>
        <td>
            <form action="" method="post" style="display:inline;">
                <input type="hidden" name="categorie_id" value="' . $row["categorie_id"] . '">
                <input type="submit" name="delete" value="Verwijderen">
            </form>
            <a href="edit_categorie.php?id=' . $row["categorie_id"] . '">Wijzig</a>
        </td>
    </tr>';
}

echo '</tbody>
</table>';

$conn->close(); // Sluit de databaseverbinding
?>