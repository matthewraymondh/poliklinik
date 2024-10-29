<?php
session_start();
include 'config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID dari POST
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Hapus data pemeriksaan
    $deleteQuery = $db->prepare("DELETE FROM periksa WHERE id = ?");
    $deleteQuery->execute([$id]);

    header("Location: periksa.php");
    exit();
}
?>
