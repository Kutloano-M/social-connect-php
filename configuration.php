<?php
// ---------------------------------------------
// Configuration File
// Purpose: Connect to the database safely
// ---------------------------------------------

// Start the session (used to track user activity, like logins)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection details
$host    = '127.0.0.1';        // Database host (localhost)
$port    = '3306';             // Default MySQL port
$db      = 'connect_sphere';   // Database name
$user    = 'root';             // Database username
$pass    = '';                 // Database password (empty for XAMPP by default)
$charset = 'utf8mb4';          // Character set to support all common symbols

// Build the Data Source Name (DSN) string for PDO
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

// Define PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Fetch results as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Use real prepared statements (safer)
];

try {
    // Create PDO connection
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Stop script execution and show a readable error message
    die('Database Connection Failed: ' . htmlspecialchars($e->getMessage()));
}

// ---------------------------------------------
// Helper Function: Escape output for HTML safety
// ---------------------------------------------
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
