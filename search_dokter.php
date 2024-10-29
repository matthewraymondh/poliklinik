<?php
include 'config.php';

$search = isset($_POST['search']) ? $_POST['search'] : '';

$query = $db->prepare("SELECT * FROM dokter WHERE nama LIKE :search OR spesialis LIKE :search");
$query->execute(['search' => "%$search%"]);
$dokters = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($dokters as $dokter) {
    echo "<tr class='hover:bg-gray-100'>
            <td class='border px-4 py-2'>{$dokter['id']}</td>
            <td class='border px-4 py-2'>{$dokter['nama_dokter']}</td>
            <td class='border px-4 py-2'>{$dokter['spesialis']}</td>
            <td class='border px-4 py-2'>{$dokter['telepon']}</td>
          </tr>";
}
?>
