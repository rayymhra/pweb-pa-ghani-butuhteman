<?php
    session_start();

    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "butuh_teman";

    $conn = mysqli_connect($host, $user, $pass, $db);
    if (! $conn) {
        die("Koneksi gagal: " . mysqli_connect_error());
    }

    // Check if user is logged in
    if (! isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }

    $user_id = $_SESSION['user']['id'];
    $q       = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
    $user    = mysqli_fetch_assoc($q);

    // ====== update profil umum ======
    if (isset($_POST['update_profile'])) {
        $name     = mysqli_real_escape_string($conn, $_POST['name']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
        $gender   = mysqli_real_escape_string($conn, $_POST['gender']);
        $password = $_POST['password'];

        // upload foto jika ada
        $update_photo = "";
        if (! empty($_FILES['profile_photo']['name'])) {
            if (! is_dir("uploads")) {
                mkdir("uploads");
            }

            $fileName = time() . "_" . basename($_FILES['profile_photo']['name']);
            $target   = "uploads/" . $fileName;
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target)) {
                $update_photo = ", profile_photo='$target'";
            }
        }

        // update password jika diisi
        $update_password = "";
        if (! empty($password)) {
            $hashed          = password_hash($password, PASSWORD_DEFAULT);
            $update_password = ", password='$hashed'";
        }

        $sql = "UPDATE users SET
    name='$name',
    location='$location',
    phone='$phone',
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
        $sql   = "UPDATE users SET hobby='$hobby' WHERE id=$user_id";
        mysqli_query($conn, $sql);
        header("Location: profil_user.php?hobby_updated=1");
        exit;
    }

    // ====== ambil data user ======
    $q    = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
    $user = mysqli_fetch_assoc($q);




    $user_session = $_SESSION['user'];
$user_id = $user_session['id'];

