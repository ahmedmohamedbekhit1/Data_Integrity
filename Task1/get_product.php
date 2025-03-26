<?php
require 'jwt_middleware.php'; // Include the JWT middleware
require 'db_connection.php';
header('Content-Type: application/json');

// Get the product ID from the query string
$pid = $_GET['pid'];

// Fetch the product from the database
try {
    $stmt = $conn->prepare("SELECT * FROM Products WHERE pid = ?");
    $stmt->bindParam(1, $pid);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        echo json_encode($product);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["message" => "Product not found"]);
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
?>