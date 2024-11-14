<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['question'])) {
    $user_id = $_SESSION['user_id'];
    $question = $conn->real_escape_string($_POST['question']);

    $sql = "INSERT INTO customer_support (user_id, question) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $question);
    $stmt->execute();

    header("Location: complete_profile.php");
    exit();
}
?>
<html><link rel="stylesheet" href="css/styles.css"></html>