<?php
require_once 'jwt_auth.php';

$headers = apache_request_headers();

if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["message" => "Authorization header missing"]);
    exit();
}

$authHeader = $headers['Authorization'];
$token = str_replace("Bearer ", "", $authHeader);

error_log("Token received: " . $token); // DEBUG

$user_id = validate_jwt($token);
if (!$user_id) {
    error_log("Token validation failed."); // DEBUG
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

error_log("Token valid. User ID: $user_id"); // DEBUG

?>