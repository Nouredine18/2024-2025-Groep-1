<?php
session_start();
include 'connect.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    echo "<p>Je moet ingelogd zijn om een product uit je verlanglijst te verwijderen.</p>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Controleer of het artikelnr is meegegeven
if (isset($_POST['artikelnr'])) {
    $artikelnr = $_POST['artikelnr'];

    // Verwijder het product uit de verlanglijst van de gebruiker
    $sql = "DELETE FROM WishList WHERE user_id = ? AND artikelnr = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $artikelnr);

    if ($stmt->execute()) {
        // Redirect terug naar de verlanglijstpagina na succesvol verwijderen
        header("Location: wishlist.php");
        exit();
    } else {
        echo "<p>Er is een fout opgetreden bij het verwijderen van het product.</p>";
    }
} else {
    echo "<p>Geen product opgegeven om te verwijderen.</p>";
}
?>
