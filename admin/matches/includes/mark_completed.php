<?php
session_start();
require_once '../../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: ../');
    exit;
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("UPDATE matches SET is_completed = 1 WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['success'] = 'Pertandingan berhasil ditandai sebagai selesai';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Gagal menandai pertandingan: ' . $e->getMessage();
}

header('Location: ../');
exit;
?>