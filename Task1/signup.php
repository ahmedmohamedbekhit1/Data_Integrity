<?php
require 'db_connection.php';
require 'twofa_helper.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (empty($data['username']) || empty($data['password'])) {
    http_response_code(400);
    die(json_encode(['error' => 'Username and password are required']));
}

try {
    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM Users WHERE username = ?");
    $stmt->execute([$data['username']]);
    if ($stmt->fetch()) {
        http_response_code(409);
        die(json_encode(['error' => 'Username already exists']));
    }

    // Generate and store 2FA secret (not returned)
    $helper = new TwoFAHelper();
    $twofa_secret = $helper->generateSecret();
    $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);

    // Store user with 2FA secret
    $stmt = $conn->prepare("INSERT INTO Users (username, password, twofa_secret) VALUES (?, ?, ?)");
    $stmt->execute([$data['username'], $hashed_password, $twofa_secret]);

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful. You will setup 2FA on first login.'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>