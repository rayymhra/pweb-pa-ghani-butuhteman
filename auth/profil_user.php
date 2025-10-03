<?php

$host = "localhost";
$user = "root"; 
$pass = "";     
$db   = "butuh_teman";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

session_start();
// Asumsi user sudah login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // default sementara
}
$user_id = $_SESSION['user_id'];

// ====== update profil umum ======
if (isset($_POST['update_profile'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $location   = mysqli_real_escape_string($conn, $_POST['location']);
    $gender   = mysqli_real_escape_string($conn, $_POST['gender']);
    $password = $_POST['password'];
    
    // upload foto jika ada
    $update_photo = "";
    if (!empty($_FILES['profile_photo']['name'])) {
        if (!is_dir("uploads")) mkdir("uploads");
        $fileName = time() . "_" . basename($_FILES['profile_photo']['name']);
        $target   = "uploads/" . $fileName;
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target)) {
            $update_photo = ", profile_photo='$target'";
        }
    }
    
    // update password jika diisi
    $update_password = "";
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update_password = ", password='$hashed'";
    }
    
    $sql = "UPDATE users SET 
    name='$name',
    location='$location',
    gender='$gender'
    $update_photo
    $update_password
    WHERE id=$user_id";
    
    mysqli_query($conn, $sql);
    header("Location: profil_user.php?success=1");
    exit;
}

// ====== update about khusus ======
if (isset($_POST['update_about'])) {
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $sql = "UPDATE users SET bio='$bio' WHERE id=$user_id";
    mysqli_query($conn, $sql);
    header("Location: profil_user.php?about_updated=1");
    exit;
}

// ====== update hobby khusus ======
if (isset($_POST['update_hobby'])) {
    $hobby = mysqli_real_escape_string($conn, $_POST['hobby']);
    $sql = "UPDATE users SET hobby='$hobby' WHERE id=$user_id";
    mysqli_query($conn, $sql);
    header("Location: profil_user.php?hobby_updated=1");
    exit;
}


// ====== ambil data user ======
$q = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Client</title>
    
    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600&display=swap');
        * { 
            font-family: "Josefin Sans", sans-serif; 
        }

        .navbar 
        { 
            background-color: #FECE6A !important; 
        }

        .nav-link { 
            color: #315DB4; 
            font-weight: 500; 
        }

        .actived { 
            color: #06206C; 
            font-weight: 600; 
        }

        .profile-card { 
            border: 1px solid #f2e6c9; 
            border-radius: 12px; 
            background-color: #fffdf7; 
            padding: 1.5rem; 
            margin-top: 1rem; 
        }

        .profile-img { 
            width: 100px; 
            height: 100px; 
            border-radius: 50%; 
            object-fit: cover; 
            margin-right: 1rem; 
            border: 3px solid #FECE6A; 
        }
        .profile-name { 
            font-size: 1.3rem; 
            font-weight: 600; 
        }

        .box { 
            background: #fff; 
            border-radius: 12px; 
            padding: 1rem 1.2rem; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            margin-bottom: 1rem; 
        }

        .tag { 
            background-color: #FECE6A; 
            padding: 5px 12px; 
            border-radius: 20px; 
            font-size: 0.85rem; 
            font-weight: 500; 
            margin: 3px; 
            display: inline-block; 
            color: #333; 
        }

        .profile-img-nav {
            width: 50px;
            object-fit: cover;
            height: 50px;
            border-radius: 50%;
        }
    </style>
