<?php
$host = 'localhost';
$dbname = 'tiket_sepakbola';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET time_zone = '+07:00'"); // Sesuaikan timezone
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>