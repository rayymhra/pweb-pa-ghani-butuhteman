<?php
// ====== KONEKSI DATABASE ======
$conn = new mysqli("localhost", "root", "", "butuh_teman");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// ====== SIMPAN / UPDATE ======
if (isset($_POST['save'])) {
    $id   = $_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $desc = $conn->real_escape_string($_POST['description']);
    $created_by = 3; 

    // ====== HANDLE UPLOAD FOTO ======
    $photo_name = "";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_name = "uploads/".uniqid().".".$ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_name);
    }

    if ($id == "") {
        $sql = "INSERT INTO communities (name, description, photo, created_by) VALUES ('$name', '$desc', '$photo_name', '$created_by')";
    } else {
        if ($photo_name != "") {
            $sql = "UPDATE communities SET name='$name', description='$desc', photo='$photo_name' WHERE id=$id";
        } else {
            $sql = "UPDATE communities SET name='$name', description='$desc' WHERE id=$id";
        }
    }
    $conn->query($sql);
    header("Location: komunitas.php");
    exit;
}

// ====== HAPUS ======
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Hapus foto lama
    $res = $conn->query("SELECT photo FROM communities WHERE id=$id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if ($row['photo'] && file_exists($row['photo'])) unlink($row['photo']);
    }
    $conn->query("DELETE FROM communities WHERE id=$id");
    header("Location: komunitas.php");
    exit;
}

// ====== AMBIL DATA ======
$result = $conn->query("SELECT c.*, u.name as creator 
                        FROM communities c 
                        JOIN users u ON c.created_by=u.id 
                        ORDER BY c.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Komunitas - Admin</title>
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
.topbar a{ color: black; }
img.community-photo { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
.topbar a { color: #06206C;}
</style>
</head>
<body>

<div class="sidebar">
    <img src="assets/img/Greatest_Logo.png" alt="Logo">
    <a href="dashboard.php"><i class="bi bi-house-door-fill me-2"></i>Dashboard</a>
    <a href="users.php"><i class="bi bi-people-fill me-2"></i>User</a>
    <a href="komunitas.php" class="active"><i class="bi bi-people-fill me-2"></i>Komunitas</a>
</div>

<div class="topbar">
    <h4>Manajemen Komunitas</h4>
    <div>
        <a href="nontifikasi.php"><i class="bi bi-bell-fill me-3"></i></a>
              <a href="../auth/logout.php" class="btn btn-danger">Logout</a>

    </div>
</div>

<div class="content">
<div class="card p-3">
<div class="d-flex justify-content-between align-items-center">
<h5>Daftar Komunitas</h5>
<button class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#formKomunitas" id="btnTambah">
<i class="bi bi-plus-circle"></i> Tambah Komunitas
</button>
</div>

<div class="collapse collapse-form" id="formKomunitas">
<h6 class="mb-3" id="formTitle">Form Tambah Komunitas</h6>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="id" id="komunitasId">
<div class="mb-3">
<label class="form-label">Nama Komunitas</label>
<input type="text" name="name" class="form-control" id="namaKomunitas" required>
</div>
<div class="mb-3">
<label class="form-label">Deskripsi</label>
<textarea name="description" class="form-control" id="deskripsiKomunitas" rows="3" required></textarea>
</div>
<div class="mb-3">
<label class="form-label">Foto Komunitas</label>
<input type="file" name="photo" class="form-control">
</div>
<button type="submit" name="save" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
<button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#formKomunitas">Batal</button>
</form>
</div>

<table class="table mt-3 table-hover">
<thead>
<tr>
<th>No</th>
<th>Foto</th>
<th>Nama Komunitas</th>
<th>Deskripsi</th>
<th>Dibuat Oleh</th>
<th>Tanggal Dibuat</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php $no=1; while($row=$result->fetch_assoc()): ?>
<tr>
<td><?= $no++ ?></td>
<td>
<?php if($row['photo'] && file_exists($row['photo'])): ?>
<img src="<?= $row['photo'] ?>" class="community-photo" alt="Foto">
<?php endif; ?>
</td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['description']) ?></td>
<td><?= htmlspecialchars($row['creator']) ?></td>
<td><?= $row['created_at'] ?></td>
<td>
<button class="btn btn-sm btn-warning" onclick="editData('<?= $row['id'] ?>','<?= htmlspecialchars($row['name']) ?>','<?= htmlspecialchars($row['description']) ?>')"><i class="bi bi-pencil-square"></i> Edit</button>
<a href="komunitas.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus komunitas ini?')"><i class="bi bi-trash"></i> Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script>
function editData(id, name, desc){
    document.getElementById('komunitasId').value = id;
    document.getElementById('namaKomunitas').value = name;
    document.getElementById('deskripsiKomunitas').value = desc;
    document.getElementById('formTitle').textContent = "Form Edit Komunitas";
    new bootstrap.Collapse(document.getElementById('formKomunitas'), {show: true});
}
</script>
</body>
</html>
