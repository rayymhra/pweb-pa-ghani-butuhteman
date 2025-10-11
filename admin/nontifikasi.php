<?php
include 'config.php'; // Pastikan file koneksi sudah benar

// Ambil semua notifikasi terbaru
$notif = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");

// Hitung total notifikasi belum dibaca
$total_notif = $conn->query("SELECT COUNT(*) AS jml FROM notifications WHERE is_read = 0")->fetch_assoc()['jml'];

// Kalau admin klik "tandai dibaca" (opsional)
if (isset($_GET['read_all'])) {
  $conn->query("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
  header("Location: nontifikasi.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifikasi - Admin</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fdfcf7;
    }
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
    .content {
      margin-left: 240px;
      padding: 20px;
    }
    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .notif-item {
      border-bottom: 1px solid #eee;
      padding: 12px 0;
    }
    .notif-item:last-child {
      border-bottom: none;
    }
    .notif-time {
      font-size: 0.85rem;
      color: gray;
    }
    .topbar a {
      color: black;
    }
    .topbar h4 {
      margin: 0;
      font-weight: 600;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="assets/img/Greatest_Logo.png" alt="Logo">
    <a href="dashboard.php"><i class="bi bi-house-door-fill me-2"></i>Dashboard</a>
    <a href="users.php"><i class="bi bi-people-fill me-2"></i>User</a>
    <a href="komunitas.php"><i class="bi bi-people-fill me-2"></i>Komunitas</a>
  </div>

  <!-- Topbar -->
  <div class="topbar">
    <h4>Notifikasi Admin</h4>
    <div>
      <a href="nontifikasi.php" class="position-relative">
        <i class="bi bi-bell-fill me-3"></i>
        <?php if ($total_notif > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          <?= $total_notif ?>
        </span>
        <?php endif; ?>
      </a>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>

    </div>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <h5>Daftar Notifikasi</h5>
        <a href="?read_all=1" class="btn btn-sm btn-outline-primary">Tandai Semua Dibaca</a>
      </div>

      <div class="mt-3">
        <?php if ($notif->num_rows > 0): ?>
          <?php while($n = $notif->fetch_assoc()): ?>
            <div class="notif-item">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <i class="bi bi-info-circle text-primary me-2"></i>
                  <?= htmlspecialchars($n['message']) ?>
                  <div class="notif-time">
                    <?= date("d M Y H:i", strtotime($n['created_at'])) ?>
                  </div>
                </div>
                <?php if ($n['is_read'] == 0): ?>
                  <span class="badge bg-warning text-dark">Baru</span>
                <?php else: ?>
                  <span class="badge bg-success">Dibaca</span>
                <?php endif; ?>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-muted text-center mt-3">Belum ada notifikasi.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
