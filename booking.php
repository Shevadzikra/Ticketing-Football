<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$match_id = $_GET['match_id'];

// Ambil data pertandingan
$stmt = $pdo->prepare("SELECT * FROM matches WHERE id = ?");
$stmt->execute([$match_id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    header('Location: index.php');
    exit;
}

// Format tanggal pertandingan
$match_date = new DateTime($match['match_date']);
$formatted_date = $match_date->format('l, d F Y H:i');

// Ambil data tiket dengan jumlah kursi tersedia
$tickets = $pdo->prepare("
    SELECT 
        t.*,
        (t.quantity_available - IFNULL((
            SELECT COUNT(*) 
            FROM seats s 
            WHERE s.ticket_id = t.id AND s.status = 'booked'
        ), 0)) as available_seats
    FROM tickets t 
    WHERE t.match_id = ? AND (t.quantity_available - IFNULL((
        SELECT COUNT(*) 
        FROM seats s 
        WHERE s.ticket_id = t.id AND s.status = 'booked'
    ), 0)) > 0
");
$tickets->execute([$match_id]);
$tickets = $tickets->fetchAll(PDO::FETCH_ASSOC);

// Ambil kursi yang tersedia untuk pertandingan ini
$seats_query = $pdo->prepare("
    SELECT 
        s.*, 
        t.ticket_type
    FROM seats s
    JOIN tickets t ON s.ticket_id = t.id
    WHERE s.match_id = ? AND s.status = 'available'
    ORDER BY s.ticket_id, s.seat_row, s.seat_column
");
$seats_query->execute([$match_id]);
$available_seats = $seats_query->fetchAll(PDO::FETCH_ASSOC);

// Kelompokkan kursi berdasarkan tiket_id
$grouped_seats = [];
foreach ($available_seats as $seat) {
    $grouped_seats[$seat['ticket_id']][] = $seat;
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Form Pemesanan -->
        <div class="lg:w-1/2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-green-600 py-3 px-6">
                    <h2 class="text-white text-2xl font-bold">Pembelian Tiket</h2>
                    <p class="text-white"><?= htmlspecialchars($match['team_home']) ?> vs <?= htmlspecialchars($match['team_away']) ?></p>
                    <p class="text-white"><?= $formatted_date ?> - <?= htmlspecialchars($match['stadium']) ?></p>
                </div>
                
                <div class="p-6">
                    <form action="process_booking.php" method="post" id="booking-form">
                        <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                        
                        <div class="mb-6">
                            <h3 class="text-xl font-semibold mb-4">Pilih Tiket dan Kursi</h3>
                            
                            <?php if (empty($tickets)): ?>
                                <p class="text-red-500">Tiket untuk pertandingan ini sudah habis.</p>
                            <?php else: ?>
                                <div class="space-y-4" id="ticket-selection">
                                    <?php foreach ($tickets as $ticket): ?>
                                    <div class="border rounded-md p-4" data-ticket-id="<?= $ticket['id'] ?>">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-bold"><?= htmlspecialchars($ticket['ticket_type']) ?></h4>
                                                <p class="text-gray-600"><?= htmlspecialchars($ticket['description']) ?></p>
                                                <p class="text-green-600 font-bold mt-2">Rp <?= number_format($ticket['price'], 0, ',', '.') ?></p>
                                                <p class="text-sm <?= $ticket['available_seats'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                                    Tersedia: <?= $ticket['available_seats'] ?> kursi
                                                </p>
                                            </div>
                                            <div class="flex items-center">
                                                <span id="selected-count-<?= $ticket['id'] ?>" class="mr-2 font-semibold">0</span>
                                                <span>dipilih</span>
                                                <input type="hidden" name="quantity[<?= $ticket['id'] ?>]" 
                                                       id="quantity-<?= $ticket['id'] ?>" 
                                                       value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($tickets)): ?>
                        <div class="mb-6">
                            <h3 class="text-xl font-semibold mb-4">Informasi Pembeli</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 mb-2">Nama Lengkap</label>
                                    <input type="text" name="full_name" required class="w-full border rounded-md py-2 px-3">
                                </div>
                                <div>
                                    <label class="block text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" required class="w-full border rounded-md py-2 px-3">
                                </div>
                                <div>
                                    <label class="block text-gray-700 mb-2">Nomor Telepon</label>
                                    <input type="tel" name="phone" required class="w-full border rounded-md py-2 px-3">
                                </div>
                                <div>
                                    <label class="block text-gray-700 mb-2">Metode Pembayaran</label>
                                    <select name="payment_method" required class="w-full border rounded-md py-2 px-3">
                                        <option value="">Pilih Metode</option>
                                        <option value="bank_transfer">Transfer Bank</option>
                                        <option value="credit_card">Kartu Kredit</option>
                                        <option value="e_wallet">E-Wallet</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-md transition duration-300">
                                Lanjutkan Pembayaran
                            </button>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Pilihan Kursi -->
        <div class="lg:w-1/2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-4">
                <div class="bg-blue-600 py-3 px-6">
                    <h2 class="text-white text-2xl font-bold">Pilih Kursi</h2>
                    <p class="text-white">Kursi hijau = tersedia, abu-abu = sudah dipesan</p>
                </div>
                
                <div class="p-6">
                    <?php if (!empty($grouped_seats)): ?>
                        <div class="space-y-8">
                            <?php foreach ($grouped_seats as $ticket_id => $seats): ?>
                                <?php 
                                $ticket_type = $seats[0]['ticket_type'];
                                $rows = array_unique(array_column($seats, 'seat_row'));
                                ?>
                                <div>
                                    <h3 class="text-lg font-semibold mb-4"><?= htmlspecialchars($ticket_type) ?></h3>
                                    <div class="bg-gray-100 p-4 rounded-lg">
                                        <?php foreach ($rows as $row): ?>
                                            <div class="mb-4">
                                                <h4 class="font-medium mb-2">Baris <?= $row ?></h4>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php 
                                                    $row_seats = array_filter($seats, function($seat) use ($row) {
                                                        return $seat['seat_row'] == $row;
                                                    });
                                                    ?>
                                                    <?php foreach ($row_seats as $seat): ?>
                                                        <button type="button" 
                                                                class="seat-btn w-12 h-12 flex items-center justify-center rounded-md transition-all
                                                                       bg-green-200 hover:bg-green-300 cursor-pointer"
                                                                data-seat-id="<?= $seat['id'] ?>" 
                                                                data-ticket-id="<?= $ticket_id ?>"
                                                                data-seat-number="<?= htmlspecialchars($seat['seat_number']) ?>">
                                                            <?= htmlspecialchars($seat['seat_column']) ?>
                                                        </button>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-red-500">Tidak ada kursi yang tersedia untuk pertandingan ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const seatButtons = document.querySelectorAll('.seat-btn');
    const selectedSeats = {};
    const ticketLimits = {};
    
    // Inisialisasi selectedSeats dan ticketLimits
    <?php foreach ($tickets as $ticket): ?>
        selectedSeats[<?= $ticket['id'] ?>] = [];
        ticketLimits[<?= $ticket['id'] ?>] = <?= $ticket['available_seats'] ?>;
    <?php endforeach; ?>
    
    // Handle klik kursi
    seatButtons.forEach(button => {
        button.addEventListener('click', function() {
            const seatId = this.dataset.seatId;
            const ticketId = this.dataset.ticketId;
            const seatNumber = this.dataset.seatNumber;
            
            // Cek apakah kursi sudah dipilih
            const index = selectedSeats[ticketId].indexOf(seatId);
            
            if (index === -1) {
                // Cek apakah sudah mencapai batas maksimal
                if (selectedSeats[ticketId].length >= ticketLimits[ticketId]) {
                    alert(`Anda hanya dapat memilih maksimal ${ticketLimits[ticketId]} kursi untuk tiket ini.`);
                    return;
                }
                
                // Tambahkan kursi
                selectedSeats[ticketId].push(seatId);
                this.classList.remove('bg-green-200', 'hover:bg-green-300');
                this.classList.add('bg-blue-500', 'text-white');
                
                // Tambahkan input hidden untuk kursi yang dipilih
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `seats[${ticketId}][]`;
                input.value = seatId;
                input.id = `seat-${seatId}`;
                document.getElementById('booking-form').appendChild(input);
            } else {
                // Hapus kursi
                selectedSeats[ticketId].splice(index, 1);
                this.classList.remove('bg-blue-500', 'text-white');
                this.classList.add('bg-green-200', 'hover:bg-green-300');
                
                // Hapus input hidden
                const input = document.getElementById(`seat-${seatId}`);
                if (input) input.remove();
            }
            
            // Update counter
            updateSelectionCount(ticketId);
        });
    });
    
    // Fungsi untuk update counter
    function updateSelectionCount(ticketId) {
        const count = selectedSeats[ticketId].length;
        document.getElementById(`quantity-${ticketId}`).value = count;
        document.getElementById(`selected-count-${ticketId}`).textContent = count;
        
        // Highlight ticket box
        const ticketBox = document.querySelector(`[data-ticket-id="${ticketId}"]`);
        if (count > 0) {
            ticketBox.classList.add('ring-2', 'ring-blue-500');
        } else {
            ticketBox.classList.remove('ring-2', 'ring-blue-500');
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>