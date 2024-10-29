<?php
include 'config.php';

$search = isset($_POST['search']) ? $_POST['search'] : '';

$query = $db->prepare("
    SELECT p.id, d.nama_dokter AS dokter_nama, pa.nama_pasien AS pasien_nama, p.tanggal, p.keluhan, p.diagnosa, p.resep_obat
    FROM periksa AS p
    JOIN dokter AS d ON p.dokter_id = d.id
    JOIN pasien AS pa ON p.pasien_id = pa.id
    WHERE d.nama_dokter LIKE :search OR pa.nama_pasien LIKE :search OR p.keluhan LIKE :search OR p.diagnosa LIKE :search
");
$query->execute(['search' => "%$search%"]);
$periksaData = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($periksaData as $data) {
    echo "<tr class='hover:bg-gray-100'>
            <td class='border px-4 py-2'>{$data['id']}</td>
            <td class='border px-4 py-2'>{$data['dokter_nama']}</td>
            <td class='border px-4 py-2'>{$data['pasien_nama']}</td>
            <td class='border px-4 py-2'>{$data['tanggal']}</td>
            <td class='border px-4 py-2'>{$data['keluhan']}</td>
            <td class='border px-4 py-2'>{$data['diagnosa']}</td>
            <td class='border px-4 py-2'>{$data['resep_obat']}</td>
            <td class='border px-4 py-2'>
                <a href='edit_periksa.php?id={$data['id']}' class='text-blue-500 hover:underline'>Edit</a>
                <form action='delete_periksa.php' method='POST' class='inline'>
                    <input type='hidden' name='id' value='{$data['id']}'>
                    <button type='submit' class='text-red-500 hover:underline'>Hapus</button>
                </form>
            </td>
          </tr>";
}
?>
