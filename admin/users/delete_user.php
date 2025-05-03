<?php
session_start();
require_once '../../config/database.php';

// Check admin auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_GET['id'];

// Check if user exists and is not admin
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND is_admin = 0");
$stmt->execute([$user_id]);
if (!$stmt->fetch()) {
    header('Location: index.php');
    exit;
}

// Delete user
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

header('Location: index.php');
exit;
?>