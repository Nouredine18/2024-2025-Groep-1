<?php
include 'connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

// Handle admin response
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response'])) {
    $feedback_id = $_POST['feedback_id'];
    $response = htmlspecialchars($_POST['response']);

    $sql = "UPDATE customer_feedback SET response = ?, response_date = NOW() WHERE feedback_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $response, $feedback_id);

    if ($stmt->execute()) {
        $success_message = "Response submitted successfully.";
    } else {
        $error_message = "There was an error submitting your response. Please try again.";
    }

    $stmt->close();
}

// Fetch all feedback
$sql = "SELECT cf.feedback_id, cf.feedback, cf.feedback_type, cf.feedback_date, cf.response, cf.response_date, u.voornaam, u.naam
        FROM customer_feedback cf
        JOIN User u ON cf.user_id = u.user_id
        ORDER BY cf.feedback_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Feedback</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .feedback-overview {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .feedback-overview h2 {
            margin-bottom: 20px;
        }
        .feedback-overview textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .feedback-overview button {
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .feedback-overview button:hover {
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
<?php include 'admin_header.php'; ?>

<div class="feedback-overview">
    <h2>Feedback Overview</h2>
    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Feedback Type</th>
                    <th>Feedback</th>
                    <th>Feedback Date</th>
                    <th>Response</th>
                    <th>Response Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['voornaam'] . ' ' . $row['naam']); ?></td>
                        <td><?php echo htmlspecialchars($row['feedback_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['feedback']); ?></td>
                        <td><?php echo htmlspecialchars($row['feedback_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['response']); ?></td>
                        <td><?php echo htmlspecialchars($row['response_date']); ?></td>
                        <td>
                            <?php if ($_SESSION['user_type'] == 'admin' && !$row['response']): ?>
                                <form action="customer_feedback.php" method="post">
                                    <textarea name="response" rows="2" required placeholder="Enter your response here..."></textarea>
                                    <input type="hidden" name="feedback_id" value="<?php echo $row['feedback_id']; ?>">
                                    <button type="submit">Submit Response</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No feedback found.</p>
    <?php endif; ?>
</div>

</body>
</html>