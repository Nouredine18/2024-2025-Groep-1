<?php
include 'connect.php';
session_start();

// Zorg voor initiële initialisatie
$users = []; 
$successMessage = '';

// Haal eerst alle gebruikers op als er geen POST-verzoek is
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    // Haal alle gebruikers op
    $sqlAllUsers = "SELECT * FROM user WHERE user_type='user'";
    $resultAllUsers = $conn->query($sqlAllUsers);

    while ($row = $resultAllUsers->fetch_assoc()) {
        $users[] = $row; // Voeg elke gebruiker toe aan de lijst
    }
}

// Controleer of er een POST-verzoek is
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Formulierinvoer voor aanpassen, activeren, deactiveren
    if (isset($_POST["aanpassen"])) {
        $userid = $_POST["userid"];
        $naam = $_POST["naam"];
        $voornaam = $_POST["voornaam"];
        $email = $_POST["email"];
        $schoenmaat = $_POST["schoenmaat"];

        $sql2 = "UPDATE user SET naam=?, voornaam=?, email=?, schoenmaat=? WHERE user_id=?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("sssii", $naam, $voornaam, $email, $schoenmaat, $userid);
        $stmt2->execute();

        $successMessage = "Gegevens succesvol aangepast.";
    }

    // Activeren of deactiveren
    if (isset($_POST["activeren"]) || isset($_POST["deactiveren"])) {
        $userid = $_POST["userid"];
        $actiefStatus = isset($_POST["activeren"]) ? 1 : 0;
        $sqlUpdate = "UPDATE user SET actief = ? WHERE user_id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ii", $actiefStatus, $userid);
        $stmtUpdate->execute();

        $successMessage = $actiefStatus ? "Succesvol geactiveerd." : "Succesvol gedeactiveerd.";
    }

    // Zoek op gebruiker
    if (isset($_POST["indienen"])) {
        $zoekresultaat = $_POST["zoekresultaat"];
        $sql = "SELECT * FROM `User` WHERE naam LIKE ? AND user_type='user'";
        $searchparameter = '%' . $zoekresultaat . '%';

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $searchparameter);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $users[] = $row; // Voeg elke gevonden gebruiker toe aan de lijst
        }
    }
}

if (!isset($conn)) {
    die("Databaseverbinding mislukt.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoek gebruiker</title>
    <link rel="stylesheet" href="css/style.css"> 
    <header>
    <div class="logo">SchoenenWijns</div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">Logout</a></li>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="add_product.php">Voeg Product Toe</a></li>
                    <li><a href="manage_products.php">Beheer Producten</a></li>
                    <li><a href="active_deactivate_show_users.php">Users</a></li>
                <?php endif; ?>
                <li><a href="#">Welcome, <?php echo $_SESSION['voornaam']; ?></a></li>
            <?php else: ?>
                <li><a href="login_register.php">Login/Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
</head>
<body>

<form action="active_deactivate_show_users.php" method="post">
    <label>Geef de gebruiker in van de persoon:</label>
    <input type="text" name="zoekresultaat" required>
    <input type="submit" name="indienen" value="Zoeken">
</form>

<?php if ($successMessage): ?>
    <div class='success-message'><?php echo $successMessage; ?></div>
<?php endif; ?>

<h2>Gebruikerslijst</h2>
<table>
    <thead>
        <tr>
            <th>Naam</th>
            <th>Voornaam</th>
            <th>UserID</th>
            <th>Actief</th>
            <th>Acties</th>
            <th>Aanpassen</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["naam"]); ?></td>
                    <td><?php echo htmlspecialchars($row["voornaam"]); ?></td>
                    <td><?php echo htmlspecialchars($row["user_id"]); ?></td>
                    <td><?php echo ($row["actief"] == 0 ? "Niet actief" : "Wel actief"); ?></td>
                    <td>
                        <form action='active_deactivate_show_users.php' method='post' style='display:inline;'>
                            <input type='hidden' name='userid' value='<?php echo $row["user_id"]; ?>'>
                            <input type='submit' name='activeren' value='Activeren'>
                        </form>
                        <form action='active_deactivate_show_users.php' method='post' style='display:inline;'>
                            <input type='hidden' name='userid' value='<?php echo $row["user_id"]; ?>'>
                            <input type='submit' name='deactiveren' value='Deactiveren'>
                        </form>
                    </td>
                    <td>
                        <form action='active_deactivate_show_users.php' method='post'>
                            <label>Naam</label>
                            <input type='text' name='naam' required value='<?php echo htmlspecialchars($row["naam"]); ?>'>
                            <label>Voornaam</label>
                            <input type='text' name='voornaam' required value='<?php echo htmlspecialchars($row["voornaam"]); ?>'>
                            <label>Email</label>
                            <input type='email' name='email' required value='<?php echo htmlspecialchars($row["email"]); ?>'>
                            <label>Schoenmaat</label>
                            <input type='number' name='schoenmaat' required value='<?php echo htmlspecialchars($row["schoenmaat"]); ?>'>
                            <input type='hidden' name='userid' value='<?php echo $row["user_id"]; ?>'>
                            <input type='submit' name='aanpassen' value='Verander'>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan='6'>Geen overeenkomstige gebruikers gevonden.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>