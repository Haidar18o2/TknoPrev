<?php
include '../koneksi.php';

//Paginasi
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10;

$backgroundColorsKategori = [
    'rgba(255, 99, 132)',
    'rgba(54, 162, 235)',
    'rgba(255, 206, 86)',
    'rgba(75, 192, 192)',
    'rgba(153, 102, 255)',
    'rgba(255, 159, 64)'
];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.1.0/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/js/bootstrap.bundle.min.js"></script>
    <title>Perencanaan dan Pengendalian Pemeliharaan</title>
</head>

<body>
<?php include "../navigasi.php"; ?><br>
    <div class="w3-main">
        <div class="w3-light-grey">
            <div class="w3-container"><br>
                <h1>Perencanaan dan Pengendalian Pemeliharaan</h1>
                <div class="container my-3" style="max-width: 100%">
                    <a class="btn btn-warning" href="/tkno/rendalhar/create.php" role="button">
                    <i class="fa-solid fa-plus" style="color: black;"></i>
                    </a>
                    <a class="btn btn-primary" href="/tkno/rendalhar/history.php" role="button">
                    <i class="fa-solid fa-clock-rotate-left" style="color: black;"></i>
                    </a>
                    <br><br>
                      <!-- Card Total -->
                      <div style="display: flex; gap: 10px; justify-content:center;">
                         <div class="card" style="width: 25%">
                            <div class="card-body">
                                <h5 class="card-title"><b>Total TKNO per Kategori</b></h5>
                                <?php
                                    $sql_total_tkno_per_kategori = "SELECT 
                                                                        SUM(CASE WHEN tipe_borongan = 'Borongan' THEN formasi ELSE 0 END) AS total_tkno_borongan,
                                                                        SUM(CASE WHEN tipe_borongan = 'Non Borongan' THEN formasi ELSE 0 END) AS total_tkno_non_borongan,
                                                                        SUM(formasi) AS total_tkno,
                                                                        SUM(CASE WHEN tipe_borongan = 'Borongan' THEN aktual ELSE 0 END) AS total_aktual_tkno_borongan,
                                                                        SUM(CASE WHEN tipe_borongan = 'Non Borongan' THEN aktual ELSE 0 END) AS total_aktual_tkno_non_borongan,
                                                                        SUM(aktual) AS total_aktual_tkno
                                                                    FROM rendalhar";
                                    $result_total_tkno_per_kategori = $connection->query($sql_total_tkno_per_kategori);
                                    $data_total_tkno_per_kategori = $result_total_tkno_per_kategori->fetch_assoc();
                                ?>
                                <div id="customLegend" style="font-size: 14px;">
                                    <div class="legend-item" style="display: inline-block; margin-right: 10px;">
                                        <span style="background-color: rgba(255, 99, 132); width: 20px; height: 10px; display: inline-block; margin-right: 5px;"></span>
                                        Total TKNO
                                    </div>
                                    <div class="legend-item" style="display: inline-block; margin-right: 10px;">
                                        <span style="background-color: rgba(54, 162, 235); width: 20px; height: 10px; display: inline-block; margin-right: 5px;"></span>
                                        Total TKNO Borongan
                                    </div>
                                    <div class="legend-item" style="display: inline-block; margin-right: 10px;">
                                        <span style="background-color: rgba(255, 206, 86); width: 20px; height: 10px; display: inline-block; margin-right: 5px;"></span>
                                        Total TKNO Non Borongan
                                    </div>
                                </div><br>
                                <canvas id="myBarChartKategori"></canvas>
                                <h6>Keterangan: Kiri = Formasi | Kanan = Aktual</h6>
                            </div>
                        </div>
                        <div class="card" style="width: 25%">
                            <div class="card-body">
                                <h5 class="card-title"><b>Total TKNO per Departemen</b></h5>
                                <?php
                                $sql_total_tkno_per_departemen = "SELECT departemen, SUM(formasi) as total_formasi, SUM(aktual) as total_aktual FROM rendalhar GROUP BY departemen";
                                $result_total_tkno_per_departemen = $connection->query($sql_total_tkno_per_departemen);
                                $data_total_tkno_per_departemen = array();

                                while ($row_total_tkno_per_departemen = $result_total_tkno_per_departemen->fetch_assoc()) {
                                    $data_total_tkno_per_departemen[] = $row_total_tkno_per_departemen;
                                }
                                ?>
                                <div id="customLegend" style="font-size: 14px;">
                                    <?php
                                    foreach ($data_total_tkno_per_departemen as $i => $row) {
                                        $label = $row['departemen'];
                                        $backgroundColor = $backgroundColorsKategori[$i];
                                    ?>
                                    <div class="legend-item" style="display: inline-block; margin-right: 10px;">
                                        <span style="background-color: <?php echo $backgroundColor; ?>; width: 20px; height: 10px; display: inline-block; margin-right: 5px;"></span>
                                        <?php echo $label; ?>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div><br>
                                <canvas id="myBarChartDepartemen"></canvas>
                                <h6>Keterangan: Kiri = Formasi | Kanan = Aktual</h6>
                            </div>
                        </div>
                        <div class="card" style="width: 25%">
                            <div class="card-body">
                                <h5 class="card-title"><b>Total TKNO per Departemen Borongan</b></h5>
                                <h6 class="card-subtitle mb-2 text-body-secondary">
                                <?php
                                    $sql_total_tkno_per_departemen_borongan = "SELECT 
                                                                                    departemen,
                                                                                    SUM(CASE WHEN tipe_borongan = 'Borongan' THEN formasi ELSE 0 END) AS total_tkno_borongan,
                                                                                    SUM(CASE WHEN tipe_borongan = 'Borongan' THEN aktual ELSE 0 END) AS total_aktual_tkno_borongan
                                                                                FROM rendalhar
                                                                                GROUP BY departemen";
                                                                                
                                    $result_total_tkno_per_departemen_borongan = $connection->query($sql_total_tkno_per_departemen_borongan);
                                    $data_total_tkno_per_departemen_borongan = array();

                                    while ($row_total_tkno_per_departemen_borongan = $result_total_tkno_per_departemen_borongan->fetch_assoc()) {
                                        $data_total_tkno_per_departemen_borongan[] = $row_total_tkno_per_departemen_borongan;
                                    }
                                ?>
                                <div id="customLegend" style="font-size: 14px;">
                                    <?php
                                    foreach ($data_total_tkno_per_departemen_borongan as $i => $row) {
                                        $label = $row['departemen'];
                                        $backgroundColor = $backgroundColorsKategori[$i];
                                    ?>
                                    <div class="legend-item" style="display: inline-block; margin-right: 10px;">
                                        <span style="background-color: <?php echo $backgroundColor; ?>; width: 20px; height: 10px; display: inline-block; margin-right: 5px;"></span>
                                        <?php echo $label; ?>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div><br>
                                <canvas id="myBarChartDepartemenBorongan"></canvas>
                                <h6>Keterangan: Kiri = Formasi | Kanan = Aktual</h6>
                            </div>
                        </div>
                        <div class="card" style="width: 25%">
                            <div class="card-body">
                                <h5 class="card-title"><b>Total TKNO per Departemen Non Borongan</b></h5>
                                <h6 class="card-subtitle mb-2 text-body-secondary">
                                <?php
                                    $sql_total_tkno_per_departemen_nonborongan = "SELECT 
                                                                                    departemen,
                                                                                    SUM(CASE WHEN tipe_borongan = 'Non Borongan' THEN formasi ELSE 0 END) AS total_tkno_nonborongan,
                                                                                    SUM(CASE WHEN tipe_borongan = 'Non Borongan' THEN aktual ELSE 0 END) AS total_aktual_tkno_nonborongan
                                                                                FROM rendalhar
                                                                                GROUP BY departemen";
                                                                                
                                    $result_total_tkno_per_departemen_nonborongan = $connection->query($sql_total_tkno_per_departemen_nonborongan);
                                    $data_total_tkno_per_departemen_nonborongan = array();

                                    while ($row_total_tkno_per_departemen_nonborongan = $result_total_tkno_per_departemen_nonborongan->fetch_assoc()) {
                                        $data_total_tkno_per_departemen_nonborongan[] = $row_total_tkno_per_departemen_nonborongan;
                                    }
                                ?>
                                <div id="customLegend" style="font-size: 14px;">
                                    <?php
                                    foreach ($data_total_tkno_per_departemen_nonborongan as $i => $row) {
                                        $label = $row['departemen'];
                                        $backgroundColor = $backgroundColorsKategori[$i];
                                    ?>
                                    <div class="legend-item" style="display: inline-block; margin-right: 10px;">
                                        <span style="background-color: <?php echo $backgroundColor; ?>; width: 20px; height: 10px; display: inline-block; margin-right: 5px;"></span>
                                        <?php echo $label; ?>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div><br>
                                <canvas id="myBarChartDepartemenNonBorongan"></canvas>
                                <h6>Keterangan: Kiri = Formasi | Kanan = Aktual</h6>
                            </div>
                        </div>
                    </div><br>
                    <!-- Dropdowns for Filters -->
                    <div style="display: flex; gap: 10px; margin-right: 2px;">
                        <label class="col-form-label" for="departemenFilter">Departemen:</label>
                        <select class="col form-select" id="departemenFilter">
                            <option value="">Semua</option>
                            <option value="Departemen Keandalan Pabrik">Departemen Keandalan Pabrik</option>
                            <option value="Departemen Penjamin Kualitas">Departemen Penjamin Kualitas</option>
                            <option value="Departemen Perencanaan Pemeliharaan">Departemen Perencanaan Pemeliharaan</option>
                            <option value="Departemen Perencanaan & Pengendalian Turn Around">Departemen Perencanaan & Pengendalian Turn Around</option>
                            <option value="Kompartemen Perencanaan & Pengendalian Pemeliharaan">Kompartemen Perencanaan & Pengendalian Pemeliharaan</option>
                        </select>
                        <label class="col-form-label" for="tipeFilter">Tipe:</label>
                        <select class="col form-select" id="tipeFilter">
                            <option value="">Semua</option>
                            <option value="Borongan">Borongan</option>
                            <option value="Non Borongan">Non Borongan</option>
                        </select>
                    </div><br>
                    <!-- Tabel -->
                    <div class="table-responsive">
                        <table id="myTable" class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th data-searchable="true">Departemen</th>
                                    <th data-searchable="true">Jenis Pekerjaan</th>
                                    <th data-searchable="true">Tipe</th>
                                    <th data-searchable="true">Klasifikasi</th>
                                    <th data-searchable="true">Formasi</th>
                                    <th data-searchable="true">Aktual</th>
                                    <th data-searchable="true">Keterangan</th>
                                    <th data-searchable="true">Bukti</th>
                                    <th data-searchable="false" class='no-export'>Tinjau</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM rendalhar";
                                $result = $connection->query($sql);

                                if (!$result) {
                                    die("Query tidak valid: " . $connection->error);
                                }

                                while ($row = $result->fetch_assoc()) {
                                    $id = $row['id'];
                                    echo "
                                        <tr id='data_$id'>
                                            <td>$row[departemen]</td>
                                            <td>$row[jenis_pekerjaan]</td>
                                            <td>$row[tipe_borongan]</td>
                                            <td>$row[klasifikasi]</td>
                                            <td>$row[formasi]</td>
                                            <td>$row[aktual]</td>
                                            <td>$row[keterangan]</td>
                                            <td>";

                                    // Periksa apakah ada file yang diunggah
                                    if (!empty($row['file_name'])) {
                                        echo "$row[file_name] <br>
                                        <a class='btn-sm btn-margin' href='/tkno/rendalhar/uploads/$row[file_name]' target='_blank'><i class='fa-solid fa-up-right-from-square' style='color: ;'></i></a>
                                        <a class='btn-sm btn-margin' onclick='showDeleteFileConfirmationModal($row[id])'><i class='fa-solid fa-trash' style='color: ;'></i></a>";
                                    } else {
                                        echo "File belum diupload";
                                    }

                                    echo "
                                        </td>
                                        <td>
                                            <a class='btn btn-warning btn-sm btn-margin' href='/tkno/rendalhar/edit.php?id=$id&page=$page'><i class= 'fa-solid fa-pen-to-square' style='color: #51461f;'></i></a>
                                            <a class='btn btn-danger btn-sm btn-margin' onclick='showDeleteConfirmationModal($row[id])'><i class='fa-solid fa-trash' style='color: ;'></i></a>";

                                    echo "
                                        </td>
                                    </tr>
                                    ";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal Konfirmasi Hapus -->
                    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="justify-content: left;">
                                    <i class="fa-solid fa-triangle-exclamation fa-lg" style="color: #ff0000; margin-right: 10px;"></i>
                                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Konfirmasi Hapus Data</h5>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus data ini?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <a id="deleteLink" href="javascript:void(0);" class="btn btn-danger">Hapus</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Konfirmasi Hapus File -->
                    <div class="modal fade" id="deleteFileConfirmationModal" tabindex="-1" aria-labelledby="deleteFileConfirmationModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="justify-content: left;">
                                    <i class="fa-solid fa-triangle-exclamation fa-lg" style="color: #ff0000; margin-right: 10px;"></i>
                                    <h5 class="modal-title" id="deleteFileConfirmationModalLabel">Konfirmasi Hapus File</h5>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus file ini?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <a id="deleteFile" href="#" class="btn btn-danger">Hapus</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Paginasi -->
                    <?php
                    $sql_total = "SELECT COUNT(*) as total FROM rendalhar";
                    $result_total = $connection->query($sql_total);
                    $total_rows = $result_total->fetch_assoc()['total'];
                    $total_pages = ceil($total_rows / $limit);
                    ?>
                    <span id="current-page" style="display: none;"><?php echo $page; ?></span>
                </div>
            </div>
        </div>
        <button onclick="scrollToTop()" id="scrollToTopBtn" title="Go to top"><i class="fas fa-arrow-circle-up"></i></button>
    </div>

    <script>
        //grafik total Kategori
        var ctxBarKategori = document.getElementById('myBarChartKategori').getContext('2d');
        var dataBarKategori = <?php echo json_encode($data_total_tkno_per_kategori); ?>;
 
        var labelsBarKategori = ['Total TKNO', 'Total TKNO Borongan', 'Total TKNO Non Borongan'];
        var formasiDataBarKategori = [
            dataBarKategori.total_tkno,
            dataBarKategori.total_tkno_borongan,
            dataBarKategori.total_tkno_non_borongan
        ];
 
        var aktualDataBarKategori = [
            dataBarKategori.total_aktual_tkno,
            dataBarKategori.total_aktual_tkno_borongan,
            dataBarKategori.total_aktual_tkno_non_borongan
        ];
 
        var myBarChartKategori = new Chart(ctxBarKategori, {
            type: 'bar',
            data: {
                labels: labelsBarKategori,
                datasets: [
                    {
                        data: formasiDataBarKategori,
                        backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                        borderWidth: 1,
                        label: 'Formasi'
                    },
                    {
                        data: aktualDataBarKategori,
                        backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                        borderWidth: 1,
                        label: 'Aktual'
                    }
                ]
            },
            options: {
            scales: {
                x: {
                    stacked: false,
                    ticks: {
                        display: false // Set to false to hide x-axis labels on bars
                    }
                },
                y: {
                    stacked: false
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
        });
 
   // grafik total per departemen
    var ctxBarDepartemen = document.getElementById('myBarChartDepartemen').getContext('2d');
    var dataBarDepartemen = <?php echo json_encode($data_total_tkno_per_departemen); ?>;

    var labelsBarDepartemen = dataBarDepartemen.map(function (item) {
        return item.departemen;
    });

    var formasiDataBarDepartemen = dataBarDepartemen.map(function (item) {
        return item.total_formasi;
    });

    var aktualDataBarDepartemen = dataBarDepartemen.map(function (item) {
        return item.total_aktual;
    });

    var myBarChartDepartemen = new Chart(ctxBarDepartemen, {
        type: 'bar',
        data: {
            labels: labelsBarDepartemen,
            datasets: [
                {
                    label: 'Formasi',
                    data: formasiDataBarDepartemen,
                    backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                    borderWidth: 1,
                    order: 1
                },
                {
                    label: 'Aktual',
                    data: aktualDataBarDepartemen,
                    backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                    borderWidth: 1,
                    order: 2
                }
            ]
        },
        options: {
            scales: {
                x: {
                    stacked: false,
                    ticks: {
                        display: false // Set to false to hide x-axis labels on bars
                    }
                },
                y: {
                    stacked: false
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
        //grafik total per departemen borongan

        var ctxBarDepartemenBorongan = document.getElementById('myBarChartDepartemenBorongan').getContext('2d');
        var dataBarDepartemenBorongan = <?php echo json_encode($data_total_tkno_per_departemen_borongan); ?>;

        var labelsBarDepartemenBorongan = dataBarDepartemenBorongan.map(function (item) {
            return item.departemen;
        });

        var formasiDataBarDepartemenBorongan = dataBarDepartemenBorongan.map(function (item) {
            return item.total_tkno_borongan;
        });

        var aktualDataBarDepartemenBorongan = dataBarDepartemenBorongan.map(function (item) {
            return item.total_aktual_tkno_borongan;
        });

        var myBarChartDepartemenBorongan = new Chart(ctxBarDepartemenBorongan, {
            type: 'bar',
            data: {
                labels: labelsBarDepartemenBorongan,
                datasets: [
                    {
                        data: formasiDataBarDepartemenBorongan,
                        backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                        borderWidth: 1,
                        label: 'Formasi'
                    },
                    {
                        data: aktualDataBarDepartemenBorongan,
                        backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                        borderWidth: 1,
                        label: 'Aktual'
                    }
                ]
            },
            options: {
            scales: {
                x: {
                    stacked: false,
                    ticks: {
                        display: false // Set to false to hide x-axis labels on bars
                    }
                },
                y: {
                    stacked: false
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
        });

        //grafik total per departemen non borongan
        var ctxBarDepartemenNonBorongan = document.getElementById('myBarChartDepartemenNonBorongan').getContext('2d');
        var dataBarDepartemenNonBorongan = <?php echo json_encode($data_total_tkno_per_departemen_nonborongan); ?>;

        var labelsBarDepartemenNonBorongan = dataBarDepartemenNonBorongan.map(function (item) {
            return item.departemen;
        });

        var formasiDataBarDepartemenNonBorongan = dataBarDepartemenNonBorongan.map(function (item) {
            return item.total_tkno_nonborongan;
        });

        var aktualDataBarDepartemenNonBorongan = dataBarDepartemenNonBorongan.map(function (item) {
            return item.total_aktual_tkno_nonborongan;
        });

        var myBarChartDepartemenNonBorongan = new Chart(ctxBarDepartemenNonBorongan, {
            type: 'bar',
            data: {
                labels: labelsBarDepartemenNonBorongan,
                datasets: [
                    {
                        data: formasiDataBarDepartemenNonBorongan,
                        backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                        borderWidth: 1,
                        label: 'Formasi'
                    },
                    {
                        data: aktualDataBarDepartemenNonBorongan,
                        backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                        borderWidth: 1,
                        label: 'Aktual'
                    }
                ]
            },
            options: {
            scales: {
                x: {
                    stacked: false,
                    ticks: {
                        display: false // Set to false to hide x-axis labels on bars
                    }
                },
                y: {
                    stacked: false
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
        });

            //Konfigurasi DataTables
        $(document).ready(function () {
            var dataTable = $('#myTable').DataTable({
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

        // Auto-scroll setelah batal mengedit ataupun menyimpan data yang diedit
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const page = urlParams.get('page');
            if (page) {
                const targetElementId = urlParams.get('targetElementId');
                if (targetElementId) {
                    const dataElement = document.getElementById(targetElementId);
                    if (dataElement) {
                        dataElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        // Highlight selama 2 detik
                        dataElement.style.outline = '2px solid yellow';
                        setTimeout(function() {
                            dataElement.style.outline = 'none';
                        }, 2000);
                    }
                }
            }
        });

        function deleteData(id) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "/tkno/rendalhar/delete.php?action=delete&id=" + id, true);

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 400) {
                    // Permintaan berhasil, periksa jumlah baris di halaman saat ini
                    var currentPage = parseInt(document.getElementById('current-page').innerText);
                    var totalRowsOnPage = document.querySelectorAll('tbody tr').length;

                    if (totalRowsOnPage === 1 && currentPage > 1) {
                        // Jika tidak ada baris lagi di halaman saat ini dan pengguna bukan di halaman pertama, navigasikan ke halaman sebelumnya
                        window.location.href = '?page=' + (currentPage - 1);
                    } else {
                        // Muat ulang halaman saat penghapusan berhasil
                        location.reload();
                    }
                } else {
                    // Kesalahan saat melakukan permintaan
                    console.error(xhr.statusText);
                }
            };

            xhr.onerror = function() {
                // Kesalahan jaringan
                console.error(xhr.statusText);
            };

            xhr.send();
        }

        function deleteFile(id) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "/tkno/rendalhar/deleteFile.php?id=" + id, true);

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 400) {
                    // Permintaan berhasil, muat ulang halaman
                    location.reload();
                } else {
                    // Kesalahan saat melakukan permintaan
                    console.error(xhr.statusText);
                }
            };

            xhr.onerror = function() {
                // Kesalahan jaringan
                console.error(xhr.statusText);
            };

            xhr.send();
        }

        // Fungsi untuk menampilkan modal konfirmasi
        function showDeleteConfirmationModal(id) {
            var deleteLink = document.getElementById('deleteLink');
            deleteLink.href = 'javascript:deleteData(' + id + ')';
            var modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            modal.show();
        }

        function showDeleteFileConfirmationModal(id) {
            var deleteFile = document.getElementById('deleteFile');
            deleteFile.href = 'javascript:deleteFile(' + id + ')';
            var modal = new bootstrap.Modal(document.getElementById('deleteFileConfirmationModal'));
            modal.show();
        }
        
        
         //autoscroll keatas
        // Scroll to Top Button Functionality
        function scrollToTop() {
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE, and Opera
        }

        // Show/Hide Scroll to Top Button
        window.onscroll = function() {
            var scrollToTopBtn = document.getElementById("scrollToTopBtn");
            // Display the button when scrolling down
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                scrollToTopBtn.style.display = "block";
            } else {
                scrollToTopBtn.style.display = "none";
            }
        };
    </script>
</body>

</html>