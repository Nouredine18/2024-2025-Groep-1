<?php
include 'connect.php';
session_start();

// Zorg voor initiële initialisatie
$users = []; 
$successMessage = '';

// Haal eerst alle gebruikers op als er geen POST-verzoek is
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    // Haal alle gebruikers op
    $sqlAllUsers = "SELECT * FROM User";
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
        
        $sql2 = "UPDATE User SET naam=?, voornaam=?, email=?, schoenmaat=? WHERE user_id=?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ssssi", $naam, $voornaam, $email, $schoenmaat, $userid);
        $stmt2->execute();

        $successMessage = "Gegevens succesvol aangepast.";
    }

    // Activeren of deactiveren
    if (isset($_POST["activeren"]) || isset($_POST["deactiveren"])) {
        $userid = $_POST["userid"];
        $userType = $_POST["user_type"];

        if (isset($_POST["deactiveren"]) && $userType === 'admin') {
            $adminCountQuery = "SELECT COUNT(*) AS admin_count FROM User WHERE user_type = 'admin' AND actief = 1";
            $adminCountResult = $conn->query($adminCountQuery);
            $adminCountRow = $adminCountResult->fetch_assoc();
            $adminCount = $adminCountRow['admin_count'];

            if ($adminCount <= 1) {
                $successMessage = "Deactivatie mislukt: er moet altijd ten minste één admin actief zijn.";
            } else {
                $actiefStatus = 0; // Deactiveren
                $sqlUpdate = "UPDATE User SET actief = ? WHERE user_id = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("ii", $actiefStatus, $userid);
                $stmtUpdate->execute();
                $successMessage = "Succesvol gedeactiveerd.";
            }
        } else {
            $actiefStatus = isset($_POST["activeren"]) ? 1 : 0; // Activeren of deactiveren based on button clicked
            $sqlUpdate = "UPDATE User SET actief = ? WHERE user_id = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ii", $actiefStatus, $userid);
            $stmtUpdate->execute();
            $successMessage = $actiefStatus ? "Succesvol geactiveerd." : "Succesvol gedeactiveerd.";
        }
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

    // Beheerder maken of demotiveren
    if (isset($_POST['create_beheerder'])) {
        $userid = $_POST["userid"];
        $sqlBeheerder = "UPDATE User SET user_type='admin' WHERE user_id=?";
        $stmtBeheerder = $conn->prepare($sqlBeheerder);
        $stmtBeheerder->bind_param("i", $userid);
        $stmtBeheerder->execute();
        $successMessage = "Succesvol gemaakt als admin.";
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

<main>
    <form action="active_deactivate_show_users.php" method="post" class="form-inline">
        <label for="zoekresultaat">Geef de gebruiker in van de persoon:</label>
        <input type="text" name="zoekresultaat" id="zoekresultaat" required>
        <input type="submit" name="indienen" value="Zoeken" class="btn btn-activate">
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
                <th>Punten</th>
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
                        <td><?php echo htmlspecialchars($row["punten"] ?? 0); ?></td>
                        <td>
                            <form action='active_deactivate_show_users.php' method='post' style='display:inline;'>
                                <input type='hidden' name='userid' value='<?php echo $row["user_id"]; ?>'>
                                <input type='hidden' name='user_type' value='<?php echo $row["user_type"]; ?>'>
                                <input type='submit' name='activeren' value='Activeren' class='btn btn-activate'>
                            </form>
                            <form action='active_deactivate_show_users.php' method='post' style='display:inline;'>
                                <input type='hidden' name='userid' value='<?php echo $row["user_id"]; ?>'>
                                <input type='hidden' name='user_type' value='<?php echo $row["user_type"]; ?>'>
                                <input type='submit' name='deactiveren' value='Deactiveren' class='btn btn-deactivate'>
                            </form>
                            <?php if ($row["user_type"] == "user"): ?>
                            <form action="active_deactivate_show_users.php" method="post" style='display:inline;'>
                                <input type='hidden' name='userid' value='<?php echo $row["user_id"]; ?>'>
                                <input type="submit" name="create_beheerder" value="Maak beheerder" class='btn btn-activate'>
                            </form>
                            <?php elseif ($row["user_type"] == "beheerder"): ?>
                            <form action="active_deactivate_show_users.php" method="post" style='display:inline;'>
                                <input type='hidden' name='userid' value='<?php echo $row["user_id"]; ?>'>
                                <input type="submit" name="destroy_beheerder" value="Demotiveer beheerder" class='btn btn-deactivate'>
                            </form>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action='active_deactivate_show_users.php' method='post' class='form-inline'>
                                <label>Naam</label>
                                <input type='text' name='naam' required value='<?php echo htmlspecialchars($row["naam"]); ?>'>
                                <label>Voornaam</label>
                                <input type='text' name='voornaam' required value='<?php echo htmlspecialchars($row["voornaam"]); ?>'>
                                <label>Email</label>
                                <input type='email' name='email' required value='<?php echo htmlspecialchars($row["email"]); ?>'>
                                <label>Schoenmaat</label>
                                <input type='number' name='schoenmaat' required value='<?php echo htmlspecialchars($row["schoenmaat"]); ?>'>
                                <input type='hidden' name='userid' value='<?php echo $row["user_id"]; ?>'>
                                <input type='submit' name='aanpassen' value='Verander' class='btn btn-edit'>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan='7'>Geen overeenkomstige gebruikers gevonden.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<footer>
    <p>&copy; 2024 SchoenenWijns. Alle rechten voorbehouden.</p>
    <div class="social-media">
        <h3>Volg ons op:</h3>
        <ul>
            <li><a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-f"></i> Facebook</a></li>
            <li><a href="https://www.twitter.com" target="_blank"><i class="fab fa-twitter"></i> Twitter</a></li>
            <li><a href="https://www.instagram.com" target="_blank"><i class="fab fa-instagram"></i> Instagram</a></li>
            <li><a href="https://www.linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i> LinkedIn</a></li>
        </ul>
    </div>
</footer>
</body>
</html>