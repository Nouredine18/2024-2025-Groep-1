<?php
include 'connect.php';
session_start();

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login_register.php");
    exit();
}

// Fetch customer orders
$sql = "SELECT f.bestelling_id, f.user_id, f.address_id, f.oorspronkelijke_prijs, f.reductie, f.betalingsmethode, u.voornaam, u.naam
        FROM factuur f
        JOIN User u ON f.user_id = u.user_id
        ORDER BY f.bestelling_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Customer Orders</h1>
    <div class="orders">
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                      
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['bestelling_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['voornaam'] . ' ' . $row['naam']); ?></td>
                            <td><a href="order_details.php?order_id=<?php echo $row['bestelling_id']; ?>">View Details</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>
</body>
</html>