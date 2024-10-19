<?php
// Verbinding maken met de database via connect.php
include 'connect.php'; // Zorg ervoor dat dit bestand zich in dezelfde map bevindt

// Bezorgopties ophalen
$sqlSelect = "SELECT * FROM StructuurBezorgopties";
$result = $connection->query($sqlSelect);

if ($result->num_rows > 0) {
    // Output van elke rij
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['BezorgoptieID'] . " - Naam: " . $row['Naam'] . " - Kosten: €" . $row['Kosten'] . " - Verwachte Levertijd: " . $row['VerwachteLevertijd'] . " - Actief: " . ($row['IsActief'] ? 'Ja' : 'Nee') . "<br>";
    }
} else {
    echo "Geen bezorgopties gevonden.";
}

// Verbinding sluiten
$connection->close();
?>
