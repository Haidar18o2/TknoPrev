<?php
error_reporting(0);
$servername = "localhost";
$username = "root";
$password = "";
$database = "tkno";

$connection = new mysqli($servername, $username, $password, $database);

$id = "";
$direktorat = "";
$kompartemen = "";
$departemen  = "";
$jenis_pekerjaan = "";
$tipe_borongan = "";
$klasifikasi = "";
$formasi = "";
$aktual = "";
$keterangan = "";
$file_name = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // METODE POST: mengupdate data proyekproyek

    // Ambil nilai dari form
    $id = $_POST["id"];
    $direktorat = $_POST["direktorat"];
    $kompartemen = $_POST["kompartemen"];
    $departemen = $_POST["departemen"];
    $jenis_pekerjaan = $_POST["jenis_pekerjaan"];
    $tipe_borongan = $_POST["tipe_borongan"];
    $klasifikasi = $_POST["klasifikasi"];
    $formasi = $_POST["formasi"];
    $aktual = $_POST["aktual"];
    $keterangan = $_POST["keterangan"];

    // Periksa apakah data yang diperlukan sudah diisi
    if (empty($id) || empty($jenis_pekerjaan) || empty($tipe_borongan) || empty($klasifikasi) || empty($formasi) || empty($aktual) || empty($keterangan)) {
        $errorMessage = "Semua kolom harus diisi";
    } else {
        // Periksa apakah pengguna memilih file baru
        if ($_FILES["uploaded_file"]["error"] == UPLOAD_ERR_OK) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["uploaded_file"]["name"]);
            if (move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $target_file)) {
                $file_name = basename($_FILES["uploaded_file"]["name"]);
            } else {
                $errorMessage = "Gagal mengunggah file.";
            }
        } else {
            // Jika tidak ada file yang diunggah, tetap gunakan file yang ada
            $sql_get_file = "SELECT file_name FROM proyek_proyek WHERE id = $id";
            $result_get_file = $connection->query($sql_get_file);
            $row_get_file = $result_get_file->fetch_assoc();
            $file_name = $row_get_file["file_name"];
        }

        // Periksa apakah ada perubahan sebelum menyimpan ke riwayat
        $sql_get_old_data = "SELECT * FROM proyek_proyek WHERE id = $id";
        $result_get_old_data = $connection->query($sql_get_old_data);
        $old_data = $result_get_old_data->fetch_assoc();

        $changes = [];

        if ($old_data["klasifikasi"] != $klasifikasi) {
            $changes[] = "Mengubah klasifikasi pada " . date("d/m/Y");
        }

        if ($old_data["formasi"] != $formasi) {
            $changes[] = "Mengubah formasi pada " . date("d/m/Y");
        }

        if ($old_data["aktual"] != $aktual) {
            $changes[] = "Mengubah aktual pada " . date("d/m/Y");
        }

        if ($old_data["keterangan"] != $keterangan) {
            $changes[] = "Mengubah keterangan pada " . date("d/m/Y");
        }

        if ($_FILES["uploaded_file"]["error"] == UPLOAD_ERR_OK) {
            $changes[] = "Mengubah bukti pada " . date("d/m/Y");
        }

        $perubahan = implode(", ", $changes);

        // Hanya jika ada perubahan data maka akan disimpan di riwayat
        if (!empty($perubahan)) {
            $sql_riwayat = "INSERT INTO riwayat_proyekproyek (direktorat, kompartemen, departemen, jenis_pekerjaan, tipe_borongan, klasifikasi, formasi, aktual, keterangan, file_name, perubahan) VALUES ('$direktorat', '$kompartemen', '$departemen', '$jenis_pekerjaan', '$tipe_borongan', '$klasifikasi', '$formasi', '$aktual', '$keterangan', '$file_name', '$perubahan')";
            $result_riwayat = $connection->query($sql_riwayat);

            if (!$result_riwayat) {
                $errorMessage .= " Gagal menyimpan data riwayat: " . $connection->error;
            }
        }

        // Update data proyekproyek
        $sql_update_proyekproyek = "UPDATE proyek_proyek SET direktorat = '$direktorat', kompartemen = '$kompartemen', departemen = '$departemen', jenis_pekerjaan= '$jenis_pekerjaan', tipe_borongan = '$tipe_borongan', klasifikasi = '$klasifikasi', formasi = '$formasi', aktual = '$aktual', keterangan = '$keterangan', file_name = '$file_name' WHERE id=$id";
        $result_update_proyekproyek = $connection->query($sql_update_proyekproyek);

        if ($result_update_proyekproyek) {
            $successMessage = "Data rekap berhasil diupdate.";
            echo "<script>
                    window.location.href = '/tkno/proyekproyek/proyekproyek.php?page=" . $_GET['page'] . "&targetElementId=data_" . $id . "';
                </script>";
            exit;
        } else {
            $errorMessage = "Gagal mengupdate data proyekproyek: " . $connection->error;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // METODE GET: menampilkan data proyekproyek
    if (!isset($_GET["id"])) {
        header("location: /tkno/proyekproyek/proyekproyek.php");
        exit;
    }

    $id = $_GET["id"];

    // Membaca baris data yang dipilih dari tabel database
    $sql = "SELECT * FROM proyek_proyek WHERE id=$id";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: /tkno/proyekproyek/proyekproyek.php");
        exit;
    }

    $direktorat =$row["direktorat"];
    $kompartemen =$row["kompartemen"];
    $departemen =$row["departemen"];
    $jenis_pekerjaan = $row["jenis_pekerjaan"];
    $tipe_borongan = $row["tipe_borongan"];
    $klasifikasi = $row["klasifikasi"];
    $formasi = $row["formasi"];
    $aktual = $row["aktual"];
    $keterangan = $row["keterangan"];
    $file_name = $row["file_name"];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyek Proyek || Edit Data</title>
    <link rel="stylesheet" href="/tkno/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
