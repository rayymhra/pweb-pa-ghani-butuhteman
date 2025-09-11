<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                                    <a href="" class="btn btn-primary">ADD</a>
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
                                    <a href="" class="btn btn-primary">ADD</a>
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
                                    <a href="" class="btn btn-primary">ADD</a>
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
                                    <a href="" class="btn btn-primary">ADD</a>
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
                                    <a href="" class="btn btn-primary">ADD</a>
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
                                    <a href="" class="btn btn-primary">ADD</a>
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