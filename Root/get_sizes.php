<?php
include 'connect.php';

// Haal artikelnr op uit de queryparameters
$artikelnr = isset($_GET['artikelnr']) ? intval($_GET['artikelnr']) : 0;
$kleur = isset($_GET['kleur']) ? $_GET['kleur'] : '';

// Initialiseer variabelen
$sizes = [];

// Als geen kleur is opgegeven, haal de eerste beschikbare kleur op
if (empty($kleur)) {
    $sql_first_color = "SELECT kleur FROM ProductVariant WHERE artikelnr = ? LIMIT 1";
    $stmt_first_color = $conn->prepare($sql_first_color);
    $stmt_first_color->bind_param("i", $artikelnr);
    $stmt_first_color->execute();
    $result_first_color = $stmt_first_color->get_result();
    if ($row = $result_first_color->fetch_assoc()) {
        $kleur = $row['kleur'];
    }
    $stmt_first_color->close();
}

// Haal beschikbare maten op voor de geselecteerde kleur
$sql_sizes = "SELECT DISTINCT maat FROM ProductVariant WHERE artikelnr = ? AND kleur = ?";
$stmt_sizes = $conn->prepare($sql_sizes);
$stmt_sizes->bind_param("is", $artikelnr, $kleur);
$stmt_sizes->execute();
$result_sizes = $stmt_sizes->get_result();
while ($row = $result_sizes->fetch_assoc()) {
    $sizes[] = $row['maat'];
}

// Sluit de statement en de verbinding
$stmt_sizes->close();
$conn->close();

// Stuur de maten terug als JSON
header('Content-Type: application/json');
echo json_encode($sizes);
?>