<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$property_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("INSERT INTO saved_properties (user_id, property_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $property_id]);
    header('Location: dashboard.php');
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        header('Location: dashboard.php'); // Already saved
    } else {
        die("Error: " . $e->getMessage());
    }
}
?>
