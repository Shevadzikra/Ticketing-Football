<?php
session_start();
require_once '../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['ticket_id'])) {
    header("Location: tickets.php");
    exit();
}

$ticket_id = $_GET['ticket_id'];

// Fetch ticket info
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    header("Location: tickets.php");
    exit();
}

// Handle seat status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seat_id'])) {
    try {
        $seat_id = $_POST['seat_id'];
        $status = $_POST['status'];
        
        $stmt = $pdo->prepare("UPDATE seats SET status = ? WHERE id = ?");
        $stmt->execute([$status, $seat_id]);
        
        $_SESSION['success'] = "Status kursi berhasil diperbarui";
        header("Location: index.php?ticket_id=$ticket_id");
        exit();
    } catch (PDOException $e) {
        $error = "Gagal memperbarui status kursi: " . $e->getMessage();
    }
}

// Fetch seats for this ticket
$stmt = $pdo->prepare("SELECT * FROM seats WHERE ticket_id = ? ORDER BY seat_row, seat_column");
$stmt->execute([$ticket_id]);
$seats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group seats by row
$seatsByRow = [];
foreach ($seats as $seat) {
    $seatsByRow[$seat['seat_row']][] = $seat;
}

// Handle success message
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kursi - <?php echo $ticket['ticket_type']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .seat {
            display: inline-block;
            width: 30px;
            height: 30px;
            margin: 2px;
            text-align: center;
            line-height: 30px;
            cursor: pointer;
            border-radius: 4px;
        }
        .available { background-color: #4ade80; }
        .booked { background-color: #f87171; cursor: not-allowed; }
        .reserved { background-color: #fbbf24; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Kelola Kursi - <?php echo htmlspecialchars($ticket['ticket_type']); ?></h1>
            <a href="../tickets/" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                Kembali ke Tiket
            </a>
        </div>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-2">Informasi Tiket</h2>
            <p>Pertandingan ID: <?php echo $ticket['match_id']; ?></p>
            <p>Harga: Rp<?php echo number_format($ticket['price'], 0, ',', '.'); ?></p>
            <p>Kursi Tersedia: <?php echo $ticket['quantity_available']; ?></p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Denah Kursi</h2>
            
            <div class="stadium-map">
                <?php foreach ($seatsByRow as $row => $seatsInRow): ?>
                    <div class="seat-row mb-2">
                        <span class="font-medium">Baris <?php echo $row; ?>:</span>
                        <?php foreach ($seatsInRow as $seat): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="seat_id" value="<?php echo $seat['id']; ?>">
                                <button type="submit" name="status" value="<?php echo $seat['status'] === 'available' ? 'booked' : 'available'; ?>"
                                    class="seat <?php echo $seat['status']; ?>"
                                    title="Kursi <?php echo $seat['seat_number']; ?> - Klik untuk mengubah status">
                                    <?php echo $seat['seat_column']; ?>
                                </button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-6">
                <h3 class="font-medium mb-2">Tambah Kursi Baru</h3>
                <form method="POST" action="create_seat.php" class="flex items-end gap-2">
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                    <input type="hidden" name="match_id" value="<?php echo $ticket['match_id']; ?>">
                    
                    <div>
                        <label class="block text-gray-700 mb-1">Baris</label>
                        <input type="text" name="seat_row" required class="px-3 py-2 border rounded-lg">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-1">Kolom</label>
                        <input type="text" name="seat_column" required class="px-3 py-2 border rounded-lg">
                    </div>
                    
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        Tambah Kursi
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>