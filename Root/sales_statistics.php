<?php
session_start();

// Databaseverbinding
include 'connect.php';

// Functie om query uit te voeren
function executeQuery($conn, $sql) {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}


// Haal statistieken op
$totalRevenueResult = executeQuery($conn, 
    "SELECT SUM(bp.aantal * p.prijs) AS total_revenue
     FROM BoughtProducts bp
     JOIN Products p ON bp.artikelnr = p.artikelnr
     WHERE bp.status != 'geannuleerd'");

$totalRevenue = $totalRevenueResult->fetch_assoc()['total_revenue'];

$productSalesResult = executeQuery($conn, 
    "SELECT p.naam, SUM(bp.aantal) AS units, SUM(bp.aantal * p.prijs) AS revenue
     FROM BoughtProducts bp
     JOIN Products p ON bp.artikelnr = p.artikelnr
     WHERE bp.status != 'geannuleerd'
     GROUP BY p.naam");

$productSales = [];
while ($row = $productSalesResult->fetch_assoc()) {
    $productSales[] = $row;
}

// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verkoopstatistieken</title>
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
        .total-revenue {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        /* Navbar Styles */
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            margin-bottom: 20px;
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
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
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
                            <li><a href="adminPanel.php">Panel</a></li>
                            <li><a href="add_product.php">Add Product</a></li>
                            <li><a href="manage_products.php">Manage Products</a></li>
                            <li><a href="overview_facturen.php">Manage Invoices</a></li>
                            <li><a href="active_discounts.php">Active discounts</a></li>
                            <li><a href="active_deactivate_show_users.php">Users</a></li>
                            <li><a href="admin_chat.php">Admin Chat Board</a></li>
                            <li><a href="customer_support.php">Customer Support</a></li>
                            <li><a href="admin_payment_methods.php">Admin Payment Methods</a></li>
                            <li><a href="viewcount_cart.php">View Count Cart</a></li>
                            <li><a href="view_bestellingen.php">View Count Users</a></li>
                            <li><a href="overview_discounts.php">View Discounts</a></li>
                            <li><a href="add_brand.php">Add Brand</a></li>
                            <li><a href="stock_overview.php">Stock Overview</a></li>
                            <li><a href="most_sold_products.php">Most Sold Products</a></li>
                            <li><a href="customer_satisfaction.php">Customer Satisfaction</a></li>
                            <li><a href="customer_feedback.php">Customer Feedback</a></li>
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

<h1>Verkoopstatistieken</h1>
<div class="total-revenue">Totale omzet: €<?php echo number_format($totalRevenue, 2, ',', '.'); ?></div>
<table>
    <thead>
        <tr>
            <th>Productnaam</th>
            <th>Aantal verkocht</th>
            <th>Omzet (€)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($productSales as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['naam']); ?></td>
                <td><?php echo htmlspecialchars($product['units']); ?></td>
                <td>€<?php echo number_format($product['revenue'], 2, ',', '.'); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="adminPanel.php" class="back-button">Terug naar Admin Panel</a>

</body>
</html>