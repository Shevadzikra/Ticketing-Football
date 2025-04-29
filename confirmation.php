<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $match_id = $_POST['match_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $payment_method = $_POST['payment_method'];
    $quantities = $_POST['quantity'] ?? [];
    $seats = $_POST['seats'] ?? [];

    // Hitung total dan siapkan items
    $total_amount = 0;
    $items = [];
    $ticket_updates = []; // Untuk menyimpan jumlah pengurangan per tiket
    
    foreach ($quantities as $ticket_id => $quantity) {
        if ($quantity > 0) {
            // Ambil detail tiket
            $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
            $stmt->execute([$ticket_id]);
            $ticket = $stmt->fetch();
            
            $seat_ids = $seats[$ticket_id] ?? [];
            
            for ($i = 0; $i < $quantity; $i++) {
                $seat_id = $seat_ids[$i] ?? null;
                $items[] = [
                    'ticket_id' => $ticket_id,
                    'ticket_type' => $ticket['ticket_type'],
                    'ticket_price' => $ticket['price'],
                    'seat_id' => $seat_id,
                    'quantity' => 1
                ];
                $total_amount += $ticket['price'];
            }
            
            // Simpan jumlah pengurangan tiket
            if (!isset($ticket_updates[$ticket_id])) {
                $ticket_updates[$ticket_id] = 0;
            }
            $ticket_updates[$ticket_id] += $quantity;
        }
    }

    try {
        $pdo->beginTransaction();
        
        // 1. Simpan order utama
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, payment_method, customer_name, customer_email, customer_phone) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $total_amount,
            $payment_method,
            $full_name,
            $email,
            $phone
        ]);
        $order_id = $pdo->lastInsertId();
        
        // 2. Simpan order items
        foreach ($items as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items 
                (order_id, ticket_id, seat_id, price, quantity) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $order_id,
                $item['ticket_id'],
                $item['seat_id'],
                $item['ticket_price'],
                $item['quantity']
            ]);
            
            // 3. Update status kursi jika ada
            if ($item['seat_id']) {
                $stmt = $pdo->prepare("UPDATE seats SET status = 'booked' WHERE id = ?");
                $stmt->execute([$item['seat_id']]);
            }
        }
        
        // 4. Update quantity_available di tabel tickets
        foreach ($ticket_updates as $ticket_id => $quantity) {
            $stmt = $pdo->prepare("
                UPDATE tickets 
                SET quantity_available = quantity_available - ? 
                WHERE id = ? AND quantity_available >= ?
            ");
            $stmt->execute([$quantity, $ticket_id, $quantity]);
        }
        
        $pdo->commit();
        
        // Fungsi untuk ambil detail kursi
        function getSeatDetails($pdo, $seat_id) {
            if (!$seat_id) return null;
            $stmt = $pdo->prepare("
                SELECT s.*, t.ticket_type 
                FROM seats s
                JOIN tickets t ON s.ticket_id = t.id
                WHERE s.id = ?
            ");
            $stmt->execute([$seat_id]);
            return $stmt->fetch();
        }
        ?>
        
        <!-- Tampilan konfirmasi sukses -->
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-green-600 py-3 px-6">
                    <h2 class="text-white text-2xl font-bold">Pemesanan Berhasil</h2>
                    <p class="text-white">Nomor Order: <?= $order_id ?></p>
                </div>
                
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold mb-4">Detail Pembeli</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p><strong>Nama:</strong> <?= htmlspecialchars($full_name) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
                            </div>
                            <div>
                                <p><strong>Telepon:</strong> <?= htmlspecialchars($phone) ?></p>
                                <p><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($payment_method) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold mb-4">Rincian Pembayaran</h3>
                        <div class="border rounded-md p-4 bg-gray-50">
                            <p class="text-lg font-bold text-right">Total: Rp <?= number_format($total_amount, 0, ',', '.') ?></p>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold mb-4">Detail Tiket</h3>
                        <div class="space-y-4">
                            <?php foreach ($items as $item): 
                                $seat = $item['seat_id'] ? getSeatDetails($pdo, $item['seat_id']) : null;
                            ?>
                            <div class="border rounded-md p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-bold"><?= htmlspecialchars($item['ticket_type']) ?></p>
                                        <p>Rp <?= number_format($item['ticket_price'], 0, ',', '.') ?></p>
                                        <?php if ($seat): ?>
                                            <p class="text-sm mt-2">
                                                <span class="font-medium">Kursi:</span> 
                                                <?= htmlspecialchars($seat['seat_number']) ?> 
                                                (Baris <?= htmlspecialchars($seat['seat_row']) ?>)
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="bg-blue-100 px-3 py-1 rounded-full text-sm font-medium">
                                        Qty: 1
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="text-center space-x-4">
                        <a href="print_ticket.php?order_id=<?= $order_id ?>" 
                           class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-md transition duration-300">
                            Cetak Tiket
                        </a>
                        <a href="profile.php" 
                           class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-md transition duration-300">
                            Lihat Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        ?>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-red-600 py-3 px-6">
                    <h2 class="text-white text-2xl font-bold">Gagal Memproses Pesanan</h2>
                </div>
                <div class="p-6">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <p class="font-bold">Error:</p>
                        <p><?= htmlspecialchars($e->getMessage()) ?></p>
                    </div>
                    <div class="text-center">
                        <a href="booking.php?match_id=<?= $match_id ?>" 
                           class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-md transition duration-300">
                            Kembali ke Halaman Booking
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    header('Location: index.php');
    exit;
}

require_once 'includes/footer.php';