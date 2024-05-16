<?php
include '../koneksi.php';

$tipe_borongan = isset($_GET['tipe_borongan']) ? $_GET['tipe_borongan'] : 'Borongan';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="/tkno/index.css">
    <title>Riwayat SBUJPP </title>
</head>

<body>
<?php include "../navigasi.php"; ?>
    <div class="w3-main">
        <div class="w3-light-grey">
            <div class="w3-container"><br>
                <h1>Riwayat SBUJPP</h1>
                <div class="container my-3" style="max-width: 100%">
                    <a class="btn btn-warning" href="/tkno/sbujpp/sbujpp.php" role="button"><i class="fa-solid fa-arrow-left"></i></a>
                    <br><br>
                    <!-- Dropdowns for Filters -->
                    <div style="display: flex; gap: 10px; margin-right: 2px;">
                        <label class="col-form-label" for="departemenFilter">Departemen:</label>
                        <select class="col form-select" id="departemenFilter">
                            <option value="">Semua</option>
                            <option value="Departemen Bisnis & Keuangan JPP">Departemen Bisnis & Keuangan JPP</option>
                            <option value="Departemen Bengkel & Alat Berat">Departemen Bengkel & Alat Berat</option>
                            <option value="Departemen Operasi & Pemeliharaan I">Departemen Operasi & Pemeliharaan I</option>
                            <option value="Departemen Operasi & Pemeliharaan II">Departemen Operasi & Pemeliharaan II</option>
                            <option value="Kompartemen SBU Jasa Pemeliharaan Pabrik">Kompartemen SBU Jasa Pemeliharaan Pabrik</option>
                        </select>
                        <label class="col-form-label" for="tipeFilter">Tipe:</label>
                        <select class="col form-select" id="tipeFilter">
                            <option value="">Semua</option>
                            <option value="Borongan">Borongan</option>
                            <option value="Non Borongan">Non Borongan</option>
                        </select>
                    </div><br>
                    <table id='historyTable' class='table table-hover table-bordered'>
                        <thead class='table-light'>
                            <tr>
                                <th>Departemen</th>
                                <th>Jenis Pekerjaan </th>
                                <th>Tipe Borongan</th>
                                <th>Klasifikasi</th>
                                <th>Formasi</th>
                                <th>Aktual</th>
                                <th>Keterangan</th>
                                <th>Bukti</th>
                                <th>Perubahan</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "SELECT * FROM riwayat_sbujpp";
                        $result = $connection->query($sql);

                        if (!$result) {
                            die("Query tidak valid: " . $connection->error);
                        }
                        
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>$row[departemen]</td>
                                <td>$row[jenis_pekerjaan]</td>
                                <td>$row[tipe_borongan]</td>
                                <td>$row[klasifikasi]</td>
                                <td>$row[formasi]</td>
                                <td>$row[aktual]</td>
                                <td>$row[keterangan]</td>
                                <td>";

                                if (!empty($row['file_name'])) {
                                    echo "$row[file_name]";
                                } else {
                                    echo "File belum diupload";
                                }

                                echo "
                                    </td>
                                    <td>$row[perubahan]</td>
                                </tr>";
                        }
                        
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        //Konfigurasi DataTables
        $(document).ready(function () {
            var dataTable = $('#historyTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: '',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    }
                ]
            });

            // Apply filters when dropdown values change
            $('#departemenFilter, #tipeFilter').on('change', function () {
                var departemenFilter = $('#departemenFilter').val();
                var tipeFilter = $('#tipeFilter').val();

                // Set filters
                dataTable.column(0).search(departemenFilter).draw();
                
                // Apply more specific filter for "tipe"
                if (tipeFilter === 'Borongan') {
                    dataTable.column(2).search('^' + tipeFilter, true, false).draw();
                } else {
                    dataTable.column(2).search(tipeFilter).draw();
                }
            });
        });
    </script>
</body>

</html>