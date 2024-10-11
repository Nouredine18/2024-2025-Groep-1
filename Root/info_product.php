<?php
include 'connect.php';
session_start();

// Haal het product-ID op uit de queryparameter
$artikelnr = isset($_GET['artikelnr']) ? intval($_GET['artikelnr']) : 0;

// Initialiseer variabelen
$product = null;
$colors = [];
$sizes = [];
$reviews = [];

// Haal productdetails op
$sql_product = "SELECT * FROM Products WHERE artikelnr = ?";
$stmt_product = $conn->prepare($sql_product);
$stmt_product->bind_param("i", $artikelnr);
$stmt_product->execute();
$result_product = $stmt_product->get_result();
if ($result_product->num_rows > 0) {
    $product = $result_product->fetch_assoc();
}

// Haal beschikbare kleuren en maten voor het product op
$sql_variants = "SELECT DISTINCT kleur, maat FROM ProductVariant WHERE artikelnr = ?";
$stmt_variants = $conn->prepare($sql_variants);
$stmt_variants->bind_param("i", $artikelnr);
$stmt_variants->execute();
$result_variants = $stmt_variants->get_result();
while ($row = $result_variants->fetch_assoc()) {
    $colors[] = $row['kleur'];
    $sizes[] = $row['maat'];
}

// Haal beoordelingen voor het product op
$sql_reviews = "SELECT r.review_text, r.rating, r.review_date, u.naam FROM Reviews r JOIN User u ON r.user_id = u.user_id WHERE r.artikelnr = ? ORDER BY r.review_date DESC";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $artikelnr);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();
while ($row = $result_reviews->fetch_assoc()) {
    $reviews[] = $row;
}

