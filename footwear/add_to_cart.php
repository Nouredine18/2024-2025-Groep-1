<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    // User must be logged in to add items to the cart
    header("Location: login_register.php");
    exit();
}

if (isset($_POST['artikelnr']) && isset($_POST['variantnr']) && isset($_POST['aantal'])) {
    $user_id = $_SESSION['user_id'];
    $artikelnr = intval($_POST['artikelnr']);
    $variantnr = intval($_POST['variantnr']);
    $aantal = intval($_POST['aantal']);

    // Check if the item already exists in the cart for this user
    $sql = "SELECT aantal FROM Cart WHERE user_id = ? AND artikelnr = ? AND variantnr = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $artikelnr, $variantnr);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Item already in the cart, update the quantity
        $row = $result->fetch_assoc();
        $new_aantal = $row['aantal'] + $aantal;
        $update_sql = "UPDATE Cart SET aantal = ? WHERE user_id = ? AND artikelnr = ? AND variantnr = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iiii", $new_aantal, $user_id, $artikelnr, $variantnr);
        $update_stmt->execute();
    } else {
        // Item not in the cart, insert a new row
        $insert_sql = "INSERT INTO Cart (user_id, artikelnr, variantnr, aantal) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iiii", $user_id, $artikelnr, $variantnr, $aantal);
        $insert_stmt->execute();
    }

    // Update cart count in session
    $cart_count_sql = "SELECT SUM(aantal) AS cart_count FROM Cart WHERE user_id = ?";
    $cart_count_stmt = $conn->prepare($cart_count_sql);
    $cart_count_stmt->bind_param("i", $user_id);
    $cart_count_stmt->execute();
    $cart_count_result = $cart_count_stmt->get_result();
    $cart_row = $cart_count_result->fetch_assoc();
    $_SESSION['cart_count'] = $cart_row['cart_count'];

    header("Location: webshop.php"); // Redirect back to webshop after adding to cart
    exit();
} else {
    echo "Invalid product details.";
}
?>
