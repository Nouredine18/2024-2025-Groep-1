<?php
// Verbinden met de database
include 'connect.php';
session_start();

$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Functie om retourgegevens op te halen
function getReturnStats($status = null) {
    global $conn;

    $sql = "SELECT return_id, product_id, return_status, COUNT(*) AS return_count FROM returns";
    
    if ($status) {
        $sql .= " WHERE return_status = '$status'";
    }

    $sql .= " GROUP BY product_id"; // Groeperen per product

    $result = $conn->query($sql);
    $returnStats = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $returnStats[] = $row;
        }
    }

    return $returnStats;
}

// Update de retourstatus
if (isset($_POST['update_status'])) {
    $return_id = $_POST['return_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE returns SET return_status = ? WHERE return_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $return_id);

    if ($stmt->execute()) {
        $message = "Retourstatus is succesvol bijgewerkt!";
    } else {
        $message = "Er is een fout opgetreden bij het bijwerken van de retourstatus.";
    }

    $stmt->close();
}

// Haal de retourstatistieken op
$returnStats = getReturnStats();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retourbeheer</title>
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

<h1>Retourbeheer</h1>

<?php if (isset($message)): ?>
    <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Aantal Retouren</th>
            <th>Status</th>
            <th>Actie</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($returnStats) > 0): ?>
            <?php foreach ($returnStats as $stat): ?>
                <tr>
                    <td><?php echo htmlspecialchars($stat['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($stat['return_count']); ?></td>
                    <td><?php echo htmlspecialchars($stat['return_status']); ?></td>
                    <td>
                        <form action="manage_returns.php" method="POST">
                            <input type="hidden" name="return_id" value="<?php echo htmlspecialchars($stat['return_id']); ?>">
                            <select name="status">
                                <option value="pending" <?php if ($stat['return_status'] == 'pending') echo 'selected'; ?>>In behandeling</option>
                                <option value="approved" <?php if ($stat['return_status'] == 'approved') echo 'selected'; ?>>Goedgekeurd</option>
                                <option value="rejected" <?php if ($stat['return_status'] == 'rejected') echo 'selected'; ?>>Afgewezen</option>
                            </select>
                            <button type="submit" name="update_status">Wijzig Status</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">Geen retouren gevonden.</td>
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
