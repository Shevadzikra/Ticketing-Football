<?php
session_start();
require_once '../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: ./');
    exit;
}

$id = $_GET['id'];

// Ambil data pertandingan
$stmt = $pdo->prepare("SELECT * FROM matches WHERE id = ?");
$stmt->execute([$id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    header('Location: ./');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_home = $_POST['team_home'];
    $team_away = $_POST['team_away'];
    $match_date = $_POST['match_date'];
    $stadium = $_POST['stadium'];
    $description = $_POST['description'];
    
    try {
        $stmt = $pdo->prepare("UPDATE matches 
                               SET team_home = ?, team_away = ?, match_date = ?, stadium = ?, description = ?
                               WHERE id = ?");
        $stmt->execute([$team_home, $team_away, $match_date, $stadium, $description, $id]);
        
        $_SESSION['success'] = 'Pertandingan berhasil diperbarui';
        header("Location: ./");
        exit;
    } catch (PDOException $e) {
        $error = 'Gagal memperbarui pertandingan: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pertandingan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center mb-6">
            <a href="./" class="text-blue-500 hover:text-blue-700 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold">Edit Pertandingan</h1>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="team_home">Tim Home</label>
                <input type="text" id="team_home" name="team_home" required 
                       value="<?= htmlspecialchars($match['team_home']) ?>"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="team_away">Tim Away</label>
                <input type="text" id="team_away" name="team_away" required 
                       value="<?= htmlspecialchars($match['team_away']) ?>"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="match_date">Tanggal & Waktu</label>
                <?php
                    $match_date = new DateTime($match['match_date']);
                    $formatted_date = $match_date->format('Y-m-d\TH:i');
                ?>
                <input type="datetime-local" id="match_date" name="match_date" required 
                       value="<?= $formatted_date ?>"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="stadium">Stadion</label>
                <input type="text" id="stadium" name="stadium" required 
                       value="<?= htmlspecialchars($match['stadium']) ?>"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="description">Deskripsi</label>
                <textarea id="description" name="description" 
                          class="w-full px-3 py-2 border rounded-lg"><?= htmlspecialchars($match['description']) ?></textarea>
            </div>
            
            <div class="flex justify-end">
                <a href="./" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mr-2">
                    Batal
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Perbarui Pertandingan
                </button>
            </div>
        </form>
    </div>
</body>
</html>