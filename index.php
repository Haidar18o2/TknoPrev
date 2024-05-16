<?php 
include 'koneksi.php';

// Daftar nama tabel
$tables = [
  'sekper', 'spi', 'tb', 'operasi', 'teknologi', 'rendalhar',
  'sbu_jpp', 'rantai_pasok', 'tekbang', 'adm_keu', 'sdm', 'umum', 'proyek_proyek'
];

$totalFormasi = 0;
foreach ($tables as $table) {
    $sqlTotalFormasi = "SELECT SUM(formasi) as total_formasi FROM $table";
    $resultTotalFormasi = $connection->query($sqlTotalFormasi);
    $rowTotalFormasi = $resultTotalFormasi->fetch_assoc();
    $totalFormasi += $rowTotalFormasi['total_formasi'];
}

$totalAktual = 0;
foreach ($tables as $table) {
    $sqlTotalAktual = "SELECT SUM(aktual) as total_aktual FROM $table";
    $resultTotalAktual = $connection->query($sqlTotalAktual);
    $rowTotalAktual = $resultTotalAktual->fetch_assoc();
    $totalAktual += $rowTotalAktual['total_aktual'];
}

$tableUtama = [
  'sekper', 'spi', 'tb'
];

$sqlDirUtama = "SELECT kompartemen, SUM(formasi) as total_formasi, SUM(aktual) as total_aktual FROM (";
foreach ($tableUtama as $table) {
    $sqlDirUtama .= "SELECT kompartemen, formasi, aktual FROM $table UNION ALL ";
}
$sqlDirUtama = rtrim($sqlDirUtama, 'UNION ALL ') . ") AS combined_data GROUP BY kompartemen";

$resultDirUtama = $connection->query($sqlDirUtama);

while ($rowDirUtama = $resultDirUtama->fetch_assoc()) {
  $dirUtamaLabels[] = $rowDirUtama['kompartemen'];
  $dirUtamaFormasi[] = $rowDirUtama['total_formasi'];
  $dirUtamaAktual[] = $rowDirUtama['total_aktual'];
}

$tableDirOP= [
  'operasi', 'teknologi', 'rendalhar', 'sbu_jpp', 'rantai_pasok','tekbang'
];

$sqlDirOP ="SELECT kompartemen, SUM(formasi) as total_formasi, SUM(aktual) as total_aktual FROM (";
foreach ($tableDirOP as $table){
  $sqlDirOP .= "SELECT kompartemen, formasi, aktual FROM $table UNION ALL ";
}
$sqlDirOP = rtrim($sqlDirOP, 'UNION ALL ') .") AS combined_data GROUP BY kompartemen";

$resultDirOP = $connection->query($sqlDirOP);

while ($rowDirOP = $resultDirOP->fetch_assoc()){
  $dirOPLabels[] = $rowDirOP['kompartemen'];
  $dirOPFormasi[]= $rowDirOP['total_formasi'];
  $dirOPAktual[] = $rowDirOP['total_aktual'];
}

$tableDKU=[
  'adm_keu', 'sdm', 'umum' 
];

$sqlDKU ="SELECT kompartemen, SUM(formasi) as total_formasi, SUM(aktual) as total_aktual FROM(";
foreach ($tableDKU as $table){
  $sqlDKU .= "SELECT kompartemen, formasi, aktual FROM $table UNION ALL ";
}
$sqlDKU = rtrim($sqlDKU, 'UNION ALL') .") AS combined_data GROUP BY kompartemen";

$resultDKU = $connection->query($sqlDKU);

while ($rowDKU = $resultDKU->fetch_assoc()){
  $dirDKULabels[] = $rowDKU['kompartemen'];
  $dirDKUFormasi[]= $rowDKU['total_formasi'];
  $dirDKUAktual[] = $rowDKU['total_aktual'];
}

$sqlProyek ="SELECT SUM(formasi) as total_formasi, SUM(aktual) as total_aktual FROM proyek_proyek";

$resultProyek = $connection->query($sqlProyek);

while ($rowProyek = $resultProyek->fetch_assoc()){
  $proyekFormasi[]= $rowProyek['total_formasi'];
  $proyekAktual[] = $rowProyek['total_aktual'];
}

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="landingpage.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@700&family=Lexend+Deca&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="http://www.freepik.com">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/orgchart@2.1.9/dist/css/jquery.orgchart.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/orgchart@2.1.9/dist/js/jquery.orgchart.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <title>Beranda</title>
</head>
<body>
<?php include "navigasi.php";?>
<section id="home" class="home">
  <div class="content animate">
    <h3 class="intro">Selamat Datang!</h3>
    <h1><span> Di Sistem Informasi Tenaga Kerja Alih Daya</span></h1><br><br>
    <h3><span class="highlight">PT. Pupuk Sriwidjaja Palembang</span></h3>
  </div>
  <div class="image-container animate">
    <img src="img/logo pusri.png" alt="logo" class="animate"  id="animated-image">
  </div>
