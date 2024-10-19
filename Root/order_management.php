<?php
// Verbinden met database
include 'connect.php';

// Controleren of de admin is ingelogd (vervang deze logica door jouw inlogsystemen)
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Bestellingen ophalen
$sql = "SELECT order_id, user_id, product_id, order_date, status FROM orders";
$result = $conn->query($sql);

// Updaten van de bestelstatus
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // SQL-query om de status bij te werken
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param('si', $status, $order_id);

    if ($stmt->execute()) {
        echo "Bestelstatus succesvol bijgewerkt!";
    } else {
        echo "Fout bij het bijwerken van de status.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellingen Beheren</title>
</head>
<body>
    <h1>Beheer Bestellingen</h1>
    <table border="1">
        <tr>
            <th>Order ID</th>
            <th>Klant ID</th>
            <th>Product ID</th>
            <th>Besteldatum</th>
            <th>Status</th>
            <th>Actie</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) : ?>
        <tr>
            <td><?php echo $row['order_id']; ?></td>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo $row['product_id']; ?></td>
            <td><?php echo $row['order_date']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <form method="POST" action="order_management.php">
                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                    <select name="status">
                        <option value="In behandeling" <?php if($row['status'] == 'In behandeling') echo 'selected'; ?>>In behandeling</option>
                        <option value="Verzonden" <?php if($row['status'] == 'Verzonden') echo 'selected'; ?>>Verzonden</option>
                        <option value="Afgeleverd" <?php if($row['status'] == 'Afgeleverd') echo 'selected'; ?>>Afgeleverd</option>
                    </select>
                    <button type="submit">Status Bijwerken</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
