<?php
require_once 'JWT/JWTExceptionWithPayloadInterface.php'; // Load this FIRST
require_once 'JWT/BeforeValidException.php';
require_once 'JWT/ExpiredException.php';
require_once 'JWT/SignatureInvalidException.php';
require_once 'JWT/JWT.php';
require_once 'JWT/Key.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


// rest of your code...






// Retrieve the secret key from environment variables
$secret_key = getenv('JWT_SECRET_KEY') ?: 'f10632a7be2913df9b74827212fc182fadccde30fa6411facdff97895f4d441e6b4bfad5c32dcdeff1e11a1a805e202ffad826c2749760651c86fea8bb9c6e4c4729578c1b3d2b973f7c9658c34d251f3b3aff380541b0a8cfe428bad309885510c85b4e7178d12af6c4097599925272ab7217c1201c2313fc9d8d3576858a30f089d47c4fd84b607ced9daae2bde9c836f79e2e29ef145871e8a2cc826d47ac130a13c46314d10791dc615fc57f05db749d79cb92b906c724ed70534308323cb33bc87c36395915e965ec81126b59ff8fbca107b3e30f056c822a473784e80adf72beb62238b2ff8bde808b43e02a218003c6f5bfaaca8695ac9a6837d1d8e2';

/**
 * Generate a JWT token for a user
 */
function generate_jwt($user_id) {
    global $secret_key;
    $payload = [
        "iss" => "your_issuer",       // Issuer
        "aud" => "your_audience",     // Audience
        "iat" => time(),              // Issued at
        "exp" => time() + 600,        // Expiration time (10 minutes)
        "user_id" => $user_id         // Custom claim
    ];
    return \Firebase\JWT\JWT::encode($payload, $secret_key, 'HS256');


}
function validate_jwt($token) {
    global $secret_key;
    try {
        error_log("Token received for validation: $token"); // Debug log
        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
        error_log("Token decoded successfully. User ID: " . $decoded->user_id);
        return $decoded->user_id;
    } catch (Exception $e) {
        error_log("JWT decode error: " . $e->getMessage()); // Key debug log
        return false;
    }
}

?>