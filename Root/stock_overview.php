<?php
include 'connect.php';
session_start();

$sql = "
SELECT 
  pv.artikelnr AS article_number,
  pv.variantnr AS variant_number,
  p.naam AS product_name,
  pv.kleur AS color,
  pv.maat AS size,
  pv.stock AS current_stock,
  CASE 
    WHEN pv.stock < 15 THEN 'Too Low'
    WHEN pv.stock < 30 THEN 'Moderate'
    ELSE 'Stock Sufficient'
  END AS stock_status
FROM 
  ProductVariant pv
JOIN 
  Products p ON pv.artikelnr = p.artikelnr
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Overview</title>
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
        .too-low {
            color: red;
        }
        .moderate {
            color: orange;
        }
        .stock-sufficient {
            color: green;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<h1>Stock Overview</h1>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Article Number</th>
                <th>Variant Number</th>
                <th>Product Name</th>
                <th>Color</th>
                <th>Size</th>
                <th>Current Stock</th>
                <th>Stock Status</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        $status_class = strtolower(str_replace(' ', '-', $row['stock_status']));
        echo "<tr>
                <td>{$row['article_number']}</td>
                <td>{$row['variant_number']}</td>
                <td>{$row['product_name']}</td>
                <td>{$row['color']}</td>
                <td>{$row['size']}</td>
                <td>{$row['current_stock']}</td>
                <td class='{$status_class}'>{$row['stock_status']}</td>
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