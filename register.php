<?php
include 'config.php';

// Logika untuk menambah pengguna baru
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi
    $error = '';
    if ($password !== $confirm_password) {
        $error = 'Password dan Konfirmasi Password tidak cocok.';
    } else {
        // Cek apakah username sudah ada
        $stmt = $db->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $error = 'Username sudah digunakan.';
        } else {
            // Hash password dan simpan pengguna
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("INSERT INTO user (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed_password]);
            header("Location: login.php?registered=1");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white shadow-lg rounded-lg p-8 max-w-md w-full">
            <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Register</h1>

            <!-- Tampilkan pesan error -->
            <?php if (isset($error) && $error): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username:</label>
                    <input type="text" name="username" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password:</label>
                    <input type="password" name="password" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Konfirmasi Password:</label>
                    <input type="password" name="confirm_password" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <button type="submit" class="w-full py-2 px-4 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition">
                    Daftar
                </button>
            </form>

            <p class="text-center text-gray-600 mt-4">Sudah punya akun? <a href="login.php" class="text-blue-500 hover:underline">Login di sini</a></p>
        </div>
    </div>

</body>
</html>
