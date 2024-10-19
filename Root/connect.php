<?php
// Rapporteren van alle MySQL-fouten voor betere debugging tijdens de ontwikkelingsfase
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Databasegegevens
$servername = "srv1514.hstgr.io";
$username = "u220407022_dbfootwear";
$password = "TeamNouredine3";
$dbname = "u220407022_dbfootwear";

// Proberen een verbinding te maken
try {
    // Maak een nieuwe mysqli-verbinding
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Zet de tekenset naar UTF-8 om problemen met speciale tekens te voorkomen
    $conn->set_charset("utf8mb4");

    // Bevestig de succesvolle verbinding (alleen voor debugging, deze regel kan later worden verwijderd)
    echo "Verbinding succesvol gemaakt!";
} catch (mysqli_sql_exception $e) {
    // Foutmelding als de verbinding niet lukt
    die("Verbinding mislukt: " . $e->getMessage());
}
?>
