<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Sidebar and Header -->
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-blue-800 text-white w-64 flex-shrink-0">
            <div class="p-4">
                <h1 class="text-2xl font-bold">Football Ticketing</h1>
                <p class="text-blue-200">Admin Panel</p>
            </div>
            <nav class="mt-6">
                <div class="px-4 py-2">
                    <a href="index.php" class="block py-2 px-4 rounded hover:bg-blue-700 text-white">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="matches/" class="block py-2 px-4 rounded hover:bg-blue-700 text-white">
                        <i class="fas fa-calendar-alt mr-2"></i> Pertandingan
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="tickets/" class="block py-2 px-4 rounded hover:bg-blue-700 text-white">
                        <i class="fas fa-ticket-alt mr-2"></i> Tiket
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="orders/" class="block py-2 px-4 rounded hover:bg-blue-700 text-white">
                        <i class="fas fa-receipt mr-2"></i> Pesanan
                    </a>
                </div>
                <div class="px-4 py-2">
                    <a href="users.php" class="block py-2 px-4 rounded hover:bg-blue-700 text-white">
                        <i class="fas fa-users mr-2"></i> Pengguna
                    </a>
                </div>
                <div class="px-4 py-2 mt-6">
                    <a href="../logout.php" class="block py-2 px-4 rounded hover:bg-red-600 text-white">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center p-4">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?>
                    </h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600"><?php echo $_SESSION['username'] ?? 'Admin'; ?></span>
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content will be inserted here -->
            <main class="p-4">