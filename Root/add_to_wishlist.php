<?php
session_start();
include 'connect.php';

if (isset($_SESSION['user_id']) && isset($_POST['artikelnr'])) {
    $user_id = $_SESSION['user_id'];
    $artikelnr = intval($_POST['artikelnr']);

    // Controleer of het product al in de verlanglijst van de gebruiker staat
    $sql_check = "SELECT * FROM WishList WHERE user_id = ? AND artikelnr = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $user_id, $artikelnr);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        // Voeg het product toe aan de verlanglijst
        $sql_insert = "INSERT INTO WishList (user_id, artikelnr) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $user_id, $artikelnr);
        $stmt_insert->execute();

        // Optioneel: Je kunt een succesbericht tonen of een redirect doen
        echo "<p>Product succesvol toegevoegd aan je verlanglijst!</p>";
    } else {
        echo "<p>Dit product staat al in je verlanglijst.</p>";
    }
} else {
    echo "<p>Je moet ingelogd zijn om een product aan je verlanglijst toe te voegen.</p>";
}
?>
