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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Points</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Oswald', Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 20px;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 2em;
            color: #333;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #000;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
<?php include('header.php'); ?>

<div class="content">
    <h1>Modify Points</h1>
    <form method="post" action="">
        <label for="points">Points:</label>
        <input type="number" id="points" name="points" value="<?php echo $current_points; ?>" required>
        <input type="submit" value="Update Points">
    </form>
</div>

<?php include('footer.php'); ?>
</body>
</html>