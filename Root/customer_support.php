<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login_register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['answer'])) {
    $admin_id = $_SESSION['user_id'];
    $support_id = intval($_POST['support_id']);
    $answer = $conn->real_escape_string($_POST['answer']);

    $sql = "UPDATE customer_support SET admin_id = ?, answer = ? WHERE support_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $admin_id, $answer, $support_id);
    $stmt->execute();
}

$sql = "SELECT cs.support_id, cs.question, cs.answer, cs.timestamp, u.voornaam FROM customer_support cs JOIN User u ON cs.user_id = u.user_id ORDER BY cs.timestamp DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Support</title>
    <html><link rel="stylesheet" href="css/styles.css"></html>
</head>
<body>
    <h1>Customer Support</h1>
    <div class="support-questions">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="question">
                <strong><?php echo htmlspecialchars($row['voornaam']); ?>:</strong>
                <p><?php echo htmlspecialchars($row['question']); ?></p>
                <small><?php echo htmlspecialchars($row['timestamp']); ?></small>
                <?php if ($row['answer']): ?>
                    <p><strong>Answer:</strong> <?php echo htmlspecialchars($row['answer']); ?></p>
                <?php else: ?>
                    <form method="post" action="customer_support.php">
                        <textarea name="answer" required></textarea>
                        <input type="hidden" name="support_id" value="<?php echo $row['support_id']; ?>">
                        <button type="submit">Answer</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>