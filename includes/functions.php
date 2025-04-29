<?php
require_once __DIR__ . '/../config/database.php';

function getUpcomingMatches() {
    global $pdo;
    try {
        // Debug: Tampilkan waktu server
        $now = $pdo->query("SELECT NOW() AS current_time")->fetch();
        echo "Waktu server sekarang: " . $now['current_time'] . "<br>";
        
        $stmt = $pdo->query("SELECT * FROM matches WHERE match_date > CURDATE() ORDER BY match_date ASC");
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug: Tampilkan hasil query
        echo "Jumlah pertandingan ditemukan: " . count($matches) . "<br>";
        print_r($matches);
        
        return $matches;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

function getAvailableSeats($match_id, $ticket_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT id, seat_number, seat_row, seat_column 
            FROM seats 
            WHERE match_id = ? AND ticket_id = ? AND status = 'available'
            ORDER BY seat_row, seat_column
        ");
        $stmt->execute([$match_id, $ticket_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

function getMatchById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM matches WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getTicketsByMatch($match_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE match_id = ? AND quantity_available > 0");
    $stmt->execute([$match_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createOrder($user_id, $items, $total_amount, $payment_method) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, payment_method) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $total_amount, $payment_method]);
        $order_id = $pdo->lastInsertId();
        
        // Insert order items and update ticket availability
        foreach ($items as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, ticket_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['ticket_id'], $item['quantity'], $item['price']]);
            
            $stmt = $pdo->prepare("UPDATE tickets SET quantity_available = quantity_available - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['ticket_id']]);
        }
        
        $pdo->commit();
        return $order_id;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

// Fungsi admin
function getAllTickets() {
    global $pdo;
    $stmt = $pdo->query("SELECT t.*, m.team_home, m.team_away, m.match_date FROM tickets t JOIN matches m ON t.match_id = m.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addTicket($match_id, $ticket_type, $price, $quantity, $description) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO tickets (match_id, ticket_type, price, quantity_available, description) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$match_id, $ticket_type, $price, $quantity, $description]);
}

function updateTicket($id, $match_id, $ticket_type, $price, $quantity, $description) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE tickets SET match_id = ?, ticket_type = ?, price = ?, quantity_available = ?, description = ? WHERE id = ?");
    return $stmt->execute([$match_id, $ticket_type, $price, $quantity, $description, $id]);
}

function deleteTicket($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
    return $stmt->execute([$id]);
}
?>