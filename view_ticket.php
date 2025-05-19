<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once 'includes/header.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Ambil data order dasar
$stmt = $pdo->prepare("
    SELECT o.* 
    FROM orders o
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
    exit;
}

// Ambil data pertandingan terkait
$stmt = $pdo->prepare("
    SELECT m.team_home, m.team_away, m.match_date, m.stadium
    FROM order_items oi
    JOIN tickets t ON oi.ticket_id = t.id
    JOIN matches m ON t.match_id = m.id
    WHERE oi.order_id = ?
    LIMIT 1
");
$stmt->execute([$order_id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

// Gabungkan hasil
$order = array_merge($order, $match);

// Ambil items order dengan informasi seat jika ada
$stmt = $pdo->prepare("
    SELECT 
        oi.*, 
        t.ticket_type,
        s.seat_number, 
        s.seat_row, 
        s.seat_column
    FROM order_items oi
    JOIN tickets t ON oi.ticket_id = t.id
    LEFT JOIN seats s ON (s.ticket_id = t.id AND s.match_id = ?)
    WHERE oi.order_id = ?
");
$stmt->execute([$order['match_id'] ?? 0, $order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format tanggal pertandingan
$match_date = new DateTime($order['match_date']);
$formatted_date = $match_date->format('l, d F Y H:i');

// Data untuk QR code
$qr_data = json_encode([
    'order_id' => $order_id,
    'user_id' => $user_id,
    'match' => $order['team_home'] . ' vs ' . $order['team_away'],
    'date' => $formatted_date,
    'items' => array_map(function($item) {
        return [
            'ticket_type' => $item['ticket_type'],
            'seat' => $item['seat_number'] ?? 'General Admission'
        ];
    }, $items)
]);

// Generate QR code (pastikan library tersedia)
require_once __DIR__ . '/library/phpqrcode/qrlib.php';
$qr_temp_file = __DIR__ . '/temp/qr_' . $order_id . '.png';
QRcode::png($qr_data, $qr_temp_file, QR_ECLEVEL_L, 10);
$qr_image = 'temp/qr_' . $order_id . '.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket</title>
</head>
<body>   
    <div class="container mx-auto px-4 py-8">
        <!-- ... kode HTML Anda yang sudah ada ... -->
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-green-600 py-3 px-6">
                    <h2 class="text-white text-2xl font-bold">E-Ticket</h2>
                    <p class="text-white">Order #<?= $order_id ?></p>
                </div>
                
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- QR Code Section -->
                        <div class="md:w-1/3 flex flex-col items-center">
                            <div class="border-2 border-green-500 p-2 mb-4">
                                <img src="<?= $qr_image ?>" alt="QR Code" class="w-full h-auto">
                            </div>
                            <p class="text-sm text-gray-600 text-center">
                                Scan QR code ini saat masuk stadion
                            </p>
                        </div>
                        
                        <!-- Ticket Details -->
                        <div class="md:w-2/3">
                            <div class="mb-6">
                                <h3 class="text-xl font-semibold mb-2">Detail Pertandingan</h3>
                                <div class="bg-gray-100 p-4 rounded-lg">
                                    <p class="font-bold text-lg"><?= htmlspecialchars($order['team_home']) ?> vs <?= htmlspecialchars($order['team_away']) ?></p>
                                    <p><?= $formatted_date ?></p>
                                    <p><?= htmlspecialchars($order['stadium']) ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h3 class="text-xl font-semibold mb-2">Detail Tiket</h3>
                                <div class="space-y-4">
                                    <?php foreach ($items as $item): ?>
                                    <div class="border rounded-md p-4 hover:bg-gray-50">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-bold"><?= htmlspecialchars($item['ticket_type']) ?></p>
                                                <p>Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                                                <?php if ($item['seat_number']): ?>
                                                    <p class="text-sm mt-2">
                                                        <span class="font-medium">Kursi:</span> 
                                                        <?= htmlspecialchars($item['seat_number']) ?> 
                                                        (Baris <?= htmlspecialchars($item['seat_row']) ?>)
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-2">Instruksi</h3>
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Tunjukkan QR code ini di pintu masuk</li>
                                    <li>QR code hanya berlaku satu kali scan</li>
                                    <li>Simpan bukti ini untuk keperluan refund</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 text-center space-x-4">
                        <!-- <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-md transition duration-300">
                            Cetak Tiket
                        </button> -->
                        <a href="./" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-md transition duration-300">
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>
<!-- Tampilan HTML tetap sama seperti sebelumnya -->