<?php
// Database configuration
$db_host = 'localhost'; // Your database host (usually localhost for shared hosting)
$db_name = 'your_database_name'; // Replace with your database name
$db_user = 'your_database_user'; // Replace with your database username
$db_pass = 'your_database_password'; // Replace with your database password

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Log the error (in production, log to a file instead of showing to user)
    error_log("Database Connection Error: " . $e->getMessage());
    die("Connection failed. Please try again later.");
}
?>