</head>
<body class="bg-light">
    
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://via.placeholder.com/50" alt="" width="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#">BERANDA</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">TENTANG</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">CARI TEMAN</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">KOMUNITAS</a></li>
                    
                    <a href="">
                        <img src="<?= !empty($user['profile_photo']) ? $user['profile_photo'] : "../assets/img/user.jpg" ?>" class="profile-img-nav" alt="Profile">
                    </a>
                    
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- PROFILE CARD -->
    <div class="container">
        <div class="profile-card shadow-sm">
            <div class="d-flex">
                <img 
                src="<?= !empty($user['profile_photo']) ? $user['profile_photo'] : "../assets/img/user.jpg" ?>" 
                class="profile-img" 
                alt="Profile"
                >
                <div>
                    <div class="profile-name"><?= $user['name'] ?></div>
                    <div class="text-muted small">
                        <i class="bi bi-house"></i> 
                        <?= !empty($user['location']) ? $user['location'] : 'Alamat belum ditentukan' ?> 
                        <br>
                        
                        <i class="bi bi-gender-ambiguous"></i> <?= $user['gender'] ?>
                    </div>
                    <button class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="bi bi-pencil-square"></i> Edit Profil
                    </button>
                </div>
            </div>
        </div>
        
        <div class="my-3 text-end">
            Mau jadi Teman? <a href="" class="btn btn-warning">Join Jadi Teman</a>
        </div>
        
        <!-- TAB NAVIGATION -->
        <ul class="nav nav-tabs mt-3">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#about">About</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#community">Komunitas</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#history">Riwayat Booking</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#reviews">Ulasan Saya</a></li>
        </ul>
        
        <div class="tab-content mt-3">
            <!-- ABOUT -->
            <div class="tab-pane fade show active" id="about">
                <div class="row">
                    <!-- HOBBY -->
                    <div class="col-md-4">
                        <div class="box">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-primary">Hobby</h6>
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#hobbyModal">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                            <?php 
                            $hobbies = !empty($user['hobby']) ? explode(",", $user['hobby']) : [];
                            if (count($hobbies) > 0) {
                                foreach ($hobbies as $h) {
                                    echo "<span class='tag'> ".trim($h)."</span> ";
                                }
                            } else {
                                echo "<p class='text-muted'>Belum ada hobby</p>";
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- ABOUT & STATISTIK -->
                    <div class="col-md-8">
                        <div class="box">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-primary">About</h6>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#aboutModal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>
                            <p><?= !empty($user['bio']) ? $user['bio'] : 'Belum ada deskripsi.' ?></p>
                            <hr>
                            <h6 class="fw-bold text-primary">Statistik</h6>
                            <p class="mb-1">Booking Dilakukan: <?= $user['booking_count'] ?? 0 ?></p>
                            <p class="mb-0">Ulasan Diberikan: <?= $user['review_count'] ?? 0 ?></p>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- KOMUNITAS -->
            <div class="tab-pane fade" id="community">
                <div class="box">
                    <h6 class="fw-bold text-primary mb-3">Komunitas yang Saya Ikuti</h6>
                    <p class="text-muted">Fitur komunitas belum tersedia.</p>
                </div>
            </div>
            
            <!-- RIWAYAT BOOKING -->
            <div class="tab-pane fade" id="history">
                <div class="box">
                    <p class="text-muted">📅 Riwayat booking akan tampil di sini.</p>
                </div>
            </div>
            
            <!-- ULASAN -->
            <div class="tab-pane fade" id="reviews">
                <div class="box">
                    <p class="text-muted">⭐ Ulasan yang sudah Anda berikan akan tampil di sini.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL EDIT ABOUT -->
    <div class="modal fade" id="aboutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit About</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="update_about" value="1">
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Diri</label>
                            <textarea name="bio" rows="4" class="form-control"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Simpan About
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MODAL EDIT PROFIL -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="name" class="form-control" value="<?= $user['name'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat</label>
                                <input type="text" name="location" class="form-control" value="<?= $user['location'] ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="gender" class="form-select">
                                    <option <?= $user['gender']=='Pria'?'selected':'' ?>>Pria</option>
                                    <option <?= $user['gender']=='Wanita'?'selected':'' ?>>Wanita</option>
                                    <option <?= $user['gender']=='Lainnya'?'selected':'' ?>>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Foto Profil</label>
                                <input type="file" name="profile_photo" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ganti">
                        </div>
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MODAL EDIT HOBBY -->
    <div class="modal fade" id="hobbyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-lg"></i> Tambah / Edit Hobby</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="update_hobby" value="1">
                        <div class="mb-3">
                            <label class="form-label">Daftar Hobby (pisahkan dengan koma)</label>
                            <input type="text" name="hobby" class="form-control" 
                            value="<?= htmlspecialchars($user['hobby'] ?? '') ?>">
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-save"></i> Simpan Hobby
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
