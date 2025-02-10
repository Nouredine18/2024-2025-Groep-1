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