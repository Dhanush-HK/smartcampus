<?php
session_start(); // Start session to store login status
include 'config.php'; // Include database connection

// Set header for JSON response
header('Content-Type: application/json');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        // Login successful
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful'
        ]);
    } else {
        // Invalid credentials
        echo json_encode([
            'success' => false,
            'message' => 'Invalid username or password'
        ]);
    }

    $stmt->close();
}

$conn->close();
?>