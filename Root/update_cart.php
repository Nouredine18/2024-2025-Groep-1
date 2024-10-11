<?php
session_start();
include 'connect.php';

// Controleer of de gebruiker is ingelogd, zo niet, stuur dan door naar de login pagina
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Haal het user_id op van de ingelogde gebruiker

// Verwerken van het verwijderen van een product uit de winkelwagen
if (isset($_POST['remove'])) {
    // Haal het artikelnr en variantnr op uit de POST-request (bijv. '1-2')
    list($artikelnr, $variantnr) = explode('-', $_POST['remove']);
    
    // Verwijder het geselecteerde artikel uit de winkelwagen
    $sql = "DELETE FROM Cart WHERE user_id = ? AND artikelnr = ? AND variantnr = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $artikelnr, $variantnr); // Bind de parameters: user_id, artikelnr, variantnr
    $stmt->execute(); // Voer de query uit
    $stmt->close(); // Sluit de prepared statement
}

// Verwerken van de update van de winkelwagen (aanpassen van hoeveelheden)
if (isset($_POST['update_cart'])) {
    // Loop door de producten heen die in de winkelwagen zitten
    foreach ($_POST['quantities'] as $artikelnr => $variants) {
        foreach ($variants as $variantnr => $quantity) {
            if ($quantity > 0) {
                // Update het aantal van het artikel in de winkelwagen
                $sql = "UPDATE Cart SET aantal = ? WHERE user_id = ? AND artikelnr = ? AND variantnr = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiii", $quantity, $user_id, $artikelnr, $variantnr); // Bind de parameters
                $stmt->execute(); // Voer de query uit
                $stmt->close(); // Sluit de prepared statement
            }
        }
    }
}

// Nadat de wijzigingen zijn doorgevoerd, stuur de gebruiker terug naar de winkelwagen pagina
header("Location: cart.php");
exit();
?>
