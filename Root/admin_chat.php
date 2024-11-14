<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login_register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $admin_id = $_SESSION['user_id'];
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO admin_chat (admin_id, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $admin_id, $message);
    $stmt->execute();
}

$sql = "SELECT ac.message, ac.timestamp, u.voornaam FROM admin_chat ac JOIN User u ON ac.admin_id = u.user_id ORDER BY ac.timestamp DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat Board</title>
    <html><link rel="stylesheet" href="css/styles.css"></html>
</head>
<body>
    <h1>Admin Chat Board</h1>
    <form method="post" action="admin_chat.php">
        <textarea name="message" required></textarea>
        <button type="submit">Send</button>
    </form>
    <div class="chat-messages">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="message">
                <strong><?php echo htmlspecialchars($row['voornaam']); ?>:</strong>
                <p><?php echo htmlspecialchars($row['message']); ?></p>
                <small><?php echo htmlspecialchars($row['timestamp']); ?></small>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>