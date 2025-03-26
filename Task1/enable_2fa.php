<?php
require 'db_connection.php';
require 'jwt_middleware.php';
require 'twofa_helper.php';

header('Content-Type: application/json');

$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input, true);

if (!isset($data['action'])) {
    http_response_code(400);
    die(json_encode(["message" => "Missing action parameter"]));
}

try {
    $enable = ($data['action'] === 'enable');
    $stmt = $conn->prepare("UPDATE Users SET twofa_enabled = ? WHERE id = ?");
    $stmt->bindParam(1, $enable, PDO::PARAM_BOOL);
    $stmt->bindParam(2, $user_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        if ($enable) {
            // Get current secret to return QR code
            $stmt = $conn->prepare("SELECT twofa_secret FROM Users WHERE id = ?");
            $stmt->bindParam(1, $user_id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $twofaHelper = new TwoFAHelper();
            $qrCode = $twofaHelper->getQRCode($user_id, $user['twofa_secret']);
            
            echo json_encode([
                "message" => "2FA enabled successfully",
                "qr_code" => $qrCode
            ]);
        } else {
            echo json_encode(["message" => "2FA disabled successfully"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to update 2FA setting"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
?>