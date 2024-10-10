<?php
include 'connect.php';
session_start();

// Get the product ID from the query parameter
$artikelnr = isset($_GET['artikelnr']) ? intval($_GET['artikelnr']) : 0;

// Fetch product details
$sql_product = "SELECT * FROM Products WHERE artikelnr = ?";
$stmt_product = $conn->prepare($sql_product);
$stmt_product->bind_param("i", $artikelnr);
$stmt_product->execute();
$result_product = $stmt_product->get_result();
$product = $result_product->fetch_assoc();

// Fetch available colors and sizes for the product
$sql_variants = "SELECT DISTINCT kleur, maat FROM ProductVariant WHERE artikelnr = ?";
$stmt_variants = $conn->prepare($sql_variants);
$stmt_variants->bind_param("i", $artikelnr);
$stmt_variants->execute();
$result_variants = $stmt_variants->get_result();

$colors = [];
$sizes = [];
while ($row = $result_variants->fetch_assoc()) {
    $colors[] = $row['kleur'];
    $sizes[] = $row['maat'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FootWear | Product Details</title>
    <style>
        /* General Styles */
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

        .product-details {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .product-image {
            width: 300px;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .product-info {
            text-align: center;
        }

        .product-info h2 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .product-info p {
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .product-options {
            text-align: center;
        }

        .product-options label {
            display: block;
            margin-bottom: 5px;
        }

        .product-options select {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        .product-options .button {
            background-color: blue;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .product-options .button:hover {
            background-color: lightskyblue;
            transform: translateY(-2px);
        }

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
    </style>
</head>
<body>
<header>
    <div class="logo">FootWear | BE</div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="cart.php">Cart (<?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0'; ?>)</a></li>
                <li><a href="logout.php">Logout</a></li>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="adminPanel.php">Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo $_SESSION['voornaam']; ?></a></li>
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <div class="product-details">
        <!-- Display product image -->
        <?php if (!empty($product['directory'])): ?>
            <img src="directory/<?php echo $product['directory']; ?>" alt="<?php echo $product['naam']; ?>" class="product-image">
        <?php else: ?>
            <img src="directory/default.jpg" alt="No image available" class="product-image">
        <?php endif; ?>

        <div class="product-info">
            <h2><?php echo $product['naam']; ?></h2>
            <p>Price: €<?php echo $product['prijs']; ?></p>
        </div>

        <div class="product-options">
            <form action="add_to_cart.php" method="post">
                <input type="hidden" name="artikelnr" value="<?php echo $product['artikelnr']; ?>">
                
                <label for="kleur">Color:</label>
                <select name="kleur" id="kleur">
                    <?php foreach ($colors as $color): ?>
                        <option value="<?php echo $color; ?>"><?php echo $color; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="maat">Beschikbaar Size:</label>
                <select name="maat" id="maat">
                    <?php foreach ($sizes as $size): ?>
                        <option value="<?php echo $size; ?>"><?php echo $size; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="aantal">Quantity:</label>
                <input type="number" name="aantal" value="1" min="1" class="quantity-input">
                
                <input type="submit" value="Add to Cart" class="button">
            </form>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2024 FootWear. All rights reserved.</p>
</footer>
</body>
</html>