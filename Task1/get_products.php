<?php
require 'jwt_middleware.php'; // Include the JWT middleware
require 'db_connection.php';
header('Content-Type: application/json');

// Fetch all products from the database
try {
    $stmt = $conn->query("SELECT * FROM Products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
?>