<?php
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql_select = "SELECT * FROM adm_keu WHERE id=$id";
    $result = $connection->query($sql_select);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_name = $row['file_name'];

        // Hapus file dari sistem
        $file_path = $_SERVER['DOCUMENT_ROOT'] . "/tkno/admkeu/uploads/$file_name";
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Simpan data sebelum penghapusan ke dalam riwayat_admkeu
        $direktorat = $row['direktorat'];
        $kompartemen = $row['kompartemen'];
        $departemen = $row['departemen'];
        $jenis_pekerjaan = $row['jenis_pekerjaan'];
        $tipe_borongan = $row['tipe_borongan'];
        $klasifikasi = $row['klasifikasi'];
        $formasi = $row['formasi'];
        $aktual = $row['aktual'];
        $keterangan = $row['keterangan'];
        $perubahan = "Menghapus file";
        $tanggal_perubahan = date("d/m/Y");
        $sql_riwayat = "INSERT INTO riwayat_admkeu (direktorat, kompartemen, departemen, jenis_pekerjaan, tipe_borongan, klasifikasi, formasi, aktual, keterangan, perubahan, file_name) 
                        VALUES ('$direktorat', '$kompartemen', '$departemen', '$jenis_pekerjaan', '$tipe_borongan', '$klasifikasi', '$formasi', '$aktual', '$keterangan', '$perubahan pada $tanggal_perubahan', '$file_name')";

        if ($connection->query($sql_riwayat) === TRUE) {
            // Hapus file dari sistem
            $file_path = $_SERVER['DOCUMENT_ROOT'] . "/tkno/admkeu/uploads/$file_name";
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // Kosongkan nama file di database
            $sql_update = "UPDATE adm_keu SET file_name='' WHERE id=$id";
            if ($connection->query($sql_update) === TRUE) {
                header("Location: admkeu.php");
                exit();
            } else {
                echo "Error updating record: " . $connection->error;
            }
        } else {
            echo "Error inserting record to riwayat_admkeu: " . $connection->error;
        }
    }
} else {
    echo "Invalid request.";
}
