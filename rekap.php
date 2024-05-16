<?php
include 'koneksi.php';

// Daftar nama tabel
$tables = [
    'sekper', 'spi', 'tb', 'operasi', 'teknologi', 'rendalhar',
    'sbu_jpp', 'rantai_pasok', 'tekbang', 'adm_keu', 'sdm', 'umum', 'proyek_proyek'
];

//Paginasi
// Set limit
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Filter values
$direktoratFilter = isset($_GET['direktoratFilter']) ? $_GET['direktoratFilter'] : 'all';
$tipeBoronganFilter = isset($_GET['tipeBoronganFilter']) ? $_GET['tipeBoronganFilter'] : 'all';

// Check if the "Show All" checkbox is checked
$showAll = isset($_GET['showAll']) && $_GET['showAll'] === 'on';

// SQL query untuk mengambil data dengan filter
$sql = "SELECT direktorat, kompartemen, departemen, jenis_pekerjaan, tipe_borongan, klasifikasi, formasi, aktual, keterangan, file_name FROM (";

foreach ($tables as $table) {
    $sql .= "SELECT direktorat, kompartemen, departemen, jenis_pekerjaan, tipe_borongan, klasifikasi, formasi, aktual, keterangan, file_name FROM $table ";
    if ($direktoratFilter !== 'all') {
        $sql .= " WHERE direktorat = '$direktoratFilter' ";
    }
    if ($tipeBoronganFilter !== 'all') {
        $sql .= ($direktoratFilter !== 'all') ? " AND tipe_borongan = '$tipeBoronganFilter' " : " WHERE tipe_borongan = '$tipeBoronganFilter' ";
    }
    $sql .= " UNION ALL ";
}

// Remove the last " UNION ALL "
$sql = rtrim($sql, " UNION ALL ");

$sql .= " ) AS combined_table";

$result = $connection->query($sql);

if (!$result) {
    die("Query tidak valid: " . $connection->error);
}

//Grafik
// SQL query untuk total formasi dan aktual
$sqlTotal = "SELECT SUM(formasi) as total_formasi, SUM(aktual) as total_aktual FROM (";
foreach ($tables as $table) {
    $sqlTotal .= "SELECT formasi, aktual FROM $table UNION ALL ";
}
$sqlTotal = rtrim($sqlTotal, 'UNION ALL ') . ") AS combined_data";

// SQL query untuk total formasi dan aktual per direktorat
$sqlDirektorat = "SELECT direktorat, SUM(formasi) as total_formasi, SUM(aktual) as total_aktual FROM (";
foreach ($tables as $table) {
    $sqlDirektorat .= "SELECT direktorat, formasi, aktual FROM $table UNION ALL ";
}
$sqlDirektorat = rtrim($sqlDirektorat, 'UNION ALL ') . ") AS combined_data GROUP BY direktorat";

// SQL query untuk total formasi dan aktual per direktorat (borongan)
$sqlBorongan = "SELECT direktorat, SUM(formasi) as total_formasi, SUM(aktual) as total_aktual FROM (";
foreach ($tables as $table) {
    $sqlBorongan .= "SELECT direktorat, formasi, aktual FROM $table WHERE tipe_borongan = 'Borongan' UNION ALL ";
}
$sqlBorongan = rtrim($sqlBorongan, 'UNION ALL ') . ") AS combined_data GROUP BY direktorat";

// SQL query untuk total formasi dan aktual per direktorat (non-borongan)
$sqlNonBorongan = "SELECT direktorat, SUM(formasi) as total_formasi, SUM(aktual) as total_aktual FROM (";
foreach ($tables as $table) {
    $sqlNonBorongan .= "SELECT direktorat, formasi, aktual FROM $table WHERE tipe_borongan = 'Non Borongan' UNION ALL ";
}
$sqlNonBorongan = rtrim($sqlNonBorongan, 'UNION ALL ') . ") AS combined_data GROUP BY direktorat";

// Eksekusi query dan ambil hasilnya
$resultTotal = $connection->query($sqlTotal);
$resultDirektorat = $connection->query($sqlDirektorat);
$resultBorongan = $connection->query($sqlBorongan);
$resultNonBorongan = $connection->query($sqlNonBorongan);

// Extract data for total chart
$rowTotal = $resultTotal->fetch_assoc();
$totalFormasi = $rowTotal['total_formasi'];
$totalAktual = $rowTotal['total_aktual'];

// Extract data for direktorat chart
while ($rowDirektorat = $resultDirektorat->fetch_assoc()) {
    $direktoratLabels[] = $rowDirektorat['direktorat'];
    $direktoratFormasi[] = $rowDirektorat['total_formasi'];
    $direktoratAktual[] = $rowDirektorat['total_aktual'];
}