$current_user_query = "SELECT * FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $current_user_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $current_user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    // Update session dengan data terbaru dari database
    $_SESSION['user'] = array_merge($_SESSION['user'], $current_user);
    $user_session = $_SESSION['user'];
}
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

        /* NAVBARRRRRR */
        .navbar {
            background-color: #FECE6A !important;
        }

        .nav-link {
            color: #315DB4;
            font-weight: 500;
        }

        /* Pastikan semua nav-item rata tengah */
        .navbar .nav-link,
        .navbar .btn-login {
            display: flex;
            align-items: center;
            height: 40px; /* sesuaikan agar konsisten */
            line-height: 1.2;
            padding-top: 0;
            padding-bottom: 0;
        }

        /* Supaya foto profil benar-benar sejajar */
        .navbar .nav-link img {
            width: 35px;
            height: 35px;
            object-fit: cover;
            border-radius: 50%;
        }

        /* Untuk tombol login */
        .btn-login {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-login {
            background-color: #06206C; /* biru navy */
            color: #FECE6A; /* kuning */
            font-weight: 600;
            font-size: 18px;
            padding: 8px 20px;
            border-radius: 30px;
            border: 2px solid #06206C;
            transition: all 0.3s ease;
            /* box-shadow: 0 3px 8px rgba(6, 32, 108, 0.3); */
        }

        .btn-login:hover {
            background-color: #FECE6A; /* kuning */
            color: #06206C; /* biru navy */
            border: 2px solid #FECE6A;
            box-shadow: 0 5px 12px rgba(254, 206, 106, 0.4);
            transform: translateY(-2px);
        }

        .btn-login:active {
            transform: scale(0.95);
            background-color: #e6b95f; /* kuning gelap */
            border-color: #e6b95f;
            color: #06206C;
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

        .booking-item img{
            height: 100px;
            width: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body class="bg-light">

   <!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="../index.php">
            <img src="assets/img/logo butuh teman.png" alt="" width="50">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="../index.php">BERANDA</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="../index.php#tentang">TENTANG</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="../index.php#cari-teman">CARI TEMAN</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="../index.php#komunitas">KOMUNITAS</a>
                </li>

                <?php if (isset($_SESSION['user'])): ?>
                <!-- Jika user login -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo ! empty($user_session['profile_photo']) ? $user_session['profile_photo'] : '../assets/img/user.jpg' ?>"
                        alt="profile"
                        class="rounded-circle me-2"
                        width="35" height="35"
                        style="object-fit: cover;">
                        <?php echo htmlspecialchars($user_session['name']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <?php
                        // Determine profile link based on user role
                        $profile_link = 'profil_user.php'; // default for client
                        if ($user_session['role'] === 'Friend') {
                            $profile_link = 'profil_teman.php';
                        } elseif ($user_session['role'] === 'Admin') {
                            $profile_link = 'admin/index.php'; // or whatever admin profile page
                        }
                        ?>
                        <li><a class="dropdown-item" href="<?php echo $profile_link; ?>">Profil Saya</a></li>
                        <!-- <li><a class="dropdown-item" href="settings.php">Pengaturan</a></li> -->
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <!-- Jika belum login -->
                <li class="nav-item">
                    <a href="login.php" class="btn btn-login ms-3">Login</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>



    <!-- PROFILE CARD -->
    <div class="container">
        <div class="profile-card shadow-sm">
            <div class="d-flex">
                <img
                src="<?php echo ! empty($user['profile_photo']) ? $user['profile_photo'] : "../assets/img/user.jpg" ?>"
                class="profile-img"
                alt="Profile"
                >
                <div>
                    <div class="profile-name"><?php echo $user['name'] ?></div>
                    <div class="text-muted small">
                        <i class="bi bi-house"></i>
                        <?php echo ! empty($user['location']) ? $user['location'] : 'Alamat belum ditentukan' ?>
                        <br>

                        <i class="bi bi-gender-ambiguous"></i>                                                               <?php echo $user['gender'] ?>
                    </div>
                    <button class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="bi bi-pencil-square"></i> Edit Profil
                    </button>
                </div>
            </div>
        </div>

        <div class="my-3 text-end">
            Mau jadi Teman? <a href="join_friend.php" class="btn btn-warning">Join Jadi Teman</a>
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
                                $hobbies = ! empty($user['hobby']) ? explode(",", $user['hobby']) : [];
                                if (count($hobbies) > 0) {
                                    foreach ($hobbies as $h) {
                                        echo "<span class='tag'> " . trim($h) . "</span> ";
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
                            <p><?php echo ! empty($user['bio']) ? $user['bio'] : 'Belum ada deskripsi.' ?></p>
                            <hr>
                            <h6 class="fw-bold text-primary">Statistik</h6>
                            <p class="mb-1">Booking Dilakukan:                                                               <?php echo $user['booking_count'] ?? 0 ?></p>
                            <p class="mb-0">Ulasan Diberikan:                                                              <?php echo $user['review_count'] ?? 0 ?></p>
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
        <h6 class="fw-bold text-primary mb-3">Riwayat Booking Saya</h6>
        <?php
        // Get client's booking history
        $client_bookings_query = "SELECT b.*, u.name as friend_name, u.profile_photo as friend_photo 
                                 FROM bookings b 
                                 JOIN users u ON b.friend_id = u.id 
                                 WHERE b.client_id = ? 
                                 ORDER BY b.created_at DESC";
        
        if ($stmt = mysqli_prepare($conn, $client_bookings_query)) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $client_bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_stmt_close($stmt);
        }
        
        if (!empty($client_bookings)): ?>
            <div class="list-group">
                <?php foreach ($client_bookings as $booking): 
                    $time_remaining = strtotime($booking['start_datetime']) - time();
                    $hours_remaining = floor($time_remaining / 3600);
                ?>
                    <div class="list-group-item booking-item mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-center flex-grow-1">
                                <img src="<?php echo !empty($booking['friend_photo']) ? htmlspecialchars($booking['friend_photo']) : 'assets/img/default-user.png' ?>" 
                                     class="client-avatar me-3" alt="Friend">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($booking['friend_name']); ?></h6>
                                    <p class="mb-1 small">
                                        <i class="bi bi-calendar"></i> 
                                        <?php echo date('d M Y', strtotime($booking['start_datetime'])); ?>
                                    </p>
                                    <p class="mb-1 small">
                                        <i class="bi bi-clock"></i> 
                                        <?php echo date('H:i', strtotime($booking['start_datetime'])); ?> - 
                                        <?php echo date('H:i', strtotime($booking['end_datetime'])); ?>
                                        (<?php echo round((strtotime($booking['end_datetime']) - strtotime($booking['start_datetime'])) / 3600, 1); ?> jam)
                                    </p>
                                    <p class="mb-0 small">
                                        <i class="bi bi-currency-dollar"></i> 
                                        Rp <?php echo number_format($booking['total_price'], 0, ',', '.'); ?>
                                    </p>
                                    
                                    <!-- <?php if ($booking['status'] == 'pending' && $time_remaining > 0): ?>
                                        <p class="mb-0 small time-remaining <?php echo $hours_remaining < 24 ? 'urgent' : ''; ?>">
                                            <i class="bi bi-alarm"></i>
                                            Menunggu konfirmasi: <?php echo $hours_remaining; ?> jam lagi
                                            <?php if ($hours_remaining < 24): ?>
                                                <br><small class="text-danger">Teman harus segera konfirmasi!</small>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?> -->
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="booking-status-<?php echo $booking['status']; ?>">
                                    <?php 
                                    $status_text = [
                                        'pending' => 'Menunggu Konfirmasi',
                                        'accepted' => 'Diterima',
                                        'rejected' => 'Ditolak',
                                        'completed' => 'Selesai'
                                    ];
                                    echo $status_text[$booking['status']] ?? $booking['status'];
                                    ?>
                                </span>
                                <br>
                                <small class="text-muted">
                                    <?php echo date('d M Y H:i', strtotime($booking['created_at'])); ?>
                                </small>
                                
                                <?php if ($booking['status'] == 'rejected' && $time_remaining > 0): ?>
                                    <div class="mt-2">
                                        <a href="profil_teman.php?id=<?php echo $booking['friend_id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="bi bi-calendar-plus"></i> Booking Lagi
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">Belum ada riwayat booking.</p>
            <div class="text-center">
                <a href="../index.php#cari-teman" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari Teman untuk Booking
                </a>
            </div>
        <?php endif; ?>
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
                            <textarea name="bio" rows="4" class="form-control"><?php echo htmlspecialchars($user['bio'] ?? '') ?></textarea>
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
                                <input type="text" name="name" class="form-control" value="<?php echo $user['name'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat</label>
                                <input type="text" name="location" class="form-control" value="<?php echo $user['location'] ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor HP</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo $user['phone'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="gender" class="form-select">
                                    <option                                            <?php echo $user['gender'] == 'Pria' ? 'selected' : '' ?>>Pria</option>
                                    <option                                            <?php echo $user['gender'] == 'Wanita' ? 'selected' : '' ?>>Wanita</option>
                                    <option                                            <?php echo $user['gender'] == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" name="profile_photo" class="form-control">
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
                            value="<?php echo htmlspecialchars($user['hobby'] ?? '') ?>">
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
