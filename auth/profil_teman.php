<?php
session_start();
require 'config.php';

// --- LOGIN CHECK ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// --- AMBIL DATA USER + PROFIL ---
$sql = "SELECT u.*, f.bio, f.hourly_rate, f.location, f.tags 
        FROM users u 
        LEFT JOIN friend_profiles f ON u.id = f.user_id
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User tidak ditemukan!";
    exit;
}

// --- AMBIL DATA REVIEW ---
$sql_reviews = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                FROM reviews r 
                JOIN bookings b ON r.booking_id = b.id
                WHERE b.friend_id = ?";
$stmt2 = $conn->prepare($sql_reviews);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$reviews = $stmt2->get_result()->fetch_assoc();

$avg_rating = $reviews['avg_rating'] ?? 0;
$total_reviews = $reviews['total_reviews'] ?? 0;

// --- JUMLAH BOOKING ---
$sql_booking = "SELECT COUNT(*) as total_booking FROM bookings WHERE friend_id = ?";
$stmt3 = $conn->prepare($sql_booking);
$stmt3->bind_param("i", $user_id);
$stmt3->execute();
$total_booking = $stmt3->get_result()->fetch_assoc()['total_booking'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User</title>

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap');
        * { font-family: "Josefin Sans", sans-serif; }
        .navbar { background-color: #FECE6A !important; }
        .nav-link { color: #315DB4; font-weight: 500; }
        .actived { color: #06206C; font-weight: 600; }
        .profile-card { border: 1px solid #f2e6c9; border-radius: 12px; background-color: #fffdf7; padding: 1.5rem; margin-top: 1rem; }
        .profile-img { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; margin-right: 1rem; }
        .profile-name { font-size: 1.3rem; font-weight: 600; }
        .btn-yellow { background-color: #FECE6A; border: none; color: #000; font-weight: 500; border-radius: 8px; padding: 5px 15px; }
        .nav-tabs .nav-link { border: none; color: #315DB4; font-weight: 500; }
        .nav-tabs .nav-link.active { font-weight: 600; border-bottom: 3px solid #315DB4; color: #315DB4; }
        .hobby-tag { background-color: #FECE6A; color: #000; padding: 3px 10px; border-radius: 20px; margin: 2px; font-size: 0.85rem; display: inline-block; }
        .box { background: #fff; border-radius: 10px; padding: 1rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="assets/logo butuh teman.png" alt="" width="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link actived" href="index.php">BERANDA</a></li>
                    <li class="nav-item"><a class="nav-link" href="tentang.php">TENTANG</a></li>
                    <li class="nav-item"><a class="nav-link" href="cari-teman.php">CARI TEMAN</a></li>
                    <li class="nav-item"><a class="nav-link" href="komunitas.php">KOMUNITAS</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- PROFILE CARD -->
    <div class="container">
        <div class="profile-card shadow-sm">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <img src="<?= htmlspecialchars($user['profile_photo'] ?: 'assets/default.png') ?>" class="profile-img" alt="Profile">
                    <div>
                        <div class="profile-name"><?= htmlspecialchars($user['name']) ?></div>
                        <div class="text-warning">
                            ⭐ <?= number_format($avg_rating, 1) ?> (<?= $total_reviews ?> ulasan)
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($user['location'] ?: 'Belum diatur') ?><br>
                            <i class="bi bi-tag"></i> <?= htmlspecialchars($user['tags'] ?: '-') ?>
                        </div>
                        <div class="mt-2">
                            <button class="btn-yellow me-2">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <button class="btn-yellow">
                                <i class="bi bi-calendar-event"></i> Booking
                            </button>
                        </div>
                    </div>
                </div>
                <div class="text-muted small">
                    Bergabung: <?= date("M Y", strtotime($user['created_at'])) ?>
                </div>
            </div>
        </div>

        <!-- TAB NAVIGATION -->
        <ul class="nav nav-tabs mt-3">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#about">About</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#photos">Photos</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#community">Komunitas</a></li>
        </ul>

        <!-- TAB CONTENT -->
        <div class="tab-content mt-3">
            <!-- About -->
            <div class="tab-pane fade show active" id="about">
                <div class="row">
                    <div class="col-md-4">
                        <div class="box">
                            <h6 class="fw-bold">Hobby <button class="btn btn-sm btn-light ms-2">+</button></h6>
                            <div>
                                <?php 
                                if (!empty($user['tags'])) {
                                    $tags = explode(',', $user['tags']);
                                    foreach ($tags as $tag) {
                                        echo "<span class='hobby-tag'>" . htmlspecialchars(trim($tag)) . "</span>";
                                    }
                                } else {
                                    echo "<span class='text-muted small'>Belum ada hobby</span>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="box">
                            <h6 class="fw-bold text-primary">About</h6>
                            <p class="mb-2"><?= nl2br(htmlspecialchars($user['bio'] ?: 'Belum ada deskripsi.')) ?></p>
                            <h6 class="fw-bold text-primary">Statistik</h6>
                            <p class="mb-0">
                                Booking Dilakukan: <?= $total_booking ?><br>
                                Ulasan Diberikan: <?= $total_reviews ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Photos -->
            <div class="tab-pane fade" id="photos">
                <p>Foto-foto user tampil di sini.</p>
            </div>

            <!-- Komunitas -->
            <div class="tab-pane fade" id="community">
                <p>Komunitas user tampil di sini.</p>
            </div>
        </div>
    </div>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
