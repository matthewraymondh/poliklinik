<?php
// session_start();
include 'config.php';

$current_page = basename($_SERVER['PHP_SELF']);

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil jumlah data untuk statistik
$totalDokter = $db->query("SELECT COUNT(*) FROM dokter")->fetchColumn();
$totalPasien = $db->query("SELECT COUNT(*) FROM pasien")->fetchColumn();
$totalPeriksa = $db->query("SELECT COUNT(*) FROM periksa")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Poliklinik</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen bg-gray-100 text-gray-800">

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

    <!-- Main Content -->
    <div class="container mx-auto my-10 p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-3xl font-semibold text-gray-700 text-center mb-6">Selamat Datang di Dashboard Poliklinik</h1>
        
        <?php if (isset($_SESSION['username'])): ?>
            <p class="text-center text-gray-600 mb-8">Halo, <span class="font-bold text-blue-500"><?php echo htmlspecialchars($_SESSION['username']); ?></span>! Anda bisa mengelola data di poliklinik melalui menu di atas.</p>
        <?php endif; ?>

        <!-- Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-100 p-4 rounded-lg text-center shadow-md">
                <h2 class="text-lg font-bold text-blue-600">Total Dokter</h2>
                <p class="text-3xl font-semibold text-blue-700"><?php echo $totalDokter; ?></p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg text-center shadow-md">
                <h2 class="text-lg font-bold text-green-600">Total Pasien</h2>
                <p class="text-3xl font-semibold text-green-700"><?php echo $totalPasien; ?></p>
            </div>
            <div class="bg-indigo-100 p-4 rounded-lg text-center shadow-md">
                <h2 class="text-lg font-bold text-indigo-600">Total Pemeriksaan</h2>
                <p class="text-3xl font-semibold text-indigo-700"><?php echo $totalPeriksa; ?></p>
            </div>
        </div>

        <!-- Akses Cepat -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="dokter.php" class="bg-blue-500 text-white p-4 rounded-lg text-center shadow-md hover:bg-blue-600 transition">
                <h3 class="text-lg font-bold">Data Dokter</h3>
                <p class="text-sm">Kelola informasi dokter</p>
            </a>
            <a href="pasien.php" class="bg-green-500 text-white p-4 rounded-lg text-center shadow-md hover:bg-green-600 transition">
                <h3 class="text-lg font-bold">Data Pasien</h3>
                <p class="text-sm">Kelola data pasien</p>
            </a>
            <a href="periksa.php" class="bg-indigo-500 text-white p-4 rounded-lg text-center shadow-md hover:bg-indigo-600 transition">
                <h3 class="text-lg font-bold">Data Pemeriksaan</h3>
                <p class="text-sm">Lihat dan atur pemeriksaan</p>
            </a>
        </div>
    </div>
    <div class="flex-grow"></div>
    <!-- Footer -->
<footer class="bg-white shadow-md py-4 mt-6">
    <div class="container mx-auto text-center">
        <p class="text-sm">
            &copy; <?= date("Y") ?> Matthew Raymond Hartono A11.2021.13275. All rights reserved.
        </p>
    </div>
</footer>


</body>
</html>
