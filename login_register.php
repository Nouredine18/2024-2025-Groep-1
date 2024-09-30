<?php
include 'connect.php';
session_start();

$login_error = $register_error = "";
$reset_error = $reset_success = "";

// Handle login
if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Check if the email exists
    $sql = "SELECT * FROM `User` WHERE email='$email' AND actief=1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
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

// Handle registration
if (isset($_POST['register'])) {
    $voornaam = $conn->real_escape_string($_POST['voornaam']);
    $naam = $conn->real_escape_string($_POST['naam']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the email is already registered
    $sql = "SELECT * FROM `User` WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $register_error = "Email is already registered!";
    } else {
        // Insert user into the database
        $sql = "INSERT INTO `User` (voornaam, naam, email, password_hash, user_type, actief) VALUES ('$voornaam', '$naam', '$email', '$password', 'user', 1)";

        if ($conn->query($sql) === TRUE) {
            echo "Registration successful! You can now login.";
        } else {
            $register_error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Handle password reset request
if (isset($_POST['reset_password'])) {
    $email = $conn->real_escape_string($_POST['reset_email']);
    $sql = "SELECT * FROM `User` WHERE email='$email'";

    if ($result = $conn->query($sql) && $result->num_rows > 0) {
        $token = bin2hex(random_bytes(50)); // Generate a secure token
        // Store token in the database for verification later (consider adding an expiry time)
        $sql = "UPDATE `User` SET reset_token='$token' WHERE email='$email'";
        $conn->query($sql);

        // Send password reset email
        $reset_link = "https://feralstorm.com/reset_password.php?token=$token";
        mail($email, "Password Reset Request", "Click this link to reset your password: $reset_link");

        $reset_success = "Check your email for a link to reset your password.";
    } else {
        $reset_error = "No account found with that email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
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
                <div class="success"><?php echo $reset_success; ?></div>
                <div class="error"><?php echo $reset_error; ?></div>
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