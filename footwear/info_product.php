<?php
include 'connect.php';
session_start();

// Get the product ID from the query parameter
$artikelnr = isset($_GET['artikelnr']) ? intval($_GET['artikelnr']) : 0;

// Initialize variables
$product = null;
$colors = [];
$sizes = [];
$reviews = [];

// Fetch product details
$sql_product = "SELECT * FROM products WHERE artikelnr = ?";
$stmt_product = $conn->prepare($sql_product);
$stmt_product->bind_param("i", $artikelnr);
$stmt_product->execute();
$result_product = $stmt_product->get_result();
if ($result_product->num_rows > 0) {
    $product = $result_product->fetch_assoc();
}

// Fetch available colors and sizes for the product
$sql_variants = "SELECT DISTINCT kleur, maat FROM productvariant WHERE artikelnr = ?";
$stmt_variants = $conn->prepare($sql_variants);
$stmt_variants->bind_param("i", $artikelnr);
$stmt_variants->execute();
$result_variants = $stmt_variants->get_result();
while ($row = $result_variants->fetch_assoc()) {
    $colors[] = $row['kleur'];
    $sizes[] = $row['maat'];
}

// Fetch reviews for the product
$sql_reviews = "SELECT r.review_text, r.rating, r.review_date, u.naam FROM reviews r JOIN user u ON r.user_id = u.user_id WHERE r.artikelnr = ? ORDER BY r.review_date DESC";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $artikelnr);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();
while ($row = $result_reviews->fetch_assoc()) {
    $reviews[] = $row;
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_text'], $_POST['rating'])) {
    $review_text = $_POST['review_text'];
    $rating = intval($_POST['rating']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    $sql_insert_review = "INSERT INTO reviews (user_id, artikelnr, review_text, rating) VALUES (?, ?, ?, ?)";
    $stmt_insert_review = $conn->prepare($sql_insert_review);
    $stmt_insert_review->bind_param("iisi", $user_id, $artikelnr, $review_text, $rating);
    $stmt_insert_review->execute();

    // Refresh the page to show the new review
    header("Location: product.php?artikelnr=$artikelnr");
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
        /* Global Styling */
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

        /* Gallery Styling */
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

        /* Product Details Styling */
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

        /* Cart Button */
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

        /* Modal styles */
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

        /* Review Section */
        .reviews {
            margin-top: 40px;
        }

        .review {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .review h4 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .review p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .review .rating {
            background-color: #f39c12;
            color: white;
            padding: 2px 10px;
            border-radius: 50px;
            font-size: 12px;
        }

        .review-form {
            margin-top: 20px;
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
    </style>
</head>

<body>

<div class="container">
    <div class="product-info">
        <!-- Main Image -->
        <div class="main-image">
            <img src="directory/<?php echo !empty($product['directory']) ? htmlspecialchars($product['directory']) : 'default.jpg'; ?>" alt="Main Product Image">
        </div>

        <!-- Product Details -->
        <div class="product-details">
            <div class="rating">★ Goede beoordeling</div>
            <h1><?php echo htmlspecialchars($product['naam'] ?? 'Product'); ?></h1>
            <p class="price">€<?php echo htmlspecialchars($product['prijs'] ?? ''); ?></p>

            <!-- Color Options -->
            <div class="colors">
                <?php if (!empty($colors)): ?>
                    <?php foreach ($colors as $color): ?>
                        <div class="color-option" data-color="<?php echo htmlspecialchars($color); ?>" style="background-color: <?php echo htmlspecialchars($color); ?>;"></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No colors available.</p>
                <?php endif; ?>
            </div>

            <!-- Size Options -->
            <div class="sizes">
                <?php if (!empty($sizes)): ?>
                    <?php foreach ($sizes as $size): ?>
                        <button type="button"><?php echo htmlspecialchars($size); ?></button>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No sizes available.</p>
                <?php endif; ?>
            </div>

            <!-- Add to Cart Button -->
            <form action="add_to_cart.php" method="post">
                <input type="hidden" name="artikelnr" value="<?php echo htmlspecialchars($product['artikelnr'] ?? ''); ?>">
                <input type="hidden" name="kleur" id="selected-color" value="">
                <input type="hidden" name="maat" id="selected-size" value="">
                <input type="number" name="aantal" value="1" min="1" class="quantity-input" required>
                <button type="submit" class="cart-button">Voeg toe aan winkelmandje</button>
            </form>
        </div>
    </div>

    <!-- Product Description -->
    <div class="description">
        <h2>Description</h2>
        <p><?php echo htmlspecialchars($product['product_information'] ?? 'No description available.'); ?></p>
    </div>

    <!-- Reviews Section -->
    <div class="reviews">
        <h2>Reviews</h2>
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <h4><?php echo htmlspecialchars($review['naam']); ?> <span class="rating">★ <?php echo htmlspecialchars($review['rating']); ?></span></h4>
                    <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                    <small><?php echo htmlspecialchars($review['review_date']); ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet. Be the first to review this product!</p>
        <?php endif; ?>
    </div>

    <!-- Review Form -->
    <div class="review-form">
        <h2>Write a Review</h2>
        <form action="" method="post">
            <textarea name="review_text" placeholder="Write your review here..." required></textarea>
            <select name="rating" required>
                <option value="">Select Rating</option>
                <option value="1">1 - Poor</option>
                <option value="2">2 - Fair</option>
                <option value="3">3 - Good</option>
                <option value="4">4 - Very Good</option>
                <option value="5">5 - Excellent</option>
            </select>
            <button type="submit">Submit Review</button>
        </form>
    </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Please select both a color and a size before adding to cart.</p>
    </div>
</div>

<script>
    // JavaScript to handle color and size selection
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

    // Ensure the form is only submitted if a color and size are selected
    document.querySelector('form').addEventListener('submit', function (event) {
        const selectedColor = document.getElementById('selected-color').value;
        const selectedSize = document.getElementById('selected-size').value;
        if (!selectedColor || !selectedSize) {
            event.preventDefault();
            document.getElementById('myModal').style.display = "block";
        }
    });

    // Close the modal
    document.querySelector('.close').addEventListener('click', function () {
        document.getElementById('myModal').style.display = "none";
    });

    // Close the modal when clicking outside of it
    window.addEventListener('click', function (event) {
        if (event.target == document.getElementById('myModal')) {
            document.getElementById('myModal').style.display = "none";
        }
    });
</script>

</body>

</html>