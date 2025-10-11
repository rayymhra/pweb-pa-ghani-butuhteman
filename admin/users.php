<?php
// ==================== BACKEND ====================
include "config.php"; // koneksi database

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$action = $_GET['action'] ?? '';

if ($action) {
    if ($action == "create") {
        $name     = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
        $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
        $email    = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
        $password = mysqli_real_escape_string($conn, $_POST['password'] ?? '');
        $phone    = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
        $gender   = mysqli_real_escape_string($conn, $_POST['gender'] ?? '');
        $location = mysqli_real_escape_string($conn, $_POST['location'] ?? '');
        $role     = mysqli_real_escape_string($conn, $_POST['role'] ?? 'Client');

        if ($name && $username && $email && $password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, username, email, password, phone, gender, location, role, created_at)
                    VALUES ('$name','$username','$email','$hashed','$phone','$gender','$location','$role',NOW())";
            if ($conn->query($sql)) {
                header("Location: users.php?status=success_add");
                exit;
            } else {
                echo "<script>alert('Gagal menambah user!'); window.location='users.php';</script>";
                exit;
            }
        }

    } elseif ($action == "update") {
        $id       = intval($_POST['id'] ?? 0);
        $name     = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
        $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
        $email    = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
        $phone    = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
        $gender   = mysqli_real_escape_string($conn, $_POST['gender'] ?? '');
        $location = mysqli_real_escape_string($conn, $_POST['location'] ?? '');
        $role     = mysqli_real_escape_string($conn, $_POST['role'] ?? 'Client');

        if ($id > 0) {
            $sql = "UPDATE users 
                    SET name='$name', username='$username', email='$email', phone='$phone', 
                        gender='$gender', location='$location', role='$role'
                    WHERE id=$id";
            if ($conn->query($sql)) {
                header("Location: users.php?status=success_update");
                exit;
            } else {
                echo "<script>alert('Gagal mengupdate user!'); window.location='users.php';</script>";
                exit;
            }
        }

    } elseif ($action == "delete") {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $sql = "DELETE FROM users WHERE id=$id";
            if ($conn->query($sql)) {
                header("Location: users.php?status=success_delete");
                exit;
            } else {
                echo "<script>alert('Gagal menghapus user!'); window.location='users.php';</script>";
                exit;
            }
        }
    }
}

