<?php
// Database connection settings
$host = 'localhost';
$dbname = 'delicious_bites';
$username = 'root';
$password = '';

try {
    // Set up the PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // If connection fails, display the error message
    echo "Connection failed: " . $e->getMessage();
}
?>
