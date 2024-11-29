<?php
// Start de sessie alleen als deze nog niet actief is
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

    <title>Schoenen Wijns | BE</title>
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

.navbar {
    display: flex;
    align-items: center;
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
    margin: 0 10px;
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
    margin-left: 0px;
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
    margin-left: 0px; /* Add some space between the nav and search */
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
    <div class="navbar">
    <div class="logo">
        <img src="images_main/Logo.png" alt="FootWear BE">
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="mens.php">Heren</a></li>
            <li><a href="womens.php">Dames</a></li>
            <li><a href="kids.php">Kids</a></li>
            <li><a href="sale.php">Sale</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="cart.php">Winkelwagen (<?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0'; ?>)</a></li>
        </ul>
    </nav>
    </div>
    <h1>Schoenen Wijns</h1>
    <div class="navbar">
    <form method="POST" action="search.php" class="search-form">
        <input type="text" name="zoekresultaat" placeholder="Zoek producten..." required>
        <button type="submit">Zoeken</button>
    </form>
        <nav>
        <ul>
    <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="adminPanel.php">Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Uitloggen</a></li>
            <?php else: ?>
                <li><a href="login_register.php">Inloggen/Registreren</a></li>
            <?php endif; ?>
                    </ul>
    </nav>
            
    <div class="header-icons">
        <a href="profile.php"><img src="images_main/profile-user.png" alt="Profiel" style="width:30px; height:30px; border-radius:50%;"></a>
    </div>
    </div>
</header>

<!-- Add Font Awesome to support icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
