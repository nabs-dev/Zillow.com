<?php
$host = 'localhost';
$dbname = 'dbg76scobon6ym';
$username = 'u8gr0sjr9p4p4';
$password = '9yxuqyo3mt85';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
