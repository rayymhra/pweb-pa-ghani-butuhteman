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
    .topbar a{
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
    <img src="Greatest_Logo.png" alt="Logo">
    <a href="dashboard.php"><i class="bi bi-house-door-fill me-2"></i>Dashboard</a>
    <a href="user.php"><i class="bi bi-people-fill me-2"></i>User</a>
    <a href="komunitas.php"><i class="bi bi-people-fill me-2"></i>Komunitas</a>
  </div>

  <!-- Topbar -->
  <div class="topbar">
    <h4>Notifikasi</h4>
    <div>
      <a href="notifikasi.php"><i class="bi bi-bell-fill me-3"></i></a>
      <i class="bi bi-person-circle"></i>
    </div>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="card p-3">
      <h5>Daftar Notifikasi</h5>
      <div class="mt-3">

        <!-- Notifikasi 1 -->
        <div class="notif-item">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <i class="bi bi-chat-dots-fill text-primary me-2"></i>
              <strong>Budi</strong> meminta admin membuat komunitas <strong>Komunitas Hiking</strong>.
              <div class="notif-time">1 jam lalu</div>
            </div>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#lihatModal">Lihat</button>
          </div>
        </div>

        <!-- Notifikasi 2 -->
        <div class="notif-item">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <i class="bi bi-person-check-fill text-success me-2"></i>
              <strong>Sinta</strong> baru saja login ke sistem.
              <div class="notif-time">2 jam lalu</div>
            </div>
            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#detailModal">Detail</button>
          </div>
        </div>

        <!-- Notifikasi 3 -->
        <div class="notif-item">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <i class="bi bi-people-fill text-warning me-2"></i>
              <strong>Andi</strong> bergabung dengan komunitas <strong>Travel Lovers</strong>.
              <div class="notif-time">Kemarin</div>
            </div>
            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#okModal">OK</button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Modal Lihat -->
  <div class="modal fade" id="lihatModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content rounded-3">
        <div class="modal-header">
          <h5 class="modal-title">Detail Permintaan Komunitas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><strong>User:</strong> Budi</p>
          <p><strong>Komunitas:</strong> Komunitas Hiking</p>
          <p><strong>Deskripsi:</strong> Komunitas untuk pecinta hiking dan pendaki gunung.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success">Setujui</button>
          <button class="btn btn-danger">Tolak</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Detail -->
  <div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content rounded-3">
        <div class="modal-header">
          <h5 class="modal-title">Detail Aktivitas User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><strong>User:</strong> Sinta</p>
          <p><strong>Aktivitas:</strong> Login ke sistem</p>
          <p><strong>Waktu:</strong> 2 jam lalu</p>
          <p><strong>Perangkat:</strong> Chrome - Windows 10</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal OK -->
  <div class="modal fade" id="okModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content rounded-3">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Apakah Anda sudah membaca notifikasi ini?</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" data-bs-dismiss="modal">OK, Tandai Dibaca</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
