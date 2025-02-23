<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Verwijderen van producten uit de winkelwagen
if (isset($_POST['remove'])) {
    list($artikelnr, $variantnr) = explode('-', $_POST['remove']);
    $sql_delete = "DELETE FROM Cart WHERE user_id = ? AND artikelnr = ? AND variantnr = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("iii", $user_id, $artikelnr, $variantnr);
    $stmt_delete->execute();
}

// Bijwerken van hoeveelheden en persoonlijke berichten
if (isset($_POST['update_cart'])) {
    if (isset($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $artikelnr => $variants) {
            foreach ($variants as $variantnr => $aantal) {
                $aantal = intval($aantal);
                if ($aantal > 0) {
                    $sql_update = "UPDATE Cart SET aantal = ? WHERE user_id = ? AND artikelnr = ? AND variantnr = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("iiii", $aantal, $user_id, $artikelnr, $variantnr);
                    $stmt_update->execute();
                }
            }
        }
    }

    if (isset($_POST['personal_message'])) {
        foreach ($_POST['personal_message'] as $artikelnr => $variants) {
            foreach ($variants as $variantnr => $persoonlijk_bericht) {
                $persoonlijk_bericht = trim($persoonlijk_bericht);
                $sql_update_msg = "UPDATE Cart SET persoonlijk_bericht = ? WHERE user_id = ? AND artikelnr = ? AND variantnr = ?";
                $stmt_update_msg = $conn->prepare($sql_update_msg);
                $stmt_update_msg->bind_param("siii", $persoonlijk_bericht, $user_id, $artikelnr, $variantnr);
                $stmt_update_msg->execute();
            }
        }
    }
}

// Terugsturen naar de winkelwagen
header("Location: cart.php");
exit();
?>
