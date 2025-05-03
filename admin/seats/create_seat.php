<?php
session_start();
require_once '../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../../login.php');
    exit;
}

if (!isset($_GET['match_id'])) {
    header('Location: ../matches/');
    exit;
}

$match_id = $_GET['match_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ticket_type = $_POST['ticket_type'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $description = $_POST['description'];
        
        // Validasi input
        if (empty($ticket_type) || empty($price) || empty($quantity)) {
            throw new Exception("Semua field harus diisi");
        }
        
        if ($quantity <= 0) {
            throw new Exception("Jumlah kursi harus lebih dari 0");
        }

        // Mulai transaksi
        $pdo->beginTransaction();

        // 1. Buat tiket baru
        $stmt = $pdo->prepare("INSERT INTO tickets (match_id, ticket_type, price, quantity_available, description) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$match_id, $ticket_type, $price, $quantity, $description]);
        $ticket_id = $pdo->lastInsertId();

        // 2. Generate kursi secara otomatis
        $rowLetters = range('A', 'Z');
        $seatsCreated = 0;
        $rowsNeeded = ceil($quantity / 20); // Misal 20 kursi per baris
        
        for ($row = 0; $row < $rowsNeeded && $seatsCreated < $quantity; $row++) {
            for ($col = 1; $col <= 20 && $seatsCreated < $quantity; $col++) {
                $seat_row = $rowLetters[$row];
                $seat_column = $col;
                $seat_number = $seat_row . $col;
                
                $stmt = $pdo->prepare("INSERT INTO seats (match_id, ticket_id, seat_number, seat_row, seat_column, status) 
                                      VALUES (?, ?, ?, ?, ?, 'available')");
                $stmt->execute([$match_id, $ticket_id, $seat_number, $seat_row, $seat_column]);
                $seatsCreated++;
            }
        }

        $pdo->commit();
        
        $_SESSION['success'] = "Tiket dan kursi berhasil dibuat";
        header("Location: index.php?ticket_id=$ticket_id");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tiket & Kursi Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center mb-6">
            <a href="../matches/" class="text-blue-500 hover:text-blue-700 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold">Buat Tiket & Kursi Baru</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="ticket_type">Nama Tiket/Tipe</label>
                <input type="text" id="ticket_type" name="ticket_type" required
                        class="w-full px-3 py-2 border rounded-lg" placeholder="Contoh: VIP, Reguler, Tribun Utara">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="price">Harga Tiket (Rp)</label>
                <input type="number" id="price" name="price" required min="0"
                        class="w-full px-3 py-2 border rounded-lg" placeholder="Contoh: 150000">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="quantity">Jumlah Total Kursi</label>
                <input type="number" id="quantity" name="quantity" required min="1"
                        class="w-full px-3 py-2 border rounded-lg" placeholder="Contoh: 100">
                <p class="text-sm text-gray-500 mt-1">* Sistem akan membuat kursi secara otomatis</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="description">Deskripsi</label>
                <input type="text" id="description" name="description" required min="0"
                        class="w-full px-3 py-2 border rounded-lg" placeholder="Masukkan deskripsi tiket">
            </div>
            <div class="flex justify-end">
                <a href="../matches/" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mr-2">
                    Batal
                </a>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-save mr-2"></i>Simpan Tiket & Kursi
                </button>
            </div>
        </form>
    </div>
</body>
</html>