<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Greatest</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fdfcf7;
    }

    /* Sidebar */
    .sidebar {
      width: 240px;
      height: 100vh;
      background-color: #FECE6A;
      color: #06206C;
      position: fixed;
      top: 0;
      left: 0;
      padding: 20px 15px;
      text-align: center;
    }
    .sidebar img {
      width: 80px;
      margin-bottom: 30px;
    }
    .sidebar a {
      display: block;
      padding: 10px 15px;
      margin: 8px 0;
      text-decoration: none;
      color: #06206C;
      border-radius: 8px;
      transition: 0.3s;
      font-weight: 500;
      text-align: left;
    }
    .sidebar a:hover,
    .sidebar a.active {
      background-color: #06206C;
      color: #FECE6A;
    }

    /* Topbar */
    .topbar {
      margin-left: 240px;
      height: 60px;
      background-color: #FECE6A;
      color: #06206C;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      position: sticky;
      top: 0;
      z-index: 10;
      border-bottom: 2px solid #06206C;
    }
    .topbar h4 {
      margin: 0;
      font-weight: 600;
    }

    /* Content */
    .content {
      margin-left: 240px;
      padding: 20px;
    }

    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .card h5 {
      color: #06206C;
    }

    /* Table */
    .table thead {
      background-color: #FECE6A;
      color: #06206C;
    }
    .table tbody tr:hover {
      background-color: rgba(254,206,106,0.2);
    }

    /* Buttons */
    .btn-custom {
      background-color: #FECE6A;
      color: #06206C;
      border-radius: 8px;
      transition: 0.3s;
      font-weight: 600;
    }
    .btn-custom:hover {
      background-color: #06206C;
      color: #FECE6A;
    }
    .topbar a{
      color: black;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="Greatest_Logo.png" alt="Logo">
    <a href="dashboard.php" class="active"><i class="bi bi-house-door-fill me-2"></i>Dashboard</a>
    <a href="user.php"><i class="bi bi-people-fill me-2"></i>User</a>
    <a href="komunitas.php"><i class="bi bi-people-fill me-2"></i>Komunitas</a>
  </div>

  <!-- Topbar -->
  <div class="topbar">
    <h4>Dashboard</h4>
    <div>
      <a href="nontifikasi.php"><i class="bi bi-bell-fill me-3"></i></a>
      <i class="bi bi-person-circle"></i>
    </div>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="row g-4">
      <!-- Statistik -->
      <div class="col-md-4">
        <div class="card p-3 text-center">
          <h5>Total Pengguna</h5>
          <h2 style="color:#06206C;">1,245</h2>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-3 text-center">
          <h5>Total Booking</h5>
          <h2 style="color:#06206C;">867</h2>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-3 text-center">
          <h5>Total Pendapatan</h5>
          <h2 style="color:#06206C;">Rp 12,500K</h2>
        </div>
      </div>
    </div>

    <!-- Charts -->
<div class="row g-4 mt-2 align-items-stretch">
  <div class="col-md-8">
    <div class="card p-3 h-100">
      <h5>Tren Booking & Pendapatan</h5>
      <canvas id="bookingChart" class="w-100 h-100"></canvas>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 h-100">
      <h5>Kategori Populer</h5>
      <canvas id="categoryChart" class="w-100 h-100"></canvas>
    </div>
  </div>
</div>


    <!-- Table -->
    <div class="card mt-4 p-3">
      <h5>Booking Terbaru</h5>
      <table class="table table-hover mt-3">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Tanggal</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Budi</td>
            <td>Gaming</td>
            <td>20 Sep 2025</td>
            <td><span class="badge bg-success">Selesai</span></td>
          </tr>
          <tr>
            <td>Sinta</td>
            <td>Travel</td>
            <td>22 Sep 2025</td>
            <td><span class="badge bg-warning text-dark">Proses</span></td>
          </tr>
          <tr>
            <td>Andi</td>
            <td>Kuliner</td>
            <td>23 Sep 2025</td>
            <td><span class="badge bg-danger">Batal</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

  <!-- Chart.js Script -->
  <script>
    // Line Chart
    new Chart(document.getElementById("bookingChart"), {
      type: "line",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul"],
        datasets: [
          {
            label: "Booking",
            data: [30, 45, 60, 40, 75, 90, 100],
            borderColor: "#06206C",
            backgroundColor: "rgba(6,32,108,0.15)",
            fill: true,
            tension: 0.4
          },
          {
            label: "Pendapatan",
            data: [10, 20, 40, 30, 60, 80, 95],
            borderColor: "#FECE6A",
            backgroundColor: "rgba(254,206,106,0.4)",
            fill: true,
            tension: 0.4
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: "top" }
        }
      }
    });

    // Pie Chart
    new Chart(document.getElementById("categoryChart"), {
      type: "doughnut",
      data: {
        labels: ["Gaming", "Travel", "Kuliner", "Olahraga"],
        datasets: [{
          data: [40, 25, 20, 15],
          backgroundColor: ["#FECE6A", "#06206C", "#FECE6A80", "#06206C80"],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: "bottom" } }
      }
    });
  </script>
</body>
</html>