<?php
include 'connect.php';
session_start();

$sql_most_sold = "
SELECT 
  p.artikelnr AS article_number,
  p.naam AS product_name,
  SUM(bp.aantal) AS total_sold
FROM 
  BoughtProducts bp
JOIN 
  Products p ON bp.artikelnr = p.artikelnr
GROUP BY 
  p.artikelnr, p.naam
ORDER BY 
  total_sold DESC
LIMIT 10
";

$sql_least_sold = "
SELECT 
  p.artikelnr AS article_number,
  p.naam AS product_name,
  SUM(bp.aantal) AS total_sold
FROM 
  BoughtProducts bp
JOIN 
  Products p ON bp.artikelnr = p.artikelnr
GROUP BY 
  p.artikelnr, p.naam
HAVING 
  SUM(bp.aantal) = 0
ORDER BY 
  total_sold ASC
LIMIT 10
";

$result_most_sold = $conn->query($sql_most_sold);
$result_least_sold = $conn->query($sql_least_sold);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sold Products Overview</title>
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

<h1>Most Sold Products</h1>

<?php
if ($result_most_sold->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Article Number</th>
                <th>Product Name</th>
                <th>Total Sold</th>
            </tr>";
    while($row = $result_most_sold->fetch_assoc()) {
        echo "<tr>
                <td>{$row['article_number']}</td>
                <td>{$row['product_name']}</td>
                <td>{$row['total_sold']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No results found.</p>";
}
?>

<h1>Least Sold Products</h1>

<?php
if ($result_least_sold->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Article Number</th>
                <th>Product Name</th>
                <th>Total Sold</th>
            </tr>";
    while($row = $result_least_sold->fetch_assoc()) {
        echo "<tr>
                <td>{$row['article_number']}</td>
                <td>{$row['product_name']}</td>
                <td>{$row['total_sold']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No results found.</p>";
}

$conn->close();
?>

</body>
</html>