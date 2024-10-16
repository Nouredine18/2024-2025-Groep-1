<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connect.php';

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT * FROM `User` WHERE reset_token='$token'";
    $result = $conn->query($sql);
    
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    
    if ($result->num_rows > 0) {
        if (isset($_POST['reset_password'])) {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password == $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $sql = "UPDATE `User` SET password_hash='$hashed_password', reset_token=NULL WHERE reset_token='$token'";
                if ($conn->query($sql) === TRUE) {
                    echo "Je wachtwoord is succesvol gewijzigd.";
                    header("Location: login_register.php");
                    exit();
                } else {
                    echo "Er ging iets mis bij het updaten van het wachtwoord. Probeer het opnieuw.";
                }
            } else {
                echo "Wachtwoorden komen niet overeen.";
            }
        }
    } else {
        echo "Ongeldige of verlopen token.";
        exit();
    }
} else {
    echo "Geen token gevonden.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wachtwoord Resetten</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .reset-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 100%;
        }
        .reset-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .reset-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .reset-container button {
            width: 100%;
            padding: 10px;
            background-color: #5865F2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .reset-container button:hover {
            background-color: #4752c4;
        }
    </style>
</head>
<body>

<div class="reset-container">
    <h2>Reset je wachtwoord</h2>
    <form method="POST" action="">
        <label for="new_password">Nieuw wachtwoord:</label>
        <input type="password" name="new_password" id="new_password" required>

        <label for="confirm_password">Bevestig wachtwoord:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <button type="submit" name="reset_password">Reset wachtwoord</button>
    </form>
</div>

</body>
</html>