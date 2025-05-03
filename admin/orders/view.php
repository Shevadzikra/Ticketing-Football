<?php
session_start();
require_once '../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['id'];

// Ambil data order
$stmt = $pdo->prepare("SELECT o.*, u.username 
                        FROM orders o
                        LEFT JOIN users u ON o.user_id = u.id
                        WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
    exit;
}

// Ambil item order (jika ada tabel order_items)
$items = [];
if ($pdo->query("SHOW TABLES LIKE 'order_items'")->rowCount() > 0) {
    $stmt = $pdo->prepare("SELECT oi.*, t.ticket_type 
                            FROM order_items oi
                            LEFT JOIN tickets t ON oi.ticket_id = t.id
                            WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Use fetchAll to get all rows
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Order #<?= $order['id'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <a href="index.php" class="text-blue-500 hover:text-blue-700 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold">Detail Order #<?= $order['id'] ?></h1>
            </div>
            <!-- <div>
                <a href="edit.php?id=<?= $order['id'] ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg mr-2">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="index.php?delete=<?= $order['id'] ?>" 
                   class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg"
                   onclick="return confirm('Apakah Anda yakin ingin menghapus order ini?')">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </a>
            </div> -->
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Informasi Order</h2>
                        <div class="space-y-2">
                            <p><span class="font-medium">Tanggal:</span> <?= date('d M Y H:i', strtotime($order['order_date'])) ?></p>
                            <p><span class="font-medium">Status:</span> 
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
                            </p>
                            <p><span class="font-medium">Total:</span> Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></p>
                            <p><span class="font-medium">Metode Pembayaran:</span> <?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></p>
                            <!-- <p><span class="font-medium">User:</span> <?= $order['username'] ?? 'Guest' ?></p> -->
                        </div>
                    </div>
                    
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Informasi Customer</h2>
                        <div class="space-y-2">
                            <p><span class="font-medium">Nama:</span> <?= htmlspecialchars($order['customer_name']) ?></p>
                            <p><span class="font-medium">Email:</span> <?= htmlspecialchars($order['customer_email']) ?></p>
                            <p><span class="font-medium">Telepon:</span> <?= htmlspecialchars($order['customer_phone']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Item Order -->
        <?php if (!empty($items)): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold">Item Order</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuantitas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($item['ticket_type']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $item['quantity'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp<?= number_format($item['quantity'] * $item['price'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>