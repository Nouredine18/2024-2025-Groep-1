<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login_register.php");
    exit();
}

if (isset($_POST['complete_profile'])) {
    $familyName = $conn->real_escape_string($_POST['familyName']);
    $email = $_SESSION['email'];
    $givenName = $_SESSION['givenName'];
    $userType = "user";
    
    if (str_ends_with($email, '@feralstorm.com')) {
        $userType = "admin";
    }

    $stmt = $conn->prepare("INSERT INTO `User` (voornaam, naam, email, password_hash, user_type, actief) VALUES (?, ?, ?, '', ?, 1)");
    $stmt->bind_param("ssss", $givenName, $familyName, $email, $userType);
    $stmt->execute();

    $sql = "SELECT * FROM `User` WHERE email='$email' AND actief=1";
    $result = $conn->query($sql);

    $user = $result->fetch_assoc();

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['voornaam'] = $givenName;
    $_SESSION['email'] = $email;
    $_SESSION['user_type'] = $userType;

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Profile</title>
</head>
<body>
    <h2>Complete Your Profile</h2>
    <form action="complete_profile.php" method="post">
        <label for="familyName">Family Name:</label>
        <input type="text" name="familyName" required><br>
        <input type="submit" name="complete_profile" value="Complete Profile">
    </form>
</body>
</html>
