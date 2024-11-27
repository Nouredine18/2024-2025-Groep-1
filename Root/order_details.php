<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
if ($order_id === false) {
    echo "Invalid order ID.";
    exit();
}

// Fetch order details
$sql = "SELECT p.naam, pv.kleur, pv.maat, bp.aantal, p.prijs, bp.status, bp.koopdatum
        FROM BoughtProducts bp
        JOIN ProductVariant pv ON bp.variantnr = pv.variantnr AND bp.artikelnr = pv.artikelnr
        JOIN Products p ON bp.artikelnr = p.artikelnr
        WHERE bp.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Order Details</h1>
    <div class="order-details">
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Purchase Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['naam']); ?></td>
                            <td><?php echo htmlspecialchars($row['kleur']); ?></td>
                            <td><?php echo htmlspecialchars($row['maat']); ?></td>
                            <td><?php echo htmlspecialchars($row['aantal']); ?></td>
                            <td>€<?php echo number_format($row['prijs'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['koopdatum']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No order details found.</p>
        <?php endif; ?>
    </div>
</body>
</html>