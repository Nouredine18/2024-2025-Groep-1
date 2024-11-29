<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['payment_method'])) {
        $_SESSION['payment_method'] = $_POST['payment_method'];
        header("Location: payement.php");
        exit();
    } else {
        $error = "Please select a payment method.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Payment Method</title>
</head>
<body>
    <h1>Choose Payment Method</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label>
            <input type="radio" name="payment_method" value="Stripe"> Stripe
        </label>
        <br>
        <label>
            <input type="radio" name="payment_method" value="PayPal"> PayPal
        </label>
        <br>
        <button type="submit">Proceed to Payment</button>
    </form>
</body>
</html>
