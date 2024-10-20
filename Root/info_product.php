<?php
include 'connect.php';
session_start();

$artikelnr = isset($_GET['artikelnr']) ? intval($_GET['artikelnr']) : 0;

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
$sql_reviews = "SELECT r.review_text, r.rating, r.review_date, u.naam FROM Reviews r JOIN User u ON r.user_id = u.user_id WHERE r.artikelnr = ? ORDER BY r.review_date DESC";
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
                </div>
            <?php endforeach; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <h3>Schrijf een beoordeling</h3>
                <form action="info_product.php?artikelnr=<?php echo htmlspecialchars($artikelnr); ?>" method="post" class="review-form">
                    <textarea name="review_text" placeholder="Schrijf je beoordeling hier..." required></textarea>
                    <select name="rating" required>
                        <option value="5">5 - Uitstekend</option>
                        <option value="4">4 - Goed</option>
                        <option value="3">3 - Gemiddeld</option>
                        <option value="2">2 - Slecht</option>
                        <option value="1">1 - Zeer slecht</option>
                    </select>
                    <button type="submit">Beoordeling indienen</button>
                </form>
            <?php else: ?>
                <p>Log in om een beoordeling te schrijven.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="imageModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="modalImage">
        <div id="caption"></div>
        <a class="prev" id="prev">&#10094;</a>
        <a class="next" id="next">&#10095;</a>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const firstColor = document.querySelector('.color');
            if (firstColor) {
                firstColor.click();
            }
        });

        document.querySelectorAll('.color').forEach(function (colorDiv) {
            colorDiv.addEventListener('click', function () {
                const selectedColor = this.dataset.color;
                document.getElementById('selected-color').value = selectedColor;
                document.getElementById('selected-color-display').textContent = selectedColor;
                fetchSizes(selectedColor);
                fetchImages(selectedColor);
            });
        });

        function fetchSizes(color) {
            const artikelnr = <?php echo $artikelnr; ?>;
            fetch(`fetch_sizes.php?artikelnr=${artikelnr}&color=${color}`)
                .then(response => response.json())
                .then(data => {
                    const sizesContainer = document.getElementById('sizes-container');
                    sizesContainer.innerHTML = '';
                    data.sizes.forEach(size => {
                        const button = document.createElement('button');
                        button.className = 'size';
                        button.dataset.size = size;
                        button.textContent = size;
                        button.addEventListener('click', function () {
                            const selectedSize = this.dataset.size;
                            document.getElementById('selected-size').value = selectedSize;
                            document.getElementById('selected-size-display').textContent = selectedSize;
                            document.getElementById('add-to-cart-button').disabled = false;
                            document.getElementById('order-button').disabled = false;
                        });
                        sizesContainer.appendChild(button);
                    });
                });
        }

        function fetchImages(color) {
            const artikelnr = <?php echo $artikelnr; ?>;
            fetch(`fetch_images.php?artikelnr=${artikelnr}&color=${color}`)
                .then(response => response.json())
                .then(data => {
                    const gallery = document.getElementById('gallery');
                    const mainImage = document.getElementById('main-image');
                    gallery.innerHTML = '';
                    data.images.forEach(image => {
                        const img = document.createElement('img');
                        img.src = image;
                        img.alt = 'Product afbeelding';
                        gallery.appendChild(img);
                    });
                    if (data.images.length > 0) {
                        mainImage.src = data.images[0];
                    } else {
                        mainImage.src = 'img/main_product_image.jpg';
                    }
                    // Add click event to images for modal
                    addImageClickEvent(data.images);
                });
        }

        function addImageClickEvent(images) {
            const modal = document.getElementById("imageModal");
            const modalImg = document.getElementById("modalImage");
            const captionText = document.getElementById("caption");
            let currentIndex = 0;

            document.querySelectorAll('.gallery img').forEach(function(img, index) {
                img.onclick = function() {
                    modal.style.display = "block";
                    modalImg.src = this.src;
                    captionText.innerHTML = this.alt;
                    currentIndex = index;
                }
            });

            const span = document.getElementsByClassName("close")[0];
            span.onclick = function() {
                modal.style.display = "none";
            }

            const prev = document.getElementById("prev");
            const next = document.getElementById("next");

            prev.onclick = function() {
                currentIndex = (currentIndex > 0) ? currentIndex - 1 : images.length - 1;
                modalImg.src = images[currentIndex];
            }

            next.onclick = function() {
                currentIndex = (currentIndex < images.length - 1) ? currentIndex + 1 : 0;
                modalImg.src = images[currentIndex];
            }
        }
    </script>

    <!-- Include the image_click.php file here -->
    <?php include 'image_click.php'; ?>

</body>
</html>