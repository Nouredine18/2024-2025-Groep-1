<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT f.bestelling_id, f.oorspronkelijke_prijs, f.reductie, f.betalingsmethode, b.eindprijs, f.address_id
        FROM factuur f
        JOIN betaling b ON f.bestelling_id = b.bestelling_id
        WHERE f.user_id = ?
        ORDER BY f.bestelling_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Purchase History</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>My Purchase History</h1>
    <div class="purchase-history">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="order">
                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($row['bestelling_id']); ?></p>
                <p><strong>Total Price:</strong> €<?php echo htmlspecialchars($row['eindprijs']); ?></p>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($row['betalingsmethode']); ?></p>
                <a href="order_details.php?order_id=<?php echo $row['bestelling_id']; ?>">View Details</a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>