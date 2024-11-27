<?php
include 'connect.php';
session_start();

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login_register.php");
    exit();
}

// Fetch products from the cart table and calculate the difference between added to cart and purchased
$sql = "SELECT p.naam, p.prijs,p.popularity,
               COUNT(c.artikelnr) AS cart_count, 
               COALESCE(SUM(bp.artikelnr IS NOT NULL), 0) AS purchase_count,
               COUNT(c.artikelnr) - COALESCE(SUM(bp.artikelnr IS NOT NULL), 0) AS difference
        FROM Cart c
        LEFT JOIN BoughtProducts bp ON c.artikelnr = bp.artikelnr
        JOIN Products p ON c.artikelnr = p.artikelnr
        GROUP BY c.artikelnr, p.naam, p.prijs
        ORDER BY difference DESC";
$result = $conn->query($sql);

// Debugging: Check for SQL errors
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products in Carts</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Products in Carts</h1>
    <div class="cart-products">
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Times Added to Cart</th>
                        <th>Times Purchased</th>
                        <th>Difference</th>
                        <th>popularity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['naam']); ?></td>
                            <td>€<?php echo number_format($row['prijs'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['cart_count']); ?></td>
                            <td><?php echo htmlspecialchars($row['purchase_count']); ?></td>
                            <td><?php echo htmlspecialchars($row['difference']); ?></td>
                            <td><?php echo htmlspecialchars($row['popularity']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products found in carts.</p>
        <?php endif; ?>
    </div>
</body>
</html>