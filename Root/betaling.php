<?php
// Gegevens uit URL ophalen
$artikelnr = isset($_GET['artikelnr']) ? intval($_GET['artikelnr']) : 0;
$kleur = isset($_GET['kleur']) ? htmlspecialchars($_GET['kleur']) : '';
$maat = isset($_GET['maat']) ? htmlspecialchars($_GET['maat']) : '';
$aantal = isset($_GET['aantal']) ? intval($_GET['aantal']) : 1;

// Gebruik deze variabelen voor verdere verwerking, zoals de betaling of orderoverzicht.
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Betaling</title>
    <style>
        /* Algemene stijlen */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #353030;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-top: 20px;
        }

        h2 {
            color: #555;
            text-align: center;
            margin-top: 20px;
        }

        /* Paragraafstijl */
        p {
            font-size: 16px;
            line-height: 1.5;
            color: #666;
            text-align: center;
            margin-bottom: 15px;
        }

        /* Formulierstijl */
        .container {
            max-width: 400px;
            margin: auto;
            margin-top: 50px; /* Pas aan indien nodig */
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #193ecf; /* Aangepaste randkleur voor invoervelden */
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus {
            border: 1px solid #193ecf; /* Aangepaste focusrandkleur */
        }

        input[type="submit"] {
            background-color: #000; /* Knopkleur */
            color: white;
            border: none;
            padding: 15px; /* Verbeterde padding */
            border-radius: 8px; /* Verbeterde border-radius */
            cursor: pointer;
            transition: background 0.3s, transform 0.3s; /* Toegevoegde transformatie-overgang */
            width: 100%; /* Volledige breedte voor de verzendknop */
            font-size: 1em; /* Verbeterde lettergrootte */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Toegevoegde schaduw */
        }

        input[type="submit"]:hover {
            background-color: #333; /* Hoverkleur voor de verzendknop */
            transform: translateY(-3px); /* Lift-effect bij hover */
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); /* Verbeterde schaduw bij hover */
        }

        .error {
            color: red;
            text-align: center;
            margin: 10px 0;
        }

        /* Responsief ontwerp */
        @media (max-width: 768px) {
            .container {
                width: 90%; /* Volledige breedte op kleinere schermen */
            }
        }
    </style>
</head>
<body>
    <h1>Bevestiging van de bestelling</h1>
    <p>Artikelnummer: <?php echo $artikelnr; ?></p>
    <p>Kleur: <?php echo htmlspecialchars($kleur); ?></p>
    <p>Maat: <?php echo htmlspecialchars($maat); ?></p>
    <p>Aantal: <?php echo $aantal; ?></p>
    
    <h2>Klantgegevens</h2>
    <div class="container">
        <form method="POST" action="">
            <label for="naam">Naam:</label>
            <input type="text" id="naam" name="naam" required>
            
            <label for="voornaam">Voornaam:</label>
            <input type="text" id="voornaam" name="voornaam" required>
            
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="adres">Adres:</label>
            <input type="text" id="adres" name="adres" required>
            
            <input type="submit" value="Verzenden">
        </form>
    </div>

    <!-- Voeg hier verdere betaalopties toe -->
</body>
</html>
