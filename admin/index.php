<?php
session_start();
require_once '../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Hitung data untuk dashboard
$matches_count = $pdo->query("SELECT COUNT(*) FROM matches")->fetchColumn();
$tickets_count = $pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
$orders_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$revenue = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <?php include 'includes/admin_header.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Pertandingan</p>
                            <h3 class="text-2xl font-bold"><?= $matches_count ?></h3>
                        </div>
                    </div>
                </div>
                
                <!-- <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-ticket-alt text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Tiket Tersedia</p>
                            <h3 class="text-2xl font-bold"><?= $tickets_count ?></h3>
                        </div>
                    </div>
                </div> -->
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-receipt text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Pesanan</p>
                            <h3 class="text-2xl font-bold"><?= $orders_count ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow w-60">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-money-bill-wave text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Pendapatan</p>
                            <h3 class="text-2xl font-bold w-52">Rp <?= number_format($revenue, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Pesanan Terbaru</h2>
                    <a href="./orders/" class="text-blue-500 hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            $orders = $pdo->query("
                                SELECT o.id, o.customer_name, o.order_date, o.total_amount, o.status 
                                FROM orders o 
                                ORDER BY o.order_date DESC 
                                LIMIT 5
                            ")->fetchAll();
                            
                            foreach ($orders as $order): 
                                $order_date = new DateTime($order['order_date']);
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap"><?= $order['id'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $order_date->format('d M Y H:i') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>