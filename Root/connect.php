<?php
$servername = "srv1514.hstgr.io";
$username = "u220407022_dbfootwear";
$password = "TeamNouredine3";
$dbname = "u220407022_dbfootwear"; 

/* $servername = "localhost";
$username = "root";
$password = "";
$dbname = "u220407022_dbfootwear"; */

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}/*  else {
 echo "Connected succesfully";
} */

// Create customer_feedback table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS customer_feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    feedback TEXT NOT NULL,
    feedback_type VARCHAR(50) NOT NULL,
    feedback_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    response TEXT DEFAULT NULL,
    response_date DATETIME DEFAULT NULL,
    status VARCHAR(50) DEFAULT 'sended',
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE
)";
$conn->query($sql);

// Alter customer_feedback table to add response, response_date, and status columns if they don't exist
$sql = "ALTER TABLE customer_feedback 
        ADD COLUMN IF NOT EXISTS response TEXT DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS response_date DATETIME DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'sended'";
$conn->query($sql);

// Create customer_complaints table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS customer_complaints (
    complaint_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    complaint TEXT NOT NULL,
    complaint_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE
)";
$conn->query($sql);
?>