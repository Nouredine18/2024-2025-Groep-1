<?php
include 'connect.php';
session_start();

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
$client = new Google\Client;

$client->setClientId("YES");
$client->setClientSecret("NO");
$client->SetRedirectUri("YES");

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();

$login_error = $register_error = "";

if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM `User` WHERE email='$email' AND actief=1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['voornaam'] = $user['voornaam'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];

            header("Location: index.php");
            exit();
        } else {
            $login_error = "Incorrect password!";
        }
    } else {
        $login_error = "No account found with that email!";
    }
}

if (isset($_POST['register'])) {
    $voornaam = $conn->real_escape_string($_POST['voornaam']);
    $naam = $conn->real_escape_string($_POST['naam']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "SELECT * FROM `User` WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $register_error = "Email is already registered!";
    } else {
        $sql = "INSERT INTO `User` (voornaam, naam, email, password_hash, user_type, actief) VALUES ('$voornaam', '$naam', '$email', '$password', 'user', 1)";

        if ($conn->query($sql) === TRUE) {
            echo "Registration successful! You can now login.";
        } else {
            $register_error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

if (isset($_POST['reset_password'])) {
    $email = $conn->real_escape_string($_POST['reset_email']);
    $sql = "SELECT * FROM `User` WHERE email='$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(50));
        $sql = "UPDATE `User` SET reset_token='$token' WHERE email='$email'";
        $conn->query($sql);

        $reset_link = "https://schoenenwijns.feralstorm.com/reset_password.php?token=$token";
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.hostinger.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->Username = 'password@feralstorm.com';
        $mail->Password = 'PASSWORD';
        $mail->setFrom('password@feralstorm.com', 'Reset Password');
        $mail->addReplyTo('password@feralstorm.com', 'Reset Password');
        $mail->addAddress($email, $email);
        $mail->Subject = 'Reset Password @ Schoenen Wijns';

        $html_body = file_get_contents('email_templates/resetpassword.html');
        if ($html_body === false) {
            echo 'Kan het HTML-bestand niet vinden of openen.';
            exit;
        }

        $html_body = str_replace('{{reset_link}}', $reset_link, $html_body);
        $mail->msgHTML($html_body, __DIR__);

        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'De e-mail is verzonden.';
            header('Location: login_register.php');
        }
    } else {
        echo "Aantal rijen: " . $result->num_rows;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Login/Register</title>
</head>
<body>
    <div class="container">
        <h2>Login/Register</h2>

        <div id="login-form" style="display: block;">
            <h3>Login</h3>
            <form action="login_register.php" method="post">
                <input type="email" name="email" placeholder="Email" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="submit" name="login" value="Login">
                <div class="error"><?php echo $login_error; ?></div>
            </form>
            <span class="switch-link" onclick="toggleForm()">Switch to Register</span><br>
            <span class="forgot-password-link" onclick="showResetForm()">Forgot Password?</span>
        </div>

        <a href="<?= $url ?>"> Sign in with Google</a>

        <div id="register-form" style="display: none;">
            <h3>Register</h3>
            <form action="login_register.php" method="post">
                <input type="text" name="voornaam" placeholder="First Name" required><br>
                <input type="text" name="naam" placeholder="Last Name" required><br>
                <input type="email" name="email" placeholder="Email" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="submit" name="register" value="Register">
                <div class="error"><?php echo $register_error; ?></div>
            </form>
            <span class="switch-link" onclick="toggleForm()">Switch to Login</span>
        </div>

        <div id="reset-form" style="display: none;">
            <h3>Reset Password</h3>
            <form action="login_register.php" method="post">
                <input type="email" name="reset_email" placeholder="Enter your email" required><br>
                <input type="submit" name="reset_password" value="Reset Password">
            </form>
            <span class="switch-link" onclick="hideResetForm()">Back to Login</span>
        </div>
    </div>

    <script>
        function toggleForm() {
            var loginForm = document.getElementById('login-form');
            var registerForm = document.getElementById('register-form');
            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            }
        }

        function showResetForm() {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('reset-form').style.display = 'block';
        }

        function hideResetForm() {
            document.getElementById('reset-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        }
    </script>
</body>
</html>
