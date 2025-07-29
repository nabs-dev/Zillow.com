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
    $stmt = $pdo->prepare("DELETE FROM saved_properties WHERE user_id = ? AND property_id = ?");
    $stmt->execute([$user_id, $property_id]);
    header('Location: saved-properties.php');
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
