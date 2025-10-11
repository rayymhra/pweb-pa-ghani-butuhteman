<?php
session_start();
require 'config.php'; // koneksi db

// pastikan user login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rate      = mysqli_real_escape_string($conn, $_POST['hourly_rate']);
    $location  = mysqli_real_escape_string($conn, $_POST['location']);
    // $tags      = mysqli_real_escape_string($conn, $_POST['tags']);
    $available = isset($_POST['available']) ? 1 : 0;

    // insert ke friend_profiles
    $sql = "INSERT INTO friend_profiles (user_id, hourly_rate, location, available)
            VALUES ($user_id, '$rate', '$location', $available)";
    mysqli_query($conn, $sql);

    // update role jadi Friend
    mysqli_query($conn, "UPDATE users SET role='Friend' WHERE id=$user_id");

    // update session biar langsung berubah
    $_SESSION['user']['role'] = 'Friend';

    header("Location: profil_teman.php?joined=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Gabung Jadi Teman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Aktifkan Profil Teman</h2>
    <p class="text-muted">Lengkapi informasi berikut agar client bisa menemukan dan membooking kamu.</p>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Tarif per Jam (Rp)</label>
            <input type="number" name="hourly_rate" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Lokasi</label>
            <input type="text" name="location" class="form-control" placeholder="Contoh: Jakarta, Bandung">
        </div>

        <!-- <div class="mb-3">
            <label class="form-label">Tags (pisahkan dengan koma)</label>
            <input type="text" name="tags" class="form-control" placeholder="contoh: gaming, belajar, jalan-jalan">
        </div> -->

        <div class="form-check mb-3">
            <input type="checkbox" name="available" class="form-check-input" id="available">
            <label class="form-check-label" for="available">Tersedia untuk booking</label>
        </div>

        <button type="submit" class="btn btn-primary">Gabung Sekarang</button>
        <a href="profil_user.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
