<?php
session_start();
require_once '../../config/database.php';

// Check admin auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: ./');
    exit;
}

$user_id = $_GET['id'];

// Get user details
$stmt = $pdo->prepare("SELECT id, username, email, full_name, phone 
                      FROM users 
                      WHERE id = ? AND is_admin = 0");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ./');
    exit;
}

// Get user orders
$orders = $pdo->prepare("SELECT id, order_date, total_amount, status, payment_method 
                        FROM orders 
                        WHERE user_id = ? 
                        ORDER BY order_date DESC");
$orders->execute([$user_id]);
$orders = $orders->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Orders - <?= htmlspecialchars($user['username']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include './includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <a href="index.php" class="text-blue-500 hover:text-blue-700 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold">Transaksi oleh: <?= htmlspecialchars($user['full_name']) ?></h1>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4">Informasi Pengguna</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p><span class="font-medium">Username:</span> <?= htmlspecialchars($user['username']) ?></p>
                        <p><span class="font-medium">Email:</span> <?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    <div>
                        <p><span class="font-medium">Nama Lengkap:</span> <?= htmlspecialchars($user['full_name']) ?></p>
                        <p><span class="font-medium">Telepon:</span> <?= htmlspecialchars($user['phone']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold">Riwayat Transaksi</h2>
            </div>
            <?php if (empty($orders)): ?>
                <div class="p-6 text-center text-gray-500">
                    Tidak ada transaksi dari pengguna ini.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">#<?= $order['id'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= date('d M Y H:i', strtotime($order['order_date'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $status_classes = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'paid' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                    ?>
                                    <span class="px-2 py-1 text-xs rounded-full <?= $status_classes[$order['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="../orders/view.php?id=<?= $order['id'] ?>" class="text-blue-500 hover:text-blue-700" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>