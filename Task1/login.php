<?php
require 'db_connection.php';
require 'twofa_helper.php';
require 'jwt_auth.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (empty($data['username']) || empty($data['password'])) {
    http_response_code(400);
    die(json_encode(['error' => 'Username and password are required']));
}

try {
    $stmt = $conn->prepare("SELECT id, password, twofa_secret FROM Users WHERE username = ?");
    $stmt->execute([$data['username']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($data['password'], $user['password'])) {
        http_response_code(401);
        die(json_encode(['error' => 'Invalid credentials']));
    }

    $helper = new TwoFAHelper();

    // Handle 2FA verification
    if (!isset($data['twofa_code'])) {
        $qrResult = $helper->getQRCode($data['username'], $user['twofa_secret']);
        
        $response = [
            'success' => true,
            'message' => '2FA verification required',
            'manual_entry' => $qrResult['manual_entry'],
            'next_step' => 'verify_2fa'
        ];
        
        if ($qrResult['success']) {
            $response['qr_code'] = $qrResult['qr_code'];
        }
        
        echo json_encode($response);
        exit;
    }

    // Verify 2FA code
    if (!$helper->verifyCode($user['twofa_secret'], $data['twofa_code'])) {
        http_response_code(401);
        die(json_encode(['error' => 'Invalid 2FA code']));
    }

    // Generate JWT token (10 minute expiry)
    $token = generate_jwt($user['id'], 600);
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'token' => $token
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>