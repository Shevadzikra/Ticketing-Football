<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Tiket Sepak Bola</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <header class="bg-green-700 text-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">
                    <a href="index.php">TiketBola.com</a>
                </h1>
                <nav>
                    <ul class="flex space-x-6 items-center">
                        <li><a href="index.php" class="hover:underline">Jadwal</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="logout.php" class="hover:underline">Logout</a></li>
                            <li><a href="profile.php" class="hover:underline">Profil</a></li>
                            <?php else: ?>
                            <!-- <li><a href="login.php" class="hover:underline">Login</a></li>
                            <li><a href="register.php" class="hover:underline">Daftar</a></li> -->
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <main>