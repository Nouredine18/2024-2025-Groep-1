<?php
include 'connect.php';
session_start();

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login_register.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch all payment methods to ensure we handle unchecked checkboxes
    $sql = "SELECT * FROM payment_methods";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $method_id = $row['id'];
        $is_enabled = isset($_POST['methods'][$method_id]) ? 1 : 0;

        $sql_update = "UPDATE payment_methods SET is_enabled = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ii", $is_enabled, $method_id);
        $stmt->execute();
    }
}

// Fetch payment methods
$sql = "SELECT * FROM payment_methods";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Payment Methods</title>
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

<h1>Manage Payment Methods</h1>
<form method="POST" action="">
    <table>
        <tr>
            <th>Payment Method</th>
            <th>Enabled</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['method_name']); ?></td>
            <td>
                <input type="checkbox" name="methods[<?php echo $row['id']; ?>]" value="1" <?php echo $row['is_enabled'] ? 'checked' : ''; ?>>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <button type="submit">Update</button>
</form>

</body>
</html>