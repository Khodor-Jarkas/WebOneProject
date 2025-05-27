<?php
header('Content-Type: application/json');

// configuration ta3eet l database
$servername = "localhost";
$username = "root";  // Default XAMPP username
$password = "";      // Default XAMPP password
$dbname = "portfolio_db";

// create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// get the data from the form
$name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
$email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
$subject = isset($_POST['subject']) ? $conn->real_escape_string($_POST['subject']) : '';
$message = isset($_POST['message']) ? $conn->real_escape_string($_POST['message']) : '';

// Validate inputs
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

// use the SQL statement - easy :)
$stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $subject, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Message received successfully!']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>