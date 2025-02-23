<?php
require 'vendor/autoload.php';
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die('User ID is missing.');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_points = $_POST['points'];

    $updateSql = "UPDATE Points SET points = ? WHERE id = 1";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $new_points);
    if ($updateStmt->execute()) {
        $message = "Points updated successfully!";
    } else {
        $message = "Failed to update points.";
    }
}

$pointSql = "SELECT points FROM Points WHERE id = 1";
$pointStmt = $conn->prepare($pointSql);
$pointStmt->execute();
$pointresult = $pointStmt->get_result();
$pointrow = $pointresult->fetch_assoc();
$current_points = $pointrow['points'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Points</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .header {
            background-color: #000;
            color: white;
            text-align: center;
            padding: 20px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 30px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .container {
            display: flex;
            justify-content: center;
            margin: 20px;
            flex-wrap: wrap;
        }

        .content {
            width: 100%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 2em;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="number"] {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="submit"] {
            padding: 10px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #003f7f;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            color: green;
        }
    </style>
</head>
<body>

    <?php include('header.php'); ?>

    <div class="container">
        <div class="content">
            <h1>Modify Points</h1>
            <form method="post" action="">
                <label for="points">Points:</label>
                <input type="number" id="points" name="points" value="<?php echo htmlspecialchars($current_points); ?>" required>
                <input type="submit" value="Update Points">
            </form>
            <?php if ($message): ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <?php include('footer.php'); ?>

</body>
</html>