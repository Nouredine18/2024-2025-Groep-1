<?php
include 'connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $complaint = htmlspecialchars($_POST['complaint']);
    $feedback_type = htmlspecialchars($_POST['feedback_type']);

    $sql = "INSERT INTO customer_feedback (user_id, feedback, feedback_type, feedback_date, status) VALUES (?, ?, ?, NOW(), 'sended')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $complaint, $feedback_type);

    if ($stmt->execute()) {
        $success_message = "Thank you for your feedback!";
    } else {
        $error_message = "There was an error submitting your feedback. Please try again.";
    }

    $stmt->close();
}

// Fetch user feedback
$user_id = $_SESSION['user_id'];
$sql = "SELECT feedback, feedback_type, feedback_date, response, response_date, status 
        FROM customer_feedback 
        WHERE user_id = ? 
        ORDER BY feedback_date DESC";
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
    <title>Submit a Complaint</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Oswald', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
        }
        .complaint-form, .feedback-status {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .complaint-form h2, .feedback-status h2 {
            margin-bottom: 20px;
        }
        .complaint-form textarea, .complaint-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .complaint-form button {
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .complaint-form button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
<?php include('header.php'); ?>

<div class="content">
    <div class="complaint-form">
        <h2>Submit Your Feedback</h2>
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="complain.php" method="post">
            <label for="feedback_type">Feedback Type:</label>
            <select name="feedback_type" id="feedback_type" required>
                <option value="suggestion">Suggestion</option>
                <option value="complaint">Complaint</option>
                <option value="compliment">Compliment</option>
            </select>
            <textarea name="complaint" rows="5" required placeholder="Enter your feedback here..."></textarea>
            <button type="submit">Submit Feedback</button>
        </form>
    </div>

    <div class="feedback-status">
        <h2>Your Feedback Status</h2>
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Feedback</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Response</th>
                        <th>Response Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['feedback']); ?></td>
                            <td><?php echo htmlspecialchars($row['feedback_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['feedback_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['response']); ?></td>
                            <td><?php echo htmlspecialchars($row['response_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not submitted any feedback yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('footer.php'); ?>

</body>
</html>
