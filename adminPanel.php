<?php
include 'connect.php';
session_start();
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Initialize maintenance status to a default value (e.g., 0 for "off")
$maintenance_status = 0;

// Check if the .htaccess file indicates maintenance mode
$htaccess_path = '.htaccess';
if (file_exists($htaccess_path)) {
    $htaccess_content = file_get_contents($htaccess_path);
    if (strpos($htaccess_content, "RewriteRule ^(.*)$ /maintenance.html [R=503,L]") !== false) {
        $maintenance_status = 1;
    }
}

// Controleer of de gebruiker is ingelogd en admin is
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login_register.php"); // Redirect naar login als niet ingelogd
    exit();
}

// Handle Maintenance Mode Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_maintenance'])) {
    $maintenance_status = $_POST['maintenance_status']; // 1 for on, 0 for off

    if ($maintenance_status == 1) {
        // Add maintenance redirect rule
        $htaccess_content = "RewriteEngine On\n";
        $htaccess_content .= "RewriteCond %{REMOTE_ADDR} !^84\\.198\\.155\\.\n"; // Allow IP range 84.198.155.*
        $htaccess_content .= "RewriteCond %{REQUEST_URI} !^/maintenance.html\n"; // Skip maintenance page
        $htaccess_content .= "RewriteRule ^(.*)$ /maintenance.html [R=503,L]\n";
        $htaccess_content .= "ErrorDocument 503 /maintenance.html\n";
        $htaccess_content .= "Header always set Retry-After \"3600\"\n"; // Optional retry after 1 hour
    } else {
        // Disable maintenance mode by removing rules
        $htaccess_content = "# Maintenance mode disabled\n";
    }

    // Write to .htaccess file
    file_put_contents($htaccess_path, $htaccess_content);
}

// Fetch the products for managing stock
$sql = "SELECT * FROM ProductVariant";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Beheer Voorraad</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #e5e5e5;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        form {
            display: flex;
            align-items: center;
        }

        input[type="number"] {
            width: 60px;
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

        .social-media {
            margin-top: 10px;
        }

        .social-media h3 {
            margin-bottom: 10px;
        }

        .social-media ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-media ul li {
            display: inline;
        }

        .social-media ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 1.2em;
            transition: color 0.3s;
        }

        .social-media ul li a:hover {
            color: #007bff;
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
                    <li><a href="add_product.php">Voeg Product Toe</a></li>
                    <li><a href="manage_products.php">Beheer Producten</a></li>
                    <li><a href="active_deactivate_show_users.php">Users</a></li>
                    <li><a href="add_product.php">Product Toevoegen</a></li>
                    <li><a href="add_variant.php">Variant Toevoegen</a></li>
                    <li><a href="add_wijzig_categorie.php">categories</a></li>
                    <li><a href="add_discount_code.php">Kortingscode Toevoegen</a></li>
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo htmlspecialchars($_SESSION['voornaam']); ?></a></li>
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h1>Beheer Voorraad van Producten</h1>

    <?php if (isset($success_message)) : ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)) : ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="" method="post">
        <h2>Maintenance Mode</h2>
        <p>Status: <?php echo $maintenance_status ? 'AAN' : 'UIT'; ?></p>
        <input type="hidden" name="maintenance_status" value="<?php echo $maintenance_status ? 0 : 1; ?>">
        <input type="submit" name="toggle_maintenance" value="<?php echo $maintenance_status ? 'Schakel UIT' : 'Schakel AAN'; ?>">
    </form>

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
                                <input type="hidden" name="product_id" value="<?php echo $row['variantnr']; ?>"> <!-- Zorg ervoor dat dit de juiste variantnr is -->
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
</main>

<footer>
    <p>&copy; 2024 FootWear. Alle rechten voorbehouden.</p>
    <div class="social-media">
        <h3>Volg ons</h3>
        <ul>
            <li><a href="#">Facebook</a></li>
            <li><a href="#">Instagram</a></li>
            <li><a href="#">Twitter</a></li>
        </ul>
    </div>
</footer>
</body>
</html>
