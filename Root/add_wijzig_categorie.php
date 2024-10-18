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