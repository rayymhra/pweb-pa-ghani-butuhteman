<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komunitas - Butuh Teman</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <style>
        /* Semua style yang sudah ada tetap dipertahankan */
        .footer {
            background: linear-gradient(135deg, #F4D03F, #F7DC6F);
            padding: 60px 40px 30px;
            color: #0D47A1;
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
            color: #0D47A1;
        }

        .footer-section h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #0D47A1;
            border-bottom: 2px solid #0D47A1;
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: white;
            min-height: 100vh;
        }

        @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap');

        * {
            font-family: "Josefin Sans", sans-serif;
        }

        .navbar {
    background-color: #FECE6A !important;
    position: sticky;
    top: 0;
    z-index: 1020;}

        .nav-link {
            color: #315DB4;
            font-weight: 500;
        }

        .actived {
            color: #06206C;
            font-weight: 600;
        }

        .main-content {
            flex: 1;
            background: white;
            border-radius: 15px;
            padding: 25px;
        }

  .sidebar {
    width: 290px;
    background: white;
    border-radius: 15px;
    padding: 15px; /* Padding diperkecil */
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    height: 550px; /* Tinggi diperpendek */
    position: sticky;
    top: 90px; /* Disesuaikan dengan tinggi navbar */
    border: 3px solid #2c4aa5;
    margin-left: 35px;
    z-index: 1010;
   
} 
        #main-page {
            margin-top: -40px; /* tarik lebih dekat navbar */
        }

        .page-title {
            color: #06206C;
            font-size: 30px;
            font-weight: bold;
            margin-top: 20px;
        }

        .page-title span {
            color: #294ebe; /* warna biru lebih terang untuk "TEMAN" */
        }

        .page-subtitle {
            color: #1c3c75; /* lebih lembut seperti di gambar */
            font-size: 16px;
            margin-bottom: 18px;
            font-weight: 500;
            line-height: 1.3; /* lebih lega */
        }

        .main-grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .feature-card {
            background:  #06206C;
            color: white;
            padding: 20px;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
            grid-row: span 1;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20px;
            width: 100px;
            height: 200%;
        }

        .feature-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .feature-desc {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .feature-stats {
            display: flex;
            gap: 15px;
            font-size: 12px;
        }

        .profile-image {
            background: #ddd;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 24px;
            grid-row: span 2;
            min-height: 300px;
            overflow: hidden;
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .bottom-content {
            grid-column: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .content-image {
            background: #ddd;
            border-radius: 10px;
            height: 210px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 24px;
            overflow: hidden;
        }

        .content-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .content-text {
            font-size: 40px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border: 2px solid #FECE6A;
        }

        .content-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .content-desc {
            font-size: 14px;
            color: #7f8c8d;
            line-height: 1.5;
        }

        .sidebar-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .category-list {
            max-height: 465px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .category-list::-webkit-scrollbar {
            width: 6px;
        }

        .category-list::-webkit-scrollbar-track {
            background: #ecf0ff;
            border-radius: 3px;
        }

        .category-list::-webkit-scrollbar-thumb {
            background: #2c4aa5;
            border-radius: 2px;
        }

        .category-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #eaeaea;
        }

        .category-item:hover {
            border-color: #FECE6A;
        }

        .category-item.active {
            background: #fdf6e3;
            border-color: #FECE6A;
            border-radius: 10px;
        }

        .category-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .category-name {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        .footer {
            background: #FECE6A;
            padding: 15px 0;
            margin-top: 30px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-left {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .footer-section {
            text-align: left;
        }

        .footer-section h4 {
            color: #333;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .footer-section p {
            color: #555;
            font-size: 12px;
            margin: 2px 0;
        }

        .social-icons {
            display: flex;
            gap: 10px;
        }

        .social-icon {
            width: 30px;
            height: 30px;
            background: #333;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 14px;
        }

        .copyright {
            text-align: center;
            color: #333;
            font-size: 12px;
            margin-top: 10px;
        }

        .login-section {
            margin-top: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }
        
        .login-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .login-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        
        .login-option {
            padding: 10px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .login-option:hover {
            background: #f0f0f0;
            border-color: #FECE6A;
        }

        /* Styles untuk halaman komunitas */
        .community-profiles {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-top: 20px;
        }

        .profile-card {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }

        .profile-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .profile-details {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .profile-hobby {
            font-size: 12px;
            color: #06206C;
            font-style: italic;
        }

        .connect-btn {
            background: #06206C;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .connect-btn:hover {
            background: #0d3bb1;
        }

        .hidden {
            display: none;
        }
        
        /* Tambahan untuk tata letak dengan Bootstrap */
        .main-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .content-wrapper {
            flex: 1;
            min-width: 300px;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 100%;
                margin-top: 20px;
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
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <!-- Konten Utama -->
            <div class="col-lg-8 col-md-7">
                <main class="main-content">
                    <!-- Halaman Utama -->
                    <div id="main-page">
                        <h1 class="page-title">Temukan <span>TEMAN</span> <p>berdasarkan hobi!</h1>
                        <p class="page-subtitle">
                            Kami menyediakan banyak komunitas untuk saling berbincang<br>
                            dengan pembahasan satu frekuensi.
                       </p>
                        <!-- Grid utama dengan keunggulan komunitas dan foto profil panjang -->
                        <div class="main-grid-container">
                            <div class="feature-card">
                                <div class="feature-title">Keunggulan komunitas</div>
                                <div class="feature-desc">
                                    • Akses lengkap ke komunitas<br>
                                    • Sistem keamanan terjamin<br>
                                    • Mudah digunakan<br>
                                    • Support 24/7
                                </div>
                                <div class="feature-stats">
                                    <span>🔒 Aman</span>
                                    <span>⚡ Cepat</span>
                                    <span>🎯 Tepat</span>
                                </div>
                            </div>
                            
                            <div class="profile-image" style="grid-row: span 2;">
                                <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="Komunitas Teman">
                            </div>
                            
                            <!-- Grid untuk konten bawah -->
                            <div class="bottom-content">
                                <div class="content-image">
                                    <img src="c:\Users\nka.raa\Pictures\Screenshots\Screenshot 2025-09-04 090021.png" alt="Aktivitas Komunitas">
                                </div>
                                
                                <div class="content-text">
                                    <div class="content-title">Lets <br> JOIN!!</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Halaman Komunitas (Akan ditampilkan ketika kategori dipilih) -->
                    <div id="community-page" class="hidden">
                        <h1 class="page-title" id="community-category-title">Komunitas</h1>
                        <p class="page-subtitle" id="community-category-desc">Temukan orang-orang dengan minat yang sama dan bangun hubungan yang bermakna.</p>
                        
                        <div class="community-profiles" id="community-profiles-container">
                            <!-- Profil akan diisi oleh JavaScript -->
                        </div>
                    </div>
                </main>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4 col-md-5">
                <aside class="sidebar">
                    <div class="sidebar-title">Kategori</div>
                    <div class="category-list">
                        <div class="category-item" data-category="musik">
                            <div class="category-icon" style="background: #ff6b6b;">🎵</div>
                            <div class="category-name">MUSIK</div>
                        </div>
                        <div class="category-item" data-category="kuliner">
                            <div class="category-icon" style="background: #4ecdc4;">🍽️</div>
                            <div class="category-name">KULINER</div>
                        </div>
                        <div class="category-item" data-category="travel">
                            <div class="category-icon" style="background: #45b7d1;">✈️</div>
                            <div class="category-name">TRAVEL</div>
                        </div>
                        <div class="category-item" data-category="game">
                            <div class="category-icon" style="background: #f39c12;">🎮</div>
                            <div class="category-name">GAME</div>
                        </div>
                        <div class="category-item" data-category="olahraga">
                            <div class="category-icon" style="background: #e74c3c;">⚽</div>
                            <div class="category-name">OLAHRAGA</div>
                        </div>
                        <div class="category-item" data-category="teknologi">
                            <div class="category-icon" style="background: #9b59b6;">💻</div>
                            <div class="category-name">TEKNOLOGI</div>
                        </div>
                        <div class="category-item" data-category="fotografi">
                            <div class="category-icon" style="background: #34495e;">📷</div>
                            <div class="category-name">FOTOGRAFI</div>
                        </div>
                        <div class="category-item" data-category="hiking">
                            <div class="category-icon" style="background: #27ae60;">🏔️</div>
                            <div class="category-name">HIKING</div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <footer class="footer">
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

    <script>
        // Data untuk setiap kategori
        const categoryData = {
            musik: {
                title: "Komunitas Musik",
                description: "Temukan orang-orang dengan selera musik yang sama. Diskusikan artis favorit, berbagi playlist, dan bahkan buat band bersama!",
                profiles: [
                    { name: "Jihan", age: "20 th", job: "mahasiswa", hobby: "Bermain gitar dan menyanyi" },
                    { name: "Rudi", age: "25 th", job: "musisi", hobby: "Bermain drum dan produksi musik" },
                    { name: "Sari", age: "22 th", job: "penyanyi", hobby: "Menulis lagu dan bernyanyi" },
                    { name: "Budi", age: "28 th", job: "produser", hobby: "Mixing dan mastering musik" }
                ]
            },
            kuliner: {
                title: "Komunitas Kuliner",
                description: "Bagikan resep, temukan tempat makan terbaik, dan bertemu dengan sesama food enthusiast.",
                profiles: [
                    { name: "Dewi", age: "26 th", job: "koki", hobby: "Memasak masakan tradisional" },
                    { name: "Anton", age: "30 th", job: "food vlogger", hobby: "Mencari kuliner street food" },
                    { name: "Maya", age: "24 th", job: "pastry chef", hobby: "Membuat kue dan dessert" },
                    { name: "Rina", age: "27 th", job: "food photographer", hobby: "Memotret makanan" }
                ]
            },
            travel: {
                title: "Komunitas Travel",
                description: "Bertukar cerita perjalanan, tips backpacking, dan rencanakan trip bersama ke destinasi impian.",
                profiles: [
                    { name: "Ahmad", age: "29 th", job: "travel blogger", hobby: "Backpacking ke tempat terpencil" },
                    { name: "Lina", age: "25 th", job: "tour guide", hobby: "Mengeksplor budaya lokal" },
                    { name: "Randy", age: "31 th", job: "fotografer", hobby: "Memotret landscape" },
                    { name: "Dina", age: "23 th", job: "mahasiswa", hobby: "Travelling dengan budget terbatas" }
                ]
            },
            game: {
                title: "Komunitas Game",
                description: "Temukan squad untuk bermain game, diskusikan strategi, dan ikuti turnamen bersama.",
                profiles: [
                    { name: "Rizky", age: "19 th", job: "esports player", hobby: "Bermain game kompetitif" },
                    { name: "Sinta", age: "22 th", job: "streamer", hobby: "Live streaming game" },
                    { name: "Fajar", age: "26 th", job: "game developer", hobby: "Membuat game indie" },
                    { name: "Nina", age: "24 th", job: "gamer", hobby: "Bermain RPG" }
                ]
            },
            olahraga: {
                title: "Komunitas Olahraga",
                description: "Temukan partner olahraga, bagikan tips kesehatan, dan ikuti event olahraga bersama.",
                profiles: [
                    { name: "Andi", age: "27 th", job: "personal trainer", hobby: "Fitness dan angkat beban" },
                    { name: "Rina", age: "24 th", job: "atlet", hobby: "Lari marathon" },
                    { name: "Budi", age: "29 th", job: "instruktur yoga", hobby: "Yoga dan meditasi" },
                    { name: "Dewi", age: "26 th", job: "guru olahraga", hobby: "Bulu tangkis" }
                ]
            },
            teknologi: {
                title: "Komunitas Teknologi",
                description: "Diskusikan perkembangan teknologi terbaru, berbagi pengetahuan, dan kembangkan skill bersama.",
                profiles: [
                    { name: "Rudi", age: "28 th", job: "software engineer", hobby: "Pemrograman dan AI" },
                    { name: "Sari", age: "25 th", job: "data scientist", hobby: "Analisis data dan machine learning" },
                    { name: "Budi", age: "30 th", job: "IT consultant", hobby: "Cloud computing dan cybersecurity" },
                    { name: "Dewi", age: "26 th", job: "UI/UX designer", hobby: "Desain interface dan pengalaman pengguna" }
                ]
            },
            fotografi: {
                title: "Komunitas Fotografi",
                description: "Bagikan karya fotografi, pelajari teknik baru, dan eksplorasi dunia melalui lensa kamera.",
                profiles: [
                    { name: "Ahmad", age: "32 th", job: "fotografer profesional", hobby: "Fotografi landscape dan alam" },
                    { name: "Lina", age: "27 th", job: "fotografer wedding", hobby: "Fotografi pernikahan dan portrait" },
                    { name: "Randy", age: "29 th", job: "fotojurnalis", hobby: "Fotografi dokumenter dan jurnalistik" },
                    { name: "Dina", age: "24 th", job: "fotografer travel", hobby: "Fotografi perjalanan dan budaya" }
                ]
            },
            hiking: {
                title: "Komunitas Hiking",
                description: "Jelajahi alam bersama, bagikan pengalaman pendakian, dan temukan jalur hiking terbaik.",
                profiles: [
                    { name: "Andi", age: "31 th", job: "pemandu wisata", hobby: "Pendakian gunung dan camping" },
                    { name: "Rina", age: "28 th", job: "pegiat alam", hobby: "Hiking dan fotografi alam" },
                    { name: "Budi", age: "33 th", job: "petualang", hobby: "Explorasi jalur hiking baru" },
                    { name: "Dewi", age: "26 th", job: "environmentalis", hobby: "Hiking dan konservasi alam" }
                ]
            }
        };

        // Fungsi untuk menampilkan halaman komunitas berdasarkan kategori
        function showCommunityPage(category) {
            // Sembunyikan halaman utama
            document.getElementById('main-page').classList.add('hidden');
            
            // Tampilkan halaman komunitas
            document.getElementById('community-page').classList.remove('hidden');
            
            // Update active state pada kategori
            const categoryItems = document.querySelectorAll('.category-item');
            categoryItems.forEach(item => item.classList.remove('active'));
            
            // Cari item yang sesuai dengan kategori
            const activeItem = Array.from(categoryItems).find(item => item.getAttribute('data-category') === category);
            if (activeItem) {
                activeItem.classList.add('active');
            }
            
            // Update konten berdasarkan kategori
            const data = categoryData[category] || {
                title: `Komunitas ${category.charAt(0).toUpperCase() + category.slice(1)}`,
                description: "Temukan dan bergabunglah dengan komunitas untuk saling berhubungan dengan orang-orang yang memiliki minat yang sama.",
                profiles: [
                    { name: "John", age: "25 th", job: "pekerja", hobby: "Hobi terkait kategori" },
                    { name: "Jane", age: "23 th", job: "profesional", hobby: "Hobi terkait kategori" },
                    { name: "Alex", age: "27 th", job: "freelancer", hobby: "Hobi terkait kategori" },
                    { name: "Sarah", age: "24 th", job: "desainer", hobby: "Hobi terkait kategori" }
                ]
            };
            
            // Update judul dan deskripsi
            document.getElementById('community-category-title').textContent = data.title;
            document.getElementById('community-category-desc').textContent = data.description;
            
            // Update daftar profil
            const profilesContainer = document.getElementById('community-profiles-container');
            profilesContainer.innerHTML = '';
            
            data.profiles.forEach(profile => {
                const profileCard = document.createElement('div');
                profileCard.className = 'profile-card';
                profileCard.innerHTML = `
                    <div class="profile-avatar">👤</div>
                    <div class="profile-info">
                        <div class="profile-name">${profile.name}</div>
                        <div class="profile-details">${profile.age}, ${profile.job}</div>
                        <div class="profile-hobby">${profile.hobby}</div>
                    </div>
                    <button class="connect-btn">Connect</button>
                `;
                profilesContainer.appendChild(profileCard);
            });
        }

        // Fungsi untuk kembali ke halaman utama
        function showMainPage() {
            document.getElementById('community-page').classList.add('hidden');
            document.getElementById('main-page').classList.remove('hidden');
            
            // Remove active state dari semua kategori
            const categoryItems = document.querySelectorAll('.category-item');
            categoryItems.forEach(item => item.classList.remove('active'));
        }

        // Menambahkan event listener ke semua kategori
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                showCommunityPage(category);
            });
        });

        // Event listener untuk tombol connect
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('connect-btn')) {
                const btn = e.target;
                if (btn.textContent === 'Connect') {
                    btn.textContent = 'Connected';
                    btn.style.background = '#27ae60';
                } else {
                    btn.textContent = 'Connect';
                    btn.style.background = '#06206C';
                }
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"></script>
</body>
</html>