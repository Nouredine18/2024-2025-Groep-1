<?php
include 'connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql_count = "SELECT SUM(aantal) AS total_items FROM Cart WHERE user_id = ?";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $row_count = $result_count->fetch_assoc();

    $_SESSION['cart_count'] = $row_count['total_items'] ? $row_count['total_items'] : 0;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT p.naam, pv.kleur, pv.maat, c.aantal, p.prijs, c.artikelnr, c.variantnr, pv.stock, c.persoonlijk_bericht 
        FROM Cart c 
        JOIN ProductVariant pv ON c.artikelnr = pv.artikelnr AND c.variantnr = pv.variantnr
        JOIN Products p ON c.artikelnr = p.artikelnr 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Bereken de totale prijs van de winkelwagen
$total_price = 0;
while ($row = $result->fetch_assoc()) {
    $item_total = $row['aantal'] * $row['prijs'];
    $total_price += $item_total;
}

// Toepassen van de kortingscode
$final_price = $total_price;
if (isset($_SESSION['discount'])) {
    $discount_percentage = $_SESSION['discount'];
    $discount_amount = ($total_price * $discount_percentage) / 100;
    $final_price = $total_price - $discount_amount;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('header.php'); ?>

<div class="content">
    <main>
        <h2>Your Cart</h2>
        <div class="cart-items">
            <?php if ($result->num_rows > 0): ?>
                <form action="update_cart.php" method="POST">
                    <table>
                        <tr>
                            <th>Product</th>
                            <th>Color</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Personal Message</th>
                            <th>Actions</th>
                        </tr>
                        <?php
                        // Herstart de cursor van het resultaatset om de tabel opnieuw te vullen
                        $result->data_seek(0);
                        while ($row = $result->fetch_assoc()):
                            $item_total = $row['aantal'] * $row['prijs'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['naam']); ?></td>
                            <td><?php echo htmlspecialchars($row['kleur']); ?></td>
                            <td><?php echo htmlspecialchars($row['maat']); ?></td>
                            <td>
                                <input type="number" name="quantities[<?php echo $row['artikelnr']; ?>][<?php echo $row['variantnr']; ?>]" value="<?php echo $row['aantal']; ?>" min="1" max="100">
                            </td>
                            <td>€<?php echo number_format($row['prijs'], 2); ?></td>
                            <td>€<?php echo number_format($item_total, 2); ?></td>
                            <td>
                                <textarea name="personal_message[<?php echo $row['artikelnr']; ?>][<?php echo $row['variantnr']; ?>]"><?php echo htmlspecialchars($row['persoonlijk_bericht']); ?></textarea>
                            </td>
                            <td>
                                <button type="submit" name="remove" value="<?php echo $row['artikelnr'] . '-' . $row['variantnr']; ?>">Remove</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <tr>
                            <td colspan="5" class="total">Total Price:</td>
                            <td class="total">€<?php echo number_format($total_price, 2); ?></td>
                            <td></td>
                        </tr>

                        <?php if (isset($discount_amount)): ?>
                            <tr>
                                <td colspan="5" class="total">Discount:</td>
                                <td class="total">-€<?php echo number_format($discount_amount, 2); ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="total">Final Price:</td>
                                <td class="total">€<?php echo number_format($final_price, 2); ?></td>
                                <td></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                    <button type="submit" name="update_cart">Update Cart</button>
                    <button type="submit" name="pay" formaction="payement.php">Pay</button>
                </form>
            <?php else: ?>
                <p class="empty-cart">Your cart is empty.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include('footer.php'); ?>

</body>
</html>
