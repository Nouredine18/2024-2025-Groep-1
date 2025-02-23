<?php
include 'connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['discount_code'])) {
    $discount_code = trim($_POST['discount_code']);
    
    if (!empty($discount_code)) {
        $sql = "SELECT discount_percentage, expiration_date FROM discount_codes WHERE code = ? AND expiration_date >= CURDATE()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $discount_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $discount_percentage = $row['discount_percentage'];
            $_SESSION['discount'] = $discount_percentage;
            $_SESSION['discount_message'] = "Kortingscode toegepast! Je krijgt $discount_percentage% korting.";
        } else {
            $_SESSION['discount_error'] = "Ongeldige of verlopen kortingscode.";
        }
    } else {
        $_SESSION['discount_error'] = "Voer een kortingscode in.";
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
