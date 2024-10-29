<?php
// var_dump($_GET);
// session_start();
include 'config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk mengambil data dengan filter pencarian
$query = $db->prepare("
    SELECT p.id, d.nama_dokter AS dokter_nama, pa.nama_pasien AS pasien_nama, p.tanggal, p.keluhan, p.diagnosa, p.resep_obat
    FROM periksa AS p
    JOIN dokter AS d ON p.dokter_id = d.id
    JOIN pasien AS pa ON p.pasien_id = pa.id
    WHERE d.nama_dokter LIKE :search OR pa.nama_pasien LIKE :search OR p.keluhan LIKE :search OR p.diagnosa LIKE :search
");
$query->execute(['search' => "%$search%"]);
$periksaData = $query->fetchAll(PDO::FETCH_ASSOC);

// Ambil data dokter dan pasien dari database
$dokterQuery = $db->query("SELECT id, nama_dokter FROM dokter");
$dokters = $dokterQuery->fetchAll(PDO::FETCH_ASSOC);

$pasienQuery = $db->query("SELECT id, nama_pasien FROM pasien");
$pasiens = $pasienQuery->fetchAll(PDO::FETCH_ASSOC);

// Proses penambahan data pemeriksaan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_periksa'])) {
    $dokter_id = $_POST['dokter_id'];
    $pasien_id = $_POST['pasien_id'];
    $tanggal = $_POST['tanggal'];
    $keluhan = $_POST['keluhan'];
    $diagnosa = $_POST['diagnosa'];
    $resep_obat = $_POST['resep_obat'];

    $insertQuery = $db->prepare("INSERT INTO periksa (dokter_id, pasien_id, tanggal, keluhan, diagnosa, resep_obat) VALUES (?, ?, ?, ?, ?, ?)");
    $insertQuery->execute([$dokter_id, $pasien_id, $tanggal, $keluhan, $diagnosa, $resep_obat]);

    $successMessage = "Data pemeriksaan berhasil ditambahkan!";
}

// Ambil semua data pemeriksaan
$periksaQuery = $db->query("SELECT p.*, d.nama_dokter AS dokter_nama, pa.nama_pasien AS pasien_nama FROM periksa p JOIN dokter d ON p.dokter_id = d.id JOIN pasien pa ON p.pasien_id = pa.id");
$periksaData = $periksaQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pemeriksaan</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <a href="index.php" class="text-2xl font-bold text-blue-600">Poliklinik</a>
        
        <div class="space-x-4 relative">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Dropdown Data Master -->
                <div class="relative inline-block text-left">
                    <button onclick="toggleDropdown()" class="text-gray-700 hover:text-blue-500 font-semibold focus:outline-none">
                        Data Master
                    </button>
                    <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                        <a href="dokter.php" class="<?php echo ($current_page == 'dokter.php') ? 'block px-4 py-2 text-blue-500 font-semibold' : 'block px-4 py-2 text-gray-700 hover:text-blue-500'; ?>">Data Dokter</a>
                        <a href="pasien.php" class="<?php echo ($current_page == 'pasien.php') ? 'block px-4 py-2 text-green-500 font-semibold' : 'block px-4 py-2 text-gray-700 hover:text-green-500'; ?>">Data Pasien</a>
                        <a href="periksa.php" class="<?php echo ($current_page == 'periksa.php') ? 'block px-4 py-2 text-indigo-500 font-semibold' : 'block px-4 py-2 text-gray-700 hover:text-indigo-500'; ?>">Periksa</a>
                    </div>
                </div>

                <a href="logout.php" class="text-red-500 hover:text-red-600">Logout</a>
            <?php else: ?>
                <a href="login.php" class="<?php echo ($current_page == 'login.php') ? 'text-green-500 font-semibold' : 'text-gray-700 hover:text-green-500'; ?>">Login</a>
                <a href="register.php" class="<?php echo ($current_page == 'register.php') ? 'text-blue-500 font-semibold' : 'text-gray-700 hover:text-blue-500'; ?>">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    // Function to toggle dropdown visibility
    function toggleDropdown() {
        var dropdown = document.getElementById("dropdownMenu");
        dropdown.classList.toggle("hidden");
    }

    // Optional: Close the dropdown when clicking outside of it
    document.addEventListener("click", function(event) {
        var dropdown = document.getElementById("dropdownMenu");
        var button = document.querySelector("button[onclick='toggleDropdown()']");
        if (!button.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add("hidden");
        }
    });