// Verwerk het indienen van een beoordeling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_text'], $_POST['rating'])) {
    $review_text = $_POST['review_text'];
    $rating = intval($_POST['rating']);
    $user_id = $_SESSION['user_id']; // Aannemende dat user_id in de sessie is opgeslagen

    $sql_insert_review = "INSERT INTO Reviews (user_id, artikelnr, review_text, rating) VALUES (?, ?, ?, ?)";
    $stmt_insert_review = $conn->prepare($sql_insert_review);
    $stmt_insert_review->bind_param("iisi", $user_id, $artikelnr, $review_text, $rating);
    $stmt_insert_review->execute();

    // Vernieuw de pagina om de nieuwe beoordeling te tonen
    header("Location: info_product.php?artikelnr=$artikelnr");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['naam'] ?? 'Product'); ?></title>
    <style>
        /* Algemene styling */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        h1, h2, h3, h4 {
            margin: 0;
        }

        .container {
            display: flex;
            flex-direction: column;
            margin: 50px auto;
            max-width: 1100px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-info {
            display: flex;
            flex-direction: row;
        }

        /* Galerij styling */
        .gallery {
            display: flex;
            flex-direction: column;
            margin-right: 20px;
        }

        .gallery img {
            width: 70px;
            height: 70px;
            margin-bottom: 15px;
            cursor: pointer;
            border-radius: 8px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .gallery img:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .main-image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .main-image img {
            max-width: 450px;
            max-height: 450px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .main-image img:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        /* Productdetails styling */
        .product-details {
            margin-left: 40px;
            flex: 1;
        }

        .product-details h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        .price {
            font-size: 24px;
            color: #e74c3c;
            margin: 20px 0;
            font-weight: bold;
        }

        .description {
            margin: 20px 0;
            font-size: 16px;
            color: #555;
        }

        .colors, .sizes {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .colors div, .sizes button {
            width: 50px;
            height: 50px;
            margin-right: 10px;
            border-radius: 50%;
            border: 2px solid #ddd;
            cursor: pointer;
            transition: border-color 0.3s ease, transform 0.3s ease;
        }

        .sizes button {
            border-radius: 5px;
            background-color: #f9f9f9;
            line-height: 50px;
            text-align: center;
        }

        .sizes button:hover,
        .colors div:hover {
            border-color: #333;
            transform: scale(1.1);
        }

        /* Winkelwagenknop */
        .cart-button {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-top: 20px;
        }

        .cart-button:hover {
            background-color: #333;
            transform: translateY(-2px);
        }

        .rating {
            display: inline-block;
            background-color: #f39c12;
            color: white;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 80%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .close {
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Beoordelingssectie */
        .reviews {
            margin-top: 40px;
        }

        .review {
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .review h4 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .review p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }

        .review .rating {
            background-color: #f39c12;
            color: white;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 12px;
            display: inline-block;
        }

        .review small {
            color: #999;
        }

        .review-form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .review-form textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .review-form select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .review-form button {
            background-color: #000;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .review-form button:hover {
            background-color: #333;
            transform: translateY(-2px);
        }

        /* Terugknop */
        .go-back-button {
            display: inline-block;
            background-color: #ccc;
            color: #333;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-top: 20px;
        }

        .go-back-button:hover {
            background-color: #bbb;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

<div class="container">
    <div class="product-info">
        <!-- Hoofdafbeelding -->
        <div class="main-image">
            <img src="directory/<?php echo !empty($product['directory']) ? htmlspecialchars($product['directory']) : 'default.jpg'; ?>" alt="Hoofdafbeelding product">
        </div>

        <!-- Productdetails -->
        <div class="product-details">
            <div class="rating">★ Goede beoordeling</div>
            <h1><?php echo htmlspecialchars($product['naam'] ?? 'Product'); ?></h1>
            <p class="price">€<?php echo htmlspecialchars($product['prijs'] ?? ''); ?></p>

            <!-- Kleuropties -->
            <div class="colors">
                <?php if (!empty($colors)): ?>
                    <?php foreach ($colors as $color): ?>
                        <div class="color-option" data-color="<?php echo htmlspecialchars($color); ?>" style="background-color: <?php echo htmlspecialchars($color); ?>;"></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Geen kleuren beschikbaar.</p>
                <?php endif; ?>
            </div>

            <!-- Maatopties -->
            <div class="sizes">
                <?php if (!empty($sizes)): ?>
                    <?php foreach ($sizes as $size): ?>
                        <button type="button"><?php echo htmlspecialchars($size); ?></button>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Geen maten beschikbaar.</p>
                <?php endif; ?>
            </div>

            <!-- Winkelwagenknop -->
            <form action="add_to_cart.php" method="post" id="add-to-cart-form">
                <input type="hidden" name="artikelnr" value="<?php echo htmlspecialchars($product['artikelnr'] ?? ''); ?>">
                <input type="hidden" name="kleur" id="selected-color" value="">
                <input type="hidden" name="maat" id="selected-size" value="">
                <input type="number" name="aantal" value="1" min="1" class="quantity-input" required>
                <button type="submit" class="cart-button">Voeg toe aan winkelmandje</button>
            </form>

            <!-- Terugknop -->
            <button class="go-back-button" onclick="window.location.href='index.php'">Ga terug</button>
        </div>
    </div>

    <!-- Productbeschrijving -->
    <div class="description">
        <h2>Beschrijving</h2>
        <p><?php echo htmlspecialchars($product['product_information'] ?? 'Geen beschrijving beschikbaar.'); ?></p>
    </div>

    <!-- Beoordelingssectie -->
    <div class="reviews">
        <h2>Beoordelingen</h2>
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <h4><?php echo htmlspecialchars($review['naam']); ?> <span class="rating">★ <?php echo htmlspecialchars($review['rating']); ?></span></h4>
                    <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                    <small><?php echo htmlspecialchars($review['review_date']); ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Geen beoordelingen. Wees de eerste om dit product te beoordelen!</p>
        <?php endif; ?>
    </div>

    <!-- Beoordelingsformulier -->
    <div class="review-form">
        <h2>Schrijf een beoordeling</h2>
        <form action="" method="post">
            <textarea name="review_text" placeholder="Schrijf hier uw beoordeling..." required></textarea>
            <select name="rating" required>
                <option value="">Selecteer beoordeling</option>
                <option value="1">1 - Slecht</option>
                <option value="2">2 - Matig</option>
                <option value="3">3 - Goed</option>
                <option value="4">4 - Zeer goed</option>
                <option value="5">5 - Uitstekend</option>
            </select>
            <button type="submit">Beoordeling indienen</button>
        </form>
    </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Selecteer zowel een kleur als een maat voordat u toevoegt aan het winkelmandje.</p>
    </div>
</div>

<script>
    // JavaScript om kleur- en maatselectie te verwerken
    document.querySelectorAll('.colors div').forEach(colorDiv => {
        colorDiv.addEventListener('click', function () {
            document.querySelectorAll('.colors div').forEach(div => div.style.border = '2px solid #ddd');
            this.style.border = '2px solid #333';
            document.getElementById('selected-color').value = this.dataset.color;
        });
    });

    document.querySelectorAll('.sizes button').forEach(sizeButton => {
        sizeButton.addEventListener('click', function () {
            document.querySelectorAll('.sizes button').forEach(button => button.style.border = '2px solid #ddd');
            this.style.border = '2px solid #333';
            document.getElementById('selected-size').value = this.textContent;
        });
    });

    // Zorg ervoor dat het formulier alleen wordt ingediend als een kleur en maat zijn geselecteerd
    document.querySelector('.cart-button').addEventListener('click', function (event) {
        const selectedColor = document.getElementById('selected-color').value;
        const selectedSize = document.getElementById('selected-size').value;
        if (!selectedColor || !selectedSize) {
            event.preventDefault();
            document.getElementById('myModal').style.display = "block";
        }
    });

    // Sluit de modal
    document.querySelector('.close').addEventListener('click', function () {
        document.getElementById('myModal').style.display = "none";
    });

    // Sluit de modal bij klikken buiten de modal
    window.addEventListener('click', function (event) {
        if (event.target == document.getElementById('myModal')) {
            document.getElementById('myModal').style.display = "none";
        }
    });
</script>

</body>

</html>