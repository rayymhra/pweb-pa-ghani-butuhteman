<?php
session_start();
require_once 'config.php';

// Get the profile ID from URL parameter, default to current user if not specified
$profile_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// If no ID specified and user is logged in, use their own ID
if ($profile_id === 0 && isset($_SESSION['user'])) {
    $profile_id = $_SESSION['user']['id'];
}

if ($profile_id === 0) {
    // If no ID and no logged in user, redirect to login or show error
    header("Location: login.php");
    exit;
}

// Query untuk mengambil data user dan friend profile
$query = "SELECT u.*, fp.hourly_rate, fp.location as friend_location, fp.available 
          FROM users u 
          LEFT JOIN friend_profiles fp ON u.id = fp.user_id 
          WHERE u.id = ? AND u.role = 'Friend'";

if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $profile_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    echo "<div class='alert alert-danger mt-3'>Query gagal dipersiapkan.</div>";
    exit;
}

if (!$user) {
    echo "<div class='alert alert-danger mt-3'>Profil teman tidak ditemukan.</div>";
    exit;
}

// Check if current viewer is the profile owner
$is_owner = false;
$user_session = null;

if (isset($_SESSION['user'])) {
    $user_session = $_SESSION['user'];
    $is_owner = ($user_session['id'] == $profile_id);
    
    // Update session dengan data terbaru jika ini adalah pemilik profil
    if ($is_owner) {
        $current_user_query = "SELECT * FROM users WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $current_user_query)) {
            mysqli_stmt_bind_param($stmt, "i", $profile_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $current_user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            
            $_SESSION['user'] = array_merge($_SESSION['user'], $current_user);
            $user_session = $_SESSION['user'];
        }
    }
}

// Handle booking form submission
if (!$is_owner && isset($_SESSION['user']) && isset($_POST['submit_booking'])) {
    $client_id = $user_session['id'];
    $friend_id = $profile_id;
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $booking_time = mysqli_real_escape_string($conn, $_POST['booking_time']);
    $duration = intval($_POST['duration']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    
    // Calculate start and end datetime
    $start_datetime = $booking_date . ' ' . $booking_time . ':00';
    $end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime . ' + ' . $duration . ' hours'));
    
    // Calculate total price
    $total_price = $user['hourly_rate'] * $duration;
    
    // Insert booking into database
    $insert_query = "INSERT INTO bookings (client_id, friend_id, start_datetime, end_datetime, total_price, status) 
                     VALUES (?, ?, ?, ?, ?, 'pending')";
    
    if ($stmt = mysqli_prepare($conn, $insert_query)) {
        mysqli_stmt_bind_param($stmt, "iissd", $client_id, $friend_id, $start_datetime, $end_datetime, $total_price);
        
        if (mysqli_stmt_execute($stmt)) {
            $booking_success = true;
            $booking_message = "Booking berhasil dibuat! Menunggu konfirmasi dari " . htmlspecialchars($user['name']);
        } else {
            $booking_error = "Gagal membuat booking. Silakan coba lagi.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $booking_error = "Terjadi kesalahan sistem. Silakan coba lagi.";
    }
}