</section>
<br><br>
<div class="main">
  <section>
    <div class="row row-cols-1 row-cols-md-2 g-4">
      <div class="col">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"><b>Bagan Korporat</b></h5>
            <h6>Total Formasi Tenaga Kerja Alih Daya : <?= $totalFormasi; ?></h6>
            <h6>Total Aktual Tenaga Kerja Alih Daya : <?= $totalAktual; ?></h6>
            <div id="chart-container"></div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"></h5>
            <section id="explanation" class="explanation">
              <div class="content">
                <h3><b>Wujudkan Potensi Penuh Tenaga Kerja Anda dengan Sistem Informasi Tenaga Kerja Alih Daya</b></h3>
                <p>Sistem Informasi Tenaga Kerja Alih Daya (SI TKAD) merupakan solusi yang direncanakan untuk mengelola dan mengoptimalkan aspek-aspek kritis yang terkait dengan manajemen sumber daya manusia, khususnya dalam konteks pekerjaan alih daya. Dengan menyelaraskan teknologi informasi dengan kebutuhan strategis bisnis, SI TKAD membawa perubahan positif dalam cara perusahaan merespon dan mengelola tenaga kerja kontraktual.</p>
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  </section><br><br>
  <section>
      <div style="display: flex; gap: 10px; justify-content:center;">
        <div class="card" style="width: 25%;">
          <div class="card-body">
            <h5 class="card-title">Direktorat Utama</h5>
            <div id="customLegend" style="font-size: 14px;">
                <?php
                for ($i = 0; $i < count($dirUtamaLabels); $i++) {
                    $label = $dirUtamaLabels[$i];
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
            <canvas id="myBarChartDirektoratUtama"></canvas><br>
            <h6>Keterangan: Kiri = Formasi | Kanan = Aktual</h6>
          </div>
        </div>
        <div class="card" style="width: 25%;">
          <div class="card-body">
            <h5 class="card-title">Direktorat Operasi & Produksi</h5>
            <div id="customLegend" style="font-size: 14px;">
                <?php
                for ($i = 0; $i < count($dirOPLabels); $i++) {
                    $label = $dirOPLabels[$i];
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
            <canvas id="myBarChartDirOP"></canvas><br>
            <h6>Keterangan: Kiri = Formasi | Kanan = Aktual</h6>
          </div>
        </div>
        <div class="card" style="width: 25%;">
          <div class="card-body">
            <h5 class="card-title">Direktorat Keuangan & Umum </h5>
            <div id="customLegend" style="font-size: 14px;">
                <?php
                // Iterate through $dirUtamaLabels to generate legend items
                for ($i = 0; $i < count($dirDKULabels); $i++) {
                    $label = $dirDKULabels[$i];
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
            <canvas id="myBarChartDKU"></canvas><br>
            <h6>Keterangan: Kiri = Formasi | Kanan = Aktual</h6>
          </div>
        </div>
        <div class="card" style="width: 25%;">
          <div class="card-body">
            <h5 class="card-title">Proyek Proyek</h5>
            <canvas id="myBarChartProyek"></canvas>
          </div>
        </div>
    </div>
  </section>
  <br><br>

  <section class="section-konten">
    <div class="row">
      <div class="col-md-3">
        <div class="card card-konten text-center">
          <img src="img/direktur.jpg" alt="http://www.freepik.com"/>
          <h4 style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; font-weight: bold; color: #4070f4; class="text-center">DIREKTORAT UTAMA</h4>
          <div class="konten">
            <p>Direktur Utama memiliki kewenangan untuk merumuskan dan menetapkan kebijakan serta program umum perusahaan atau organisasi, yang sesuai dengan batas wewenang yang diberikan oleh badan pengurus atau badan pimpinan sejenis, seperti dewan komisaris.</p>
            <p>Peran Pimpinan Tertinggi melibatkan koordinasi, komunikasi, pengambilan keputusan kepemimpinan, manajemen, dan pelaksanaan tugas dalam konteks perusahaan.</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-konten text-center">
          <img src="img/operasi.jpg" alt="http://www.freepik.com"/>
          <h4 style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; font-weight: bold; color: #4070f4;  class="text-center">DIREKTORAT Operasional & Produksi</h4>
          <div class="konten">
            <p>
            Direktorat Operasi dan Produksi adalah bagian kunci dari struktur organisasi suatu perusahaan yang bertanggung jawab atas pengelolaan dan pelaksanaan kegiatan operasional serta produksi.</p>
            <p>Semua rencana, progres, hingga hasil kerja karyawan akan dilaporkan langsung oleh direktur operasional kepada direktur utama. Kemudian bila dari direktur utama ada tambahan atau revisi, maka juga akan disampaikan kembali oleh direktur operasional kepada karyawan yang bersangkutan.</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-konten">
          <img src="img/adm.jpg" alt="http://www.freepik.com"/>
         <h4 style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; font-weight: bold; color: #4070f4; class="text-center">DIREKTORAT Administrasi & Umum</h4>
          <div class="konten">
            <p>Direktorat Administrasi dan Keuangan dalam suatu organisasi atau perusahaan memiliki peran kunci dalam mengelola aspek administratif dan keuangan untuk mendukung berbagai kegiatan operasional.</p>
            <p>Direktur Administrasi dan Keuangan akan banyak berhubungan dengan administratif,pendanaan, pembelanjaan, anggaran, dan urusan keuangan.</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-konten">
          <!-- Konten Card 4 -->
          <img src="img/proyek.jpg" alt="http://www.freepik.com"/>
         <h4 style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; font-weight: bold; color: #4070f4; class="text-center">Proyek-Proyek</h4>
          <div class="konten">
            <p>Sebuah proyek adalah upaya terencana yang dilakukan untuk mencapai tujuan tertentu dalam batas waktu, anggaran, dan sumber daya yang ditetapkan. Proyek dapat mencakup pengembangan produk, perubahan organisasi, pembangunan infrastruktur.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <button onclick="scrollToTop()" id="scrollToTopBtn" title="Go to top">
    <i class="fas fa-arrow-circle-up"></i>
  </button>
</div>
<footer>
  <div class="footer-left">
    <img src="img/logo pusri.png" alt="logo" style="max-width: 991.98px; height: 3rem; margin: 0 30px 0 10px;">
  </div>
  <div class="footer-right">
    <h2>PT. Pupuk Sriwidjaja Palembang</h2>
    <p><b>Kantor Pusat:</b></p>
    <p><i class="fas fa-building"></i> Jl. Mayor Zen, Palembang 30118 - INDONESIA</p>
    <p>Contact: info@perusahaan.com</p>
  </div>
</footer>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    var content = document.querySelector('.content');
    var image = document.getElementById('animated-image');
            
    content.classList.add('animate');
    image.classList.add('animate');
  });

  document.addEventListener("DOMContentLoaded", function() {
    var content = document.querySelector('.content');
    var image = document.getElementById('animated-image');

    content.classList.add('animate');
    image.classList.add('animate');
  });
//grafik dirut
var ctxDirektorat = document.getElementById('myBarChartDirektoratUtama').getContext('2d');
var direktoratChart = new Chart(ctxDirektorat, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($dirUtamaLabels); ?>,
        datasets: [{
            label: 'Formasi',
            data: <?php echo json_encode($dirUtamaFormasi); ?>,
            backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
            borderWidth: 1
        }, {
            label: 'Aktual',
            data: <?php echo json_encode($dirUtamaAktual); ?>,
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
//grafik dirop
var ctxDirektorat = document.getElementById('myBarChartDirOP').getContext('2d');
var direktoratChart = new Chart(ctxDirektorat, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($dirOPLabels); ?>,
        datasets: [{
            label: 'Formasi',
            data: <?php echo json_encode($dirOPFormasi); ?>,
            backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
            borderWidth: 1
        }, {
            label: 'Aktual',
            data: <?php echo json_encode($dirOPAktual); ?>,
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
//grafik dirkeu
var ctxDirektorat = document.getElementById('myBarChartDKU').getContext('2d');
var direktoratChart = new Chart(ctxDirektorat, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($dirDKULabels); ?>,
        datasets: [{
            label: 'Formasi',
            data: <?php echo json_encode($dirDKUFormasi); ?>,
            backgroundColor: <?php echo json_encode($backgroundColorsKategori); ?>,
            borderWidth: 1
        }, {
            label: 'Aktual',
            data: <?php echo json_encode($dirDKUAktual); ?>,
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
//grafik dirproyek
  var ctxDirektorat = document.getElementById('myBarChartProyek').getContext('2d');
  var direktoratChart = new Chart(ctxDirektorat, {
    type: 'bar',
    data: {
        labels: ['FormasiAktual'],
        datasets: [{
            data: <?php echo json_encode($proyekFormasi); ?>,
            backgroundColor: 'red',
            borderWidth: 1,
            label: 'Formasi'
          }, {
            data: <?php echo json_encode($proyekAktual); ?>,
            backgroundColor: 'blue',
            borderWidth: 1,
            label: 'Aktual'
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

  //organisasi chart
  $(document).ready(function() {
    var datascource = {
        'name': 'PT Pupuk Sriwidjaja Palembang',
        'children': [
          {
            'name': 'Direktorat Utama',
          },
          {
            'name': 'Dir. Operasi & Produksi',
          },
          {
            'name': 'Dir. Keu & Umum',
          },
          {
            'name': 'Proyek-Proyek',
          }
        ]
    };

    $('#chart-container').orgchart({
        'data': datascource,
        'nodeContent': 'name',
        'direction': 't2b',
        'pan': true,
        'zoom': true,
        'scaleInitial': 1,
    });
  });

  //explanation
  // Automatically add the 'show' class to trigger the pop-up effect when the page loads
  window.onload = function() {
    document.getElementById('explanation').classList.add('show');
  };
</script>
</body>
</html>