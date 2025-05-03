<?php
session_start();
require_once '../../config/database.php';

// Cek auth
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ../seats/");
    exit();
}

$ticket_id = $_GET['id'];

// Fetch ticket data
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    header("Location: ../seats/");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ticket_type = $_POST['ticket_type'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        $stmt = $pdo->prepare("UPDATE tickets SET ticket_type = ?, price = ?, description = ? WHERE id = ?");
        $stmt->execute([$ticket_type, $price, $description, $ticket_id]);
        
        $_SESSION['success'] = "Tiket berhasil diperbarui";
        header("Location: ../seats/");
        exit();
    } catch (PDOException $e) {
        $error = "Gagal memperbarui tiket: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tiket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Edit Tiket</h1>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="ticket_type">Jenis Tiket</label>
                <input type="text" id="ticket_type" name="ticket_type" required 
                       value="<?php echo htmlspecialchars($ticket['ticket_type']); ?>"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="price">Harga</label>
                <input type="number" step="0.01" id="price" name="price" required 
                       value="<?php echo $ticket['price']; ?>"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="description">Deskripsi</label>
                <textarea id="description" name="description" 
                          class="w-full px-3 py-2 border rounded-lg"><?php echo htmlspecialchars($ticket['description']); ?></textarea>
            </div>
            
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    Perbarui Tiket
                </button>
                <a href="./" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
</body>
</html>