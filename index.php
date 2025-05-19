<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 2. AMBIL DATA DARI DATABASE
try {
    $query = "
        SELECT 
            m.id, 
            m.team_home, 
            m.team_away, 
            m.match_date, 
            m.stadium, 
            t.id as ticket_id, 
            t.ticket_type, 
            t.price,
            t.description,
            COUNT(s.id) as available_seats
        FROM matches m
        LEFT JOIN tickets t ON m.id = t.match_id
        LEFT JOIN seats s ON t.id = s.ticket_id AND s.status = 'available'
        GROUP BY m.id, t.id
        ORDER BY m.match_date ASC
    ";
    
    $stmt = $pdo->query($query);
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Kelompokkan tiket berdasarkan pertandingan
    $grouped_matches = [];
    foreach ($matches as $match) {
        $match_id = $match['id'];
        if (!isset($grouped_matches[$match_id])) {
            $grouped_matches[$match_id] = [
                'id' => $match['id'],
                'team_home' => $match['team_home'],
                'team_away' => $match['team_away'],
                'match_date' => $match['match_date'],
                'stadium' => $match['stadium'],
                'tickets' => []
            ];
        }
        
        if ($match['ticket_id']) {
            $grouped_matches[$match_id]['tickets'][] = [
                'id' => $match['ticket_id'],
                'type' => $match['ticket_type'],
                'price' => $match['price'],
                'description' => $match['description'],
                'available' => $match['available_seats']
            ];
        }
    }
} catch (PDOException $e) {
    die("Error mengambil data: " . $e->getMessage());
}

// 3. TAMPILKAN HTML
require_once 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./assets/css/output.css">
</head>
<body>
    
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Jadwal Pertandingan Sepak Bola</h1>
    
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">Pertandingan</th>
                        <th class="py-3 px-4 text-left">Stadion</th>
                        <th class="py-3 px-4 text-left">Tanggal & Waktu</th>
                        <th class="py-3 px-4 text-left">Tiket Tersedia</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($grouped_matches as $match): ?>
                        <?php
                        try {
                            $match_date = new DateTime($match['match_date']);
                            $date_formatted = $match_date->format('d/m/Y');
                            $time_formatted = $match_date->format('H:i');
                            $datetime_display = $date_formatted . ' • ' . $time_formatted;
                            
                            // Tambahkan indikator jika pertandingan sudah lewat
                            $is_past = ($match_date < new DateTime());
                        } catch (Exception $e) {
                            $datetime_display = 'Tanggal tidak valid';
                            $is_past = false;
                        }
                        ?>
                        <tr class="hover:bg-gray-50 <?= $is_past ? 'bg-gray-100' : '' ?>">
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-between">
                                    <div class="text-right flex-1 font-semibold">
                                        <?= htmlspecialchars($match['team_home']) ?>
                                    </div>
                                    <div class="mx-4 font-bold text-red-600">VS</div>
                                    <div class="text-left flex-1 font-semibold">
                                        <?= htmlspecialchars($match['team_away']) ?>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <?= htmlspecialchars($match['stadium']) ?>
                            </td>
                            <td class="py-4 px-4">
                                <?= $datetime_display ?>
                                <?= $is_past ? '<span class="text-red-500 text-sm">(Selesai)</span>' : '' ?>
                            </td>
                            <td class="py-4 px-4">
                                <?php if (!empty($match['tickets'])): ?>
                                    <ul class="space-y-1">
                                        <?php foreach ($match['tickets'] as $ticket): ?>
                                            <li class="flex justify-between">
                                                <span class="font-medium <?= $ticket['available'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                                    <?= $ticket['available'] ?> kursi tersedia
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="text-red-500">Tiket habis</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <?php if (!$is_past && !empty($match['tickets']) && array_sum(array_column($match['tickets'], 'available'))): ?>
                                    <a href="booking.php?match_id=<?= $match['id'] ?>" 
                                        class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md transition duration-300">
                                        Pesan
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-500">
                                        <?= $is_past ? 'Event selesai' : 'Tiket habis' ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>