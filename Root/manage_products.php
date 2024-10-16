<?php
include 'connect.php';
session_start();

// Controleer of de gebruiker is ingelogd als admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Verwerk verwijderverzoek
if (isset($_GET['delete'])) {
    $artikelnr = intval($_GET['delete']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Verwijder uit Cart
        $sql_delete = "DELETE FROM Cart WHERE artikelnr = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $artikelnr);
        if (!$stmt_delete->execute()) {
            throw new Exception($stmt_delete->error);
        }

        // Verwijder uit ProductVariant
        $sql_delete1 = "DELETE FROM ProductVariant WHERE artikelnr = ?";
        $stmt_delete1 = $conn->prepare($sql_delete1);
        $stmt_delete1->bind_param("i", $artikelnr);
        if (!$stmt_delete1->execute()) {
            throw new Exception($stmt_delete1->error);
        }

        // Verwijder uit Product
        $sql_delete2 = "DELETE FROM Products WHERE artikelnr = ?";
        $stmt_delete2 = $conn->prepare($sql_delete2);
        $stmt_delete2->bind_param("i", $artikelnr);
        if (!$stmt_delete2->execute()) {
            throw new Exception($stmt_delete2->error);
        }

        // Commit transaction
        $conn->commit();
        header("Location: manage_products.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Er is een fout opgetreden: " . $e->getMessage();
    }
}

// Initiële productquery
$sql_products = "SELECT * FROM Products";

// Controleer of er een zoekopdracht is
if (isset($_POST["indienen"])) {
    $gezochte_zoekresultaat = $_POST["zoekopdracht"];
    $zoekresultaat = "%" . $gezochte_zoekresultaat . "%";
    $sql_products = "SELECT * FROM Products WHERE naam LIKE ?";
}

// Haal alle producten op
$stmt_products = $conn->prepare($sql_products);
if (isset($_POST["indienen"])) {
    $stmt_products->bind_param("s", $zoekresultaat);
}
$stmt_products->execute();
$result_products = $stmt_products->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>SchoenenWijns | Beheer Producten</title>
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
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo htmlspecialchars($_SESSION['voornaam']); ?></a></li>
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h1>Beheer Producten</h1>
    <p>Hier kunt u producten bewerken, zoeken of verwijderen.</p>

    <div class="search-form">
        <form action="manage_products.php" method="post">
            <label for="zoekopdracht">Zoek Artikelen</label>
            <input type="text" name="zoekopdracht" placeholder="geef naam artikel" required>
            <input type="submit" name="indienen" value="Zoek">
        </form>
    </div>

    <div class="products">
        <h2>Productenlijst</h2>
        <table>
            <tr>
                <th>Artikelnummer</th>
                <th>Naam</th>
                <th>Prijs</th>
                <th>Type Schoen</th>
                <th>Acties</th>
            </tr>
            <?php while ($product = $result_products->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['artikelnr']); ?></td>
                    <td><?php echo htmlspecialchars($product['naam']); ?></td>
                    <td><?php echo htmlspecialchars($product['prijs']); ?></td>
                    <td><?php echo htmlspecialchars($product['type_of_shoe']); ?></td>
                    <td>
                        <a href="edit_product.php?artikelnr=<?php echo $product['artikelnr']; ?>" class="btn btn-edit">Bewerken</a>
                        <a href="manage_products.php?delete=<?php echo $product['artikelnr']; ?>" class="btn btn-delete" onclick="return confirm('Weet je zeker dat je dit product wilt verwijderen?');">Verwijderen</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>

<footer>
    <p>© 2024 SchoenenWijns. Alle rechten voorbehouden.</p>
</footer>
</body>
</html>