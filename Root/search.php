<?php
include 'connect.php';
session_start();

// Check if the search query is set
if (isset($_POST['zoekresultaat'])) {
    $zoekresultaat = $_POST['zoekresultaat'];

    // Prepare the SQL query
    $sql = "SELECT artikelnr, naam, prijs, directory FROM Products WHERE naam LIKE ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $zoekterm = "%" . $zoekresultaat . "%";
        $stmt->bind_param("s", $zoekterm);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // Handle SQL preparation error
        die("SQL preparation error: " . $conn->error);
    }
} else {
    // Handle case where search query is not set
    die("No search query provided.");
}

// Haal de cart count uit de database
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // SQL-query om het aantal artikelen in de winkelwagen op te halen
    $sql_count = "SELECT SUM(aantal) AS total_items FROM Cart WHERE user_id = ?";
    $stmt_count = $conn->prepare($sql_count);
    if ($stmt_count) {
        $stmt_count->bind_param("i", $user_id);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $row_count = $result_count->fetch_assoc();

        // Haal het aantal artikelen op uit de database, of zet het op 0 als er geen artikelen zijn
        $_SESSION['cart_count'] = $row_count['total_items'] ? $row_count['total_items'] : 0;
    } else {
        // Terugvallen op 0 als de query mislukt
        $_SESSION['cart_count'] = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoekresultaten | FootWear</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Oswald', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
        }
        .hero {
            text-align: center;
            margin-bottom: 20px;
        }
        .hero h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1.2em;
            color: #555;
        }
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .product-card {
            background-color: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-info {
            padding: 15px;
        }

        .product-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
<?php include('header.php'); ?>

<main class="content">
    <div class="hero">
        <h1>Zoekresultaten</h1>
        <p>Resultaten voor "<?php echo htmlspecialchars($zoekresultaat); ?>"</p>
    </div>

    <div class="container">
        <?php if (isset($result) && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-card-container">
                    <div class="product-card">
                        <a href="info_product.php?artikelnr=<?php echo $row['artikelnr']; ?>">
                            <?php if (!empty($row['directory'])): ?>
                                <img src="directory/<?php echo $row['directory']; ?>" alt="<?php echo $row['naam']; ?>" class="product-image">
                            <?php else: ?>
                                <img src="directory/default.jpg" alt="Geen afbeelding beschikbaar" class="product-image">
                            <?php endif; ?>
                        </a>
                        
                        <div class="product-info">
                            <div class="product-title"><?php echo $row['naam']; ?></div>
                            <div class="product-category">Categorie</div> <!-- Voorbeeld categorie -->
                            <div class="product-price">€<?php echo $row['prijs']; ?></div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Geen producten gevonden voor "<?php echo htmlspecialchars($zoekresultaat); ?>"</p>
        <?php endif; ?>
    </div>
</main>

<?php include('footer.php'); ?>
</body>
</html>
