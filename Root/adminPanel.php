<?php
include 'connect.php';
session_start();

$maintenance_status = 0;

$htaccess_path = '.htaccess';
if (file_exists($htaccess_path)) {
    $htaccess_content = file_get_contents($htaccess_path);
    if (strpos($htaccess_content, "RewriteRule ^(.*)$ /maintenance.html [R=503,L]") !== false) {
        $maintenance_status = 1;
    }
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login_register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_maintenance'])) {
    $maintenance_status = $_POST['maintenance_status'];

    if ($maintenance_status == 1) {
        // Add maintenance redirect rule
        $htaccess_content = "RewriteEngine On\n";
        $htaccess_content .= "RewriteCond %{REMOTE_ADDR} !^84\\.198\\.155\\.\n"; // Allow IP range 84.198.155.*
        $htaccess_content .= "RewriteCond %{REQUEST_URI} !^/maintenance.html\n"; // Skip maintenance page
        $htaccess_content .= "RewriteRule ^(.*)$ /maintenance.html [R=503,L]\n";
        $htaccess_content .= "ErrorDocument 503 /maintenance.html\n";
        $htaccess_content .= "Header always set Retry-After \"3600\"\n"; // Optional retry after 1 hour
    } else {
        // Disable maintenance mode
        $htaccess_content = "# Maintenance mode disabled\n";
    }
        file_put_contents($htaccess_path, $htaccess_content);
}
    

// Voorraad update functionaliteit (bestaande code)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $product_id = $_POST['product_id']; // Dit is de variantnr
    $new_stock = $_POST['new_stock'];

    // Update de voorraad in de database
    $sql_update = "UPDATE ProductVariant SET stock = ? WHERE variantnr = ?";
    $stmt_update = $conn->prepare($sql_update);
    if ($stmt_update) {
        $stmt_update->bind_param("ii", $new_stock, $product_id);
        if ($stmt_update->execute()) {
            $success_message = "Voorraad succesvol bijgewerkt.";
        } else {
            $error_message = "Fout bij het bijwerken van de voorraad.";
        }
        $stmt_update->close();
    }
}

// Haal alle producten en hun varianten op
$sql = "SELECT pv.variantnr, p.naam, pv.kleur, pv.maat, pv.stock 
        FROM ProductVariant pv 
        JOIN Products p ON pv.artikelnr = p.artikelnr";
$result = $conn->query($sql);

// Kortingscodes tijdelijk opslaan (voor US004)
$discount_codes = [];

// Verwerk kortingscode toevoegingen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_discount_code'])) {
    $code = $_POST['code'];
    $discount_percentage = $_POST['discount_percentage'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Voeg de kortingscode toe aan de array
    $discount_codes[] = [
        'code' => $code,
        'discount_percentage' => $discount_percentage,
        'start_date' => $start_date,
        'end_date' => $end_date,
    ];

    $success_message = "Kortingscode succesvol toegevoegd.";
}

