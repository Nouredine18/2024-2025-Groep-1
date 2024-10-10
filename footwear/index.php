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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FootWear | Home</title>
    <style>
        /* Algemene stijlen */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #343a40;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            padding: 0;
        }

        nav ul li {
            display: inline;
            margin-left: 20px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
        }

        main {
            padding: 40px 20px;
        }

        /* Hero sectie stijlen */
        .hero {
            background-color: #007bff; /* Achtergrond van de hero sectie */
            color: white;
            text-align: center;
            padding: 100px 20px;
        }

        .hero h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2em;
            margin-bottom: 30px;
        }

        /* Product grid stijlen */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .product-item {
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            height: 420px; /* Aangepaste hoogte voor uniformiteit */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Verdeel de inhoud gelijkmatig */
        }

        .product-item:hover {
            transform: scale(1.05); /* Schaal effect bij hover */
        }

        .product-item h3 {
            margin: 10px 0;
            font-size: 1.5em; /* Consistente titelgrootte */
        }

        .product-item p {
            margin: 5px 0;
        }

        .product-image {
            width: 100%; /* Maak afbeeldingen responsief */
            height: 200px; /* Vaste hoogte voor uniformiteit */
            object-fit: cover; /* Zorg ervoor dat afbeeldingen het gebied zonder vervorming bedekken */
            border-radius: 5px;
            margin-bottom: 10px;
        }

        /* Winkelwagen item stijlen */
        .cart-items {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Tabel stijlen */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #343a40;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Bijgewerkte knopstijlen */
        button, .button { /* Richt zowel op button als input submit types */
            background-color: blue; /* Blauwe achtergrond */
            color: white; /* Witte tekst */
            border: none;
            padding: 10px 20px; /* Verhoogde padding */
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px; /* Verhoogde lettergrootte */
            transition: background-color 0.3s ease, transform 0.3s ease; /* Soepele overgang voor hover effecten */
            margin-top: 10px; /* Ruimte boven de knop */
        }

        button:hover, .button:hover {
            background-color: lightskyblue; /* Donkerder blauw bij hover */
            transform: translateY(-2px); /* Licht liftend effect */
        }

        /* Bijgewerkte hoeveelheid invoerstijlen */
        .quantity-input {
            width: 60px; /* Vaste breedte */
            padding: 8px; /* Meer padding voor betere uitstraling */
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px; /* Ruimte aan de rechterkant */
            font-size: 16px; /* Match knop lettergrootte */
            text-align: center; /* Centreer het aantal nummer */
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); /* Lichte schaduw voor effect */
        }

        .quantity-input:focus {
            outline: none;
            border-color: #007bff; /* Blauwe rand bij focus */
            box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5); /* Subtiele blauwe gloed bij focus */
        }

        .total {
            font-weight: bold;
            font-size: 18px;
        }

        .empty-cart {
            text-align: center;
            font-size: 18px;
            color: #888;
        }

        /* Footer */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        footer p {
            margin: 0;
        }

        /* Responsief ontwerp */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5em;
            }

            .container {
                width: 90%; /* Volledige breedte op kleinere schermen */
            }

            /* Zorg ervoor dat het raster zich aanpast aan mobiele schermen */
            .product-grid {
                grid-template-columns: 1fr; /* Volledige breedte op kleine schermen */
            }
        }

            .social-media ul {
                list-style: none; /* Verwijder bullets */
                padding: 0;
                margin: 0; /* Verwijder eventuele marges */
                display: flex; /* Maak items horizontaal uitlijnen */
                justify-content: center; /* Centreer de knoppen horizontaal */
                gap: 15px; /* Ruimte tussen de knoppen */
            }

            .social-media ul li {
                display: inline-block;
            }

            .social-media ul li a {
                padding: 10px 20px;
                background-color: #007bff; /* Blauwe kleur */
                color: white; /* Witte tekst */
                text-decoration: none; /* Verwijder onderstreping */
                border-radius: 5px;
                font-weight: bold;
                transition: background-color 0.3s ease;
                display: flex; /* Maak de anker een flex-container */
                align-items: center; /* Centreer items verticaal */
                gap: 8px; /* Ruimte tussen pictogram en tekst */
            }

            .social-media ul li a:hover {
                background-color: #0056b3; /* Donkerder blauw bij hover */
            }

    </style>
</head>
<body>
<header>
    <div class="logo">FootWear | BE</div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="cart.php">Winkelwagen (<?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0'; ?>)</a></li>
                <li><a href="logout.php">Uitloggen</a></li>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="adminPanel.php">Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="#">Welkom, <?php echo $_SESSION['voornaam']; ?></a></li>
            <?php else: ?>
                <li><a href="login_register.php">Inloggen/Registreren</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <div class="hero">
        <h1>Welkom bij FootWear | BE</h1>
        <p>Alleen het beste van het beste voor onze PHP-liefhebbers!</p>
    </div>

    <div class="product-grid">
        <?php
        // SQL-query om producten op te halen
        $sql = "SELECT artikelnr, naam, prijs, directory FROM Products";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):  // Controleer of de query resultaten heeft opgeleverd
            while ($row = $result->fetch_assoc()): 
        ?>
            <div class="product-item"> <!-- Product item container -->
                <!-- Toon productafbeelding -->
                <?php if (!empty($row['directory'])): ?>
                    <img src="directory/<?php echo $row['directory']; ?>" alt="<?php echo $row['naam']; ?>" class="product-image">
                <?php else: ?>
                    <img src="directory/default.jpg" alt="Geen afbeelding beschikbaar" class="product-image">
                <?php endif; ?>
                
                <h3><?php echo $row['naam']; ?></h3> <!-- Toon de productnaam -->
                <p>Prijs: €<?php echo $row['prijs']; ?></p> <!-- Toon de productprijs -->
                <form action="add_to_cart.php" method="post"> <!-- Formulier om product aan winkelwagen toe te voegen -->
                    <input type="hidden" name="artikelnr" value="<?php echo $row['artikelnr']; ?>"> <!-- Verborgen invoer voor artikelnummer -->
                </form>
                <a href="info_product.php?artikelnr=<?php echo $row['artikelnr']; ?>" class="button">Meer zien</a> <!-- Meer zien knop -->
            </div>
        <?php 
            endwhile;
        else: 
            echo "<p>Momenteel geen producten beschikbaar.</p>";
        endif;
        ?>
    </div>
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