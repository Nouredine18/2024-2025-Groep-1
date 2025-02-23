<?php
include 'connect.php';
session_start();

$artikelnr = isset($_GET['artikelnr']) ? intval($_GET['artikelnr']) : 0;
$sql = "UPDATE Products SET popularity = popularity + 1 WHERE artikelnr = $artikelnr";
$conn->query($sql);

$product = null;
$colors = [];
$reviews = [];
$images = [];

// Fetch product details
$sql_product = "SELECT * FROM Products WHERE artikelnr = ?";
$stmt_product = $conn->prepare($sql_product);
$stmt_product->bind_param("i", $artikelnr);
$stmt_product->execute();
$result_product = $stmt_product->get_result();
if ($result_product->num_rows > 0) {
    $product = $result_product->fetch_assoc();
}

// Fetch available colors
$sql_colors = "SELECT DISTINCT kleur FROM ProductVariant WHERE artikelnr = ?";
$stmt_colors = $conn->prepare($sql_colors);
$stmt_colors->bind_param("i", $artikelnr);
$stmt_colors->execute();
$result_colors = $stmt_colors->get_result();
while ($row = $result_colors->fetch_assoc()) {
    $colors[] = $row['kleur'];
}

// Fetch reviews
$sql_reviews = "SELECT r.review_id, r.review_text, r.rating, r.review_date, u.naam FROM Reviews r JOIN User u ON r.user_id = u.user_id WHERE r.artikelnr = ? ORDER BY r.review_date DESC";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $artikelnr);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();
while ($row = $result_reviews->fetch_assoc()) {
    $reviews[] = $row;
}

// Fetch images for the default color (first color)
if (!empty($colors)) {
    $default_color = $colors[0];
    $sql_images = "SELECT variant_directory FROM ProductVariant WHERE artikelnr = ? AND kleur = ?";
    $stmt_images = $conn->prepare($sql_images);
    $stmt_images->bind_param("is", $artikelnr, $default_color);
    $stmt_images->execute();
    $result_images = $stmt_images->get_result();
    if ($result_images->num_rows > 0) {
        $row = $result_images->fetch_assoc();
        $images = explode(" | ", $row['variant_directory']);
    }
}

// Handle review deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review'])) {
    if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin') {
        $review_id = $_POST['review_id'];
        $sql_delete_review = "DELETE FROM Reviews WHERE review_id = ?";
        $stmt_delete_review = $conn->prepare($sql_delete_review);
        $stmt_delete_review->bind_param("i", $review_id);
        $stmt_delete_review->execute();
        header("Location: info_product.php?artikelnr=$artikelnr");
        exit();
    } else {
        echo "You do not have permission to delete reviews.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_text'], $_POST['rating'])) {
    $review_text = $_POST['review_text'];
    $rating = intval($_POST['rating']);
    $user_id = $_SESSION['user_id'];

    $sql_insert_review = "INSERT INTO Reviews (user_id, artikelnr, review_text, rating) VALUES (?, ?, ?, ?)";
    $stmt_insert_review = $conn->prepare($sql_insert_review);
    $stmt_insert_review->bind_param("iisi", $user_id, $artikelnr, $review_text, $rating);
    $stmt_insert_review->execute();

    header("Location: info_product.php?artikelnr=$artikelnr");
    exit;
}

