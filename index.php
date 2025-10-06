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
    // if (! isset($_SESSION['user'])) {
    //     header("Location: auth/login.php");
    //     exit;
    // }

$user = null; // default null

if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
    $user_id = intval($_SESSION['user']['id']);
    $q       = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
    $user    = mysqli_fetch_assoc($q);
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        
        @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap');
        
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
        
        
        
        
        
        
        
        .btn-yellow {
            background-color: #FECE6A;
            color: #0F4457;
            border-color: #FECE6A;
            font-weight: 600;
            font-size: 18px;
        }
        
        .btn-yellow:hover {
            background-color: #e6b95f; /* slightly darker yellow for hover */
            color: #0F4457;
        }
        
        .btn-yellow:focus,
        .btn-yellow:active {
            background-color: #d9aa54 !important; /* pressed state */
            color: #0F4457 !important;
            box-shadow: 0 0 0 0.25rem rgba(254, 206, 106, 0.5);
        }
        
        
        .actived {
            color: #06206C;
            font-weight: 600;
        }
        
        .beranda-title {
            color: #06206C;
            font-size: 60px;
            font-weight: 700;
            
        }
        
        .beranda-title-title {
            color: #315DB4;
            font-size: 60px;
            font-weight: 700;
        }
        
        .tentang-kami-section {
            background-color: #06206C;
        }
        
        .tentang-description {
            text-align: justify;
            font-size: 17px;
        }
        
        .tentang-title {
            color: #FECE6A;
            width: fit-content;
            position: relative;
        }
        
        .tentang-title::after {
            content: "";
            width: 100%;
            height: 7px;
            
            background-color: #315DB4;
            
            position: absolute;
            bottom: -2px;
            left: 0;
        }
        
        .btn-warning {
            background-color: #FECE6A;
            color: #0F4457;
            font-weight: 600;
            border-color: #FECE6A;
            border-width: 3px;
        }
        
        .btn-warning:hover {
            background-color: #ffffff00;
            color: #0F4457;
            border-color: #FECE6A;
            border-width: 3px;
            
        }
        
        .btn-outline-warning {
            color: #0F4457;
            border-color: #FECE6A;
            border-width: 3px;
            font-weight: 600;
            
        }
        
        .btn-outline-warning:hover {
            background-color: #FECE6A;
            border-color: #FECE6A;
            color: #0F4457;
        }
        
        .statistic-section {
            background-color: #FECE6A;
        }
        
        .statistic-title {
            color: #0F4457;
            font-weight: 600;
        }
        
        .statistic-number {
            font-weight: 700;
        }
        
        /* CARI TEMAN INDEX */
        .cari-teman-section .card {
            border: #FECE6A 3px solid;
        }
        
        .cari-teman-title h1{
            color: #06206C;
            font-weight: 700;
        }
        
        .cari-teman-title:after {
            content: "";
            width: 24%; 
            height: 4px;
            background: #FECE6A;
            margin-left: auto;
            margin-right: auto;
            display: block;
        }
        
        .komunitas-title {
            color: #06206C;
            font-weight: 700;
        }
        
        .komunitas-title:after {
            content: "";
            width: 24%; 
            height: 4px;
            background: #FECE6A;
            margin-left: auto;
            margin-right: auto;
            display: block;
        }
        
        
        
        
        
        .footer {
            background: #FECE6A;
            padding: 60px 40px 30px;
            color: #06206C;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 50px;
            align-items: start;
        }
        
        .footer-section h2 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 25px;
            line-height: 1.2;
            color: #06206C;
        }
        
        .footer-section h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1976D2;
            border-bottom: 2px solid #2196F3;
            padding-bottom: 8px;
            display: inline-block;
        }
        
        .footer-section p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
            color: #1565C0;
        }
        
        .footer-section ul {
            list-style: none;
        }
        
        .footer-section ul li {
            margin-bottom: 12px;
        }
        
        .footer-section ul li a {
            color: #1976D2;
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .footer-section ul li a:hover {
            color: #0D47A1;
            text-decoration: underline;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 16px;
            color: #1565C0;
        }
        
        .contact-item i {
            font-size: 20px;
            margin-right: 12px;
            color: #2196F3;
            width: 25px;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background-color: #2196F3;
            color: white;
            border-radius: 50%;
            font-size: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background-color: #1976D2;
            transform: translateY(-2px);
        }
        
        .footer-bottom {
            text-align: center;
            margin-top: 50px;
            padding-top: 25px;
            border-top: 1px solid rgba(33, 150, 243, 0.3);
            font-size: 16px;
            color: #1976D2;
            font-weight: 500;
        }
        
        @media (max-width: 1024px) {
            .footer-content {
                grid-template-columns: 1fr 1fr;
                gap: 40px;
            }
        }
        
        @media (max-width: 768px) {
            .footer {
                padding: 40px 20px 20px;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 35px;
                text-align: center;
            }
            
            .footer-section h2 {
                font-size: 28px;
            }
            
            .footer-section h3 {
                font-size: 20px;
            }
            
            .contact-item {
                justify-content: center;
            }
            
            .social-links {
                justify-content: center;
            }
        }
        
        
        
        
        
        
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="">
                <img src="assets/img/logo butuh teman.png" alt="" width="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link actived" aria-current="page" href="#home">BERANDA</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="#tentang">TENTANG</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="#cari-teman">CARI TEMAN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="#komunitas">KOMUNITAS</a>
                    </li>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                    <!-- Jika user login -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo ! empty($user['profile_photo']) ? 'auth/' . $user['profile_photo'] : 'assets/img/default-user.png' ?>"
                            alt="profile"
                            class="rounded-circle me-2"
                            width="35" height="35"
                            style="object-fit: cover;">
                            <?php echo htmlspecialchars($_SESSION['user']['name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="auth/profil_user.php">Profil Saya</a></li>
                            <li><a class="dropdown-item" href="settings.php">Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <!-- Jika belum login -->
                    <li class="nav-item">
                        <a href="auth/login.php" class="btn btn-login ms-3">Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
                
            </div>
        </div>
    </nav>
    
    <div class="beranda-section py-5" id="home">
        <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-6">
                    <h1 class="beranda-title">
                        BUTUH
                    </h1>
                    <h1 class="beranda-title-title">
                        TEMAN
                    </h1>
                    <h5 class="beranda-subtitle">
                        Butuh Teman Ngobrol? Sewa Sekarang!
                    </h5>
                    
                    <div class="btn-beranda mt-5">
                        <a href="" class="btn btn-outline-warning">Lihat Teman</a>
                        <a href="auth/login.php" class="btn btn-warning">Gabung Jadi Teman</a>
                    </div>
                    
                </div>
                <div class="col-6">
                    <img src="assets/img/undraw_living_9un5.svg" alt="" class="w-100">
                </div>
            </div>
        </div>
    </div>
    
    <div class="tentang-kami-section py-5" id="tentang">
        <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-6">
                    <div class="tentang-title">
                        <h1>TENTANG KAMI</h1>
                    </div>
                </div>
                <div class="col-6 text-white tentang-description">
                    Kami menyadari bahwa banyak orang yang membutuhkan teman tetapi hanya untuk menemani pergi ke suatu tempat saja atau untuk agar tidak merasa kesepian saja. Di jaman sekarang ini memang terasa sulit untuk mempunyai teman yang memiliki waktu luang karena teman kita selalu sibuk, jadi kita memutuskan untuk membuat “Butuh Teman” untuk mengatasi masalah umum ini.
                </div>
            </div>
        </div>
    </div>
    
    <div class="statistic-section py-5" >
        <div class="container">
            <div class="row">
                <div class="col-3 text-center">
                    <h1 class="statistic-number text-black mb-0">
                        2020
                    </h1>
                    <h5 class="statistic-title">
                        Perusahaan didirikan
                    </h5>
                </div>
                <div class="col-3 text-center">
                    <h1 class="statistic-number text-black mb-0">
                        2jt+
                    </h1>
                    <h5 class="statistic-title">
                        Teman
                    </h5>
                </div>
                <div class="col-3 text-center">
                    <h1 class="statistic-number text-black mb-0">
                        200
                    </h1>
                    <h5 class="statistic-title">
                        Komunitas
                    </h5>
                </div>
                <div class="col-3 text-center">
                    <h1 class="statistic-number text-black mb-0">
                        1jt+
                    </h1>
                    <h5 class="statistic-title">
                        Telah berteman
                    </h5>
                </div>
            </div>
        </div>
        
    </div>
    
    <div class="cari-teman-section py-5" id="cari-teman">
        <div class="container">
            <div class="cari-teman-title text-center">
                <h1 class="mb-0">CARI TEMAN</h1>
            </div>
            <div class="cari-teman-desc text-center mt-3">
                Temukan teman yang kalian cari cari selama ini!
            </div>
            
            <div class="row">
                <div class="col-4 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex align-items-center">
                                <div class="col-6">
                                    <img src="assets/img/est1.jpeg" alt="" style="aspect-ratio: 1 / 1; overflow: hidden; object-fit: cover; height: 100%;" class="w-100">
                                </div>
                                <div class="col-6 text-center">
                                    <h5>Saint Sky</h5>
                                    <a href="" class="btn btn-primary">BOOK</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex align-items-center">
                                <div class="col-6">
                                    <img src="assets/img/est1.jpeg" alt="" style="aspect-ratio: 1 / 1; overflow: hidden; object-fit: cover; height: 100%;" class="w-100">
                                </div>
                                <div class="col-6 text-center">
                                    <h5>Saint Sky</h5>
                                    <a href="" class="btn btn-primary">BOOK</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex align-items-center">
                                <div class="col-6">
                                    <img src="assets/img/est1.jpeg" alt="" style="aspect-ratio: 1 / 1; overflow: hidden; object-fit: cover; height: 100%;" class="w-100">
                                </div>
                                <div class="col-6 text-center">
                                    <h5>Saint Sky</h5>
                                    <a href="" class="btn btn-primary">BOOK</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex align-items-center">
                                <div class="col-6">
                                    <img src="assets/img/est1.jpeg" alt="" style="aspect-ratio: 1 / 1; overflow: hidden; object-fit: cover; height: 100%;" class="w-100">
                                </div>
                                <div class="col-6 text-center">
                                    <h5>Saint Sky</h5>
                                    <a href="" class="btn btn-primary">BOOK</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex align-items-center">
                                <div class="col-6">
                                    <img src="assets/img/est1.jpeg" alt="" style="aspect-ratio: 1 / 1; overflow: hidden; object-fit: cover; height: 100%;" class="w-100">
                                </div>
                                <div class="col-6 text-center">
                                    <h5>Saint Sky</h5>
                                    <a href="" class="btn btn-primary">BOOK</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex align-items-center">
                                <div class="col-6">
                                    <img src="assets/img/est1.jpeg" alt="" style="aspect-ratio: 1 / 1; overflow: hidden; object-fit: cover; height: 100%;" class="w-100">
                                </div>
                                <div class="col-6 text-center">
                                    <h5>Saint Sky</h5>
                                    <a href="" class="btn btn-primary">BOOK</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-end mt-4">
                <a href="cari-teman/index.html" class="btn btn-yellow">LIHAT LEBIH BANYAK</a>
            </div>
        </div>
        
    </div>
    
    <div class="komunitas-section" id="komunitas">
        <div class="container">
            <h1 class="komunitas-title text-center">KOMUNITAS</h1>
            <p class="komunitas-desc text-center mt-3">Join komunitas untuk diskusi dan temukan teman sefrekuensi!</p>
            
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="card position-relative text-white" style="aspect-ratio: 1 / 1; overflow: hidden;">
                        <img src="assets/img/berani baca.jpeg" class="card-img" alt="Komunitas" style="object-fit: cover; height: 100%;">
                        
                        <div class="position-absolute top-0 end-0 m-2">
                            <a href="#" class="btn btn-yellow btn-sm">Join Sekarang</a>
                        </div>
                        
                        <div class="position-absolute bottom-0 w-100 p-4" 
                        style="background: rgba(0,0,0,0.4);">
                        <h5 class="mb-1">Berani Baca</h5>
                        <p class="mb-0 small">
                            Untuk kalian yang berminat bergabung bersama komunitas Berani Baca pantengin Instagram Berani baca yaa
                        </p>
                    </div>
                </div>
                
                
                
            </div>
            <div class="col-6 mt-3">
                <div class="card position-relative text-white" style="aspect-ratio: 1 / 1; overflow: hidden;">
                    <img src="assets/img/cosplay wiwiwiwi.jpeg" class="card-img" alt="Komunitas" style="object-fit: cover; height: 100%;">
                    
                    <div class="position-absolute top-0 end-0 m-2">
                        <a href="#" class="btn btn-yellow btn-sm">Join Sekarang</a>
                    </div>
                    
                    <div class="position-absolute bottom-0 w-100 p-4" 
                    style="background: rgba(0,0,0,0.4);">
                    <h5 class="mb-1">Cosplay Jakarta</h5>
                    <p class="mb-0 small">
                        One Stop For Event Service, Community, News, & Info for Cosplay, JPop, & Popculture since 2008
                    </p>
                </div>
            </div>
            
            
            
        </div>
        <div class="col-6 mt-3">
            <div class="card position-relative text-white" style="aspect-ratio: 1 / 1; overflow: hidden;">
                <img src="assets/img/anak teknik.jpeg" class="card-img" alt="Komunitas" style="object-fit: cover; height: 100%;">
                
                <div class="position-absolute top-0 end-0 m-2">
                    <a href="#" class="btn btn-yellow btn-sm">Join Sekarang</a>
                </div>
                
                <div class="position-absolute bottom-0 w-100 p-4" 
                style="background: rgba(0,0,0,0.4);">
                <h5 class="mb-1">Anak Teknik Indonesia</h5>
                <p class="mb-0 small">
                    Terbuka untuk semua mahasiswa atau alumnus FTIK seluruh indonesia ataupun teman-teman yang ingin bagi-bagi ilmunya.
                </p>
            </div>
        </div>
        
        
        
    </div>
    <div class="col-6 mt-3">
        <div class="card position-relative text-white" style="aspect-ratio: 1 / 1; overflow: hidden;">
            <img src="assets/img/jakarta swim.jpg" class="card-img" alt="Komunitas" style="object-fit: cover; height: 100%;">
            
            <div class="position-absolute top-0 end-0 m-2">
                <a href="#" class="btn btn-yellow btn-sm">Join Sekarang</a>
            </div>
            
            <div class="position-absolute bottom-0 w-100 p-4" 
            style="background: rgba(0,0,0,0.4);">
            <h5 class="mb-1">Jakarta Swim Community</h5>
            <p class="mb-0 small">
                komunitas non profesional untuk umum. keep swimming and you never swim alone. contact : 085693061529.
            </p>
        </div>
    </div>
    
    
    
</div>

</div>

<div class="text-end mt-4">
    <a href="komunitas/index.html" class="btn btn-yellow">LIHAT LEBIH BANYAK</a>
</div>
</div>
</div>

<footer class="footer mt-5">
    <div class="footer-content">
        <div class="footer-section">
            <h2>BUTUH<br>TEMAN</h2>
            <p>Di tengah kesibukan dunia modern, kami memahami sulitnya menemukan teman yang punya waktu luang. Karena itu, kami menjadi jembatan untuk menghubungkan orang-orang yang membutuhkan kebersamaan dengan teman yang siap hadir.</p>
        </div>
        
        <div class="footer-section">
            <h3>Navigasi</h3>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#tentang">Tentang</a></li>
                <li><a href="#cari-teman">Cari Teman</a></li>
                <li><a href="#komunitas">Komunitas</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h3>Kontak Kami</h3>
            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>Cileungsi, Indonesia</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <span>+62 813 9028 6789</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <span>butuhteman@gmail.com</span>
            </div>
        </div>
        
        <div class="footer-section">
            <h3>Ikuti Kami</h3>
            <div class="social-links">
                <a href="#" aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" aria-label="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        © 2025 BUTUH TEMAN - All Rights Reserved.
    </div>
</footer>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>
</html>