<?php include "../navigasi.php"; ?>
    <div class="w3-main">
        <div class="w3-light-grey">
            <div class="w3-container">
                <div class="container my-5">
                    <h2>Edit Data</h2><br>
                    <?php
                    if (!empty($errorMessage)) {
                        echo "
                            <div class ='alert alert-warning alert-dismissible fade show col-md-9' role='alert'>
                                <strong>$errorMessage</strong><br>
                                <span>*Gunakan simbol (-) sebagai pengganti angka 0</span>
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>
                        ";
                    }
                    ?>

                    <form method="post" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Direktorat</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="direktorat" value="Proyek Proyek" readonly>
                                </div>
                        </div>
                        <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Kompartemen</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="kompartemen" value="Proyek Proyek" readonly>
                                </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Departemen</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="departemen" value="<?php echo $departemen; ?>" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Jenis Pekerjaan</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jenis_pekerjaan" value="<?php echo $jenis_pekerjaan; ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Tipe Borongan</label>
                            <div class="col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipe_borongan" id="tipe_borongan1" value="Borongan" <?php
                                                                                                                                            if ($tipe_borongan == "Borongan") {
                                                                                                                                                echo "checked";
                                                                                                                                            }
                                                                                                                                            ?>>
                                    <label class="form-check-label" for="tipe_borongan1">
                                        Borongan
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipe_borongan" id="tipe_borongan2" value="Non Borongan" <?php
                                                                                                                                                if ($tipe_borongan == "Non Borongan") {
                                                                                                                                                    echo "checked";
                                                                                                                                                }
                                                                                                                                                ?>>
                                    <label class="form-check-label" for="tipe_borongan2">
                                        Non Borongan
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Klasifikasi</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="klasifikasi" value="<?php echo $klasifikasi; ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Formasi</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="formasi" value="<?php echo $formasi; ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Aktual</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="aktual" value="<?php echo $aktual; ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Keterangan</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="keterangan" value="<?php echo $keterangan; ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Ganti File</label>
                            <div class="col-sm-6">
                                <input type="file" class="form-control" name="uploaded_file" value="<?php echo $file_name; ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">File saat ini</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="file" value="<?php echo $file_name; ?>" disabled>
                            </div>
                        </div>

                        <?php
                        if (!empty($successMessage)) {
                            echo "
                                <div class ='alert alert-success alert-dismissible fade show' role='alert'>
                                    <strong>$successMessage</strong>
                                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                </div>
                            ";
                        }
                        ?>

                        <div class="row mb-3">
                            <div class="offset-sm-3 col-sm-3 d-grid">
                                <button type="submit" class="btn btn-warning"><b>Simpan</b></button>
                            </div>
                            <div class="col-sm-3 d-grid">
                                <a class="btn btn-danger" id="batalButton" role="button"><b>Batal</b></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('batalButton').addEventListener('click', function() {
            var page = "<?php echo $_GET['page']; ?>";
            var id = "<?php echo $id; ?>";
            var targetElementId = "data_" + id;
            var link = "/tkno/proyekproyek/proyekproyek.php?page=" + page + "&targetElementId=" + targetElementId;
            window.location.href = link;
        });
    </script>
</body>

</html>