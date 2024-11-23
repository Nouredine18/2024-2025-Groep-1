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

$sql = "SELECT p.naam, pv.kleur, pv.maat, c.aantal, p.prijs, c.artikelnr, c.variantnr, pv.stock 
        FROM Cart c 
        JOIN ProductVariant pv ON c.artikelnr = pv.artikelnr AND c.variantnr = pv.variantnr
        JOIN Products p ON c.artikelnr = p.artikelnr 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    if ($row['stock'] == 0) {
        $sql_delete = "DELETE FROM Cart WHERE user_id = ? AND artikelnr = ? AND variantnr = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("iii", $user_id, $row['artikelnr'], $row['variantnr']);
        $stmt_delete->execute();
    }
}

$stmt->execute();
$result = $stmt->get_result();
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
    <style>
        body {
            font-family: 'Oswald', Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 20px;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
            font-size: 2em;
            color: #333;
        }
        .cart-items {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #343a40;
            color: white;
            font-size: 1.1em;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        button {
            background-color: #000;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #333;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
        }
        .empty-cart {
            text-align: center;
            font-size: 1.2em;
            color: #888;
        }
        input[type="number"] {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>
<?php
include('header.php');
?>

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
                            <th>Actions</th>
                        </tr>
                        <?php
                        $total_price = 0;
                        while ($row = $result->fetch_assoc()):
                            $item_total = $row['aantal'] * $row['prijs'];
                            $total_price += $item_total;
                        ?>
                        <tr>
                            <td><?php echo $row['naam']; ?></td>
                            <td><?php echo $row['kleur']; ?></td>
                            <td><?php echo $row['maat']; ?></td>
                            <td>
                                <input type="number" name="quantities[<?php echo $row['artikelnr']; ?>][<?php echo $row['variantnr']; ?>]" value="<?php echo $row['aantal']; ?>" min="1" max="100">
                            </td>
                            <td>€<?php echo number_format($row['prijs'], 2); ?></td>
                            <td>€<?php echo number_format($item_total, 2); ?></td>
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