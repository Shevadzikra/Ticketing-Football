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
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-2">Riwayat Pemesanan</h3>
                    <?php
                    $orders = $pdo->prepare("
                        SELECT o.id, o.order_date, o.total_amount, o.status 
                        FROM orders o 
                        WHERE o.user_id = ? 
                        ORDER BY o.order_date DESC
                        LIMIT 5
                    ");
                    $orders->execute([$_SESSION['user_id']]);
                    $orders = $orders->fetchAll();
                    
                    if ($orders): ?>
                        <ul class="space-y-2">
                            <?php foreach ($orders as $order): ?>
                                <li class="border-b pb-2">
                                    <a href="order_detail.php?id=<?= $order['id'] ?>" class="hover:underline">
                                        #<?= $order['id'] ?> - <?= number_format($order['total_amount'], 0, ',', '.') ?> - 
                                        <?= (new DateTime($order['order_date']))->format('d/m/Y') ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Belum ada riwayat pemesanan</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>