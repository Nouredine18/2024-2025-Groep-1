<?php
include 'connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>SchoenenWijns | Home</title>
</head>
<body>
<header>
    <div class="logo">SchoenenWijns</div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">Logout</a></li>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="#">Admin Panel</a></li> <!-- Admin Panel button -->
                <?php endif; ?>
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <div class="hero">
        <h1>Welcome to SchoenenWijns</h1>
        <p>Only the best of the best for our PHP lovers!</p>
    </div>

    <div class="products">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <!-- Product items will be dynamically loaded here -->
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2024 SchoenenWijns. All rights reserved.</p>
</footer>
</body>
</html>