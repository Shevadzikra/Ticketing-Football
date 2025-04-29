<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Jika sudah login, langsung redirect ke dashboard
if (isset($_SESSION['user_id']) && $_SESSION['is_admin']) {
    header('Location: index.php');
    exit;
}

$error = '';

// Proses form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek admin berdasarkan username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_admin = 1 LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Cek apakah user ditemukan & password cocok
    if ($user && $password === $user['password']) {
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = $user['is_admin'];

        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-sm bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Login Admin</h2>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Username</label>
                <input type="text" name="username" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required class="w-full border rounded px-3 py-2">
            </div>
            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                Login
            </button>
        </form>
    </div>
</body>
</html>