// Extract data for borongan chart
while ($rowBorongan = $resultBorongan->fetch_assoc()) {
    $boronganLabels[] = $rowBorongan['direktorat'];
    $boronganFormasi[] = $rowBorongan['total_formasi'];
    $boronganAktual[] = $rowBorongan['total_aktual'];
}

// Extract data for non-borongan chart
while ($rowNonBorongan = $resultNonBorongan->fetch_assoc()) {
    $nonBoronganLabels[] = $rowNonBorongan['direktorat'];
    $nonBoronganFormasi[] = $rowNonBorongan['total_formasi'];
    $nonBoronganAktual[] = $rowNonBorongan['total_aktual'];
}

$backgroundColorsKategori = [
    'rgba(255, 99, 132)',
    'rgba(54, 162, 235)',
    'rgba(255, 206, 86)',
    'rgba(153, 102, 255)'
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
    <title>Rekap</title>
</head>

<body>
<?php include "navigasi.php"; ?>
    <div class="w3-main">
        <div class="w3-light-grey">
            <div class="w3-container"><br>
                <h1>Rekap</h1>
                <div class="container my-3" style="max-width:100%">
                    <!-- Card Total -->
                    <div style="display: flex; gap: 10px;">
                        <div class="card" style="width: 25%">
                            <div class="card-body">
                                <h5 class="card-title"><b>Total Formasi dan Aktual</b></h5>
                                <div class="chart-container">
                                    <canvas id="totalChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card" style="width: 25%">
                            <div class="card-body">
                                <h5 class="card-title"><b>Total Formasi dan Aktual per Direktorat</b></h5>
                                <div class="chart-container">
                                    <div id="customLegend" style="font-size: 14px;">
                                        <?php
                                        for ($i = 0; $i < count($direktoratLabels); $i++) {
                                            $label = $direktoratLabels[$i];
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
                                    <canvas id="direktoratChart"></canvas>
                                    <h6>Keterangan: Kiri = Formasi | Kanan = Aktual</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card" style="width: 25%">
                            <div class="card-body">
                                <h5 class="card-title"><b>Total Formasi dan Aktual per Direktorat (Borongan)</b></h5>
                                <div class="chart-container">
                                    <div id="customLegend" style="font-size: 14px;">
                                        <?php
                                        for ($i = 0; $i < count($boronganLabels); $i++) {
                                            $label = $boronganLabels[$i];
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
                                    <canvas id="boronganChart"></canvas>
                                    <h6>Keterangan: Kiri = Formasi | Kanan = Aktual</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card" style="width: 25%">
                            <div class="card-body">
                                <h5 class="card-title"><b>Total Formasi dan Aktual per Direktorat (Non Borongan)</b></h5>
                                <div class="chart-container">
                                    <div id="customLegend" style="font-size: 14px;">
                                        <?php
                                        for ($i = 0; $i < count($nonBoronganLabels); $i++) {
                                            $label = $nonBoronganLabels[$i];
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
                                    <canvas id="nonBoronganChart"></canvas>
                                    <h6>Keterangan: Kiri = Formasi | Kanan = Aktual</h6>
                                </div>
                            </div>
                        </div>
                    </div><br>
                    <!-- Dropdowns for Filters -->
                    <div style="display: flex; gap: 10px; margin-right: 2px;">
                        <label class="col-form-label" for="direktoratFilter">Direktorat:</label>
                        <select class="col form-select" id="direktoratFilter">
                            <option value="">Semua</option>
                            <option value="Direktorat Utama">Direktorat Utama</option>
                            <option value="Direktorat Operasi & Produksi">Direktorat Operasi & Produksi</option>
                            <option value="Direktorat Keu & Umum">Direktorat Keu & Umum</option>
                            <option value="Proyek Proyek">Proyek Proyek</option>
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
                        <table  id="myTable" class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Direktorat</th>
                                    <th>Kompartemen</th>
                                    <th>Departemen</th>
                                    <th>Jenis Pekerjaan</th>
                                    <th>Tipe</th>
                                    <th>Klasifikasi</th>
                                    <th>Formasi</th>
                                    <th>Aktual</th>
                                    <th>Keterangan</th>
                                    <th>Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = $result->fetch_assoc()) {
                                    echo "
                                    <tr>
                                        <td>$row[direktorat]</td>
                                        <td>$row[kompartemen]</td>
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
                                        echo "$row[file_name]";
                                    } else {
                                        echo "File belum diupload";
                                    }

                                    echo "
                                        </td>
                                    </tr>
                                    ";
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        // Count the total number of rows with the same filters
                        $countSql = "SELECT COUNT(*) AS total_rows FROM (";
                        foreach ($tables as $table) {
                            $countSql .= "SELECT direktorat, kompartemen, departemen, jenis_pekerjaan, tipe_borongan, klasifikasi, formasi, aktual, keterangan, file_name FROM $table ";
                            if ($direktoratFilter !== 'all') {
                                $countSql .= " WHERE direktorat = '$direktoratFilter' ";
                            }
                            if ($tipeBoronganFilter !== 'all') {
                                $countSql .= ($direktoratFilter !== 'all') ? " AND tipe_borongan = '$tipeBoronganFilter' " : " WHERE tipe_borongan = '$tipeBoronganFilter' ";
                            }
                            $countSql .= " UNION ALL ";
                        }
                        $countSql = rtrim($countSql, " UNION ALL ") . ") AS count_table";
                        $countResult = $connection->query($countSql);
                        $totalRows = $countResult->fetch_assoc()['total_rows'];
                        $totalPages = ceil($totalRows / $limit);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <button onclick="scrollToTop()" id="scrollToTopBtn" title="Go to top"><i class="fas fa-arrow-circle-up"></i></button>
    </div>

<script>
    var ctxTotal = document.getElementById('totalChart').getContext('2d');
    var totalChart = new Chart(ctxTotal, {
        type: 'bar',
        data: {
            labels: ['FormasiAktual'],
            datasets: [{
                data: [<?php echo $totalFormasi; ?>],
                backgroundColor: 'red',
                borderWidth: 1,
                label: 'Total Formasi'
            }, {
                data: [<?php echo $totalAktual; ?>],
                backgroundColor: 'blue',
                borderWidth: 1,
                label: 'Total Aktual'
            }]
        },
        options: {
            plugins: {
                legend: {
                    align: 'start'
                }
            },
      scales: {
            x: {
                ticks: {
                    display: false // Set to false to hide x-axis labels on bars
                }
            },
            y: {
                beginAtZero: true
            }
        }
        }
    });

    var ctxDirektorat = document.getElementById('direktoratChart').getContext('2d');
    var direktoratChart = new Chart(ctxDirektorat, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($direktoratLabels); ?>,
            datasets: [{
                label: 'Formasi',
                data: <?php echo json_encode($direktoratFormasi); ?>,
                backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                borderWidth: 1
            }, {
                label: 'Aktual',
                data: <?php echo json_encode($direktoratAktual); ?>,
                backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    ticks: {
                        display: false // Set to false to hide x-axis labels on bars
                    }
                },
                y: {
                    beginAtZero: true
                }
            }
        },
    });

    var ctxBorongan = document.getElementById('boronganChart').getContext('2d');
    var boronganChart = new Chart(ctxBorongan, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($boronganLabels); ?>,
            datasets: [{
                label: 'Formasi',
                data: <?php echo json_encode($boronganFormasi); ?>,
                backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                borderWidth: 1
            }, {
                label: 'Aktual',
                data: <?php echo json_encode($boronganAktual); ?>,
                backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    ticks: {
                        display: false // Set to false to hide x-axis labels on bars
                    }
                },
                y: {
                    beginAtZero: true
                }
            }
        },
    });

    var ctxNonBorongan = document.getElementById('nonBoronganChart').getContext('2d');
    var nonBoronganChart = new Chart(ctxNonBorongan, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($nonBoronganLabels); ?>,
            datasets: [{
                label: 'Formasi',
                data: <?php echo json_encode($nonBoronganFormasi); ?>,
                backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                borderWidth: 1
            }, {
                label: 'Aktual',
                data: <?php echo json_encode($nonBoronganAktual); ?>,
                backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    ticks: {
                        display: false // Set to false to hide x-axis labels on bars
                    }
                },
                y: {
                    beginAtZero: true
                }
            }
        },
    });

    $(document).ready(function () {
        var dataTable = $('#myTable').DataTable({
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: ''
                }
            ]
        });

        // Apply filters when dropdown values change
        $('#direktoratFilter, #tipeFilter').on('change', function () {
            var direktoratFilter = $('#direktoratFilter').val();
            var tipeFilter = $('#tipeFilter').val();
            // Set filters
            dataTable.column(0).search(direktoratFilter).draw();
            // Apply more specific filter for "tipe"
            if (tipeFilter === 'Borongan') {
                dataTable.column(4).search('^' + tipeFilter, true, false).draw();
            } else {
                dataTable.column(4).search(tipeFilter).draw();
            }
        });
    });

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