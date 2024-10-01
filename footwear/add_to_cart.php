<?php
include 'connect.php'; // Verbind met de database
session_start(); // Start een nieuwe of herstelt een bestaande sessie

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    // De gebruiker moet ingelogd zijn om artikelen aan de winkelwagentje toe te voegen
    header("Location: login_register.php"); // Redirect naar de inlogpagina
    exit();
}

// Controleer of de vereiste gegevens zijn verzonden via POST
if (isset($_POST['artikelnr']) && isset($_POST['variantnr']) && isset($_POST['aantal'])) {
    $user_id = $_SESSION['user_id']; // Verkrijg de gebruikers-ID uit de sessie
    $artikelnr = intval($_POST['artikelnr']); // Zet het artikelnummer om naar een geheel getal
    $variantnr = intval($_POST['variantnr']); // Zet het variantnummer om naar een geheel getal
    $aantal = intval($_POST['aantal']); // Zet het aantal om naar een geheel getal

    // Controleer of het artikel al in het winkelwagentje van deze gebruiker staat
    $sql = "SELECT aantal FROM Cart WHERE user_id = ? AND artikelnr = ? AND variantnr = ?";
    $stmt = $conn->prepare($sql); // Bereid de SQL-query voor
    $stmt->bind_param("iii", $user_id, $artikelnr, $variantnr); // Bind de parameters
    $stmt->execute(); // Voer de query uit
    $result = $stmt->get_result(); // Verkrijg het resultaat

    if ($result->num_rows > 0) {
        // Artikel is al in het winkelwagentje, update het aantal
        $row = $result->fetch_assoc(); // Haal de huidige hoeveelheid op
        $new_aantal = $row['aantal'] + $aantal; // Bereken het nieuwe aantal
        $update_sql = "UPDATE Cart SET aantal = ? WHERE user_id = ? AND artikelnr = ? AND variantnr = ?";
        $update_stmt = $conn->prepare($update_sql); // Bereid de update-query voor
        $update_stmt->bind_param("iiii", $new_aantal, $user_id, $artikelnr, $variantnr); // Bind de parameters
        $update_stmt->execute(); // Voer de update uit
    } else {
        // Artikel staat nog niet in het winkelwagentje, voeg een nieuwe rij toe
        $insert_sql = "INSERT INTO Cart (user_id, artikelnr, variantnr, aantal) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql); // Bereid de invoer-query voor
        $insert_stmt->bind_param("iiii", $user_id, $artikelnr, $variantnr, $aantal); // Bind de parameters
        $insert_stmt->execute(); // Voer de invoer uit
    }

    // Update het aantal in het winkelwagentje in de sessie
    $cart_count_sql = "SELECT SUM(aantal) AS cart_count FROM Cart WHERE user_id = ?";
    $cart_count_stmt = $conn->prepare($cart_count_sql); // Bereid de query voor om het totaal aantal op te halen
    $cart_count_stmt->bind_param("i", $user_id); // Bind de parameter
    $cart_count_stmt->execute(); // Voer de query uit
    $cart_count_result = $cart_count_stmt->get_result(); // Verkrijg het resultaat
    $cart_row = $cart_count_result->fetch_assoc(); // Haal de som op
    $_SESSION['cart_count'] = $cart_row['cart_count']; // Bewaar het totale aantal in de sessie

    header("Location: webshop.php"); // Redirect terug naar de webshop na het toevoegen aan het winkelwagentje
    exit();
} else {
    echo "Ongeldige productgegevens."; // Toon foutmelding als gegevens ongeldig zijn
}
?>