// Handle adding to wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_wishlist'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Check if the product is already in the wishlist
        $sql_check = "SELECT * FROM WishList WHERE user_id = ? AND artikelnr = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ii", $user_id, $artikelnr);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows == 0) {
            // Add product to wishlist
            $sql_insert = "INSERT INTO WishList (user_id, artikelnr) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ii", $user_id, $artikelnr);
            $stmt_insert->execute();
            echo "<p>Product toegevoegd aan je verlanglijst!</p>";
        } else {
            echo "<p>Dit product staat al in je verlanglijst.</p>";
        }
    } else {
        echo "<p>Je moet ingelogd zijn om een product aan je verlanglijst toe te voegen.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['naam'] ?? 'Product'); ?></title>
    <link rel="stylesheet" href="css/cart.css">
</head>

<body>
    <div class="container">
        <div class="product-info">
            <div class="gallery" id="gallery">
                <?php foreach ($images as $image): ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="Product afbeelding">
                <?php endforeach; ?>
            </div>
            <div class="main-image">
                <img id="main-image" src="<?php echo htmlspecialchars($images[0] ?? 'img/main_product_image.jpg'); ?>" alt="Hoofd product afbeelding">
            </div>
            <div class="product-details">
                <h1><?php echo htmlspecialchars($product['naam'] ?? 'Productnaam'); ?></h1>
                <div class="price">&euro;<?php echo number_format($product['prijs'] ?? 0, 2, ',', '.'); ?></div>
                <div class="description">
                    <h2>Productinformatie</h2>
                    <?php echo nl2br(htmlspecialchars($product['product_information'] ?? 'Geen beschrijving beschikbaar.')); ?>
                </div>
                <div class="colors">
                    <?php foreach ($colors as $color): ?>
                        <div class="color" style="background-color: <?php echo htmlspecialchars($color); ?>" data-color="<?php echo htmlspecialchars($color); ?>"></div>
                    <?php endforeach; ?>
                </div>
                <div class="sizes" id="sizes-container">
                    <!-- Maatopties worden hier dynamisch geladen -->
                </div>

                <div class="selected-options">
                    <p>Geselecteerde kleur: <span id="selected-color-display">Geen</span></p>
                    <p>Geselecteerde maat: <span id="selected-size-display">Geen</span></p>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="add_to_cart.php" method="post" id="add-to-cart-form">
                        <input type="hidden" name="artikelnr" value="<?php echo htmlspecialchars($product['artikelnr'] ?? ''); ?>">
                        <input type="hidden" name="kleur" id="selected-color" value="">
                        <input type="hidden" name="maat" id="selected-size" value="">
                        <input type="number" name="aantal" value="1" min="1" class="quantity-input" required>
                        <button type="submit" class="cart-button" id="add-to-cart-button" disabled>Voeg toe aan winkelmandje</button>
                    </form>
                <?php else: ?>
                    <form action="betaling.php" method="get">
                        <input type="hidden" name="artikelnr" value="<?php echo htmlspecialchars($product['artikelnr'] ?? ''); ?>">
                        <input type="hidden" name="kleur" id="selected-color" value="">
                        <input type="hidden" name="maat" id="selected-size" value="">
                        <input type="number" name="aantal" value="1" min="1" class="quantity-input" required>
                        <button type="submit" class="cart-button" id="order-button" disabled>Bestel</button>
                    </form>
                <?php endif; ?>

                <div class="rating">
                    Gemiddelde beoordeling: 4.5/5
                </div>
                
                <!-- Invoerveld voor kortingscode -->
                <form action="apply_discount.php" method="post">
                    <input type="text" name="discount_code" placeholder="Voer kortingscode in" class="discount-input">
                    <button type="submit" class="discount-button">Toepassen</button>
                </form>

                <?php
                // Melding weergeven als de kortingscode succesvol is toegepast
                if (isset($_SESSION['discount_message'])) {
                    echo "<p style='color: green;'>" . $_SESSION['discount_message'] . "</p>";
                    unset($_SESSION['discount_message']);
                }

                // Melding weergeven als er een fout is met de kortingscode
                if (isset($_SESSION['discount_error'])) {
                    echo "<p style='color: red;'>" . $_SESSION['discount_error'] . "</p>";
                    unset($_SESSION['discount_error']);
                }
                ?>



                <!-- Voeg de verlanglijst knop toe -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="info_product.php?artikelnr=<?php echo htmlspecialchars($artikelnr); ?>" method="post" class="wishlist-form">
                        <button type="submit" name="add_to_wishlist" class="wishlist-button">Voeg toe aan verlanglijst</button>
                    </form>
                <?php else: ?>
                    <p>Log in om dit product aan je verlanglijst toe te voegen.</p>
                <?php endif; ?>

                <!-- Tekstvak voor een persoonlijk bericht -->
                <form action="add_personal_message.php" method="post">
                    <input type="hidden" name="artikelnr" value="<?php echo htmlspecialchars($product['artikelnr'] ?? ''); ?>">
                    <textarea name="personal_message" placeholder="Voeg een persoonlijk bericht toe..." rows="4" cols="50"></textarea>
                    <button type="submit" class="message-button">Opslaan</button>
                </form>

                <button onclick="window.location.href='index.php'" class="back-button">Ga Terug</button>
            </div>
        </div>

        <div class="reviews">
            <h2>Beoordelingen</h2>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <h4><?php echo htmlspecialchars($review['naam']); ?></h4>
                    <div class="rating">Rating: <?php echo htmlspecialchars($review['rating']); ?>/5</div>
                    <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                    <small>Beoordeeld op <?php echo htmlspecialchars($review['review_date']); ?></small>
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                        <form method="post" action="info_product.php?artikelnr=<?php echo $artikelnr; ?>">
                            <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                            <button type="submit" name="delete_review">Verwijder review</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <!-- Voeg een nieuwe review toe -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" action="info_product.php?artikelnr=<?php echo $artikelnr; ?>">
                    <textarea name="review_text" placeholder="Schrijf een beoordeling..." required></textarea>
                    <select name="rating" required>
                        <option value="1">1 Ster</option>
                        <option value="2">2 Sterren</option>
                        <option value="3">3 Sterren</option>
                        <option value="4">4 Sterren</option>
                        <option value="5">5 Sterren</option>
                    </select>
                    <button type="submit">Plaats beoordeling</button>
                </form>
            <?php else: ?>
                <p>Log in om een beoordeling achter te laten.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
