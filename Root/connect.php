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
?>