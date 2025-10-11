<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "butuh_teman");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

/* ====== TOTAL DATA ====== */
// Total pengguna dari tabel users
$totalUser = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];

// Total booking dari tabel bookings
$totalBooking = $conn->query("SELECT COUNT(*) AS total FROM bookings")->fetch_assoc()['total'];

// Total pendapatan dari bookings.total_price
$totalPendapatan = $conn->query("SELECT SUM(total_price) AS total FROM bookings")->fetch_assoc()['total'] ?? 0;

/* ====== BOOKING TERBARU ====== */
$bookingTerbaru = $conn->query("
    SELECT 
        u.name AS nama_client,
        b.start_datetime AS mulai,
        b.end_datetime AS selesai,
        b.status
    FROM bookings b
    JOIN users u ON b.client_id = u.id
    ORDER BY b.created_at DESC
    LIMIT 5
");

/* ====== DATA UNTUK CHART ====== */
// Booking dan pendapatan per bulan
$chartData = $conn->query("
    SELECT 
        MONTH(created_at) AS bulan, 
        COUNT(*) AS total_booking, 
        SUM(total_price) AS total_pendapatan
    FROM bookings
    GROUP BY MONTH(created_at)
");

$labels = [];
$bookingData = [];
$pendapatanData = [];
while ($row = $chartData->fetch_assoc()) {
    $labels[] = "Bulan " . $row['bulan'];
    $bookingData[] = $row['total_booking'];
    $pendapatanData[] = $row['total_pendapatan'];
}

// Kategori populer (berdasarkan role Friend di tabel users)
$kategoriData = $conn->query("
    SELECT role, COUNT(*) AS jumlah
    FROM users
    WHERE role IN ('Client','Friend','Admin')
    GROUP BY role
");
$kategoriLabels = [];
$kategoriJumlah = [];
while ($row = $kategoriData->fetch_assoc()) {
    $kategoriLabels[] = $row['role'];
    $kategoriJumlah[] = $row['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Greatest</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body { font-family: 'Segoe UI', sans-serif; background-color: #fdfcf7; }
    .sidebar { width: 240px; height: 100vh; background-color: #FECE6A; color: #06206C; position: fixed; top: 0; left: 0; padding: 20px 15px; text-align: center; }
    .sidebar img { width: 80px; margin-bottom: 30px; }
    .sidebar a { display: block; padding: 10px 15px; margin: 8px 0; text-decoration: none; color: #06206C; border-radius: 8px; transition: 0.3s; font-weight: 500; text-align: left; }
    .sidebar a:hover, .sidebar a.active { background-color: #06206C; color: #FECE6A; }
    .topbar { margin-left: 240px; height: 60px; background-color: #FECE6A; color: #06206C; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; position: sticky; top: 0; z-index: 10; border-bottom: 2px solid #06206C; }
    .content { margin-left: 240px; padding: 20px; }
    .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .table thead { background-color: #FECE6A; color: #06206C; }
    .table tbody tr:hover { background-color: rgba(254,206,106,0.2); }
    .topbar a { color: #06206C;}
    .topbar h4 { margin: 0; font-weight: 600; }

  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="assets/img/Greatest_Logo.png" alt="Logo">
    <a href="dashboard.php" class="active"><i class="bi bi-house-door-fill me-2"></i>Dashboard</a>
    <a href="users.php"><i class="bi bi-people-fill me-2"></i>User</a>
    <a href="komunitas.php"><i class="bi bi-chat-left-dots-fill me-2"></i>Komunitas</a>
  </div>

  <!-- Topbar --> 
  <div class="topbar">
    <h4>Dashboard</h4>
    <div>
      <a href="nontifikasi.php"><i class="bi bi-bell-fill me-3"></i></a>
      <a href="../auth/logout.php" class="btn btn-danger">Logout</a>

    </div>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card p-3 text-center">
          <h5>Total Pengguna</h5>
          <h2><?= number_format($totalUser) ?></h2>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-3 text-center">
          <h5>Total Booking</h5>
          <h2><?= number_format($totalBooking) ?></h2>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-3 text-center">
          <h5>Total Pendapatan</h5>
          <h2>Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h2>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-3">
      <div class="col-md-8">
        <div class="card p-3">
          <h5>Tren Booking & Pendapatan</h5>
          <canvas id="bookingChart"></canvas>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-3">
          <h5>Peran Populer</h5>
          <canvas id="categoryChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Table Booking Terbaru -->
    <div class="card mt-4 p-3">
      <h5>Booking Terbaru</h5>
      <table class="table table-hover mt-3">
        <thead>
          <tr>
            <th>Nama Client</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $bookingTerbaru->fetch_assoc()): ?>
          <tr>
            <td><?= $row['nama_client'] ?></td>
            <td><?= date("d M Y H:i", strtotime($row['mulai'])) ?></td>
            <td><?= date("d M Y H:i", strtotime($row['selesai'])) ?></td>
            <td>
              <?php
              $status = $row['status'];
              if ($status == 'completed') echo '<span class="badge bg-success">Selesai</span>';
              elseif ($status == 'pending') echo '<span class="badge bg-warning text-dark">Menunggu</span>';
              elseif ($status == 'accepted') echo '<span class="badge bg-info text-dark">Diterima</span>';
              else echo '<span class="badge bg-danger">Ditolak</span>';
              ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    // Line Chart
    new Chart(document.getElementById("bookingChart"), {
      type: "line",
      data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
          { label: "Booking", data: <?= json_encode($bookingData) ?>, borderColor: "#06206C", fill: false, tension: 0.3 },
          { label: "Pendapatan", data: <?= json_encode($pendapatanData) ?>, borderColor: "#FECE6A", fill: true, tension: 0.3 }
        ]
      }
    });

    // Doughnut Chart
    new Chart(document.getElementById("categoryChart"), {
      type: "doughnut",
      data: {
        labels: <?= json_encode($kategoriLabels) ?>,
        datasets: [{ data: <?= json_encode($kategoriJumlah) ?>, backgroundColor: ["#FECE6A", "#06206C", "#FECE6A99"] }]
      }
    });
  </script>
</body>
</html>
