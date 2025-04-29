<?php
session_start();
require_once '../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Handle success message
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Fetch all tickets
$stmt = $pdo->query("SELECT t.*, 
                    (SELECT COUNT(*) FROM seats s WHERE s.ticket_id = t.id AND s.status = 'available') AS actual_available
                    FROM tickets t");
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Tiket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<?php include './includes/admin_header.php'; ?>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Manajemen Tiket</h1>
            <a href="./create_tickets.php" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                + Tiket Baru
            </a>
        </div>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($tickets as $ticket): ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($ticket['ticket_type']); ?></h2>
                    <p class="text-gray-600 mb-1">Pertandingan ID: <?php echo $ticket['match_id']; ?></p>
                    <p class="text-gray-600 mb-1">Harga: Rp<?php echo number_format($ticket['price'], 0, ',', '.'); ?></p>
                    <p class="text-gray-600 mb-3">
                        Tersedia: <?php echo $ticket['actual_available']; ?> / <?php echo $ticket['quantity_available']; ?>
                    </p>
                    <p class="text-gray-700 mb-4"><?php echo htmlspecialchars($ticket['description']); ?></p>
                    
                    <div class="flex space-x-2">
                        <a href="edit_ticket.php?id=<?php echo $ticket['id']; ?>" 
                           class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">
                            Edit
                        </a>
                        <a href="../seats/index.php?ticket_id=<?php echo $ticket['id']; ?>" 
                           class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                            Kelola Kursi
                        </a>
                        <a href="delete_ticket.php?id=<?php echo $ticket['id']; ?>" 
                           class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus tiket ini?')">
                            Hapus
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>