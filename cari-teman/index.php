<?php
session_start();
require_once '../auth/config.php';

// Get search parameters
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$location_filter = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : '';

// Build query to get available friends
$query = "SELECT u.id, u.name, u.username, u.profile_photo, u.bio, u.location as user_location, 
                 u.hobby, fp.hourly_rate, fp.location as friend_location, fp.available
          FROM users u 
          INNER JOIN friend_profiles fp ON u.id = fp.user_id 
          WHERE u.role = 'Friend'";

// Add search filters
if (!empty($search_query)) {
    $query .= " AND (u.name LIKE '%$search_query%' 
                OR u.hobby LIKE '%$search_query%' 
                -- OR fp.tags LIKE '%$search_query%'
                OR u.bio LIKE '%$search_query%')";
}

if (!empty($location_filter)) {
    $query .= " AND (fp.location LIKE '%$location_filter%' OR u.location LIKE '%$location_filter%')";
}

$query .= " ORDER BY u.name ASC";

$result = mysqli_query($conn, $query);
$friends = [];
if ($result) {
    $friends = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

$user = null; // default null

if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
    $user_id = intval($_SESSION['user']['id']);
    $q       = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
    $user    = mysqli_fetch_assoc($q);
}

if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
$user_session = $_SESSION['user'];
$user_id = $user_session['id'];
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cari Teman - Butuh Teman</title>
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
     * {
      font-family: "Josefin Sans", sans-serif;
    }

    /* Navbar */
    .navbar {
      background-color: #FECE6A !important;
    }
    .nav-link {
      color: #315DB4;
      font-weight: 500;
    }
    .actived {
      color: #06206C !important;
      font-weight: 600;
    }

    /* Section Cari Teman */
    .cari-teman {
      padding: 50px 20px;
      text-align: center;
    }
    .cari-teman h1 {
      font-weight: 700;
      color: #06206C;
    }
    .cari-teman p {
      color: #555;
    }
    .search-bar {
      margin: 25px auto;
      max-width: 500px;
      display: flex;
      border: 1px solid #ccc;
      border-radius: 25px;
      overflow: hidden;
    }
    .search-bar input {
      flex: 1;
      padding: 10px 15px;
      border: none;
      outline: none;
    }
    .search-bar button {
      background: #FECE6A;
      border: none;
      padding: 10px 20px;
      cursor: pointer;
      font-weight: 600;
      color: #06206C;
    }

    /* Cards */
    .card {
      border: none;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s;
      height: 100%;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .card img {
      height: 220px;
      object-fit: cover;
    }
    .card-body h5 {
      font-weight: 600;
      color: #06206C;
    }
    .tag {
      display: inline-block;
      background: #FECE6A;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 12px;
      margin-top: 8px;
      color: #06206C;
      font-weight: 600;
      margin-right: 5px;
      margin-bottom: 5px;
    }
    .btn-primary {
      background: #FECE6A;
      border: none;
      color: #06206C;
      font-weight: 600;
    }
    .btn-primary:hover {
      background: #f1b93e;
    }

    /* Price Tag */
    .price-tag {
      background: #FECE6A;
      color: #06206C;
      padding: 4px 12px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 600;
      display: inline-block;
      margin-top: 8px;
    }

    /* Filter Section */
    .filter-section {
      background: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      border: 1px solid #e0e0e0;
    }

    .no-friends {
      text-align: center;
      padding: 60px 20px;
      color: #666;
    }
    .no-friends i {
      font-size: 4rem;
      color: #ddd;
      margin-bottom: 20px;
    }

    /* Footer */
    footer {
      background: #FECE6A;
      padding: 40px 20px;
      margin-top: 80px;
    }
    footer h4 {
      font-weight: 600;
      color: #06206C;
    }
    footer p, footer a {
      font-size: 14px;
      color: #315DB4;
      text-decoration: none;
      margin-bottom: 8px;
    }
    footer a:hover {
      text-decoration: underline;
    }
    .copyright {
      text-align: center;
      background: #f1b93e;
      padding: 10px;
      font-size: 13px;
      color: #06206C;
    }

    /* Additional Styles */
    .card-text {
      color: #555;
      margin-bottom: 8px;
      font-size: 0.9rem;
    }

    .card-footer {
      background: white;
      border-top: 1px solid #f0f0f0;
      padding: 1rem 1.5rem;
    }

    .tags {
      margin-top: 10px;
    }
  </style>
</head>
<body>
  
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="../../index.php">
            <img src="assets/img/logo butuh teman.png" alt="" width="50">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link " aria-current="page" href="../index.php">BERANDA</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="../index.php#tentang">TENTANG</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link actived" aria-current="page" href="../index.php#cari-teman">CARI TEMAN</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="../index.php#komunitas">KOMUNITAS</a>
                </li>

                <?php if (isset($_SESSION['user'])): ?>
                <!-- Jika user login -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo ! empty($user_session['profile_photo']) ? '../auth/' . $user_session['profile_photo'] : 'assets/img/user.jpg' ?>"
                        alt="profile"
                        class="rounded-circle me-2"
                        width="35" height="35"
                        style="object-fit: cover;">
                        <?php echo htmlspecialchars($user_session['name']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <?php
                        // Determine profile link based on user role
                        $profile_link = '../auth/profil_user.php'; // default for client
                        if ($user_session['role'] === 'Friend') {
                            $profile_link = '../auth/profil_teman.php';
                        } elseif ($user_session['role'] === 'Admin') {
                            $profile_link = '../admin/index.php'; // or whatever admin profile page
                        }
                        ?>
                        <li><a class="dropdown-item" href="<?php echo $profile_link; ?>">Profil Saya</a></li>
                        <!-- <li><a class="dropdown-item" href="settings.php">Pengaturan</a></li> -->
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="auth/logout.php">Logout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <!-- Jika belum login -->
                <li class="nav-item">
                    <a href="../auth/login.php" class="btn btn-login ms-3">Login</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
  
  <!-- Section Cari Teman -->
  <section id="cari-teman" class="cari-teman">
    <div class="container text-center">
      <h1>CARI TEMAN</h1>
      <p>Temukan teman yang kalian cari disini!</p>
      <form method="GET" action="" class="search-bar">
        <input type="text" name="search" id="searchInput" 
               placeholder="Cari berdasarkan nama, hobby, atau tags..." 
               value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Cari</button>
      </form>
    </div>
  </section>
  
  <!-- Filters -->
  <div class="container">
    <div class="filter-section">
      <form method="GET" action="" class="row g-3 align-items-center">
        <div class="col-md-10">
          <label for="location" class="form-label">Filter Lokasi:</label>
          <input type="text" name="location" id="location" class="form-control" 
                 placeholder="Masukkan lokasi..." value="<?php echo htmlspecialchars($location_filter); ?>">
        </div>
        <div class="col-md-2">
          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="?" class="btn btn-outline-secondary">Reset</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Grid Cards -->
  <div class="container pb-5">
    <?php if (empty($friends)): ?>
      <div class="no-friends">
        <i class="bi bi-people"></i>
        <h3>Tidak ada teman yang ditemukan</h3>
        <p><?php echo !empty($search_query) ? 'Coba gunakan kata kunci lain atau hapus filter.' : 'Belum ada teman yang tersedia saat ini.'; ?></p>
      </div>
    <?php else: ?>
      <div class="row g-4" id="temanList">
        <?php foreach ($friends as $friend): 
          // Determine location (prefer friend_profile location)
          $location = !empty($friend['friend_location']) ? $friend['friend_location'] : $friend['user_location'];
          $location = !empty($location) ? $location : 'Lokasi tidak tersedia';
          
          // Get profile photo
          $profile_photo = !empty($friend['profile_photo']) ? '../auth/' . $friend['profile_photo'] : '../assets/img/user.jpg';
          
          // Process tags
          $tags = [];
          if (!empty($friend['tags'])) {
              $tags = explode(',', $friend['tags']);
          }
          if (!empty($friend['hobby'])) {
              $hobbies = explode(',', $friend['hobby']);
              $tags = array_merge($tags, $hobbies);
          }
          $tags = array_slice(array_unique($tags), 0, 5); // Limit to 5 tags
        ?>
          <div class="col-md-6 col-lg-4 col-xl-3 teman-card">
            <div class="card">
              <img src="<?php echo htmlspecialchars($profile_photo); ?>" 
                   class="card-img-top" 
                   alt="<?php echo htmlspecialchars($friend['name']); ?>"
                   onerror="this.src='../assets/img/user.jpg'">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($friend['name']); ?></h5>
                <p class="card-text"><?php echo !empty($friend['bio']) ? htmlspecialchars($friend['bio']) : 'Teman yang siap menemani aktivitas Anda.'; ?></p>
                <p class="card-text">
                  <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($location); ?>
                </p>
                <p class="card-text">
                  <span class="price-tag">
                    <i class="bi bi-currency-dollar"></i> 
                    Rp <?php echo number_format($friend['hourly_rate'], 0, ',', '.'); ?>/jam
                  </span>
                </p>
                
                <?php if (!empty($tags)): ?>
                  <div class="tags">
                    <?php foreach ($tags as $tag): ?>
                      <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
              <div class="card-footer d-flex justify-content-between">
                <?php if (isset($_SESSION['user'])): ?>
                  <button class="btn btn-primary" 
                          onclick="window.location.href='../auth/profil_teman.php?id=<?php echo $friend['id']; ?>'">
                    <i class="bi bi-calendar-check"></i> Booking
                  </button>
                  <button class="btn btn-primary" 
                          onclick="window.location.href='../chat.php?user_id=<?php echo $friend['id']; ?>'">
                    <i class="bi bi-chat-dots"></i> Chat
                  </button>
                <?php else: ?>
                  <button class="btn btn-primary" onclick="window.location.href='../auth/login.php'">
                    <i class="bi bi-calendar-check"></i> Booking
                  </button>
                  <button class="btn btn-primary" onclick="window.location.href='../auth/login.php'">
                    <i class="bi bi-chat-dots"></i> Chat
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  
  <!-- Footer -->
  <footer class="mt-5">
    <div class="container">
      <div class="row gy-4">
        <div class="col-md-3">
          <h4>BUTUH TEMAN</h4>
          <p>Website untuk mencari teman baru dan membangun komunitas.</p>
        </div>
        <div class="col-md-3">
          <h4>Navigasi</h4>
          <p><a href="../index.php">Home</a></p>
          <p><a href="../index.php#tentang">Tentang</a></p>
          <p><a href="../index.php#cari-teman">Cari Teman</a></p>
          <p><a href="../index.php#komunitas">Komunitas</a></p>
        </div>
        <div class="col-md-3">
          <h4>Kontak Kami</h4>
          <p>Cirebon, Indonesia</p>
          <p>+62 853 6002 6250</p>
          <p>butuhteman@gmail.com</p>
        </div>
        <div class="col-md-3">
          <h4>Ikuti Kami</h4>
          <p>Instagram | Facebook | Twitter</p>
        </div>
      </div>
    </div>
  </footer>
  <div class="copyright">
    © 2025 BUTUH TEMAN | All Rights Reserved
  </div>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Simple search functionality
    function searchTeman() {
        const searchInput = document.getElementById('searchInput');
        const searchTerm = searchInput.value.trim();
        
        if (searchTerm) {
            window.location.href = '?search=' + encodeURIComponent(searchTerm);
        } else {
            window.location.href = '?';
        }
    }
    
    // Enter key support for search
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchTeman();
        }
    });
    
    // Client-side filtering (optional enhancement)
    function filterTeman() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.teman-card');
        
        cards.forEach(card => {
            const nama = card.getAttribute('data-nama')?.toLowerCase() || '';
            const text = card.textContent.toLowerCase();
            
            if (nama.includes(searchTerm) || text.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
  </script>
</body>
</html>