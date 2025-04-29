<?php
session_start();
require_once '../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ticket_id = $_POST['ticket_id'];
        $match_id = $_POST['match_id'];
        $seat_row = $_POST['seat_row'];
        $seat_column = $_POST['seat_column'];
        $seat_number = $seat_row . $seat_column;

        $stmt = $pdo->prepare("INSERT INTO seats (match_id, ticket_id, seat_number, seat_row, seat_column, status) 
                              VALUES (:match_id, :ticket_id, :seat_number, :seat_row, :seat_column, 'available')");
        $stmt->execute([
            ':match_id' => $match_id,
            ':ticket_id' => $ticket_id,
            ':seat_number' => $seat_number,
            ':seat_row' => $seat_row,
            ':seat_column' => $seat_column
        ]);
        
        $_SESSION['success'] = "Kursi berhasil ditambahkan";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Gagal menambahkan kursi: " . $e->getMessage();
    }
    
    header("Location: index.php?ticket_id=$ticket_id");
    exit();
}

header("Location: ./index.php");
exit();
?>