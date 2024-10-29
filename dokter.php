<?php
include 'config.php';
checkLogin();

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk mengambil data dokter berdasarkan kata kunci pencarian
$query = $db->prepare("SELECT * FROM dokter WHERE nama_dokter LIKE :search OR spesialis LIKE :search");
$query->execute(['search' => "%$search%"]);
$dokters = $query->fetchAll(PDO::FETCH_ASSOC);

// Tambah data dokter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $nama = $_POST['nama'];
    $spesialis = $_POST['spesialis'];
    $kontak = $_POST['kontak'];
    $stmt = $db->prepare("INSERT INTO dokter (nama_dokter, spesialis, kontak) VALUES (?, ?, ?)");
    $stmt->execute([$nama, $spesialis, $kontak]);
}

// Edit data dokter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $spesialis = $_POST['spesialis'];
    $kontak = $_POST['kontak'];
    $stmt = $db->prepare("UPDATE dokter SET nama_dokter = ?, spesialis = ?, kontak = ? WHERE id = ?");
    $stmt->execute([$nama, $spesialis, $kontak, $id]);
}

// Hapus data dokter
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM dokter WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: dokter.php");
}

// Tampilkan data dokter
$dokterList = $db->query("SELECT * FROM dokter")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dokter</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">

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

        fetch('search_dokter.php', {
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



    <div class="container mx-auto py-10">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-8">Data Dokter</h1>

        
        
        
        <div class="bg-white shadow-md rounded-lg p-6 mb-8 max-w-md mx-auto">
            <h2 class="text-2xl font-semibold text-center text-gray-700 mb-4">Tambah/Edit Dokter</h2>
            <form method="POST" action="dokter.php" class="space-y-4">
                <input type="hidden" name="id" value="<?php echo isset($_GET['edit']) ? $_GET['edit'] : ''; ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama:</label>
                    <input type="text" name="nama" required class="mt-1 block w-full border border-gray-300 rounded-md p-2" value="<?php echo isset($_GET['edit']) ? htmlspecialchars($editDokter['nama_dokter']) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Spesialis:</label>
                    <input type="text" name="spesialis" required class="mt-1 block w-full border border-gray-300 rounded-md p-2" value="<?php echo isset($_GET['edit']) ? htmlspecialchars($editDokter['spesialis']) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kontak:</label>
                    <input type="text" name="kontak" required class="mt-1 block w-full border border-gray-300 rounded-md p-2" value="<?php echo isset($_GET['edit']) ? htmlspecialchars($editDokter['kontak']) : ''; ?>">
                </div>
                <button type="submit" name="<?php echo isset($_GET['edit']) ? 'edit' : 'add'; ?>" class="w-full py-2 px-4 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition">
                    <?php echo isset($_GET['edit']) ? 'Update Dokter' : 'Tambah Dokter'; ?>
                </button>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <!-- Form Pencarian -->
            <form method="GET" action="dokter.php" class="mb-4">
        <input type="text" name="search" placeholder="Cari dokter..." class="border rounded px-3 py-2 w-full" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 mt-2 rounded">Cari</button>
    </form>
            <!-- Tabel Data Dokter -->
         <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Nama</th>
                    <th class="border px-4 py-2">Spesialis</th>
                    <th class="border px-4 py-2">Telepon</th>
                    <th class="border px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dokters as $dokter): ?>
                    <tr class="hover:bg-gray-100">
                        <td class="border px-4 py-2"><?= $dokter['id'] ?></td>
                        <td class="border px-4 py-2"><?= $dokter['nama_dokter'] ?></td>
                        <td class="border px-4 py-2"><?= $dokter['spesialis'] ?></td>
                        <td class="border px-4 py-2"><?= $dokter['kontak'] ?></td>
                          <td class="py-3 px-4 border-b">
                            <a href="dokter.php?edit=<?php echo $dokter['id']; ?>" class="text-blue-500 hover:underline">Edit</a> |
                            <a href="dokter.php?delete=<?php echo $dokter['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>

</body>
</html>
