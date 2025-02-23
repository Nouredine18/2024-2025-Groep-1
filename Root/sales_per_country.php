<?php

include 'connect.php';
session_start();

// Haal de verkoopgegevens per land op
$sqlSalesPerCountry = "
    SELECT a.land, SUM(bp.aantal) as totaal_verkocht, SUM(bp.aantal * p.prijs) as totaal_omzet
    FROM BoughtProducts bp
    JOIN Adres a ON bp.address_id = a.address_id
    JOIN Products p ON bp.artikelnr = p.artikelnr
    GROUP BY a.land
    ORDER BY totaal_omzet DESC
";
$resultSalesPerCountry = $conn->query($sqlSalesPerCountry);

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verkoop per Land</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .header {
            background-color: #000;
            color: white;
            text-align: center;
            padding: 20px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 30px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .container {
            display: flex;
            justify-content: center;
            margin: 20px;
            flex-wrap: wrap;
        }

        .content {
            width: 100%;
            max-width: 1200px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #ffdc00; /* Lichtere tint voor de koppen */
            text-align: center;
        }

        table {
            width: 100%; /* Zorg ervoor dat de tabel de volledige breedte gebruikt */
            border-collapse: collapse; /* Sluit tabelrandjes samen */
            margin-top: 20px; /* Voeg wat ruimte boven de tabel toe */
        }

        th, td {
            border: 1px solid #ddd; /* Grijze randen voor tabelcellen */
            padding: 10px; /* Padding binnen de cellen */
            text-align: left; /* Uitlijnen van tekst naar links */
        }

        th {
            background-color: #0056b3; /* Donkerblauwe achtergrond voor tabelkoppen */
            color: white; /* Witte tekstkleur voor tabelkoppen */
        }

        tr:nth-child(even) {
            background-color: #f9f9f9; /* Lichtere achtergrond voor even rijen */
        }

        tr:hover {
            background-color: #f1f1f1; /* Lichtere achtergrond bij hover */
        }

        .footer {
            background-color: #111;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            width: 100%;
            bottom: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <div class="content">
            <h2>Verkoop per Land</h2>
            <table>
                <thead>
                    <tr>
                        <th>Land</th>
                        <th>Totaal Verkocht</th>
                        <th>Totaal Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultSalesPerCountry->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['land']); ?></td>
                        <td><?php echo htmlspecialchars($row['totaal_verkocht']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($row['totaal_omzet'], 2)); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 Schoenen Wijns - All Rights Reserved</p>
    </div>

</body>
</html>