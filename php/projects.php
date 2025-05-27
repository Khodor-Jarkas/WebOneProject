<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "portfolio_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Handle GET request - Get all projects
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM projects ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $projects = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $projects[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'technologies' => explode(',', $row['technologies']),
                'imageUrl' => $row['image_url'],
                'projectUrl' => $row['project_url'] ?: '#',
                'createdAt' => $row['created_at']
            ];
        }
    }
    
    echo json_encode($projects);
}

// Handle POST request - Add new project
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        die(json_encode(["error" => "Invalid input data"]));
    }
    
    // Validate required fields
    if (empty($data['title']) || empty($data['description']) || empty($data['technologies']) || empty($data['imageUrl'])) {
        http_response_code(400);
        die(json_encode(["error" => "Missing required fields"]));
    }
    
    // Prepare data
    $title = $conn->real_escape_string($data['title']);
    $description = $conn->real_escape_string($data['description']);
    $technologies = $conn->real_escape_string(is_array($data['technologies']) ? implode(',', $data['technologies']) : $data['technologies']);
    $imageUrl = $conn->real_escape_string($data['imageUrl']);
    $projectUrl = isset($data['projectUrl']) ? $conn->real_escape_string($data['projectUrl']) : '';
    
    // Insert into database
    $sql = "INSERT INTO projects (title, description, technologies, image_url, project_url)
            VALUES ('$title', '$description', '$technologies', '$imageUrl', '$projectUrl')";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "id" => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
    }
}

$conn->close();
?>