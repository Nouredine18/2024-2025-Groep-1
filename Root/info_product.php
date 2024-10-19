<?php
include 'connect.php';
session_start();

$artikelnr = isset($_GET['artikelnr']) ? intval($_GET['artikelnr']) : 0;

$product = null;
$colors = [];
$sizes = []; // Array om maten op te slaan
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

// Fetch sizes for the default color (first color)
if (!empty($colors)) {
    $default_color = $colors[0];
    $sql_sizes = "SELECT maat FROM ProductVariant WHERE artikelnr = ? AND kleur = ?";
    $stmt_sizes = $conn->prepare($sql_sizes);
    $stmt_sizes->bind_param("is", $artikelnr, $default_color);
    $stmt_sizes->execute();
    $result_sizes = $stmt_sizes->get_result();
    while ($row = $result_sizes->fetch_assoc()) {
        $sizes[] = $row['maat'];
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
    <style>
        .delivery-option {
            margin: 10px 0;
        }
        .size {
            display: inline-block;
            margin: 5px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
        }
        .size.selected {
            background-color: #007bff;
            color: white;
        }
        .submit-btn {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
    </style>
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
                    <?php echo nl2br(htmlspecialchars($product['beschrijving'] ?? 'Geen beschrijving beschikbaar.')); ?>
                </div>
                <div class="colors">
                    <?php foreach ($colors as $color): ?>
                        <div class="color" style="background-color: <?php echo htmlspecialchars($color); ?>" data-color="<?php echo htmlspecialchars($color); ?>"></div>
                    <?php endforeach; ?>
                </div>

                <div class="sizes" id="sizes-container">
                    <!-- Maatknoppen worden hier dynamisch geladen -->
                    <?php foreach ($sizes as $size): ?>
                        <button class="size" data-size="<?php echo htmlspecialchars($size); ?>"><?php echo htmlspecialchars($size); ?></button>
                    <?php endforeach; ?>
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
                        <button type="submit" class="cart-button">Voeg toe aan winkelmandje</button>
                    </form>
                <?php else: ?>
                    <form action="order_form.php" method="get">
                        <input type="hidden" name="artikelnr" value="<?php echo htmlspecialchars($product['artikelnr'] ?? ''); ?>">
                        <input type="hidden" name="kleur" id="selected-color" value="">
                        <input type="hidden" name="maat" id="selected-size" value="">
                        <input type="number" name="aantal" value="1" min="1" class="quantity-input" required>
                        <button type="submit" class="cart-button">Bestel</button>
                    </form>
                <?php endif; ?>

                <div class="rating">
                    Gemiddelde beoordeling: 4.5/5
                </div>

                <!-- Bezorgopties sectie -->
                <h2>Bezorgopties</h2>
                <div id="delivery-options">
                    <div class="delivery-option">
                        <input type="radio" id="option1" name="delivery" value="Standaard Bezorging">
                        <label for="option1">Standaard Bezorging - €5,00</label>
                    </div>
                    <div class="delivery-option">
                        <input type="radio" id="option2" name="delivery" value="Versnelde Bezorging">
                        <label for="option2">Versnelde Bezorging - €10,00</label>
                    </div>
                    <div class="delivery-option">
                        <input type="radio" id="option3" name="delivery" value="Afhalen in de Winkel">
                        <label for="option3">Afhalen in de Winkel - Gratis</label>
                    </div>
                </div>
                <button onclick="submitDeliveryOption()" class="submit-btn">Bezorgoptie bevestigen</button>

                <h2>Reviews</h2>
                <div class="reviews">
                    <?php foreach ($reviews as $review): ?>
                        <div class="review">
                            <strong><?php echo htmlspecialchars($review['naam']); ?></strong>
                            <span><?php echo htmlspecialchars($review['rating']); ?>/5</span>
                            <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                            <small><?php echo htmlspecialchars($review['review_date']); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h2>Schrijf een beoordeling</h2>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="" method="post">
                        <textarea name="review_text" rows="4" placeholder="Schrijf je beoordeling hier..." required></textarea>
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

            document.querySelectorAll('.size').forEach(function (sizeButton) {
                sizeButton.addEventListener('click', function () {
                    document.querySelectorAll('.size').forEach(function (btn) {
                        btn.classList.remove('selected');
                    });
                    this.classList.add('selected');
                    const selectedSize = this.dataset.size;
                    document.getElementById('selected-size').value = selectedSize;
                    document.getElementById('selected-size-display').textContent = selectedSize;
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

            function submitDeliveryOption() {
                const selectedDelivery = document.querySelector('input[name="delivery"]:checked');
                if (selectedDelivery) {
                    alert(`Bezorgoptie bevestigd: ${selectedDelivery.value}`);
                    // Hier kun je eventueel verder gaan met de verwerking
                } else {
                    alert('Kies een bezorgoptie.');
                }
            }
        </script>

        <!-- Include the image_click.php file here -->
        <?php include 'image_click.php'; ?>

</body>
</html>
