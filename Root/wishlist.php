<?php
session_start();
include 'connect.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    echo "<p>Je moet ingelogd zijn om je verlanglijst te bekijken.</p>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Haal de producten op die de gebruiker heeft toegevoegd aan de verlanglijst
$sql = "SELECT p.artikelnr, p.naam, p.prijs, pv.variant_directory
        FROM WishList w
        JOIN Products p ON w.artikelnr = p.artikelnr
        LEFT JOIN ProductVariant pv ON p.artikelnr = pv.artikelnr
        WHERE w.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$wishlist_items = [];
while ($row = $result->fetch_assoc()) {
    $wishlist_items[] = $row;
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verlanglijst</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Je Verlanglijst</h1>

        <?php if (count($wishlist_items) > 0): ?>
            <div class="wishlist-items">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="wishlist-item">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($item['naam']); ?></h3>
                            <p>Prijs: &euro;<?php echo number_format($item['prijs'], 2, ',', '.'); ?></p>
                            <img src="<?php echo htmlspecialchars($item['variant_directory'] ?? 'img/default-product.jpg'); ?>" alt="Product afbeelding">
                        </div>
                        <div class="actions">
                            <!-- Link naar productpagina -->
                            <a href="info_product.php?artikelnr=<?php echo $item['artikelnr']; ?>" class="view-details">Bekijk product</a>
                            <!-- Verwijder uit verlanglijst -->
                            <form action="remove_from_wishlist.php" method="post" style="display:inline;">
                                <input type="hidden" name="artikelnr" value="<?php echo $item['artikelnr']; ?>">
                                <button type="submit" class="remove-button">Verwijder</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Je verlanglijst is leeg.</p>
        <?php endif; ?>

        <a href="index.php" class="back-button">Terug naar winkel</a>
    </div>
</body>
</html>
