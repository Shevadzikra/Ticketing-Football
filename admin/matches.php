<?php
session_start();
require_once '../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_match'])) {
        $team_home = $_POST['team_home'];
        $team_away = $_POST['team_away'];
        $match_date = $_POST['match_date'];
        $stadium = $_POST['stadium'];
        $description = $_POST['description'];
        
        $stmt = $pdo->prepare("INSERT INTO matches (team_home, team_away, match_date, stadium, description) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$team_home, $team_away, $match_date, $stadium, $description])) {
            $_SESSION['success'] = 'Pertandingan berhasil ditambahkan';
        } else {
            $_SESSION['error'] = 'Gagal menambahkan pertandingan';
        }
    } elseif (isset($_POST['update_match'])) {
        $id = $_POST['id'];
        $team_home = $_POST['team_home'];
        $team_away = $_POST['team_away'];
        $match_date = $_POST['match_date'];
        $stadium = $_POST['stadium'];
        $description = $_POST['description'];
        
        $stmt = $pdo->prepare("UPDATE matches SET team_home = ?, team_away = ?, match_date = ?, stadium = ?, description = ? WHERE id = ?");
        if ($stmt->execute([$team_home, $team_away, $match_date, $stadium, $description, $id])) {
            $_SESSION['success'] = 'Pertandingan berhasil diperbarui';
        } else {
            $_SESSION['error'] = 'Gagal memperbarui pertandingan';
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $stmt = $pdo->prepare("DELETE FROM matches WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success'] = 'Pertandingan berhasil dihapus';
    } else {
        $_SESSION['error'] = 'Gagal menghapus pertandingan';
    }
    header('Location: matches.php');
    exit;
}

$matches = $pdo->query("SELECT * FROM matches ORDER BY match_date DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pertandingan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Kelola Pertandingan</h1>
            <button onclick="document.getElementById('addMatchModal').classList.remove('hidden')" 
                    class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md">
                <i class="fas fa-plus mr-2"></i> Tambah Pertandingan
            </button>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertandingan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stadion</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($matches as $match): 
                            $match_date = new DateTime($match['match_date']);
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold"><?= htmlspecialchars($match['team_home']) ?> vs <?= htmlspecialchars($match['team_away']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($match['description']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= $match_date->format('d M Y H:i') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= htmlspecialchars($match['stadium']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="openEditModal(<?= htmlspecialchars(json_encode($match)) ?>)" 
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="matches.php?delete=<?= $match['id'] ?>" 
                                   class="text-red-600 hover:text-red-900" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus pertandingan ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add Match Modal -->
    <div id="addMatchModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Tambah Pertandingan Baru</h3>
                <button onclick="document.getElementById('addMatchModal').classList.add('hidden')" 
                        class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Tim Home</label>
                    <input type="text" name="team_home" required class="w-full border rounded-md py-2 px-3">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Tim Away</label>
                    <input type="text" name="team_away" required class="w-full border rounded-md py-2 px-3">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Tanggal & Waktu</label>
                    <input type="datetime-local" name="match_date" required class="w-full border rounded-md py-2 px-3">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Stadion</label>
                    <input type="text" name="stadium" required class="w-full border rounded-md py-2 px-3">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" class="w-full border rounded-md py-2 px-3"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="document.getElementById('addMatchModal').classList.add('hidden')" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-md mr-2">
                        Batal
                    </button>
                    <button type="submit" name="add_match" 
                            class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Match Modal -->
    <div id="editMatchModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Edit Pertandingan</h3>
                <button onclick="document.getElementById('editMatchModal').classList.add('hidden')" 
                        class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Tim Home</label>
                    <input type="text" name="team_home" id="edit_team_home" required class="w-full border rounded-md py-2 px-3">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Tim Away</label>
                    <input type="text" name="team_away" id="edit_team_away" required class="w-full border rounded-md py-2 px-3">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Tanggal & Waktu</label>
                    <input type="datetime-local" name="match_date" id="edit_match_date" required class="w-full border rounded-md py-2 px-3">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Stadion</label>
                    <input type="text" name="stadium" id="edit_stadium" required class="w-full border rounded-md py-2 px-3">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" id="edit_description" class="w-full border rounded-md py-2 px-3"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="document.getElementById('editMatchModal').classList.add('hidden')" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-md mr-2">
                        Batal
                    </button>
                    <button type="submit" name="update_match" 
                            class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openEditModal(match) {
            document.getElementById('edit_id').value = match.id;
            document.getElementById('edit_team_home').value = match.team_home;
            document.getElementById('edit_team_away').value = match.team_away;
            
            // Format datetime-local
            const matchDate = new Date(match.match_date);
            const timezoneOffset = matchDate.getTimezoneOffset() * 60000;
            const localISOTime = new Date(matchDate - timezoneOffset).toISOString().slice(0, 16);
            
            document.getElementById('edit_match_date').value = localISOTime;
            document.getElementById('edit_stadium').value = match.stadium;
            document.getElementById('edit_description').value = match.description;
            
            document.getElementById('editMatchModal').classList.remove('hidden');
        }
    </script>
</body>
</html>