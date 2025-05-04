<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Ambil data orders dengan informasi pertandingan
$orders = $pdo->prepare("
    SELECT o.id, o.order_date, o.total_amount, o.status
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
    LIMIT 5
");
$orders->execute([$_SESSION['user_id']]);
$orders = $orders->fetchAll();

// Ambil data pertandingan untuk masing-masing order
foreach ($orders as &$order) {
    $stmt = $pdo->prepare("
        SELECT m.team_home, m.team_away, m.match_date, m.stadium
        FROM order_items oi
        JOIN tickets t ON oi.ticket_id = t.id
        JOIN matches m ON t.match_id = m.id
        WHERE oi.order_id = ?
        LIMIT 1
    ");
    $stmt->execute([$order['id']]);
    $match = $stmt->fetch();
    
    if ($match) {
        $order = array_merge($order, $match);
    }
}
unset($order); // Hapus reference terakhir
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-600 py-3 px-6">
            <h2 class="text-white text-2xl font-bold">Profil Pengguna</h2>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-2">Informasi Pribadi</h3>
                    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                    <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Telepon:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                    
                    <!-- <div class="mt-4">
                        <a href="edit_profile.php" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-edit mr-1"></i> Edit Profil
                        </a>
                    </div> -->
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-2">Riwayat Pemesanan</h3>
                    <?php if (!empty($orders)): ?>
                        <div class="space-y-3">
                            <?php foreach ($orders as $order): 
                                $match_date = isset($order['match_date']) ? new DateTime($order['match_date']) : null;
                                $order_date = new DateTime($order['order_date']);
                            ?>
                            <div class="border rounded-md p-3 hover:bg-gray-50 transition">
                                <a href="view_ticket.php?order_id=<?= $order['id'] ?>" class="block">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold">Order #<?= $order['id'] ?></p>
                                            <?php if (isset($order['team_home'])): ?>
                                                <p class="text-sm text-gray-600">
                                                    <?= htmlspecialchars($order['team_home']) ?> vs <?= htmlspecialchars($order['team_away']) ?>
                                                </p>
                                                <?php if ($match_date): ?>
                                                    <p class="text-sm text-gray-600">
                                                        <?= $match_date->format('d M Y H:i') ?> - <?= htmlspecialchars($order['stadium']) ?>
                                                    </p>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-green-600">
                                                Rp<?= number_format($order['total_amount'], 0, ',', '.') ?>
                                            </p>
                                            <p class="text-xs <?= 
                                                $order['status'] === 'completed' ? 'text-green-500' : 
                                                ($order['status'] === 'pending' ? 'text-yellow-500' : 'text-red-500')
                                            ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <?= $order_date->format('d M Y') ?>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- <div class="mt-3 text-center">
                            <a href="order_history.php" class="text-blue-500 hover:text-blue-700 text-sm">
                                Lihat Semua Riwayat <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div> -->
                    <?php else: ?>
                        <div class="text-center py-4 bg-gray-100 rounded-md">
                            <p class="text-gray-500">Belum ada riwayat pemesanan</p>
                            <a href="matches.php" class="text-blue-500 hover:text-blue-700 mt-2 inline-block">
                                <i class="fas fa-ticket-alt mr-1"></i> Pesan Tiket Sekarang
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>