<?php
session_start();
require_once '../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ./");
    exit();
}

$ticket_id = $_GET['id'];

try {
    // Delete ticket (seats will be deleted automatically due to ON DELETE CASCADE)
    $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->execute([$ticket_id]);
    
    $_SESSION['success'] = "Tiket berhasil dihapus";
} catch (PDOException $e) {
    $_SESSION['error'] = "Gagal menghapus tiket: " . $e->getMessage();
}

header("Location: ./");
exit();
?>