<?php
// Database credentials (from your mysqli snippet)
$servername = "localhost";
$username   = "u601734414_sk_user";           // ← FULL username
$password   = "Federation2025";               // ← Your password
$database   = "u601734414_sk_federation";     // ← FULL database name

$dsn = "mysql:host=$servername;dbname=$database;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Always return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Optional: log success (uncomment if needed)
    // error_log("DB connected successfully");

} catch (PDOException $e) {
    // Log the real error to error_log (never expose in production)
    error_log("DB Error: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine());
    
    // User-friendly message (safe for production)
    die("Connection failed. Please try again later.");
}
?>