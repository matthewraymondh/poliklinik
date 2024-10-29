<?php
include 'config.php';

// Logika untuk login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek username di database
    $stmt = $db->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Verifikasi password
    $error = '';
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error = 'Username atau Password salah.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white shadow-lg rounded-lg p-8 max-w-md w-full">
            <h1 class="text-2xl font-bold text-center text-green-600 mb-6">Login</h1>

            <!-- Tampilkan pesan error -->
            <?php if (isset($error) && $error): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username:</label>
                    <input type="text" name="username" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password:</label>
                    <input type="password" name="password" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <button type="submit" class="w-full py-2 px-4 bg-green-500 text-white rounded-md font-semibold hover:bg-green-600 transition">
                    Login
                </button>
            </form>

            <p class="text-center text-gray-600 mt-4">Belum punya akun? <a href="register.php" class="text-green-500 hover:underline">Daftar di sini</a></p>
        </div>
    </div>

</body>
</html>
