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
        // Fallback to 0 if the query fails
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

        /* Hero Section Styles */
        .hero {
            background-color: #007bff; /* Hero section background */
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

        /* Product Grid Styles */
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
            height: 420px; /* Adjusted height for uniformity */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Space out content evenly */
        }

        .product-item:hover {
            transform: scale(1.05); /* Scale effect on hover */
        }

        .product-item h3 {
            margin: 10px 0;
            font-size: 1.5em; /* Consistent title size */
        }

        .product-item p {
            margin: 5px 0;
        }

        .product-image {
            width: 100%; /* Make images responsive */
            height: 200px; /* Fixed height for uniformity */
            object-fit: cover; /* Ensure images cover the area without distortion */
            border-radius: 5px;
            margin-bottom: 10px;
        }

        /* Cart Item Styles */
        .cart-items {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Table Styles */
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

        /* Updated Button Styles */
        button, .button { /* Target both button and input submit types */
            background-color: blue; /* Green background */
            color: white; /* White text */
            border: none;
            padding: 10px 20px; /* Increased padding */
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px; /* Increased font size */
            transition: background-color 0.3s ease, transform 0.3s ease; /* Smooth transition for hover effects */
            margin-top: 10px; /* Spacing above the button */
        }

        button:hover, .button:hover {
            background-color: lightskyblue; /* Darker green on hover */
            transform: translateY(-2px); /* Slight lifting effect */
        }

        /* Updated Quantity Input Styles */
        .quantity-input {
            width: 60px; /* Fixed width */
            padding: 8px; /* More padding for better appearance */
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px; /* Spacing to the right */
            font-size: 16px; /* Match button font size */
            text-align: center; /* Center the quantity number */
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); /* Light shadow for effect */
        }

        .quantity-input:focus {
            outline: none;
            border-color: #007bff; /* Blue border when focused */
            box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5); /* Subtle blue glow on focus */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5em;
            }

            .container {
                width: 90%; /* Full width on smaller screens */
            }

            /* Ensure grid adapts to mobile screens */
            .product-grid {
                grid-template-columns: 1fr; /* Full-width on small screens */
            }
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
    <div class="hero">
        <h1>Welcome to FootWear | BE</h1>
        <p>Only the best of the best for our PHP lovers!</p>
    </div>

    <div class="product-grid">
        <?php
        // SQL-query to fetch products and their variants
        $sql = "SELECT p.artikelnr, p.naam, p.prijs, pv.variantnr, pv.kleur, pv.maat, pv.stock, p.directory 
                FROM Products p 
                JOIN ProductVariant pv ON p.artikelnr = pv.artikelnr";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):  // Check if query returned results
            while ($row = $result->fetch_assoc()): 
        ?>
            <div class="product-item"> <!-- Product item container -->
                <!-- Display product image -->
                <?php if (!empty($row['directory'])): ?>
                    <img src="directory/<?php echo $row['directory']; ?>" alt="<?php echo $row['naam']; ?>" class="product-image">
                <?php else: ?>
                    <img src="directory/default.jpg" alt="No image available" class="product-image">
                <?php endif; ?>
                
                <h3><?php echo $row['naam']; ?></h3> <!-- Display the product name -->
                <p>Price: €<?php echo $row['prijs']; ?></p> <!-- Display the product price -->
                <p>Color: <?php echo $row['kleur']; ?></p> <!-- Display the product color -->
                <p>Size: <?php echo $row['maat']; ?></p> <!-- Display the product size -->
                <p>In stock: <?php echo $row['stock']; ?></p> <!-- Display the stock quantity -->
                <form action="add_to_cart.php" method="post"> <!-- Form to add product to cart -->
                    <input type="hidden" name="artikelnr" value="<?php echo $row['artikelnr']; ?>"> <!-- Hidden input for article number -->
                    <input type="hidden" name="variantnr" value="<?php echo $row['variantnr']; ?>"> <!-- Hidden input for variant number -->
                    <label for="aantal">Quantity:</label> <!-- Label for quantity -->
                    <input type="number" name="aantal" value="1" min="1" max="<?php echo $row['stock']; ?>" class="quantity-input"> <!-- Input field for quantity -->
                    <input type="submit" value="Add to Cart" class="button"> <!-- Button to add to cart -->
                </form>
            </div>
        <?php 
            endwhile;
        else: 
            echo "<p>No products available at the moment.</p>";
        endif;
        ?>
    </div>
</main>

<footer>
    <p>&copy; 2024 FootWear. All rights reserved.</p>
</footer>
</body>
</html>
