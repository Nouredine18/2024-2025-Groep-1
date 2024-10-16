<?php
include 'connect.php';

$artikelnr = isset($_GET['artikelnr']) ? intval($_GET['artikelnr']) : 0;
$color = isset($_GET['color']) ? $_GET['color'] : '';

$images = [];

if ($artikelnr > 0 && !empty($color)) {
    $sql_images = "SELECT variant_directory FROM ProductVariant WHERE artikelnr = ? AND kleur = ?";
    $stmt_images = $conn->prepare($sql_images);
    $stmt_images->bind_param("is", $artikelnr, $color);
    $stmt_images->execute();
    $result_images = $stmt_images->get_result();
    if ($result_images->num_rows > 0) {
        $row = $result_images->fetch_assoc();
        $images = explode(" | ", $row['variant_directory']);
    }
}

header('Content-Type: application/json');
echo json_encode(['images' => $images]);
?>

