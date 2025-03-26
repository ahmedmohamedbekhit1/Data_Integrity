<?php
require 'jwt_middleware.php'; // Include the JWT middleware
require 'db_connection.php';
header('Content-Type: application/json');

// Get the user ID from the URL
$id = $_GET['id'];

// Get the raw input
$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input, true);

// Check if required fields are present
if (!isset($data['name']) || !isset($data['username']) || !isset($data['password'])) {
    http_response_code(400); // Bad Request
    die(json_encode(["message" => "Missing required fields"]));
}

// Extract data from the decoded JSON
$name = $data['name'];
$username = $data['username'];
$password = password_hash($data['password'], PASSWORD_BCRYPT);

// Prepare and execute the SQL statement
try {
    $stmt = $conn->prepare("UPDATE Users SET name = ?, username = ?, password = ? WHERE id = ?");
    $stmt->bindParam(1, $name);
    $stmt->bindParam(2, $username);
    $stmt->bindParam(3, $password);
    $stmt->bindParam(4, $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User updated successfully"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["message" => "Failed to update user"]);
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
?>