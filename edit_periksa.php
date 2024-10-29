<?php
// session_start();
include 'config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID dari parameter URL
$id = $_GET['id'];

// Ambil data pemeriksaan berdasarkan ID
$periksaQuery = $db->prepare("SELECT * FROM periksa WHERE id = ?");
$periksaQuery->execute([$id]);
$dataPeriksa = $periksaQuery->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dokter_id = $_POST['dokter_id'];
    $pasien_id = $_POST['pasien_id'];
    $tanggal = $_POST['tanggal'];
    $keluhan = $_POST['keluhan'];
    $diagnosa = $_POST['diagnosa'];
    $resep_obat = $_POST['resep_obat'];

    // Update data pemeriksaan
    $updateQuery = $db->prepare("UPDATE periksa SET dokter_id = ?, pasien_id = ?, tanggal = ?, keluhan = ?, diagnosa = ?, resep_obat = ? WHERE id = ?");
    $updateQuery->execute([$dokter_id, $pasien_id, $tanggal, $keluhan, $diagnosa, $resep_obat, $id]);

    header("Location: periksa.php");
    exit();
}

// Ambil data dokter dan pasien untuk dropdown
$dokterQuery = $db->query("SELECT id, nama_dokter FROM dokter");
$dokters = $dokterQuery->fetchAll(PDO::FETCH_ASSOC);

$pasienQuery = $db->query("SELECT id, nama_pasien FROM pasien");
$pasiens = $pasienQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pemeriksaan</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

  <!-- Navbar -->
  <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-blue-600">Poliklinik</a>
            
            <div class="space-x-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dokter.php" class="<?php echo ($current_page == 'dokter.php') ? 'text-blue-500 font-semibold' : 'text-gray-700 hover:text-blue-500'; ?>">Data Dokter</a>
                    <a href="pasien.php" class="<?php echo ($current_page == 'pasien.php') ? 'text-green-500 font-semibold' : 'text-gray-700 hover:text-green-500'; ?>">Data Pasien</a>
                    <a href="periksa.php" class="<?php echo ($current_page == 'periksa.php') ? 'text-indigo-500 font-semibold' : 'text-gray-700 hover:text-indigo-500'; ?>">Periksa</a>
                    <a href="logout.php" class="text-red-500 hover:text-red-600">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="<?php echo ($current_page == 'login.php') ? 'text-green-500 font-semibold' : 'text-gray-700 hover:text-green-500'; ?>">Login</a>
                    <a href="register.php" class="<?php echo ($current_page == 'register.php') ? 'text-blue-500 font-semibold' : 'text-gray-700 hover:text-blue-500'; ?>">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Edit Pemeriksaan</h1>

        <form method="POST" class="mb-4 p-4 bg-white shadow-md rounded">
            <div class="mb-4">
                <label class="block text-gray-700">Dokter</label>
                <select name="dokter_id" required class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="">Pilih Dokter</option>
                    <?php foreach ($dokters as $dokter): ?>
                        <option value="<?= $dokter['id'] ?>" <?= $dokter['id'] == $dataPeriksa['dokter_id'] ? 'selected' : '' ?>><?= $dokter['nama_dokter'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Pasien</label>
                <select name="pasien_id" required class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="">Pilih Pasien</option>
                    <?php foreach ($pasiens as $pasien): ?>
                        <option value="<?= $pasien['id'] ?>" <?= $pasien['id'] == $dataPeriksa['pasien_id'] ? 'selected' : '' ?>><?= $pasien['nama_pasien'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Tanggal</label>
                <input type="date" name="tanggal" value="<?= $dataPeriksa['tanggal'] ?>" required class="mt-1 block w-full border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Keluhan</label>
                <textarea name="keluhan" required class="mt-1 block w-full border-gray-300 rounded-md"><?= $dataPeriksa['keluhan'] ?></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Diagnosa</label>
                <textarea name="diagnosa" required class="mt-1 block w-full border-gray-300 rounded-md"><?= $dataPeriksa['diagnosa'] ?></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Resep Obat</label>
                <textarea name="resep_obat" required class="mt-1 block w-full border-gray-300 rounded-md"><?= $dataPeriksa['resep_obat'] ?></textarea>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Pemeriksaan</button>
        </form>
    </div>
</body>
</html>