</script>

<script>
    const searchInput = document.querySelector('input[name="search"]');
    searchInput.addEventListener('input', function() {
        const searchQuery = this.value;

        fetch('search_periksa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `search=${searchQuery}`
        })
        .then(response => response.text())
        .then(data => {
            document.querySelector('tbody').innerHTML = data;
        });
    });
</script>


    <div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-center  text-indigo-700 mb-8">Data Pemeriksaan</h1>

        <!-- Form untuk menambahkan data pemeriksaan -->
        <form method="POST" class="mb-4 p-4 bg-white shadow-md rounded">
            <h2 class="text-lg font-semibold mb-2">Tambah Pemeriksaan</h2>
            <div class="mb-4">
                <label class="block text-gray-700">Dokter</label>
                <select name="dokter_id" required class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="">Pilih Dokter</option>
                    <?php foreach ($dokters as $dokter): ?>
                        <option value="<?= $dokter['id'] ?>"><?= $dokter['nama_dokter'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Pasien</label>
                <select name="pasien_id" required class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="">Pilih Pasien</option>
                    <?php foreach ($pasiens as $pasien): ?>
                        <option value="<?= $pasien['id'] ?>"><?= $pasien['nama_pasien'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Tanggal</label>
                <input type="date" name="tanggal" required class="mt-1 block w-full border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Keluhan</label>
                <textarea name="keluhan" required class="mt-1 block w-full border-gray-300 rounded-md"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Diagnosa</label>
                <textarea name="diagnosa" required class="mt-1 block w-full border-gray-300 rounded-md"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Resep Obat</label>
                <textarea name="resep_obat" required class="mt-1 block w-full border-gray-300 rounded-md"></textarea>
            </div>
            <button type="submit" name="add_periksa" class="bg-indigo-500 text-white px-4 py-2 rounded">Tambah Pemeriksaan</button>
            <?php if (isset($successMessage)): ?>
                <p class="mt-2 text-green-500"><?= $successMessage ?></p>
            <?php endif; ?>
        </form>

        <form method="GET" action="periksa.php" class="mb-4">
            <input type="text" name="search" placeholder="Cari pemeriksaan..." class="border rounded px-3 py-2 w-full" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="bg-indigo-500 text-white px-4 py-2 mt-2 rounded">Cari</button>
        </form>
        

        <!-- Tabel untuk menampilkan data pemeriksaan -->
    <!-- Tabel untuk menampilkan data pemeriksaan -->
    <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Dokter</th>
                    <th class="border px-4 py-2">Pasien</th>
                    <th class="border px-4 py-2">Tanggal</th>
                    <th class="border px-4 py-2">Keluhan</th>
                    <th class="border px-4 py-2">Diagnosa</th>
                    <th class="border px-4 py-2">Resep Obat</th>
                    <th class="border px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($periksaData as $data): ?>
                    <tr class="hover:bg-gray-100">
                        <td class="border px-4 py-2"><?= $data['id'] ?></td>
                        <td class="border px-4 py-2"><?= $data['dokter_nama'] ?></td>
                        <td class="border px-4 py-2"><?= $data['pasien_nama'] ?></td>
                        <td class="border px-4 py-2"><?= $data['tanggal'] ?></td>
                        <td class="border px-4 py-2"><?= $data['keluhan'] ?></td>
                        <td class="border px-4 py-2"><?= $data['diagnosa'] ?></td>
                        <td class="border px-4 py-2"><?= $data['resep_obat'] ?></td>
                        <td class="border px-4 py-2">
                            <a href="edit_periksa.php?id=<?= $data['id'] ?>" class="text-blue-500 hover:underline">Edit</a>
                            <form action="delete_periksa.php" method="POST" class="inline">
                                <input type="hidden" name="id" value="<?= $data['id'] ?>">
                                <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
