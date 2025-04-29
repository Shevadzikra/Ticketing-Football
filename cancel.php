<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

if (!isset($_GET['order_id']) || !isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['order_id'];
$order = getOrderById($order_id);

// Pastikan order milik user yang login
if ($order['user_id'] != $_SESSION['user_id']) {
    header('Location: index.php');
    exit;
}

// Proses pembatalan
if (isset($_POST['confirm_cancel'])) {
    cancelOrder($order_id);
    ?>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-green-600 py-3 px-6">
                <h2 class="text-white text-2xl font-bold">Pembatalan Berhasil</h2>
            </div>
            <div class="p-6 text-center">
                <p class="mb-6">Pesanan #<?= $order_id ?> telah berhasil dibatalkan.</p>
                <a href="index.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
    <?php
    require_once 'includes/footer.php';
    exit;
}

// Tampilkan konfirmasi pembatalan
?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-red-600 py-3 px-6">
            <h2 class="text-white text-2xl font-bold">Konfirmasi Pembatalan</h2>
        </div>
        <div class="p-6">
            <p class="mb-4">Anda yakin ingin membatalkan pesanan #<?= $order_id ?>?</p>
            
            <form method="post">
                <div class="flex justify-center space-x-4">
                    <a href="profile.php" class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                        Kembali
                    </a>
                    <button type="submit" name="confirm_cancel" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                        Ya, Batalkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>