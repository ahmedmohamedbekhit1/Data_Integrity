<?php
// Database configuration
$host = 'localhost';       // Database host (usually 'localhost' for XAMPP)
$dbname = 'RESTful_API_QR';    // Database name
$username = 'root';        // Database username (default for XAMPP is 'root')
$password = '';            // Database password (default for XAMPP is empty)

try {
    // Create a PDO instance (database connection)
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Set PDO to throw exceptions on errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional: Set default fetch mode to associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Uncomment the following line to enable emulated prepared statements (if needed)
    // $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    echo "Database connection successful!"; // For debugging purposes (remove in production)
} catch (PDOException $e) {
    // Handle connection errors
    die("Database connection failed: " . $e->getMessage());
}
?>