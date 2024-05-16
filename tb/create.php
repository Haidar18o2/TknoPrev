<?php
error_reporting(0);
$servername = "localhost";
$username = "root";
$password = "";
$database = "tkno";

$connection = new mysqli($servername, $username, $password, $database);

$direktorat = "";
$kompartemen = "";
$departemen  = "";
$jenis_pekerjaan = "";
$tipe_borongan = "";
$klasifikasi = "";
$formasi = "";
$aktual = "";
$keterangan = "";

$errorMessage = "";
$successMessage = "";
$file_name = "";

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$sql_total = "SELECT COUNT(*) as total FROM tb";
$result_total = $connection->query($sql_total);
$total_rows = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $direktorat = $_POST["direktorat"];
    $kompartemen = $_POST["kompartemen"];
    $departemen = $_POST["departemen"];
    $jenis_pekerjaan = $_POST["jenis_pekerjaan"];
    $tipe_borongan = $_POST["tipe_borongan"];
    $klasifikasi = $_POST["klasifikasi"];
    $formasi     = $_POST["formasi"];
    $aktual = $_POST["aktual"];
    $keterangan = $_POST["keterangan"];

    // Check if the file upload field is not empty
    if (!empty($_FILES["uploaded_file"]["name"])) {
        $uploaded_file = $_FILES["uploaded_file"];
        $target_dir = "uploads/";

        if ($uploaded_file["error"] == UPLOAD_ERR_OK) {
            $target_file = $target_dir . basename($uploaded_file["name"]);
            if (move_uploaded_file($uploaded_file["tmp_name"], $target_file)) {
                $file_name = basename($uploaded_file["name"]);
                $sql = "INSERT INTO tb (direktorat, kompartemen, departemen,  jenis_pekerjaan, tipe_borongan, klasifikasi, formasi, aktual, keterangan, file_name) " .
                    "VALUES ('$direktorat', '$kompartemen', '$departemen', '$jenis_pekerjaan', '$tipe_borongan', '$klasifikasi', '$formasi', '$aktual', '$keterangan', '$file_name')";
                $result = $connection->query($sql);

                if (!$result) {
                    $errorMessage = "Query tidak valid: " . $connection->error;
                } else {
                    // Insert data into riwayat_tb table with perubahan information
                    $perubahan_info = "Ditambahkan pada " . date("d/m/Y"); // Get current date
                    $sql_riwayat_tb = "INSERT INTO riwayat_tb (direktorat, kompartemen, departemen, jenis_pekerjaan, tipe_borongan, klasifikasi, formasi, aktual, keterangan, file_name, perubahan) " .
                        "VALUES ('$direktorat', '$kompartemen', '$departemen', '$jenis_pekerjaan', '$tipe_borongan', '$klasifikasi', '$formasi', '$aktual', '$keterangan', '$file_name', '$perubahan_info')";
                    $result_riwayat_tb = $connection->query($sql_riwayat_tb);

                    if (!$result_riwayat_tb) {
                        $errorMessage = "Query tidak valid: " . $connection->error;
                    } else {
                        $direktorat = "";
                        $kompartemen = "";
                        $departemen  = "";
                        $jenis_pekerjaan = "";
                        $tipe_borongan = "";
                        $klasifikasi = "";
                        $formasi = "";
                        $aktual = "";
                        $keterangan = "";

                        $successMessage = "Data berhasil ditambahkan";
                        // Update total rows and total pages
                        $sql_total = "SELECT COUNT(*) as total FROM tb";
                        $result_total = $connection->query($sql_total);
                        $total_rows = $result_total->fetch_assoc()['total'];
                        $total_pages = ceil($total_rows / $limit);
                    }
                }
            } else {
                $errorMessage = "Gagal mengunggah file.";
            }
        }
    }
    if (empty($departemen) ||  empty($jenis_pekerjaan) || empty($tipe_borongan) || empty($klasifikasi) || empty($formasi) || empty($aktual) || empty($keterangan) || empty($file_name)) {
        $errorMessage = "Semua kolom harus di isi";
    }
}
// Ensure current page does not exceed total pages
if ($page > $total_pages) {
    $page = $total_pages;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TB || Tambah Data</title>
    <link rel="stylesheet" href="/tkno/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
<?php include "../navigasi.php"; ?>
    <div class="w3-main">
        <div class="w3-light-grey">
            <div class="w3-container">
                <div class="container my-5">
                    <h2>Tambah Data</h2><br>
                    <div class="row justify-content-md-center">
                        <div class="col-md-12">
                        <?php
                        if (!empty($errorMessage)) {
                            echo "
                                <div class ='alert alert-warning alert-dismissible fade show col-md-9' role='alert'>
                                <strong>$errorMessage</strong>
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                </div>
                            ";
                        }
                        ?>

                        <form method="post" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Direktorat Utama</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="direktorat" value="Direktorat Utama" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Kompartemen</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="kompartemen" value="Kompartemen Transformasi Bisnis" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Departemen</label>
                                <div class="col-sm-6">
                                    <select name="departemen" class="form-select">
                                        <option value="Departemen Mitra Bisnis Layanan TI PSP (dedicated PT Pupuk Indonesia)">Departemen Mitra Bisnis Layanan TI PSP (dedicated PT Pupuk Indonesia)</option>
                                        <option value="Departemen Pengelolaan Transformasi Bisnis">Departemen Pengelolaan Transformasi Bisnis</option>
                                        <option value="Departemen Riset">Departemen Riset</option>
                                        <option value="Departemen Sistem Manajemen Terpadu & Inovasi">Departemen Sistem Manajemen Terpadu & Inovasi</option>
                                        <option value="Proyek Agro Solution">Proyek Agro Solution</option>
                                        <option value="Proyek Retail Management PSP / CCM (dedicated PT Pupuk Indonesia)">Proyek Retail Management PSP / CCM (dedicated PT Pupuk Indonesia)</option>
                                        <option value="Proyek Agro Solution">Proyek Agro Solution</option>
                                        <option value="Kompartemen Transformasi Bisnis">Kompartemen Transformasi Bisnis</option>
                                    </select>
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
                                        <input class="form-check-input" type="radio" name="tipe_borongan" id="tipe_borongan1" value="Borongan">
                                        <label class="form-check-label" for="tipe_borongan1">
                                            Borongan
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_borongan" id="tipe_borongan2" value="Non Borongan">
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
                                <label class="col-sm-3 col-form-label">Unggah File</label>
                                <div class="col-sm-6">
                                    <input type="file" class="form-control" name="uploaded_file" value="<?php echo $file_name; ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="offset-sm-3 col-sm-3 d-grid">
                                    <button type="submit" class="btn btn-warning" role="button"><b>Simpan</b></button>
                                </div>
                                <div class="col-sm-3 d-grid">
                                    <a class="btn btn-danger" button href=" /tkno/tb/tb.php" role="button"><b>Batal</b></a>
                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body d-flex flex-column align-items-center">
                    <img src="/tkno/img/checkmark.gif" alt="Check Mark" width="150px" height="150px">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Data berhasil ditambahkan!</h1><br>
                    <button type="button" class="btn btn-info" data-bs-dismiss="modal"><b>Oke</b></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            <?php if (!empty($successMessage)) : ?>
                $('#successModal').modal('show');
                $('#successModal').on('hidden.bs.modal', function() {
                    window.location.href = '/tkno/tb/tb.php?page=<?php echo $total_pages; ?>#bottom';
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>