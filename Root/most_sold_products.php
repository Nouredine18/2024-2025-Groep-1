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
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
        }
        .logo {
            font-size: 1.5em;
            font-weight: bold;
            text-align: center;
        }
        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        nav ul li {
            display: inline;
            position: relative;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            transition: background-color 0.3s;
        }
        nav ul li a:hover {
            background-color: #007bff;
        }
        nav ul li ul {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #333;
            padding: 0;
            list-style: none;
            min-width: 200px;
        }
        nav ul li:hover ul {
            display: block;
        }
        nav ul li ul li {
            display: block;
        }
        nav ul li ul li a {
            padding: 10px;
            display: block;
        }
        nav ul li ul li a:hover {
            background-color: #007bff;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">SchoenenWijns</div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">Logout</a></li>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li>
                        <a href="#">Admin Menu</a>
                        <ul>
                            <li><a href="add_product.php">Add Product</a></li>
                            <li><a href="manage_products.php">Manage Products</a></li>
                            <li><a href="overview_facturen.php">Manage Invoices</a></li>
                            <li><a href="active_deactivate_show_users.php">Users</a></li>
                            <li><a href="admin_chat.php">Admin Chat Board</a></li>
                            <li><a href="customer_support.php">Customer Support</a></li>
                            <li><a href="admin_payment_methods.php">Admin Payment Methods</a></li>
                            <li><a href="viewcount_cart.php">View Count Cart</a></li>
                            <li><a href="view_bestellingen.php">View Count Users</a></li>
                            <li><a href="add_brand.php">Add Brand</a></li>
                            <li><a href="stock_overview.php">Stock Overview</a></li>
                            <li><a href="most_sold_products.php">Sold Products Overview</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo htmlspecialchars($_SESSION['voornaam']); ?></a></li>
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

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