// Ambil semua data user
$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard User - Admin</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">

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
    .collapse-form { border: 2px solid #FECE6A; border-radius: 10px; padding: 15px; background: #fff8e6; margin-top: 15px; margin-bottom: 20px; }
    .topbar h4 { margin: 0; font-weight: 600; }
    .topbar a { color: #06206C;}

  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="assets/img/Greatest_Logo.png" alt="Logo">
    <a href="dashboard.php"><i class="bi bi-house-door-fill me-2"></i>Dashboard</a>
    <a href="users.php" class="active"><i class="bi bi-people-fill me-2"></i>User</a>
    <a href="komunitas.php"><i class="bi bi-people-fill me-2"></i>Komunitas</a>
  </div>

  <!-- Topbar -->
  <div class="topbar">
    <h4>Manajemen User</h4>
    <div>
        <a href="nontifikasi.php"><i class="bi bi-bell-fill me-3"></i></a>
      <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <h5>Daftar User</h5>
        <button class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#formUser" id="btnTambah">
          <i class="bi bi-plus-circle"></i> Tambah User
        </button>
      </div>

      <!-- Form Tambah/Edit -->
      <div class="collapse collapse-form" id="formUser">
        <h6 class="mb-3" id="formTitle">Form Tambah User</h6>
        <form method="POST" action="users.php?action=create" id="userForm">
          <input type="hidden" name="id" id="userId">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nama</label>
              <input type="text" class="form-control" name="name" id="namaUser" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" name="username" id="usernameUser" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" id="emailUser" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" name="password" id="passwordUser" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Telepon</label>
              <input type="text" class="form-control" name="phone" id="teleponUser">
            </div>
            <div class="col-md-6">
              <label class="form-label">Jenis Kelamin</label>
              <select class="form-select" name="gender" id="genderUser">
                <option value="Pria">Pria</option>
                <option value="Wanita">Wanita</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Lokasi</label>
              <input type="text" class="form-control" name="location" id="locationUser">
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              <select class="form-select" name="role" id="jabatanUser" required>
                <option value="Client">Client</option>
                <option value="Friend">Friend</option>
                <option value="Admin">Admin</option>
              </select>
            </div>
          </div>

          <button type="submit" class="btn btn-success mt-3" id="saveBtn"><i class="bi bi-save"></i> Simpan</button>
          <button type="button" class="btn btn-secondary mt-3" data-bs-toggle="collapse" data-bs-target="#formUser">Batal</button>
        </form>
      </div>

      <!-- Tabel -->
      <table class="table mt-3 table-hover">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Email</th>
            <th>Telepon</th>
            <th>Jenis Kelamin</th>
            <th>Lokasi</th>
            <th>Role</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach($users as $u): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['phone']) ?></td>
            <td><?= htmlspecialchars($u['gender']) ?></td>
            <td><?= htmlspecialchars($u['location']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td>
              <button class="btn btn-sm btn-warning editBtn"
                data-id="<?= $u['id'] ?>"
                data-nama="<?= htmlspecialchars($u['name']) ?>"
                data-username="<?= htmlspecialchars($u['username']) ?>"
                data-email="<?= htmlspecialchars($u['email']) ?>"
                data-phone="<?= htmlspecialchars($u['phone']) ?>"
                data-gender="<?= htmlspecialchars($u['gender']) ?>"
                data-location="<?= htmlspecialchars($u['location']) ?>"
                data-role="<?= htmlspecialchars($u['role']) ?>">
                <i class="bi bi-pencil-square"></i> Edit
              </button>
              <a href="users.php?action=delete&id=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('Hapus user <?= htmlspecialchars($u['name']) ?>?')">
                 <i class="bi bi-trash"></i> Hapus
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
  <script>
    const formTitle = document.getElementById('formTitle');
    const saveBtn = document.getElementById('saveBtn');
    const userForm = document.getElementById('userForm');
    const userId = document.getElementById('userId');
    const namaUser = document.getElementById('namaUser');
    const usernameUser = document.getElementById('usernameUser');
    const emailUser = document.getElementById('emailUser');
    const passwordUser = document.getElementById('passwordUser');
    const teleponUser = document.getElementById('teleponUser');
    const genderUser = document.getElementById('genderUser');
    const locationUser = document.getElementById('locationUser');
    const jabatanUser = document.getElementById('jabatanUser');

    // Klik tombol Edit
    document.querySelectorAll('.editBtn').forEach((btn) => {
      btn.addEventListener('click', () => {
        userId.value = btn.dataset.id;
        namaUser.value = btn.dataset.nama;
        usernameUser.value = btn.dataset.username;
        emailUser.value = btn.dataset.email;
        passwordUser.value = ''; // password tidak ditampilkan
        teleponUser.value = btn.dataset.phone;
        genderUser.value = btn.dataset.gender;
        locationUser.value = btn.dataset.location;
        jabatanUser.value = btn.dataset.role;

        formTitle.textContent = "Form Edit User";
        saveBtn.innerHTML = '<i class="bi bi-pencil-square"></i> Update';
        userForm.action = "users.php?action=update";

        new bootstrap.Collapse(document.getElementById('formUser'), {show: true});
      });
    });

    // Klik Tambah User
    document.getElementById('btnTambah').addEventListener('click', () => {
      formTitle.textContent = "Form Tambah User";
      saveBtn.innerHTML = '<i class="bi bi-save"></i> Simpan';
      userForm.reset();
      userId.value = "";
      userForm.action = "users.php?action=create";
    });
  </script>
</body>
</html>
