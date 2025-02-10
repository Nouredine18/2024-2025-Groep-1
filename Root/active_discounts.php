<?php
// Verbinden met de database
include 'connect.php';
session_start();

$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// SQL-query om lopende promoties op te halen
$sql = "
    SELECT code, discount_percentage, created_at, expiration_date
    FROM discount_codes
    WHERE CURDATE() BETWEEN created_at AND expiration_date
    ORDER BY created_at ASC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overzicht Lopende Promoties</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<h1>Overzicht van Lopende Promoties</h1>
<table>
    <thead>
        <tr>
            <th>Kortingscode</th>
            <th>Korting (%)</th>
            <th>Startdatum</th>
            <th>Einddatum</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['code']); ?></td>
                    <td><?= htmlspecialchars($row['discount_percentage']); ?>%</td>
                    <td><?= htmlspecialchars($row['created_at']); ?></td>
                    <td><?= htmlspecialchars($row['expiration_date']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">Er zijn momenteel geen lopende promoties.</td>
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