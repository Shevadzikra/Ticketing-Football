<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $email, $full_name, $phone]);
        
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = 0;
        
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = 'Username atau email sudah terdaftar';
        } else {
            $error = 'Terjadi kesalahan saat mendaftar';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Tiket Sepak Bola</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold text-center mb-6">Daftar Akun Baru</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" required 
                               class="w-full px-3 py-2 border rounded-md">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" required 
                               class="w-full px-3 py-2 border rounded-md">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required 
                               class="w-full px-3 py-2 border rounded-md">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="full_name" required 
                               class="w-full px-3 py-2 border rounded-md">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="tel" name="phone" 
                               class="w-full px-3 py-2 border rounded-md">
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full mt-6 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                    Daftar
                </button>
                
                <div class="mt-4 text-center">
                    <p>Sudah punya akun? <a href="login.php" class="text-blue-500 hover:underline">Login disini</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>