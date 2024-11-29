<?php
// Start the session only if it is not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&display=swap" rel="stylesheet">

    <title>FootWear | BE</title>
    <style>
     /* General reset for margins and paddings */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Oswald', Arial, sans-serif;
}

/* Styling the header */
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #fff;
    border-bottom: 1px solid #e5e5e5;
}

/* Logo styles */
.logo img {
    height: 40px; /* Adjust this to the size of your logo */
}

/* Navigation styles */
nav ul {
    list-style-type: none;
    display: flex;
    align-items: center;
}

nav ul li {
    margin: 0 15px;
}

nav ul li a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    font-size: 14px;
    transition: color 0.3s ease;
}

nav ul li a:hover {
    color: #000;
}

/* Header icons */
.header-icons {
    display: flex;
    align-items: center;
}

.header-icons a {
    margin-left: 20px;
    color: #333;
    text-decoration: none;
    font-size: 18px;
    transition: color 0.3s ease;
}

.header-icons a:hover {
    color: #000;
}

.header-icons i {
    margin-right: 5px;
}

/* Search form styles */
.search-form {
    display: flex;
    align-items: center;
    margin-left: 20px; /* Add some space between the nav and search */
}

.search-form input {
    padding: 5px; /* Reduced padding */
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 12px; /* Reduced font size */
}

.search-form button {
    padding: 5px 10px; /* Reduced padding */
    margin-left: 5px;
    background-color: #fff;
    color: #333;
    border: 1px solid #ccc;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    font-size: 12px; /* Reduced font size */
    transition: color 0.3s ease, background-color 0.3s ease;
}

.search-form button:hover {
    color: #000;
    background-color: #e5e5e5;
}

/* Media query for smaller screens */
@media (max-width: 768px) {
    nav ul {
        display: none; /* Hide the nav on mobile, you can add a hamburger menu for better UX */
    }

    .header-icons a {
        font-size: 16px;
    }

    .logo img {
        height: 35px;
    }
}
    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="images_main/adidas.jpg" alt="FootWear BE">
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            
            <!-- <li><a href="new_featured.php">Nieuw en uitgelicht</a></li>
            <li><a href="mens.php">Heren</a></li>
            <li><a href="womens.php">Dames</a></li>
            <li><a href="sale.php">Sale</a></li> -->

            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="cart.php">Winkelwagen (<?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0'; ?>)</a></li>
                <li><a href="logout.php">Uitloggen</a></li>
                <li><a href="view_bestellingen.php">View Count Users</a></li>
                <li><a href="complete_profile.php"></a></li>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="adminPanel.php">Admin Panel</a></li>
                    
                <?php endif; ?>
                <li><a href="#">Welkom, <?php echo $_SESSION['voornaam']; ?></a></li>
            <?php else: ?>
                <li><a href="login_register.php">Inloggen/Registreren</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <!-- Search Form -->
<form method="POST" action="search.php" class="search-form">
    <input type="text" name="zoekresultaat" placeholder="Zoek producten..." required>
    <button type="submit">Zoeken</button>
</form>
    
    <div class="header-icons">
        <a href="favorites.php"><i class="fa fa-heart"></i></a>
        <a href="cart.php"><i class="fa fa-shopping-cart"></i></a>
    </div>
</header>

<!-- Add Font Awesome to support icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>