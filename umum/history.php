<?php
include '../koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/tkno/index.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.1.0/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>
    <title>Riwayat Umum</title>
</head>

<body>
<?php include "../navigasi.php"; ?>
    <div class="w3-main">
        <div class="w3-light-grey">
            <div class="w3-container"><br>
                <h1>Riwayat Umum</h1>
                <div class="container my-3" style="max-width: 100%">
                    <a class="btn btn-warning" href="/tkno/umum/umum.php" role="button"><i class="fa-solid fa-arrow-left"></i></a>
                    <br><br>
                    <!-- Dropdowns for Filters -->
                    <div style="display: flex; gap: 10px; margin-right: 2px;">
                        <label class="col-form-label" for="departemenFilter">Departemen:</label>
                        <select class="col form-select" id="departemenFilter">
                            <option value="">Semua</option>
                            <option value="Departemen Sarana & Umum">Departemen Sarana & Umum</option>
                            <option value="Departemen Sekuriti">Departemen Sekuriti</option>
                            <option value="Kantor Perwakilan Jakarta Wisma 101">Kantor Perwakilan Jakarta Wisma 101</option>
                            <option value="Kompartemen Umum">Kompartemen Umum</option>
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
                        $sql = "SELECT * FROM riwayat_umum";
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