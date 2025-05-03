<?php
session_start();
require_once '../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ticket_id = $_POST['ticket_id'];
        $match_id = $_POST['match_id'];
        $seat_row = $_POST['seat_row'];
        $seat_column = $_POST['seat_column'];
        $seat_number = $seat_row . $seat_column;

        // Validasi
        if (empty($seat_row) || empty($seat_column)) {
            throw new Exception("Baris dan kolom harus diisi");
        }

        // Cek apakah kursi sudah ada
        $checkStmt = $pdo->prepare("SELECT id FROM seats WHERE ticket_id = ? AND seat_number = ?");
        $checkStmt->execute([$ticket_id, $seat_number]);
        
        if ($checkStmt->rowCount() > 0) {
            throw new Exception("Kursi $seat_number sudah ada");
        }

        // Tambahkan kursi baru
        $stmt = $pdo->prepare("INSERT INTO seats (match_id, ticket_id, seat_number, seat_row, seat_column, status) 
                              VALUES (?, ?, ?, ?, ?, 'available')");
        $stmt->execute([$match_id, $ticket_id, $seat_number, $seat_row, $seat_column]);

        // Update jumlah kursi tersedia
        $updateStmt = $pdo->prepare("UPDATE tickets 
                                    SET quantity_available = quantity_available + 1 
                                    WHERE id = ?");
        $updateStmt->execute([$ticket_id]);

        $_SESSION['success'] = "Kursi $seat_number berhasil ditambahkan";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Redirect kembali
header("Location: ./index.php?ticket_id=$ticket_id");
exit();