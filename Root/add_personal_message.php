<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['personal_message']) && isset($_POST['artikelnr'])) {
    $personal_message = trim($_POST['personal_message']);
    $artikelnr = $_POST['artikelnr'];

    // Opslaan in sessie per product
    $_SESSION['personal_messages'][$artikelnr] = $personal_message;

    // Redirect terug naar de productpagina
    header("Location: info_product.php?artikelnr=" . urlencode($artikelnr));
    exit;
} else {
    // Ongeldige toegang, terugsturen naar de homepage
    header("Location: index.php");
    exit;
}
