<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start de sessie om sessievariabelen te kunnen gebruiken
}

// Controleer of de gebruiker een admin is en of het formulier is ingediend
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $popupMessage = $_POST['popup_message']; // Haal het nieuwe popupbericht op uit het formulier
    file_put_contents('popup_message.txt', $popupMessage); // Sla het bericht op in een bestand
}

// Laad het popupbericht uit het bestand, of gebruik een standaardbericht als het bestand niet bestaat
$popupMessage = file_exists('popup_message.txt') ? file_get_contents('popup_message.txt') : 'Get 20% off on your next purchase. Use code: SAVE20';
?>



<?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?> <!-- Toon het formulier alleen aan admin-gebruikers -->
    <div class="admin-popup-form">
        <form method="POST" action="">
            <label for="popup_message">Edit Popup Message:</label>
            <textarea name="popup_message" id="popup_message" rows="4" cols="50"><?= htmlspecialchars($popupMessage) ?></textarea>
            <button type="submit">Save</button>
        </form>
    </div>
<?php endif; ?>

<style>
    .admin-popup-form {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        margin: 20px 0;
    }

    .admin-popup-form form {
        display: flex;
        flex-direction: column;
    }

    .admin-popup-form label {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .admin-popup-form textarea {
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ddd;
        font-size: 14px;
        margin-bottom: 15px;
        resize: vertical;
    }

    .admin-popup-form button {
        align-self: flex-start;
        padding: 10px 20px;
        background-color: #111;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .admin-popup-form button:hover {
        background-color: #333;
    }
</style>
