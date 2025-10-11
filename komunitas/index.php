<?php
session_start();
require_once '../auth/config.php';

// Check if user is logged in
$user_session = null;
$current_user_id = null;
if (isset($_SESSION['user'])) {
    $user_session = $_SESSION['user'];
    $current_user_id = $user_session['id'];
}

// Handle join community
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_community'])) {
    // Check if user is logged in
    if (!isset($current_user_id)) {
        $_SESSION['join_error'] = "Anda harus login terlebih dahulu untuk bergabung dengan komunitas.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    $community_id = intval($_POST['community_id']);
    
    // Check if already a member
    $check_membership = "SELECT id FROM community_memberships WHERE community_id = ? AND user_id = ?";
    if ($stmt = mysqli_prepare($conn, $check_membership)) {
        mysqli_stmt_bind_param($stmt, "ii", $community_id, $current_user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            // Join community
            $join_query = "INSERT INTO community_memberships (community_id, user_id, role) VALUES (?, ?, 'member')";
            if ($stmt = mysqli_prepare($conn, $join_query)) {
                mysqli_stmt_bind_param($stmt, "ii", $community_id, $current_user_id);
                mysqli_stmt_execute($stmt);
                $_SESSION['join_success'] = "Berhasil bergabung dengan komunitas!";
                
                // Update the communities array to reflect the new membership
                foreach ($communities as &$community) {
                    if ($community['id'] == $community_id) {
                        $community['is_member'] = 1;
                        $community['member_count'] += 1;
                        break;
                    }
                }
            }
        } else {
            $_SESSION['join_error'] = "Anda sudah bergabung dengan komunitas ini.";
        }
        mysqli_stmt_close($stmt);
    }
    
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get all communities from database
$communities_query = "SELECT c.*, u.name as creator_name, 
                     COUNT(cm.id) as member_count,
                     (SELECT COUNT(*) FROM community_memberships WHERE community_id = c.id AND user_id = ?) as is_member
                     FROM communities c 
                     LEFT JOIN users u ON c.created_by = u.id 
                     LEFT JOIN community_memberships cm ON c.id = cm.community_id 
                     GROUP BY c.id 
                     ORDER BY c.name ASC";
$communities = [];

if ($stmt = mysqli_prepare($conn, $communities_query)) {
    $user_id_param = isset($current_user_id) ? $current_user_id : 0;
    mysqli_stmt_bind_param($stmt, "i", $user_id_param);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $communities = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

// Get community members for a specific community
function getCommunityMembers($conn, $community_id) {
    $members_query = "SELECT u.id, u.name, u.profile_photo, u.hobby, u.bio, cm.role, cm.joined_at 
                     FROM community_memberships cm 
                     JOIN users u ON cm.user_id = u.id 
                     WHERE cm.community_id = ? 
                     ORDER BY 
                         CASE cm.role 
                             WHEN 'owner' THEN 1 
                             WHEN 'admin' THEN 2 
                             ELSE 3 
                         END,
                         cm.joined_at ASC 
                     LIMIT 8";
    $members = [];
    
    if ($stmt = mysqli_prepare($conn, $members_query)) {
        mysqli_stmt_bind_param($stmt, "i", $community_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $members = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
    }
    
    return $members;
}

// Get community by ID
function getCommunityById($conn, $community_id, $current_user_id = null) {
    $user_id_param = $current_user_id ? $current_user_id : 0;
    
    $query = "SELECT c.*, u.name as creator_name, 
              COUNT(cm.id) as member_count,
              (SELECT COUNT(*) FROM community_memberships WHERE community_id = c.id AND user_id = ?) as is_member
              FROM communities c 
              LEFT JOIN users u ON c.created_by = u.id 
              LEFT JOIN community_memberships cm ON c.id = cm.community_id 
              WHERE c.id = ? 
              GROUP BY c.id";
    
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "ii", $user_id_param, $community_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $community = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $community;
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komunitas - Butuh Teman</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        .member-count {
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
        }

        .community-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* All your existing CSS styles remain the same */
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
            z-index: 1020;
        }

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
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            height: 550px;
            position: sticky;
            top: 90px;
            border: 3px solid #2c4aa5;
            margin-left: 35px;
            z-index: 1010;
        }

        #main-page {
            margin-top: -40px;
        }

        .page-title {
            color: #06206C;
            font-size: 30px;
            font-weight: bold;
            margin-top: 20px;
        }

        .page-title span {
            color: #294ebe;
        }

        .page-subtitle {
            color: #1c3c75;
            font-size: 16px;
            margin-bottom: 18px;
            font-weight: 500;
            line-height: 1.3;
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
            overflow: hidden;
        }

        .category-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .category-name {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

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
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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

        .community-header {
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .community-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-join {
            background: #FECE6A;
            color: #06206C;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-join:hover {
            background: #f1b93e;
            transform: translateY(-2px);
        }

        .btn-chat {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-chat:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-joined {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            cursor: not-allowed;
        }

        .hidden {
            display: none;
        }

        /* Footer styles remain the same */
        .footer {
            background: #FECE6A;
            padding: 60px 40px 30px;
            color: #0D47A1;
            margin-top: 50px;
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
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <img src="../assets/img/logo butuh teman.png" alt="" width="50">
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
                        <a class="nav-link" aria-current="page" href="../cari-teman/index.php">CARI TEMAN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link actived" aria-current="page" href="index.php">KOMUNITAS</a>
                    </li>

                    <?php if (isset($_SESSION['user'])): ?>
                    <!-- Jika user login -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo !empty($user_session['profile_photo']) ? '../auth/' . htmlspecialchars($user_session['profile_photo']) : '../assets/img/user.jpg' ?>"
                            alt="profile"
                            class="rounded-circle me-2"
                            width="35" height="35"
                            style="object-fit: cover;">
                            <?php echo htmlspecialchars($user_session['name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <?php
                            $profile_link = '../auth/profil_user.php';
                            if ($user_session['role'] === 'Friend') {
                                $profile_link = '../auth/profil_teman.php';
                            } elseif ($user_session['role'] === 'Admin') {
                                $profile_link = '../admin/index.php';
                            }
                            ?>
                            <li><a class="dropdown-item" href="<?php echo $profile_link; ?>">Profil Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <!-- Jika belum login -->
                    <li class="nav-item">
                        <a href="../auth/login.php" class="btn btn-primary ms-3">Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php 
        // Show success/error messages from session
        if (isset($_SESSION['join_success'])): 
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['join_success'] . 
                 '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['join_success']);
        elseif (isset($_SESSION['join_error'])): 
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">' . $_SESSION['join_error'] . 
                 '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['join_error']);
        endif; 
        ?>

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
                                    <img src="../assets/img/kom.webp" alt="Aktivitas Komunitas">
                                </div>
                                
                                <div class="content-text">
                                    <div class="content-title">Lets <br> JOIN!!</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Halaman Komunitas (Akan ditampilkan ketika kategori dipilih) -->
                    <div id="community-page" class="hidden">
                        <div class="community-header" id="community-header">
                            <h1 class="page-title" id="community-category-title">Komunitas</h1>
                            <p class="page-subtitle" id="community-category-desc">Temukan orang-orang dengan minat yang sama dan bangun hubungan yang bermakna.</p>
                            <div class="community-actions" id="community-actions">
                                <!-- Join and Chat buttons will be added here by JavaScript -->
                            </div>
                        </div>
                        
                        <div class="community-profiles" id="community-profiles-container">
                            <!-- Profil akan diisi oleh JavaScript -->
                        </div>
                    </div>
                </main>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4 col-md-5">
                <aside class="sidebar">
                    <div class="sidebar-title">Daftar Komunitas</div>
                    <div class="category-list">
                        <?php foreach ($communities as $community): ?>
                            <div class="category-item" data-community-id="<?php echo $community['id']; ?>">
                                <div class="category-icon">
                                    <?php if (!empty($community['photo'])): ?>
                                        <img src="<?php echo '../admin/' .   htmlspecialchars($community['photo']); ?>" alt="<?php echo htmlspecialchars($community['name']); ?>">
                                    <?php else: ?>
                                        <i class="bi bi-people-fill"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="category-name"><?php echo htmlspecialchars($community['name']); ?></div>
                            </div>
                        <?php endforeach; ?>
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
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../index.php#tentang">Tentang</a></li>
                    <li><a href="../cari-teman/index.php">Cari Teman</a></li>
                    <li><a href="index.php">Komunitas</a></li>
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

    <!-- Join Community Form (Hidden) -->
    <form id="joinCommunityForm" method="POST" style="display: none;">
        <input type="hidden" name="join_community" value="1">
        <input type="hidden" name="community_id" id="joinCommunityId">
    </form>

<script>
    // Function to show community page
    function showCommunityPage(communityId) {
        console.log('Loading community:', communityId);
        
        // Show loading state
        document.getElementById('community-page').classList.remove('hidden');
        document.getElementById('main-page').classList.add('hidden');
        
        // Update active state
        const categoryItems = document.querySelectorAll('.category-item');
        categoryItems.forEach(item => item.classList.remove('active'));
        
        const activeItem = document.querySelector(`[data-community-id="${communityId}"]`);
        if (activeItem) {
            activeItem.classList.add('active');
        }
        
        // Show loading message
        const profilesContainer = document.getElementById('community-profiles-container');
        profilesContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat data komunitas...</p></div>';
        
        // Fetch community data via AJAX
        fetch(`get_community.php?id=${communityId}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                if (data.success) {
                    updateCommunityPage(data.community, data.members);
                } else {
                    throw new Error(data.message || 'Gagal memuat data komunitas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                profilesContainer.innerHTML = '<div class="alert alert-danger text-center">Error: ' + error.message + '</div>';
            });
    }

    // Function to update community page with data
    function updateCommunityPage(community, members) {
        console.log('Updating page with:', community, members);
        
        // Update header
        document.getElementById('community-category-title').textContent = community.name;
        document.getElementById('community-category-desc').textContent = community.description || 'Temukan orang-orang dengan minat yang sama dan bangun hubungan yang bermakna.';
        
        // Update actions
        const actionsContainer = document.getElementById('community-actions');
        actionsContainer.innerHTML = '';
        
        // Add member count
        const memberCount = document.createElement('div');
        memberCount.className = 'member-count';
        memberCount.style.cssText = 'color: #06206C; font-weight: 600; display: flex; align-items: center; gap: 8px; font-size: 16px;';
        memberCount.innerHTML = `<i class="bi bi-people-fill"></i> ${community.member_count || 0} Anggota`;
        actionsContainer.appendChild(memberCount);
        
        // Add buttons
        const joinButton = document.createElement('button');
        joinButton.type = 'button';
        joinButton.className = community.is_member ? 'btn-joined' : 'btn-join';
        joinButton.innerHTML = community.is_member ? 
            '<i class="bi bi-check-circle"></i> Sudah Bergabung' : 
            '<i class="bi bi-plus-circle"></i> Bergabung';
        
        if (!community.is_member) {
            joinButton.onclick = function() {
                joinCommunity(community.id);
            };
        }
        actionsContainer.appendChild(joinButton);
        
        const chatButton = document.createElement('button');
        chatButton.type = 'button';
        chatButton.className = 'btn-chat';
        chatButton.innerHTML = '<i class="bi bi-chat-dots"></i> Chat Komunitas';
        chatButton.onclick = function() {
            window.location.href = `community_chat.php?community_id=${community.id}`;
        };
        actionsContainer.appendChild(chatButton);
        
        // Update members list
        const profilesContainer = document.getElementById('community-profiles-container');
        profilesContainer.innerHTML = '';
        
        if (!members || members.length === 0) {
            profilesContainer.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">Belum ada anggota di komunitas ini.</p>';
        } else {
            members.forEach(member => {
                const profileCard = document.createElement('div');
                profileCard.className = 'profile-card';
                
                const profilePhoto = member.profile_photo ? 
                    `../auth/${member.profile_photo}` : 
                    '../assets/img/user.jpg';
                
                const roleIcon = member.role === 'owner' ? '👑' : 
                               member.role === 'admin' ? '⭐' : '';
                
                const roleText = member.role === 'owner' ? 'Pendiri' : 
                               member.role === 'admin' ? 'Admin' : 'Anggota';
                
                const joinDate = new Date(member.joined_at).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                
                profileCard.innerHTML = `
                    <div class="profile-avatar">
                        <img src="../auth/${profilePhoto}" alt="${member.name}" onerror="this.src='../assets/img/user.jpg'">
                    </div>
                    <div class="profile-info">
                        <div class="profile-name">${member.name} ${roleIcon}</div>
                        <div class="profile-details">${roleText} • Bergabung ${joinDate}</div>
                        <div class="profile-hobby">${member.hobby || 'Tidak ada hobi'}</div>
                    </div>
                `;
                profilesContainer.appendChild(profileCard);
            });
        }
    }

    // Function to join community
    function joinCommunity(communityId) {
        console.log('Joining community:', communityId);
        
        // Show confirmation
        if (!confirm('Apakah Anda yakin ingin bergabung dengan komunitas ini?')) {
            return;
        }
        
        // Submit the form
        document.getElementById('joinCommunityId').value = communityId;
        document.getElementById('joinCommunityForm').submit();
    }

    // Add event listeners to community items
    document.addEventListener('DOMContentLoaded', function() {
        const categoryItems = document.querySelectorAll('.category-item');
        console.log('Found category items:', categoryItems.length);
        
        categoryItems.forEach(item => {
            item.addEventListener('click', function() {
                const communityId = this.getAttribute('data-community-id');
                console.log('Category clicked:', communityId);
                showCommunityPage(communityId);
            });
        });
    });

    // Function to show main page
    function showMainPage() {
        document.getElementById('community-page').classList.add('hidden');
        document.getElementById('main-page').classList.remove('hidden');
        
        const categoryItems = document.querySelectorAll('.category-item');
        categoryItems.forEach(item => item.classList.remove('active'));
    }
</script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>