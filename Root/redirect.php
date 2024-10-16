<?php
include 'connect.php';
session_start();

require 'vendor/autoload.php';
$client = new Google\Client;

$client->setClientId("519320310592-bjj2f62ov0nodbkj028t7slhas6tho5r.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->SetRedirectUri("https://schoenenwijns.feralstorm.com/redirect.php");

if (!isset($_GET["code"])) {
    exit("Login failed");
}

$token = $client->fetchAccessTokenWithAuthCode($_GET["code"]);
$client->setAccessToken($token["access_token"]);

$oauth = new Google\Service\Oauth2($client);

$userinfo = $oauth->userinfo->get();

$email = $userinfo->email;
$givenName = $userinfo->givenName;
$familyName = $userinfo->familyName ?? '';

$sql = "SELECT * FROM `User` WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['voornaam'] = $user['voornaam'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['user_type'] = $user['user_type'];

    header("Location: index.php");
    exit();
} else {
    if (empty($familyName)) {
        $_SESSION['email'] = $email;
        $_SESSION['givenName'] = $givenName;
        header("Location: complete_profile.php");
        exit();
    }

    $userType = "user";
    
    if (str_ends_with($email, '@feralstorm.com')) {
        $userType = "admin";
    };

    $stmt = $conn->prepare("INSERT INTO `User` (voornaam, naam, email, password_hash, user_type, actief) VALUES (?, ?, ?, '', ?, 1)");
    $stmt->bind_param("ssss", $givenName, $familyName, $email, $userType);
    $stmt->execute();

    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['voornaam'] = $givenName;
    $_SESSION['email'] = $email;
    $_SESSION['user_type'] = $userType;

    header("Location: index.php");
    exit();
}
?>