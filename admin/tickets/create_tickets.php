<?php
session_start();
require_once '../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../../login.php');
    exit;
}

if (!isset($_GET['match_id'])) {
    header('Location: ../matches/create_match.php');
    exit;
}

$match_id = $_GET['match_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_type = $_POST['ticket_type'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];

    try {
        $stmt = $pdo->prepare("INSERT INTO tickets (match_id, ticket_type, price, quantity_available, description) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$match_id, $ticket_type, $price, $quantity, $description]);
        
        $_SESSION['success'] = 'Tiket berhasil ditambahkan';
        header("Location: ../");
        exit;
    } catch (PDOException $e) {
        $error = "Gagal menambahkan tiket: " . $e->getMessage();
    }
}

// Get match info
$stmt = $pdo->prepare("SELECT * FROM matches WHERE id = ?");
$stmt->execute([$match_id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    header('Location: ../');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tiket Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include './includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">
            <i class="fas fa-plus-circle text-purple-500 mr-2"></i>Tambah Tiket Baru
        </h1>
        
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-2">Informasi Pertandingan</h2>
            <p><span class="font-medium">Pertandingan:</span> <?= htmlspecialchars($match['team_home']) ?> vs <?= htmlspecialchars($match['team_away']) ?></p>
            <p><span class="font-medium">Tanggal:</span> <?= (new DateTime($match['match_date']))->format('d M Y H:i') ?></p>
            <p><span class="font-medium">Stadion:</span> <?= htmlspecialchars($match['stadium']) ?></p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 rounded-lg shadow-md">
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
                <label class="block text-gray-700 mb-2" for="quantity">Jumlah Tiket</label>
                <input type="number" id="quantity" name="quantity" required min="1"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="description">Deskripsi</label>
                <textarea id="description" name="description" 
                          class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
            
            <div class="flex justify-end">
                <a href="../matches.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mr-2">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-save mr-2"></i>Simpan Tiket
                </button>
            </div>
        </form>
    </div>
</body>
</html>