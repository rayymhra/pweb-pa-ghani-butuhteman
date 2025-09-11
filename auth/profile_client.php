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
        @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap');
        * { font-family: "Josefin Sans", sans-serif; }
        .navbar { background-color: #FECE6A !important; }
        .nav-link { color: #315DB4; font-weight: 500; }
        .actived { color: #06206C; font-weight: 600; }
        .profile-card { border: 1px solid #f2e6c9; border-radius: 12px; background-color: #fffdf7; padding: 1.5rem; margin-top: 1rem; }
        .profile-img { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; margin-right: 1rem; }
        .profile-name { font-size: 1.3rem; font-weight: 600; }
        .box { background: #fff; border-radius: 10px; padding: 1rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .tag { background-color: #FECE6A; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; margin-right: 5px; }
    </style>
</head>
<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://via.placeholder.com/50" alt="" width="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link actived" href="#">BERANDA</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">TENTANG</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">CARI TEMAN</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">KOMUNITAS</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- PROFILE CARD -->
    <div class="container">
        <div class="profile-card shadow-sm">
            <div class="d-flex">
                <img src="https://via.placeholder.com/90" class="profile-img" alt="Profile">
                <div>
                    <div class="profile-name">Nama Client</div>
                    <div class="text-muted small">
                        <i class="bi bi-envelope"></i> client@email.com <br>
                        <i class="bi bi-telephone"></i> 08123456789
                    </div>
                    <!-- TOMBOL EDIT PROFIL DITARUH DI SINI -->
                    <button class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="bi bi-pencil-square"></i> Edit Profil
                    </button>
                </div>
            </div>
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
                <div class="box">
                    <h6 class="fw-bold text-primary">Tentang Saya</h6>
                    <p>Saya seorang client yang suka mencoba pengalaman baru. Senang ngobrol dengan teman baru dan eksplorasi komunitas yang seru.</p>
                    <h6 class="fw-bold text-primary">Hobi</h6>
                    <span class="tag">Membaca</span>
                    <span class="tag">Mendaki</span>
                    <span class="tag">Ngopi</span>
                </div>
            </div>

            <!-- KOMUNITAS -->
            <div class="tab-pane fade" id="community">
                <div class="box">
                    <h6 class="fw-bold text-primary mb-3">Komunitas yang Saya Ikuti</h6>

                    <div class="d-flex align-items-center mb-2">
                        <img src="https://via.placeholder.com/50/315DB4/fff" class="me-2 rounded-circle">
                        <div>
                            <strong>Komunitas Hiking</strong><br>
                            <small class="text-muted">Tempat kumpul para pendaki gunung</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-2">
                        <img src="https://via.placeholder.com/50/FECE6A/000" class="me-2 rounded-circle">
                        <div>
                            <strong>Komunitas Ngopi Jakarta</strong><br>
                            <small class="text-muted">Ngopi santai tiap weekend ☕</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/50/f28b82/fff" class="me-2 rounded-circle">
                        <div>
                            <strong>Komunitas Baca Buku</strong><br>
                            <small class="text-muted">Diskusi buku dan sharing rekomendasi</small>
                        </div>
                    </div>
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

    <!-- MODAL EDIT PROFIL -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" value="Nama Client">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="client@email.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" value="08123456789">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select class="form-select">
                                <option selected>Laki-laki</option>
                                <option>Perempuan</option>
                                <option>Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" class="form-control" placeholder="Kosongkan jika tidak ganti">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Simpan Perubahan
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
