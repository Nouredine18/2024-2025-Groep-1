<?php
include 'connect.php';
session_start();

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
    <title>FootWear | Home</title>
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
            height: 200px; /* Standaardhoogte voor de afbeeldingen */
            object-fit: cover; /* Zorgt ervoor dat de afbeeldingen goed passen */
        }

        .product-info {
            padding: 15px;
        }

        .product-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .product-category {
            color: #777;
            margin-bottom: 5px;
        }

        .product-price {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .product-card-container {
            position: relative;
        }

        .hero-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            margin-bottom: 20px;
            background-size: cover;
            background-position: center;
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
    </style>
</head>
<body>
<?php
include('header.php');
?>

<div class="content">
    <main>
        <div class="hero">
            <h1>Welkom bij FootWear | Adidas</h1>
            <p>Ontdek de nieuwste collectie van Adidas, ontworpen voor comfort en stijl.</p>
        </div>

        <!-- Hero Image -->
        <img src="images_main/adidas_hero.png" alt="Hero Image" class="hero-image">

        <div class="container">
            <?php
            // SQL-query om producten op te halen
            $sql = "SELECT artikelnr, naam, prijs, directory FROM Products";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0):  // Controleer of de query resultaten heeft opgeleverd
                while ($row = $result->fetch_assoc()): 
            ?>
                <div class="product-card-container"> <!-- Product item container -->
                    <div class="product-card">
                        <!-- Toon productafbeelding -->
                        <a href="info_product.php?artikelnr=<?php echo $row['artikelnr']; ?>">
                            <?php if (!empty($row['directory'])): ?>
                                <img src="directory/<?php echo $row['directory']; ?>" alt="<?php echo $row['naam']; ?>" class="product-image">
                            <?php else: ?>
                                <img src="directory/default.jpg" alt="Geen afbeelding beschikbaar" class="product-image">
                            <?php endif; ?>
                        </a>
                        
                        <div class="product-info">
                            <div class="product-title"><?php echo $row['naam']; ?></div> <!-- Toon de productnaam -->
                            <div class="product-category">Categorie</div> <!-- Voorbeeld categorie -->
                            <div class="product-price">€<?php echo $row['prijs']; ?></div> <!-- Toon de productprijs -->
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            else: 
                echo "<p>Momenteel geen producten beschikbaar.</p>";
            endif;
            ?>
        </div>
    </main>
</div>

<br>
<br>

<?php include('footer.php'); ?>

</body>
</html>