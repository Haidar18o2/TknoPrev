<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET["id"])) {
        $id = $_GET["id"];

        // Ambil data yang akan dihapus
        $sql_get_data = "SELECT * FROM proyek_proyek WHERE id = $id";
        $result_get_data = $connection->query($sql_get_data);
        $row_get_data = $result_get_data->fetch_assoc();

        if (!$row_get_data) {
            header("location: /tkno/proyekproyek/proyekproyek.php");
            exit;
        }

        // Simpan data ke halaman riwayat
        $tanggal_perubahan = date("d/m/Y");
        $jenis_perubahan = "Dihapus";
        $riwayat_id = $id;
        $sql_riwayat = "INSERT INTO riwayat_proyekproyek (riwayat_id, direktorat, kompartemen, departemen, jenis_pekerjaan, tipe_borongan, klasifikasi, formasi, aktual, keterangan, file_name, perubahan) SELECT id, direktorat, kompartemen, departemen, jenis_pekerjaan, tipe_borongan, klasifikasi, formasi, aktual, keterangan, file_name, '$jenis_perubahan pada $tanggal_perubahan' FROM proyek_proyek WHERE id = $id";
        $result_riwayat = $connection->query($sql_riwayat);

        if (!$result_riwayat) {
            $errorMessage = "Gagal menyimpan data riwayat: " . $connection->error;
        } else {
            // Hapus data dari tabel proyekproyek
            $sql_delete_data = "DELETE FROM proyek_proyek WHERE id = $id";
            $result_delete_data = $connection->query($sql_delete_data);

            if ($result_delete_data) {
                header("location: /tkno/proyekproyek/proyekproyek.php");
                exit;
            } else {
                $errorMessage = "Gagal menghapus data proyekproyek: " . $connection->error;
            }
        }
    }
}
