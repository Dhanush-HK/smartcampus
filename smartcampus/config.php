<?php
// Database configuration
$host = 'localhost';      // Server name (usually localhost)
$username = 'root';       // MySQL username (default is 'root')
$password = '';           // MySQL password (default is empty)
$dbname = 'smart_campus'; // Database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>