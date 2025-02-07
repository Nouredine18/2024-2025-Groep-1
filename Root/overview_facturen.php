<?php
// Verbinden met de database
include 'connect.php';
session_start();

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