// Get booking history for profile owner
$bookings = [];
if ($is_owner) {
    $bookings_query = "SELECT b.*, u.name as client_name, u.profile_photo as client_photo 
                       FROM bookings b 
                       JOIN users u ON b.client_id = u.id 
                       WHERE b.friend_id = ? 
                       ORDER BY b.created_at DESC 
                       LIMIT 10";
    
    if ($stmt = mysqli_prepare($conn, $bookings_query)) {
        mysqli_stmt_bind_param($stmt, "i", $profile_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Profil Teman - <?php echo htmlspecialchars($user['name']); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600&display=swap');
* { font-family: "Josefin Sans", sans-serif; }
body { background-color: #f8f9fa; }
.navbar { background-color: #FECE6A !important; }
.nav-link { color: #315DB4; font-weight: 500; }
.nav-link.active { color: #06206C !important; font-weight: 600; }
.profile-card { border: 1px solid #f2e6c9; border-radius: 12px; background-color: #fffdf7; padding: 1.5rem; margin-top: 1rem; }
.profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-right: 1rem; border: 3px solid #FECE6A; }
.profile-name { font-size: 1.4rem; font-weight: 600; }
.rating i { color: #FFD700; }
.btn-custom {
    padding: 16px 32px;
    font-weight: 600;
    background: #FFED8A;
    color: #6B5800;
    border: none;
    border-radius: 50px;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    font-family: 'Inter', sans-serif;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: pointer;
    font-size: 16px;
}

.btn-custom:hover {
    background: #FFE05A;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.btn-custom:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.btn-simpan { background: #FECE6A; color: #333; }
.btn-booking { background: #315DB4; color: #fff; }
.btn-edit {
    padding: 8px 16px;
    font-weight: 500;
    background: transparent;
    color: #4a5568;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    transition: all 0.2s ease;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    font-size: 14px;
    margin-right: 8px;
}

.btn-edit:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
}

.btn-edit i {
    margin-right: 4px;
}
.box { background: #fff; border-radius: 12px; padding: 1rem 1.2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 1rem; }
.tag { background-color: #FECE6A; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 500; margin: 3px; display: inline-block; color: #333; }
.photo-grid { width: 100%; height: 250px; object-fit: cover; }
.status-available { color: #28a745; font-weight: 600; }
.status-unavailable { color: #dc3545; font-weight: 600; }
.edit-buttons { margin-top: 10px; }
.booking-item { border-left: 4px solid #315DB4; }
.booking-status-pending { color: #ffc107; font-weight: 600; }
.booking-status-accepted { color: #28a745; font-weight: 600; }
.booking-status-rejected { color: #dc3545; font-weight: 600; }
.booking-status-completed { color: #6c757d; font-weight: 600; }
.client-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
</style>
</head>
<body>

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
                        <img src="<?php echo !empty($user_session['profile_photo']) ? htmlspecialchars($user_session['profile_photo']) : 'assets/img/default-user.png' ?>"
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
                            $profile_link = 'admin/index.php';
                        }
                        ?>
                        <li><a class="dropdown-item" href="<?php echo $profile_link; ?>">Profil Saya</a></li>
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

<div class="container">
  <?php if ($is_owner && isset($_GET['joined'])): ?>
    <div class="alert alert-success mt-3">
        🎉 Selamat, profil teman kamu sudah aktif! Sekarang client bisa menemukanmu.
    </div>
  <?php endif; ?>

  <?php if (isset($booking_success) && $booking_success): ?>
    <div class="alert alert-success mt-3">
        ✅ <?php echo $booking_message; ?>
    </div>
  <?php elseif (isset($booking_error)): ?>
    <div class="alert alert-danger mt-3">
        ❌ <?php echo $booking_error; ?>
    </div>
  <?php endif; ?>

  <!-- PROFILE CARD -->
  <div class="profile-card shadow-sm">
    <div class="d-flex justify-content-between align-items-start">
      <div class="d-flex">
        <img
          src="<?php echo !empty($user['profile_photo']) ? htmlspecialchars($user['profile_photo']) : "assets/img/user.jpg" ?>"
          class="profile-img"
          alt="Profile"
        >
        <div>
          <div class="profile-name"><?php echo htmlspecialchars($user['name']); ?></div>
          <div class="rating mb-1">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <small>(5/5)</small>
          </div>
          <div class="text-muted small">
            <i class="bi bi-geo-alt"></i> 
            <?php 
            echo !empty($user['friend_location']) ? htmlspecialchars($user['friend_location']) : 
                 (!empty($user['location']) ? htmlspecialchars($user['location']) : 'Lokasi tidak tersedia'); 
            ?> 
            <br>
            <i class="bi bi-gender-ambiguous"></i> 
            <?php 
            $gender_map = [
                'Pria' => 'Laki-laki',
                'Wanita' => 'Perempuan',
                'Lainnya' => 'Lainnya'
            ];
            echo isset($gender_map[$user['gender']]) ? $gender_map[$user['gender']] : 'Tidak disebutkan';
            ?>
            <br>
            <i class="bi bi-clock"></i> 
            <span class="<?php echo $user['available'] ? 'status-available' : 'status-unavailable'; ?>">
              <?php echo $user['available'] ? 'Tersedia' : 'Tidak Tersedia'; ?>
            </span>
            <?php if (!empty($user['hourly_rate'])): ?>
              <br>
              <i class="bi bi-currency-dollar"></i> 
              Rp <?php echo number_format($user['hourly_rate'], 0, ',', '.'); ?>/jam
            <?php endif; ?>
          </div>
          
          <!-- Edit buttons for profile owner -->
          <?php if ($is_owner): ?>
          <div class="edit-buttons">
            <button class="btn btn-edit me-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
              <i class="bi bi-pencil-square"></i> Edit Profil
            </button>
            <button class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editAboutModal">
              <i class="bi bi-journal-text"></i> Edit About
            </button>
            <a href="../chat_list.php" class="btn btn-edit">List Chat</a>
          </div>
          <?php endif; ?>
          
        </div>
        
      </div>
      
      <div>
        <?php if ($is_owner): ?>
          <!-- Buttons for profile owner -->
          <button class="btn btn-success btn-custom me-2" data-bs-toggle="modal" data-bs-target="#uploadPhotosModal">
            <i class="bi bi-cloud-upload"></i> Upload Foto
          </button>
          
        <?php else: ?>
          <!-- Buttons for other users -->
          <button class="btn btn-success btn-custom me-2" onclick="window.location.href='../chat.php?user_id=<?php echo $user['id']; ?>'">
    <i class="bi bi-chat-dots-fill"></i> Chat
</button>
          <?php if ($user['available']): ?>
            <button class="btn btn-booking btn-custom" data-bs-toggle="modal" data-bs-target="#bookingModal">
              <i class="bi bi-calendar-check"></i> Booking
            </button>
          <?php else: ?>
            <button class="btn btn-secondary btn-custom" disabled>
              <i class="bi bi-calendar-x"></i> Tidak Tersedia
            </button>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
  

  <!-- TAB NAVIGATION -->
  <ul class="nav nav-tabs mt-3">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#about">About</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#photos">Photos</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#community">Komunitas</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#history">Riwayat Booking</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#reviews">Ulasan</a></li>
  </ul>

  <div class="tab-content mt-3">
    <!-- ABOUT -->
    <div class="tab-pane fade show active" id="about">
      <div class="row">
        <div class="col-md-4">
          <div class="box">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="fw-bold text-primary">Hobby</h6>
              <?php if ($is_owner): ?>
                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#editHobbyModal">
                  <i class="bi bi-pencil"></i>
                </button>
              <?php endif; ?>
            </div>
            <?php
            if (!empty($user['hobby'])) {
                $hobbies = explode(',', $user['hobby']);
                foreach ($hobbies as $hobby) {
                    echo '<span class="tag">' . htmlspecialchars(trim($hobby)) . '</span>';
                }
            } else {
                echo '<p class="text-muted">Hobi belum diisi</p>';
            }
            ?>
          </div>
          
          <div class="box">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="fw-bold text-primary">Tags</h6>
              <?php if ($is_owner): ?>
                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#editTagsModal">
                  <i class="bi bi-pencil"></i>
                </button>
              <?php endif; ?>
            </div>
            <?php
            if (!empty($user['tags'])) {
                $tags = explode(',', $user['tags']);
                foreach ($tags as $tag) {
                    echo '<span class="tag">' . htmlspecialchars(trim($tag)) . '</span>';
                }
            } else {
                echo '<p class="text-muted">Tag belum diisi</p>';
            }
            ?>
          </div>
        </div>
        
        <div class="col-md-8">
          <div class="box">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="fw-bold text-primary">About</h6>
              <?php if ($is_owner): ?>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editAboutModal">
                  <i class="bi bi-pencil-square"></i>
                </button>
              <?php endif; ?>
            </div>
            <p><?php echo !empty($user['bio']) ? nl2br(htmlspecialchars($user['bio'])) : 'Bio belum diisi.'; ?></p>
            <hr>
            <h6 class="fw-bold text-primary">Informasi Kontak</h6>
            <p class="mb-1"><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
            <?php if (!empty($user['phone'])): ?>
              <p class="mb-1"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($user['phone']); ?></p>
            <?php endif; ?>
            <p class="mb-0"><i class="bi bi-person-badge"></i> @<?php echo htmlspecialchars($user['username']); ?></p>
          </div>
          
          <div class="box">
            <h6 class="fw-bold text-primary">Statistik</h6>
            <p class="mb-1">Bergabung sejak: <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
            <p class="mb-1">Tarif per jam: Rp <?php echo !empty($user['hourly_rate']) ? number_format($user['hourly_rate'], 0, ',', '.') : '0'; ?></p>
            <p class="mb-0">Status: <span class="<?php echo $user['available'] ? 'status-available' : 'status-unavailable'; ?>">
              <?php echo $user['available'] ? 'Tersedia untuk booking' : 'Sedang tidak tersedia'; ?>
            </span></p>
          </div>
        </div>
      </div>
    </div>

     <!-- RIWAYAT BOOKING TAB -->
    <div class="tab-pane fade" id="history">
      <div class="box">
        <h6 class="fw-bold text-primary mb-3">Riwayat Booking</h6>
        <?php if ($is_owner): ?>
          <?php if (!empty($bookings)): ?>
            <div class="list-group">
              <?php foreach ($bookings as $booking): 
                $time_remaining = strtotime($booking['start_datetime']) - time();
                $hours_remaining = floor($time_remaining / 3600);
              ?>
                <div class="list-group-item booking-item mb-2">
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center flex-grow-1">
                      <img src="<?php echo !empty($booking['client_photo']) ? htmlspecialchars($booking['client_photo']) : 'assets/img/default-user.png' ?>" 
                           class="client-avatar me-3" alt="Client">
                      <div class="flex-grow-1">
                        <h6 class="mb-1"><?php echo htmlspecialchars($booking['client_name']); ?></h6>
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
                        
                        <?php if ($booking['status'] == 'pending' && $time_remaining > 0): ?>
                          <p class="mb-0 small time-remaining <?php echo $hours_remaining < 24 ? 'urgent' : ''; ?>">
                            <i class="bi bi-alarm"></i>
                            Waktu konfirmasi: <?php echo $hours_remaining; ?> jam lagi
                            <?php if ($hours_remaining < 24): ?>
                              <br><small class="text-danger">Segera konfirmasi!</small>
                            <?php endif; ?>
                          </p>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="text-end">
                      <span class="booking-status-<?php echo $booking['status']; ?>">
                        <?php 
                        $status_text = [
                            'pending' => 'Menunggu',
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
                      
                      <!-- Action Buttons for Friend -->
                      <?php if ($booking['status'] == 'pending' && $time_remaining > 0): ?>
                        <div class="booking-actions mt-2">
                          <form method="POST" class="d-inline">
                            <input type="hidden" name="update_booking_status" value="1">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="btn btn-accept btn-sm" 
                                    onclick="return confirm('Terima booking ini?')">
                              <i class="bi bi-check-lg"></i> Terima
                            </button>
                          </form>
                          <form method="POST" class="d-inline">
                            <input type="hidden" name="update_booking_status" value="1">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-reject btn-sm" 
                                    onclick="return confirm('Tolak booking ini?')">
                              <i class="bi bi-x-lg"></i> Tolak
                            </button>
                          </form>
                        </div>
                      <?php elseif ($booking['status'] == 'accepted' && strtotime($booking['end_datetime']) <= time()): ?>
                        <form method="POST" class="d-inline">
                          <input type="hidden" name="update_booking_status" value="1">
                          <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                          <input type="hidden" name="status" value="completed">
                          <button type="submit" class="btn btn-complete btn-sm mt-2">
                            <i class="bi bi-check-all"></i> Tandai Selesai
                          </button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted">Belum ada riwayat booking.</p>
          <?php endif; ?>
        <?php else: ?>
          <p class="text-muted">Hanya pemilik profil yang dapat melihat riwayat booking.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Rest of the tabs remain the same -->
    <!-- ... -->
  </div>
</div>

<!-- MODALS FOR PROFILE OWNER -->
<?php if ($is_owner): ?>
<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Form edit profil akan ditampilkan di sini...</p>
      </div>
    </div>
  </div>
</div>

<!-- Edit About Modal -->
<div class="modal fade" id="editAboutModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-journal-text"></i> Edit About</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Form edit about akan ditampilkan di sini...</p>
      </div>
    </div>
  </div>
</div>

<!-- Upload Photos Modal -->
<div class="modal fade" id="uploadPhotosModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-cloud-upload"></i> Upload Foto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Form upload foto akan ditampilkan di sini...</p>
      </div>
    </div>
  </div>
</div>

<!-- Edit Hobby Modal -->
<div class="modal fade" id="editHobbyModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-plus-lg"></i> Edit Hobby</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Form edit hobby akan ditampilkan di sini...</p>
      </div>
    </div>
  </div>
</div>

<!-- Edit Tags Modal -->
<div class="modal fade" id="editTagsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-tags"></i> Edit Tags</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Form edit tags akan ditampilkan di sini...</p>
      </div>
    </div>
  </div>
</div>


<?php endif; ?>

<!-- MODAL BOOKING (for other users) -->
<?php if (!$is_owner && isset($_SESSION['user'])): ?>
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header" style="background:#315DB4; color:#fff;">
        <h5 class="modal-title" id="bookingModalLabel">
          <i class="bi bi-calendar-check"></i> Buat Booking - <?php echo htmlspecialchars($user['name']); ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" id="formBooking">
        <input type="hidden" name="submit_booking" value="1">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Tarif per Jam</label>
            <input type="text" class="form-control" value="Rp <?php echo !empty($user['hourly_rate']) ? number_format($user['hourly_rate'], 0, ',', '.') : '0'; ?>" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Tanggal Booking</label>
            <input type="date" name="booking_date" class="form-control" id="tglBooking" min="<?php echo date('Y-m-d'); ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Waktu Booking</label>
            <input type="time" name="booking_time" class="form-control" id="jamBooking" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Durasi (jam)</label>
            <input type="number" name="duration" class="form-control" id="durasiBooking" min="1" value="1" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan (opsional)</label>
            <textarea name="notes" class="form-control" id="catatanBooking" rows="3" placeholder="Tuliskan catatanmu..."></textarea>
          </div>
          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Total: 
            <span id="totalPrice">Rp <?php echo !empty($user['hourly_rate']) ? number_format($user['hourly_rate'], 0, ',', '.') : '0'; ?></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-booking rounded-pill"><i class="bi bi-send"></i> Konfirmasi Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Calculate total price based on duration
<?php if (!$is_owner && isset($_SESSION['user'])): ?>
const hourlyRate = <?php echo $user['hourly_rate'] ?? 0; ?>;
const durasiInput = document.getElementById('durasiBooking');
const totalPriceSpan = document.getElementById('totalPrice');

function updateTotalPrice() {
    const duration = parseInt(durasiInput.value) || 1;
    const total = hourlyRate * duration;
    totalPriceSpan.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

durasiInput.addEventListener('input', updateTotalPrice);
updateTotalPrice(); // Initial calculation

// Set minimum date to today
document.getElementById('tglBooking').min = new Date().toISOString().split('T')[0];
<?php endif; ?>
</script>
</body>
</html>