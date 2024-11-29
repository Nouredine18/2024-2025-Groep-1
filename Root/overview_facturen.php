<?php
// Verbinden met de database
include 'connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Query om openstaande facturen op te halen
$sql = "SELECT f.bestelling_id, f.user_id, f.address_id, f.oorspronkelijke_prijs, 
               f.reductie, f.betalingsmethode, a.straat, a.huisnummer, a.postcode, a.stad, a.land
        FROM factuur f
        LEFT JOIN Adres a ON f.address_id = a.address_id
        WHERE f.oorspronkelijke_prijs - f.reductie > 0"; // Alleen facturen met een openstaand bedrag

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overzicht Openstaande Facturen</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Overzicht van Openstaande Facturen</h1>
    <table>
        <thead>
            <tr>
                <th>Factuurnummer</th>
                <th>Klant ID</th>
                <th>Adres</th>
                <th>Oorspronkelijke Prijs (€)</th>
                <th>Reductie (€)</th>
                <th>Openstaand Bedrag (€)</th>
                <th>Betalingsmethode</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['bestelling_id']); ?></td>
                        <td><?= htmlspecialchars($row['user_id']); ?></td>
                        <td>
                            <?= htmlspecialchars($row['straat']) . " " . 
                                htmlspecialchars($row['huisnummer']) . ", " . 
                                htmlspecialchars($row['postcode']) . " " . 
                                htmlspecialchars($row['stad']) . ", " . 
                                htmlspecialchars($row['land']); ?>
                        </td>
                        <td><?= number_format($row['oorspronkelijke_prijs'], 2); ?></td>
                        <td><?= number_format($row['reductie'], 2); ?></td>
                        <td><?= number_format($row['oorspronkelijke_prijs'] - $row['reductie'], 2); ?></td>
                        <td><?= htmlspecialchars($row['betalingsmethode']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">Geen openstaande facturen gevonden.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="adminPanel.php" style="display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">Terug naar Admin Panel</a>

</body>
</html>

<?php
$conn->close();
?>
