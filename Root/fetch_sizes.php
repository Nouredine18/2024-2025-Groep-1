<?php
include 'connect.php';

$artikelnr = isset($_GET['artikelnr']) ? intval($_GET['artikelnr']) : 0;
$color = isset($_GET['color']) ? $_GET['color'] : '';

$sizes = [];

$sql_sizes = "SELECT DISTINCT maat FROM ProductVariant WHERE artikelnr = ? AND kleur = ?";
$stmt_sizes = $conn->prepare($sql_sizes);
$stmt_sizes->bind_param("is", $artikelnr, $color);
$stmt_sizes->execute();
$result_sizes = $stmt_sizes->get_result();
while ($row = $result_sizes->fetch_assoc()) {
    $sizes[] = $row['maat'];
}

echo json_encode(['sizes' => $sizes]);
?>