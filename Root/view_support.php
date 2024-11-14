<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT cs.question, cs.answer, cs.timestamp, u.voornaam AS admin_name 
        FROM customer_support cs 
        LEFT JOIN User u ON cs.admin_id = u.user_id 
        WHERE cs.user_id = ? 
        ORDER BY cs.timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Support Questions</title>
    <html><link rel="stylesheet" href="css/styles.css"></html>
</head>
<body>
    <h1>My Support Questions</h1>
    <div class="support-questions">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="question">
                <p><strong>Question:</strong> <?php echo htmlspecialchars($row['question']); ?></p>
                <small><?php echo htmlspecialchars($row['timestamp']); ?></small>
                <?php if ($row['answer']): ?>
                    <p><strong>Answer:</strong> <?php echo htmlspecialchars($row['answer']); ?></p>
                    <small>Answered by: <?php echo htmlspecialchars($row['admin_name']); ?></small>
                <?php else: ?>
                    <p><strong>Answer:</strong> Not answered yet.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>