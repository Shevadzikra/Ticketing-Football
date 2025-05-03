<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Validasi input
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Ambil data dari form
$user_id = $_SESSION['user_id'];
$match_id = $_POST['match_id'];
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$payment_method = $_POST['payment_method'];
$quantities = $_POST['quantity'];
$seats = $_POST['seats'] ?? [];

// Validasi data
if (empty($match_id) || empty($full_name) || empty($email) || empty($phone) || empty($payment_method)) {
    die("Data tidak lengkap");
}

// Hitung total amount
$total_amount = 0;
foreach ($quantities as $ticket_id => $quantity) {
    if ($quantity > 0) {
        $stmt = $pdo->prepare("SELECT price FROM tickets WHERE id = ?");
        $stmt->execute([$ticket_id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_amount += $ticket['price'] * $quantity;
    }
}

// Mulai transaksi
$pdo->beginTransaction();

try {
    // 1. Simpan data order
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            user_id, 
            order_date, 
            total_amount, 
            status, 
            payment_method,
            customer_name,
            customer_email,
            customer_phone
        ) VALUES (
            ?, 
            NOW(), 
            ?, 
            'completed', 
            ?,
            ?,
            ?,
            ?
        )
    ");
    $stmt->execute([
        $user_id,  // Using the logged-in user's ID
        $total_amount,
        $payment_method,
        $full_name,
        $email,
        $phone
    ]);
    $order_id = $pdo->lastInsertId();
    
    // 2. Simpan detail tiket dan update kursi
    foreach ($quantities as $ticket_id => $quantity) {
        if ($quantity > 0) {
            // Simpan detail tiket
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, ticket_id, quantity, price)
                SELECT ?, id, ?, price FROM tickets WHERE id = ?
            ");
            $stmt->execute([$order_id, $quantity, $ticket_id]);
            
            // Update status kursi jika ada
            if (isset($seats[$ticket_id]) && is_array($seats[$ticket_id])) {
                foreach ($seats[$ticket_id] as $seat_id) {
                    $stmt = $pdo->prepare("UPDATE seats SET status = 'booked' WHERE id = ?");
                    $stmt->execute([$seat_id]);
                }
            }
        }
    }
    
    // Commit transaksi
    $pdo->commit();
    
    // Redirect ke halaman konfirmasi
    header("Location: confirmation.php?order_id=$order_id");
    exit;
    
} catch (PDOException $e) {
    // Rollback jika ada error
    $pdo->rollBack();
    die("Error dalam pemesanan: " . $e->getMessage());
}