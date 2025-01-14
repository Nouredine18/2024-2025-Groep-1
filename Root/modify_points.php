<?php
require 'vendor/autoload.php';
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die('User ID is missing.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_points = $_POST['points'];

    $updateSql = "UPDATE Points SET points = ? WHERE id = 1";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $new_points);
    $updateStmt->execute();

    echo "Points updated successfully!";
} else {
    $pointSql = "SELECT points FROM Points WHERE id = 1";
    $pointStmt = $conn->prepare($pointSql);
    $pointStmt->execute();
    $pointresult = $pointStmt->get_result();
    $pointrow = $pointresult->fetch_assoc();
    $current_points = $pointrow['points'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modify Points</title>
</head>
<body>
    <h1>Modify Points</h1>
    <form method="post" action="">
        <label for="points">Points:</label>
        <input type="number" id="points" name="points" value="<?php echo $current_points; ?>" required>
        <input type="submit" value="Update Points">
    </form>
</body>
</html>