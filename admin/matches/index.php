<?php
session_start();
require_once '../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM matches WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = 'Pertandingan berhasil dihapus';
        header("Location: ./");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus pertandingan: ' . $e->getMessage();
        header("Location: ./");
        exit;
    }
}

// Query untuk mendapatkan pertandingan
$upcoming_matches = $pdo->query("
    SELECT m.*, 
           (SELECT COUNT(*) FROM tickets t WHERE t.match_id = m.id AND t.quantity_available > 0) AS has_available_tickets
    FROM matches m 
    WHERE m.is_completed = 0 
    ORDER BY m.match_date ASC
")->fetchAll();

$sold_out_matches = $pdo->query("
    SELECT m.*
    FROM matches m
    WHERE m.is_completed = 0 
    AND NOT EXISTS (
        SELECT 1 FROM tickets t 
        WHERE t.match_id = m.id AND t.quantity_available > 0
    )
    ORDER BY m.match_date ASC
")->fetchAll();

$completed_matches = $pdo->query("
    SELECT * FROM matches 
    WHERE is_completed = 1 
    ORDER BY match_date DESC
")->fetchAll();
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
    <?php include './includes/admin_header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Kelola Pertandingan</h1>
            <a href="./create_match.php" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md">
                Tambah Pertandingan
            </a>
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
        <!-- Tab Navigation -->
        <div class="mb-6 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px" id="matchTabs" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" 
                            id="upcoming-tab" data-tabs-target="#upcoming" type="button" role="tab" 
                            aria-controls="upcoming" aria-selected="true">
                        <i class="fas fa-ticket-alt mr-2"></i>Pertandingan Mendatang
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                            id="soldout-tab" data-tabs-target="#soldout" type="button" role="tab" 
                            aria-controls="soldout" aria-selected="false">
                        <i class="fas fa-times-circle mr-2"></i>Tiket Tidak Tersedia
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                            id="completed-tab" data-tabs-target="#completed" type="button" role="tab" 
                            aria-controls="completed" aria-selected="false">
                        <i class="fas fa-check-circle mr-2"></i>Selesai
                    </button>
                </li>
            </ul>
        </div>
        
        <!-- Upcoming Matches Tab -->
        <div class="hidden p-4 rounded-lg bg-white" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
            <h2 class="text-xl font-semibold mb-4">
                <i class="fas fa-ticket-alt text-blue-500 mr-2"></i>Pertandingan Mendatang (Tiket Tersedia)
            </h2>
            
            <?php if (empty(array_filter($upcoming_matches, function($m) { return $m['has_available_tickets'] > 0; }))): ?>
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                    <i class="fas fa-info-circle mr-2"></i>Tidak ada pertandingan mendatang dengan tiket tersedia.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertandingan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stadion</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Tiket</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($upcoming_matches as $match): 
                                if ($match['has_available_tickets'] == 0) continue;
                                $match_date = new DateTime($match['match_date']);
                                $now = new DateTime();
                                $is_past = $match_date < $now;
                            ?>
                            <tr class="<?= $is_past ? 'bg-yellow-50' : '' ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold"><?= htmlspecialchars($match['team_home']) ?> vs <?= htmlspecialchars($match['team_away']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($match['description']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= $match_date->format('d M Y H:i') ?>
                                    <?php if ($is_past): ?>
                                        <span class="ml-2 px-2 py-1 text-xs bg-yellow-200 text-yellow-800 rounded">Lewat</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= htmlspecialchars($match['stadium']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs bg-green-200 text-green-800 rounded">
                                        <i class="fas fa-check mr-1"></i>Tiket Tersedia
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="edit_match.php?id=<?= $match['id'] ?>">
                                        <button
                                        onclick="openEditModal(<?= htmlspecialchars(json_encode($match)) ?>)" 
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </a>
                                    <a href="index.php?delete=<?= $match['id'] ?>" 
                                       class="text-red-600 hover:text-red-900" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus pertandingan ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <a href="./includes/mark_completed.php?id=<?= $match['id'] ?>" 
                                       class="text-green-600 hover:text-green-900 ml-3"
                                       onclick="return confirm('Tandai pertandingan ini sebagai selesai?')">
                                        <i class="fas fa-check"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sold Out Matches Tab -->
        <div class="hidden p-4 rounded-lg bg-white" id="soldout" role="tabpanel" aria-labelledby="soldout-tab">
            <h2 class="text-xl font-semibold mb-4">
                <i class="fas fa-times-circle text-red-500 mr-2"></i>Pertandingan dengan Tiket Habis
            </h2>
            
            <?php if (empty($sold_out_matches)): ?>
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                    <i class="fas fa-info-circle mr-2"></i>Tidak ada pertandingan dengan tiket habis.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertandingan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stadion</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Tiket</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($sold_out_matches as $match): 
                                $match_date = new DateTime($match['match_date']);
                                $now = new DateTime();
                                $is_past = $match_date < $now;

                                $nama_pertandingan = htmlspecialchars($match['team_home']) ." vs " . htmlspecialchars($match['team_away']);
                            ?>
                            
                            <tr class="<?= $is_past ? 'bg-yellow-50' : '' ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold"><?= $nama_pertandingan ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($match['description']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= $match_date->format('d M Y H:i') ?>
                                    <?php if ($is_past): ?>
                                        <span class="ml-2 px-2 py-1 text-xs bg-yellow-200 text-yellow-800 rounded">Lewat</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= htmlspecialchars($match['stadium']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs bg-red-200 text-red-800 rounded">
                                        <i class="fas fa-times mr-1"></i>Tiket Tidak Tersedia
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="edit_match.php?id=<?= $match['id'] ?>">
                                        <button onclick="openEditModal(<?= htmlspecialchars(json_encode($match)) ?>)" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </a>
                                    <a href="index.php?delete=<?= $match['id'] ?>" 
                                       class="text-red-600 hover:text-red-900" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus pertandingan ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <a href="./includes/mark_completed.php?id=<?= $match['id'] ?>" 
                                       class="text-green-600 hover:text-green-900 ml-3"
                                       onclick="return confirm('Tandai pertandingan ini sebagai selesai?')">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="../seats/create_seat.php?match_id=<?= $match['id'] ?>&np= <?= $nama_pertandingan ?>" 
                                        class="text-purple-600 hover:text-purple-900 ml-3"
                                        title="Kelola Kursi">
                                            <i class="fas fa-chair"></i> Kelola Kursi
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Completed Matches Tab -->
        <div class="hidden p-4 rounded-lg bg-white" id="completed" role="tabpanel" aria-labelledby="completed-tab">
            <h2 class="text-xl font-semibold mb-4">Pertandingan Selesai</h2>
            
            <?php if (empty($completed_matches)): ?>
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                    Tidak ada pertandingan yang selesai.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertandingan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stadion</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($completed_matches as $match): 
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
                                    <span class="px-2 py-1 text-xs bg-green-200 text-green-800 rounded">Selesai</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="edit_match.php?id=<?= $match['id'] ?>">
                                        <button onclick="openEditModal(<?= htmlspecialchars(json_encode($match)) ?>)" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </a>
                                    <a href="index.php?delete=<?= $match['id'] ?>" 
                                       class="text-red-600 hover:text-red-900" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus pertandingan ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <a href="./includes/mark_uncompleted.php?id=<?= $match['id'] ?>" 
                                       class="text-yellow-600 hover:text-yellow-900 ml-3"
                                       onclick="return confirm('Tandai pertandingan ini sebagai belum selesai?')">
                                        <i class="fas fa-undo"></i>
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
    
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Show first tab by default
            document.querySelector('[data-tabs-target="#upcoming"]').classList.add('border-blue-500', 'text-blue-600');
            document.getElementById('upcoming').classList.remove('hidden');
            
            // Tab click handlers
            document.querySelectorAll('[data-tabs-target]').forEach(tab => {
                tab.addEventListener('click', function() {
                    // Hide all tabs
                    document.querySelectorAll('[role="tabpanel"]').forEach(panel => {
                        panel.classList.add('hidden');
                    });
                    
                    // Remove active styles from all tabs
                    document.querySelectorAll('[role="tab"]').forEach(t => {
                        t.classList.remove('border-blue-500', 'text-blue-600');
                    });
                    
                    // Show selected tab
                    const target = this.getAttribute('data-tabs-target');
                    document.querySelector(target).classList.remove('hidden');
                    
                    // Add active style to clicked tab
                    this.classList.add('border-blue-500', 'text-blue-600');
                });
            });
        });
        
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
            document.getElementById('edit_is_completed').checked = match.is_completed == 1;
            
            document.getElementById('editMatchModal').classList.remove('hidden');
        }
    </script>
</body>
</html>