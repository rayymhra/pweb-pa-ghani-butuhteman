<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard User - Admin</title>

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
    .table thead {
      background-color: #FECE6A;
      color: #06206C;
    }
    .table tbody tr:hover {
      background-color: rgba(254,206,106,0.2);
    }
    .collapse-form {
      border: 2px solid #FECE6A;
      border-radius: 10px;
      padding: 15px;
      background: #fff8e6;
      margin-top: 15px;
      margin-bottom: 20px;
    }
     .topbar h4 {
      margin: 0;
      font-weight: 600;
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
    <a href="dashboard.php"><i class="bi bi-house-door-fill me-2"></i>Dashboard</a>
    <a href="user.php" class="active"><i class="bi bi-people-fill me-2"></i>User</a>
    <a href="komunitas.php"><i class="bi bi-people-fill me-2"></i>Komunitas</a>
  </div>

  <!-- Topbar -->
  <div class="topbar">
    <h4>Manajemen User</h4>
    <div>
      <a href="nontifikasi.php"><i class="bi bi-bell-fill me-3"></i></a>
      <i class="bi bi-person-circle"></i>
    </div>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <h5>Daftar User</h5>
        <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#formUser" id="btnTambah">
          <i class="bi bi-plus-circle"></i> Tambah User
        </button>
      </div>

      <!-- Form Tambah/Edit di Atas -->
      <div class="collapse collapse-form" id="formUser">
        <h6 class="mb-3" id="formTitle">Form Tambah User</h6>
        <form id="userForm">
          <input type="hidden" id="userId">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nama</label>
              <input type="text" class="form-control" id="namaUser" placeholder="Masukkan nama" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" id="usernameUser" placeholder="Masukkan username" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Telepon</label>
              <input type="text" class="form-control" id="teleponUser" placeholder="Masukkan no. telepon">
            </div>
            <div class="col-md-6">
              <label class="form-label">Jabatan</label>
              <select class="form-select" id="jabatanUser" required>
                <option value="User">User</option>
                <option value="Moderator">Moderator</option>
                <option value="Admin">Admin</option>
              </select>
            </div>
          </div>

          <button type="submit" class="btn btn-success mt-3" id="saveBtn"><i class="bi bi-save"></i> Simpan</button>
          <button type="button" class="btn btn-secondary mt-3" data-bs-toggle="collapse" data-bs-target="#formUser" id="cancelBtn">
            Batal
          </button>
        </form>
      </div>

      <!-- Tabel -->
      <table class="table mt-3 table-hover">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Telepon</th>
            <th>Jabatan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Nurazizah Zahrah</td>
            <td>azizah</td>
            <td>08123456789</td>
            <td>Admin</td>
            <td>
              <button class="btn btn-sm btn-warning editBtn"><i class="bi bi-pencil-square"></i> Edit</button>
              <button class="btn btn-sm btn-danger deleteBtn" data-nama="Nurazizah Zahrah"><i class="bi bi-trash"></i> Hapus</button>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>Rayya Mahira</td>
            <td>rayya</td>
            <td>082233445566</td>
            <td>User</td>
            <td>
              <button class="btn btn-sm btn-warning editBtn"><i class="bi bi-pencil-square"></i> Edit</button>
              <button class="btn btn-sm btn-danger deleteBtn" data-nama="Rayya Mahira"><i class="bi bi-trash"></i> Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Konfirmasi Hapus -->
  <div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg rounded-4">
        <div class="modal-header bg-danger text-white rounded-top-4">
          <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center p-4">
          <p class="fs-5 mb-3">Apakah kamu yakin ingin menghapus <br><b id="hapusNama"></b>?</p>
          <p class="text-muted mb-0">Tindakan ini tidak bisa dibatalkan.</p>
        </div>
        <div class="modal-footer border-0 d-flex justify-content-center gap-2">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Batal
          </button>
          <button type="button" class="btn btn-danger px-4" id="confirmHapusBtn">
            <i class="bi bi-trash-fill"></i> Hapus
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
  <script>
    const formTitle = document.getElementById('formTitle');
    const saveBtn = document.getElementById('saveBtn');
    const userForm = document.getElementById('userForm');
    const userId = document.getElementById('userId');
    const namaUser = document.getElementById('namaUser');
    const usernameUser = document.getElementById('usernameUser');
    const teleponUser = document.getElementById('teleponUser');
    const jabatanUser = document.getElementById('jabatanUser');
    const btnTambah = document.getElementById('btnTambah');
    const modalHapus = new bootstrap.Modal(document.getElementById('modalHapus'));
    const hapusNama = document.getElementById('hapusNama');
    let rowToDelete = null;

    // Klik tombol Edit
    document.querySelectorAll('.editBtn').forEach((btn) => {
      btn.addEventListener('click', () => {
        const row = btn.closest('tr').children;
        userId.value = row[0].textContent;
        namaUser.value = row[1].textContent;
        usernameUser.value = row[2].textContent;
        teleponUser.value = row[3].textContent;
        jabatanUser.value = row[4].textContent;

        formTitle.textContent = "Form Edit User";
        saveBtn.innerHTML = '<i class="bi bi-pencil-square"></i> Update';

        new bootstrap.Collapse(document.getElementById('formUser'), {show: true});
      });
    });

    // Klik Tambah
    btnTambah.addEventListener('click', () => {
      formTitle.textContent = "Form Tambah User";
      saveBtn.innerHTML = '<i class="bi bi-save"></i> Simpan';
      userForm.reset();
      userId.value = "";
    });

    // Klik Hapus
    document.querySelectorAll('.deleteBtn').forEach((btn) => {
      btn.addEventListener('click', () => {
        const row = btn.closest('tr');
        const nama = btn.dataset.nama;
        rowToDelete = row;
        hapusNama.textContent = `"${nama}"`;
        modalHapus.show();
      });
    });

    // Konfirmasi hapus
    document.getElementById('confirmHapusBtn').addEventListener('click', () => {
      if (rowToDelete) {
        rowToDelete.remove();
        rowToDelete = null;
      }
      modalHapus.hide();
    });
  </script>
</body>
</html>
