<?php
require 'jwt_middleware.php';
require 'db_connection.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

try {
    $stmt = $conn->prepare("INSERT INTO Products (name, description, price, quantity) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $data['name']);
    $stmt->bindParam(2, $data['description']);
    $stmt->bindParam(3, $data['price']);
    $stmt->bindParam(4, $data['quantity']);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Product added successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to add product"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
?>