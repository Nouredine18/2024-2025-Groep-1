<?php
include 'connect.php';
session_start();

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login_register.php");
    exit();
}

// Fetch customer reviews and ratings
$sql = "SELECT r.review_text, r.rating, r.review_date, u.voornaam, u.naam, p.naam AS product_name
        FROM Reviews r
        JOIN User u ON r.user_id = u.user_id
        JOIN Products p ON r.artikelnr = p.artikelnr
        ORDER BY r.review_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Satisfaction</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
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

<h1>Customer Satisfaction</h1>
<div class="customer-reviews">
    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Product Name</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['voornaam'] . ' ' . $row['naam']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['rating']); ?></td>
                        <td><?php echo htmlspecialchars($row['review_text']); ?></td>
                        <td><?php echo htmlspecialchars($row['review_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No reviews found.</p>
    <?php endif; ?>
</div>

</body>
</html>