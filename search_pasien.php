<?php
include 'config.php';

$search = isset($_POST['search']) ? $_POST['search'] : '';

$query = $db->prepare("SELECT * FROM pasien WHERE nama LIKE :search OR alamat LIKE :search");
$query->execute(['search' => "%$search%"]);
$pasiens = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($pasiens as $pasien) {
    echo "<tr class='hover:bg-gray-100'>
            <td class='border px-4 py-2'>{$pasien['id']}</td>
            <td class='border px-4 py-2'>{$pasien['nama_pasien']}</td>
            <td class='border px-4 py-2'>{$pasien['alamat']}</td>
            <td class='border px-4 py-2'>{$pasien['telepon']}</td>
          </tr>";
}
?>
