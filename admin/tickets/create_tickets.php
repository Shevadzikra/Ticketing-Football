<?php
session_start();
require_once '../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $match_id = $_POST['match_id'];
        $ticket_type = $_POST['ticket_type'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $quantity = $_POST['quantity'];

        $stmt = $pdo->prepare("INSERT INTO tickets (match_id, ticket_type, price, description, quantity_available) 
                              VALUES (:match_id, :ticket_type, :price, :description, :quantity)");
        $stmt->execute([
            ':match_id' => $match_id,
            ':ticket_type' => $ticket_type,
            ':price' => $price,
            ':description' => $description,
            ':quantity' => $quantity
        ]);
        
        $_SESSION['success'] = "Tiket berhasil dibuat";
        header("Location: tickets.php");
        exit();
    } catch (PDOException $e) {
        $error = "Gagal membuat tiket: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tiket Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Buat Tiket Baru</h1>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="match_id">ID Pertandingan</label>
                <input type="number" id="match_id" name="match_id" required 
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="ticket_type">Jenis Tiket</label>
                <input type="text" id="ticket_type" name="ticket_type" required 
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="price">Harga</label>
                <input type="number" step="0.01" id="price" name="price" required 
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="description">Deskripsi</label>
                <textarea id="description" name="description" 
                          class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="quantity">Jumlah Tersedia</label>
                <input type="number" id="quantity" name="quantity" required 
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    Buat Tiket
                </button>
                <a href="./" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
</body>
</html>