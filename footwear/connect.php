<?php
$servername = "localhost";  // Host name (Usually 'localhost')
$username = "root";         // MySQL username (in this case, 'root')
$password = "";             // MySQL password (assuming no password)
$dbname = "footwear_db";    // Name of the database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to the footwear_db!";
?>
