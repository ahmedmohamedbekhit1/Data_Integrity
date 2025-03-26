<?php
require 'jwt_middleware.php'; // Include the JWT middleware
require 'db_connection.php';
header('Content-Type: application/json');

// Get the product ID from the query string
$pid = $_GET['pid'];

// Prepare and execute the SQL statement
try {
    $stmt = $conn->prepare("DELETE FROM Products WHERE pid = ?");
    $stmt->bindParam(1, $pid);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Product deleted successfully"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["message" => "Failed to delete product"]);
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
?>