// Verwerk het toevoegen van nieuwe klanten (voor US003)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_customer'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Voeg klant toe (voor deze versie gebruiken we gewoon de sessie als voorbeeld)
    $_SESSION['customers'][] = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address
    ];

    $success_message = "Nieuwe klant succesvol toegevoegd.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Beheer</title>
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
            background-color: #f4f4f4;
        }
        .success, .error {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        form {
            display: flex;
            align-items: center;
        }
        input[type="number"], input[type="text"], input[type="email"], input[type="tel"], input[type="datetime-local"] {
            width: 100%;
            margin-right: 10px;
        }
        input[type="submit"] {
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">SchoenenWijns</div>
    <link rel="stylesheet" href="">
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

    <style>
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

        main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        footer p {
            margin: 0;
            padding: 10px 0;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            margin: 4px 2px;
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            transition: background-color 0.3s;
        }

        .btn-edit {
            background-color: #28a745;
        }

        .btn-edit:hover {
            background-color: #218838;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</header>

<main>
<form action="" method="post">
        <h2>Maintenance Mode</h2>
        <p>Status: <?php echo $maintenance_status ? 'AAN' : 'UIT'; ?></p>
        <input type="hidden" name="maintenance_status" value="<?php echo $maintenance_status ? 0 : 1; ?>">
        <input type="submit" name="toggle_maintenance" value="<?php echo $maintenance_status ? 'Schakel UIT' : 'Schakel AAN'; ?>">
    </form>
    
    <h1>Beheer Voorraad van Producten</h1>

    <?php if (isset($success_message)) : ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)) : ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Voorraadbeheer -->
    <table>
        <thead>
            <tr>
                <th>Naam</th>
                <th>Kleur</th>
                <th>Maat</th>
                <th>Voorraad</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['naam']); ?></td>
                        <td><?php echo htmlspecialchars($row['kleur']); ?></td>
                        <td><?php echo htmlspecialchars($row['maat']); ?></td>
                        <td><?php echo htmlspecialchars($row['stock']); ?></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $row['variantnr']; ?>">
                                <input type="number" name="new_stock" value="<?php echo $row['stock']; ?>" min="0">
                                <input type="submit" name="update_stock" value="Bijwerken">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Geen producten beschikbaar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Toevoegen van nieuwe klanten (US003) -->
    <h2>Nieuwe klant toevoegen</h2>
    <form action="" method="post">
        <label for="first_name">Voornaam:</label>
        <input type="text" name="first_name" required>
        
        <label for="last_name">Achternaam:</label>
        <input type="text" name="last_name" required>
        
        <label for="email">E-mail:</label>
        <input type="email" name="email" required>
        
        <label for="phone">Telefoonnummer:</label>
        <input type="tel" name="phone" required>
        
        <label for="address">Adres:</label>
        <input type="text" name="address" required>
        
        <input type="submit" name="add_customer" value="Voeg klant toe">
    </form>

    <!-- Kortingscodes beheren (US004) -->
    <h2>Kortingscodes beheren</h2>
    <form action="" method="post">
        <label for="code">Kortingscode:</label>
        <input type="text" name="code" required>
        
        <label for="discount_percentage">Kortingspercentage (%):</label>
        <input type="number" name="discount_percentage" required min="0" max="100">
        
        <label for="start_date">Startdatum:</label>
        <input type="datetime-local" name="start_date" required>
        
        <label for="end_date">Einddatum:</label>
        <input type="datetime-local" name="end_date" required>
        
        <input type="submit" name="add_discount_code" value="Voeg Kortingscode Toe">
    </form>

    <!-- Kortingscodes tonen -->
    <h3>Bestaande Kortingscodes</h3>
    <table>
        <thead>
            <tr>
                <th>Kortingscode</th>
                <th>Kortingspercentage</th>
                <th>Startdatum</th>
                <th>Einddatum</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($discount_codes)): ?>
                <?php foreach ($discount_codes as $discount_code): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($discount_code['code']); ?></td>
                        <td><?php echo htmlspecialchars($discount_code['discount_percentage']); ?>%</td>
                        <td><?php echo htmlspecialchars($discount_code['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($discount_code['end_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Geen kortingscodes beschikbaar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</main>

<footer>
    <p>&copy; 2024 FootWear. Alle rechten voorbehouden.</p>

    <div class="social-media">
        <h3>Volg ons op:</h3>
        <ul>
            <li><a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-f"></i> Facebook</a></li>
            <li><a href="https://www.twitter.com" target="_blank"><i class="fab fa-twitter"></i> Twitter</a></li>
            <li><a href="https://www.instagram.com" target="_blank"><i class="fab fa-instagram"></i> Instagram</a></li>
            <li><a href="https://www.linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i> LinkedIn</a></li>
        </ul>
    </div>
</footer>
</body>
</html>
