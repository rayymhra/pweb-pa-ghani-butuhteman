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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Handle photo deletion FIRST
    if (isset($_POST['delete_photo'])) {
        $photo_id = intval($_POST['photo_id']);
        $photo_path = mysqli_real_escape_string($conn, $_POST['photo_path']);
        
        // Verify that the photo belongs to the current user
        $verify_query = "SELECT id FROM gallery_photos WHERE id = ? AND user_id = ?";
        if ($stmt = mysqli_prepare($conn, $verify_query)) {
            mysqli_stmt_bind_param($stmt, "ii", $photo_id, $profile_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                // Delete from database
                $delete_query = "DELETE FROM gallery_photos WHERE id = ?";
                if ($stmt = mysqli_prepare($conn, $delete_query)) {
                    mysqli_stmt_bind_param($stmt, "i", $photo_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    
                    // Delete physical file
                    if (file_exists($photo_path)) {
                        unlink($photo_path);
                    }
                    
                    $_SESSION['success_message'] = "Foto berhasil dihapus!";
                }
            }
            // mysqli_stmt_close($stmt);
        }
        
        header("Location: profil_teman.php?id=" . $profile_id);
        exit;
    }
    // Handle profile photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $upload_dir = 'uploads/profile_photos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['profile_photo']['name']);
        $target_file = $upload_dir . $file_name;
        
        // Check file type
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
                // Update database
                $update_query = "UPDATE users SET profile_photo = ? WHERE id = ?";
                if ($stmt = mysqli_prepare($conn, $update_query)) {
                    mysqli_stmt_bind_param($stmt, "si", $target_file, $profile_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    
                    // Update session
                    if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $profile_id) {
                        $_SESSION['user']['profile_photo'] = $target_file;
                    }
                    
                    $_SESSION['success_message'] = "Foto profil berhasil diupload!";
                }
            }
        }
    }
    
    // Handle profile update
    if (isset($_POST['update_profile'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $hourly_rate = floatval($_POST['hourly_rate']);
        $available = isset($_POST['available']) ? 1 : 0;
        
        // Update users table
        $update_user = "UPDATE users SET name = ?, username = ?, email = ?, phone = ?, gender = ?, location = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $update_user)) {
            mysqli_stmt_bind_param($stmt, "ssssssi", $name, $username, $email, $phone, $gender, $location, $profile_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        // Update friend_profiles table
        $check_friend_profile = "SELECT id FROM friend_profiles WHERE user_id = ?";
        $friend_profile_exists = false;
        
        if ($stmt = mysqli_prepare($conn, $check_friend_profile)) {
            mysqli_stmt_bind_param($stmt, "i", $profile_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $friend_profile_exists = mysqli_num_rows($result) > 0;
            mysqli_stmt_close($stmt);
        }
        
        if ($friend_profile_exists) {
            $update_friend = "UPDATE friend_profiles SET hourly_rate = ?, location = ?, available = ? WHERE user_id = ?";
            if ($stmt = mysqli_prepare($conn, $update_friend)) {
                mysqli_stmt_bind_param($stmt, "dsii", $hourly_rate, $location, $available, $profile_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        } else {
            $insert_friend = "INSERT INTO friend_profiles (user_id, hourly_rate, location, available) VALUES (?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $insert_friend)) {
                mysqli_stmt_bind_param($stmt, "idsi", $profile_id, $hourly_rate, $location, $available);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        
        $_SESSION['success_message'] = "Profil berhasil diperbarui!";
    }
    
    // Handle about update
    if (isset($_POST['update_about'])) {
        $bio = mysqli_real_escape_string($conn, $_POST['bio']);
        $hobby = mysqli_real_escape_string($conn, $_POST['hobby']);
        
        $update_about = "UPDATE users SET bio = ?, hobby = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $update_about)) {
            mysqli_stmt_bind_param($stmt, "ssi", $bio, $hobby, $profile_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            $_SESSION['success_message'] = "About berhasil diperbarui!";
        }
    }
    
    // Handle gallery photos upload
if (isset($_FILES['gallery_photos'])) {
    $upload_dir = 'uploads/gallery_photos/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $uploaded_files = [];
    foreach ($_FILES['gallery_photos']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['gallery_photos']['error'][$key] === 0) {
            $file_name = time() . '_' . $key . '_' . basename($_FILES['gallery_photos']['name'][$key]);
            $target_file = $upload_dir . $file_name;
            
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($imageFileType, $allowed_types)) {
                if (move_uploaded_file($tmp_name, $target_file)) {
                    // Insert into gallery_photos table
                    $insert_gallery = "INSERT INTO gallery_photos (user_id, photo_path) VALUES (?, ?)";
                    if ($stmt = mysqli_prepare($conn, $insert_gallery)) {
                        mysqli_stmt_bind_param($stmt, "is", $profile_id, $target_file);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                    }
                    $uploaded_files[] = $target_file;
                }
            }
        }
    }
    
    if (!empty($uploaded_files)) {
        $_SESSION['success_message'] = count($uploaded_files) . " foto berhasil diupload!";
    }
  }
    
// Only redirect if it's NOT a booking-related submission
if (!isset($_POST['submit_booking']) && !isset($_POST['update_booking_status'])) {
    // Redirect to avoid form resubmission for other forms
    header("Location: profil_teman.php?id=" . $profile_id);
    exit;
}
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
    $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
    
    // Validate inputs
    if (empty($booking_date) || empty($booking_time) || $duration <= 0) {
        $booking_error = "Harap isi semua field dengan benar!";
    } else {
        // Calculate start and end datetime
        $start_datetime = $booking_date . ' ' . $booking_time . ':00';
        $end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime . ' + ' . $duration . ' hours'));
        
        // Check if friend is available
        if (!$user['available']) {
            $booking_error = "Teman ini sedang tidak tersedia untuk booking!";
        } else {
            // Calculate total price
            $total_price = $user['hourly_rate'] * $duration;
            
            // Check for overlapping bookings
            $check_overlap_query = "SELECT id FROM bookings WHERE friend_id = ? AND status IN ('pending', 'accepted') AND ((start_datetime <= ? AND end_datetime >= ?) OR (start_datetime <= ? AND end_datetime >= ?))";
            
            if ($stmt = mysqli_prepare($conn, $check_overlap_query)) {
                mysqli_stmt_bind_param($stmt, "issss", $friend_id, $end_datetime, $start_datetime, $start_datetime, $end_datetime);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    $booking_error = "Waktu booking bertabrakan dengan booking lain!";
                } else {
                    // Insert booking into database
                    $insert_query = "INSERT INTO bookings (client_id, friend_id, start_datetime, end_datetime, total_price, status) 
                                     VALUES (?, ?, ?, ?, ?, 'pending')";
                    
                    if ($stmt = mysqli_prepare($conn, $insert_query)) {
                        mysqli_stmt_bind_param($stmt, "iissd", $client_id, $friend_id, $start_datetime, $end_datetime, $total_price);
                        
                        if (mysqli_stmt_execute($stmt)) {
                            $booking_success = true;
                            $booking_message = "Booking berhasil dibuat! Menunggu konfirmasi dari " . htmlspecialchars($user['name']);
                            
                            // Clear form
                            unset($_POST);
                        } else {
                            $booking_error = "Gagal membuat booking. Silakan coba lagi.";
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $booking_error = "Terjadi kesalahan sistem. Silakan coba lagi.";
                    }
                }
                // mysqli_stmt_close($stmt);
            }
        }
    }
}

// Handle booking status updates
if ($is_owner && isset($_POST['update_booking_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Verify that the booking belongs to this friend
    $verify_query = "SELECT id FROM bookings WHERE id = ? AND friend_id = ?";
    if ($stmt = mysqli_prepare($conn, $verify_query)) {
        mysqli_stmt_bind_param($stmt, "ii", $booking_id, $profile_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Update booking status
            $update_query = "UPDATE bookings SET status = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $update_query)) {
                mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success_message'] = "Status booking berhasil diperbarui!";
                } else {
                    $_SESSION['error_message'] = "Gagal memperbarui status booking.";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $_SESSION['error_message'] = "Booking tidak ditemukan atau tidak memiliki akses.";
        }
        // mysqli_stmt_close($stmt);
    }
    
    header("Location: profil_teman.php?id=" . $profile_id);
    exit;
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

// Get gallery photos (you'll need to create a gallery_photos table)
$gallery_photos = [];
$gallery_query = "SELECT * FROM gallery_photos WHERE user_id = ? ORDER BY created_at DESC LIMIT 6";
if ($stmt = mysqli_prepare($conn, $gallery_query)) {
    mysqli_stmt_bind_param($stmt, "i", $profile_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $gallery_photos = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
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

/* Additional styles for modals and forms */
.current-photo img, .current-gallery img {
    border: 2px solid #dee2e6;
}

.position-relative .btn {
    transform: translate(25%, 25%);
}

.form-check-input:checked {
    background-color: #315DB4;
    border-color: #315DB4;
}

/* File input styling */
.form-control[type="file"] {
    padding: 0.5rem;
}

/* Success message styling */
.alert-success {
    border-left: 4px solid #28a745;
}


/* Photos Tab Styles */
.photo-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #e9ecef;
}

.photo-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.gallery-photo {
    cursor: pointer;
    transition: opacity 0.2s ease;
}

.gallery-photo:hover {
    opacity: 0.9;
}

/* Modal for full-size photo view */
.photo-modal .modal-dialog {
    max-width: 90%;
    max-height: 90%;
}

.photo-modal img {
    width: 100%;
    height: auto;
    max-height: 80vh;
    object-fit: contain;
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
                    <a class="nav-link" aria-current="page" href="../index.php#cari-teman">CARI TEMAN</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="../index.php#komunitas">KOMUNITAS</a>
                </li>

                <?php if (isset($_SESSION['user'])): ?>
                <!-- Jika user login -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo !empty($user_session['profile_photo']) ? htmlspecialchars($user_session['profile_photo']) : '../assets/img/user.jpg' ?>"
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
  <?php if (isset($_SESSION['success_message'])): ?>
<div class="alert alert-success alert-dismissible fade show mt-3 mx-3" role="alert">
  <i class="bi bi-check-circle-fill"></i> <?php echo $_SESSION['success_message']; ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

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
      <div class="position-relative">
        <img
          src="<?php echo !empty($user['profile_photo']) ? htmlspecialchars($user['profile_photo']) : "assets/img/user.jpg" ?>"
          class="profile-img"
          alt="Profile"
        >
        
      </div>
      <div>
        <div class="profile-name"><?php echo htmlspecialchars($user['name']); ?></div>
        <!-- <div class="rating mb-1">
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-fill"></i>
          <small>(5/5)</small>
        </div> -->
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
          <?php if ($is_owner): ?>
        <button class="btn btn-edit" 
                data-bs-toggle="modal" data-bs-target="#uploadProfilePhotoModal"
                title="Ubah Foto Profil"> 
          <i class="bi bi-camera"></i> Ubah Foto Profil
        </button>
        <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
    
    <div>
      <?php if ($is_owner): ?>
        <!-- Buttons for profile owner -->
        <!-- <button class="btn btn-success btn-custom me-2" data-bs-toggle="modal" data-bs-target="#uploadPhotosModal">
          <i class="bi bi-cloud-upload"></i> Upload Foto
        </button> -->
        
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
    <!-- <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#community">Komunitas</a></li> -->
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#history">Riwayat Booking</a></li>
    <!-- <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#reviews">Ulasan</a></li> -->
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
          
          <!-- <div class="box">
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
          </div> -->
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

    <!-- PHOTOS TAB -->
<div class="tab-pane fade" id="photos">
    <div class="box">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-primary mb-0">Gallery Foto</h6>
            <?php if ($is_owner): ?>
                <button class="btn btn-success btn-custom me-2" data-bs-toggle="modal" data-bs-target="#uploadPhotosModal">
                    <i class="bi bi-cloud-upload"></i> Upload Foto Baru
                </button>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($gallery_photos)): ?>
            <div class="row">
                <?php foreach ($gallery_photos as $photo): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card photo-card">
                            <img src="<?php echo htmlspecialchars($photo['photo_path']); ?>" 
                                 class="card-img-top gallery-photo" 
                                 alt="Gallery Photo"
                                 style="height: 360px; object-fit: cover;"
                                 onerror="this.src='assets/img/default-image.jpg'">
                            <div class="card-body p-2">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> 
                                    <?php echo date('d M Y', strtotime($photo['created_at'])); ?>
                                </small>
                                <?php if ($is_owner): ?>
                                    <div class="mt-2">
                                        <button class="btn btn-outline-danger btn-sm w-100 delete-photo-btn" 
                                                data-photo-id="<?php echo $photo['id']; ?>"
                                                data-photo-path="<?php echo htmlspecialchars($photo['photo_path']); ?>">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Load More Button if there are more photos -->
            <?php
            $total_photos_query = "SELECT COUNT(*) as total FROM gallery_photos WHERE user_id = ?";
            $total_photos = 0;
            if ($stmt = mysqli_prepare($conn, $total_photos_query)) {
                mysqli_stmt_bind_param($stmt, "i", $profile_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $total_data = mysqli_fetch_assoc($result);
                $total_photos = $total_data['total'];
                mysqli_stmt_close($stmt);
            }
            
            if ($total_photos > 6): ?>
                <div class="text-center mt-4">
                    <button class="btn btn-outline-primary" id="loadMorePhotos">
                        <i class="bi bi-arrow-down"></i> Muat Lebih Banyak Foto
                    </button>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-images display-1 text-muted"></i>
                <h5 class="text-muted mt-3">Belum ada foto di gallery</h5>
                <?php if ($is_owner): ?>
                    <p class="text-muted">Upload foto pertama Anda untuk menampilkannya di sini</p>
                    <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#uploadPhotosModal">
                        <i class="bi bi-cloud-upload"></i> Upload Foto Pertama
                    </button>
                <?php else: ?>
                    <p class="text-muted"><?php echo htmlspecialchars($user['name']); ?> belum mengupload foto gallery</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
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
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Telepon</label>
                <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <select class="form-select" name="gender" required>
                  <option value="Pria" <?php echo $user['gender'] == 'Pria' ? 'selected' : ''; ?>>Laki-laki</option>
                  <option value="Wanita" <?php echo $user['gender'] == 'Wanita' ? 'selected' : ''; ?>>Perempuan</option>
                  <option value="Lainnya" <?php echo $user['gender'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Lokasi</label>
                <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($user['location'] ?? $user['friend_location'] ?? ''); ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Tarif per Jam (Rp)</label>
                <input type="number" class="form-control" name="hourly_rate" value="<?php echo $user['hourly_rate'] ?? 0; ?>" min="0" step="1000">
              </div>
              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="available" id="availableCheck" <?php echo ($user['available'] ?? 0) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="availableCheck">Tersedia untuk booking</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="update_profile" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit About Modal -->
<div class="modal fade" id="editAboutModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-journal-text"></i> Edit About</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Bio</label>
            <textarea class="form-control" name="bio" rows="5" placeholder="Ceritakan tentang diri Anda..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Hobi (pisahkan dengan koma)</label>
            <input type="text" class="form-control" name="hobby" value="<?php echo htmlspecialchars($user['hobby'] ?? ''); ?>" placeholder="Contoh: Membaca, Traveling, Musik">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="update_about" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Upload Profile Photo Modal -->
<div class="modal fade" id="uploadProfilePhotoModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-camera"></i> Upload Foto Profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Pilih Foto</label>
            <input type="file" class="form-control" name="profile_photo" accept="image/*" required>
            <div class="form-text">Format yang didukung: JPG, JPEG, PNG, GIF. Maksimal 2MB.</div>
          </div>
          <?php if (!empty($user['profile_photo'])): ?>
          <div class="current-photo">
            <label class="form-label">Foto Saat Ini:</label>
            <div>
              <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Current Profile" class="img-thumbnail" style="max-height: 150px;">
            </div>
          </div>
          <?php endif; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Upload Foto</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Upload Gallery Photos Modal -->
<div class="modal fade" id="uploadPhotosModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-cloud-upload"></i> Upload Foto Gallery</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Pilih Foto (Maksimal 6 foto)</label>
            <input type="file" class="form-control" name="gallery_photos[]" multiple accept="image/*" required>
            <div class="form-text">Format yang didukung: JPG, JPEG, PNG, GIF. Maksimal 2MB per foto.</div>
          </div>
          
          <?php if (!empty($gallery_photos)): ?>
          <div class="current-gallery">
            <label class="form-label">Foto Gallery Saat Ini:</label>
            <div class="row">
              <?php foreach ($gallery_photos as $photo): ?>
              <div class="col-md-4 mb-3">
                <img src="<?php echo htmlspecialchars($photo['photo_path']); ?>" alt="Gallery Photo" class="img-thumbnail w-100" style="height: 120px; object-fit: cover;">
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Upload Foto</button>
        </div>
      </form>
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
      <form method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Hobi Anda (pisahkan dengan koma)</label>
            <input type="text" class="form-control" name="hobby" value="<?php echo htmlspecialchars($user['hobby'] ?? ''); ?>" placeholder="Contoh: Membaca, Traveling, Musik, Olahraga">
            <div class="form-text">Contoh: Membaca, Traveling, Musik, Olahraga</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="update_about" class="btn btn-primary">Simpan Hobi</button>
        </div>
      </form>
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
        <div class="alert alert-info">
          <i class="bi bi-info-circle"></i> Fitur tags sedang dalam pengembangan.
        </div>
        <p>Fitur ini akan memungkinkan Anda menambahkan tag untuk memudahkan pencarian.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
          <!-- Friend Information -->
          <div class="alert alert-info">
            <div class="d-flex align-items-center">
              <img src="<?php echo !empty($user['profile_photo']) ? htmlspecialchars($user['profile_photo']) : "assets/img/user.jpg" ?>" 
                   class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
              <div>
                <strong><?php echo htmlspecialchars($user['name']); ?></strong><br>
                <small>Tarif: Rp <?php echo !empty($user['hourly_rate']) ? number_format($user['hourly_rate'], 0, ',', '.') : '0'; ?>/jam</small>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Tanggal Booking *</label>
              <input type="date" name="booking_date" class="form-control" id="tglBooking" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Waktu Booking *</label>
              <input type="time" name="booking_time" class="form-control" id="jamBooking" min="08:00" max="22:00" required>
              <small class="text-muted">Pukul 08:00 - 22:00</small>
            </div>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Durasi (jam) *</label>
            <input type="number" name="duration" class="form-control" id="durasiBooking" min="1" max="8" value="1" required>
            <small class="text-muted">Maksimal 8 jam per booking</small>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Catatan untuk <?php echo htmlspecialchars($user['name']); ?> (opsional)</label>
            <textarea name="notes" class="form-control" id="catatanBooking" rows="3" placeholder="Tuliskan aktivitas yang ingin dilakukan atau kebutuhan khusus..."></textarea>
          </div>
          
          <!-- Price Summary -->
          <div class="alert alert-warning">
            <div class="d-flex justify-content-between">
              <span><strong>Total Biaya:</strong></span>
              <span id="totalPrice" class="fw-bold">Rp <?php echo !empty($user['hourly_rate']) ? number_format($user['hourly_rate'], 0, ',', '.') : '0'; ?></span>
            </div>
            <small class="text-muted">* Pembayaran dilakukan secara langsung saat bertemu</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-booking rounded-pill">
            <i class="bi bi-send"></i> Konfirmasi Booking
          </button>
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
const bookingDateInput = document.getElementById('tglBooking');
const bookingTimeInput = document.getElementById('jamBooking');

function updateTotalPrice() {
    const duration = parseInt(durasiInput.value) || 1;
    const total = hourlyRate * duration;
    totalPriceSpan.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function validateBookingTime() {
    const selectedTime = bookingTimeInput.value;
    if (selectedTime) {
        const [hours] = selectedTime.split(':').map(Number);
        if (hours < 8 || hours > 22) {
            bookingTimeInput.setCustomValidity('Waktu booking harus antara 08:00 - 22:00');
        } else {
            bookingTimeInput.setCustomValidity('');
        }
    }
}

function validateDuration() {
    const duration = parseInt(durasiInput.value) || 0;
    if (duration < 1 || duration > 8) {
        durasiInput.setCustomValidity('Durasi harus antara 1-8 jam');
    } else {
        durasiInput.setCustomValidity('');
    }
}

// Event listeners
durasiInput.addEventListener('input', function() {
    validateDuration();
    updateTotalPrice();
});

bookingTimeInput.addEventListener('change', validateBookingTime);

// Set minimum date to today
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);
bookingDateInput.min = today.toISOString().split('T')[0];

// Set default time to next hour
const nextHour = new Date();
nextHour.setHours(nextHour.getHours() + 1, 0, 0, 0);
bookingTimeInput.value = nextHour.toTimeString().slice(0, 5);

// Initial calculations
updateTotalPrice();
validateDuration();

// Form submission handling
document.getElementById('formBooking')?.addEventListener('submit', function(e) {
    const duration = parseInt(durasiInput.value);
    const bookingDate = new Date(bookingDateInput.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (bookingDate < today) {
        e.preventDefault();
        Swal.fire('Error', 'Tanggal booking tidak valid!', 'error');
        return;
    }
    
    if (duration < 1 || duration > 8) {
        e.preventDefault();
        Swal.fire('Error', 'Durasi harus antara 1-8 jam!', 'error');
        return;
    }
});
<?php endif; ?>




// Photo deletion functionality
document.addEventListener('DOMContentLoaded', function() {
    // Delete photo buttons
    const deleteButtons = document.querySelectorAll('.delete-photo-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const photoId = this.getAttribute('data-photo-id');
            const photoPath = this.getAttribute('data-photo-path');
            
            Swal.fire({
                title: 'Hapus Foto?',
                text: "Foto yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = window.location.href;
                    
                    const photoIdInput = document.createElement('input');
                    photoIdInput.type = 'hidden';
                    photoIdInput.name = 'photo_id';
                    photoIdInput.value = photoId;
                    
                    const photoPathInput = document.createElement('input');
                    photoPathInput.type = 'hidden';
                    photoPathInput.name = 'photo_path';
                    photoPathInput.value = photoPath;
                    
                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_photo';
                    deleteInput.value = '1';
                    
                    form.appendChild(photoIdInput);
                    form.appendChild(photoPathInput);
                    form.appendChild(deleteInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
    
    // Load more photos functionality
    const loadMoreBtn = document.getElementById('loadMorePhotos');
    if (loadMoreBtn) {
        let currentPage = 1;
        
        loadMoreBtn.addEventListener('click', function() {
            currentPage++;
            const profileId = <?php echo $profile_id; ?>;
            
            fetch(`load_more_photos.php?user_id=${profileId}&page=${currentPage}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const photosContainer = document.querySelector('.row');
                        data.photos.forEach(photo => {
                            const photoCol = document.createElement('div');
                            photoCol.className = 'col-md-4 mb-4';
                            photoCol.innerHTML = `
                                <div class="card photo-card">
                                    <img src="${photo.photo_path}" 
                                         class="card-img-top gallery-photo" 
                                         alt="Gallery Photo"
                                         style="height: 200px; object-fit: cover;"
                                         onerror="this.src='assets/img/default-image.jpg'">
                                    <div class="card-body p-2">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i> 
                                            ${photo.created_at_formatted}
                                        </small>
                                        <?php if ($is_owner): ?>
                                            <div class="mt-2">
                                                <button class="btn btn-outline-danger btn-sm w-100 delete-photo-btn" 
                                                        data-photo-id="${photo.id}"
                                                        data-photo-path="${photo.photo_path}">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            `;
                            photosContainer.appendChild(photoCol);
                        });
                        
                        // Re-attach event listeners to new delete buttons
                        attachDeleteListeners();
                        
                        // Hide load more button if no more photos
                        if (!data.hasMore) {
                            loadMoreBtn.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading more photos:', error);
                });
        });
    }
    
    function attachDeleteListeners() {
        const newDeleteButtons = document.querySelectorAll('.delete-photo-btn');
        newDeleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const photoId = this.getAttribute('data-photo-id');
                const photoPath = this.getAttribute('data-photo-path');
                
                Swal.fire({
                    title: 'Hapus Foto?',
                    text: "Foto yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = window.location.href;
                        
                        const photoIdInput = document.createElement('input');
                        photoIdInput.type = 'hidden';
                        photoIdInput.name = 'photo_id';
                        photoIdInput.value = photoId;
                        
                        const photoPathInput = document.createElement('input');
                        photoPathInput.type = 'hidden';
                        photoPathInput.name = 'photo_path';
                        photoPathInput.value = photoPath;
                        
                        const deleteInput = document.createElement('input');
                        deleteInput.type = 'hidden';
                        deleteInput.name = 'delete_photo';
                        deleteInput.value = '1';
                        
                        form.appendChild(photoIdInput);
                        form.appendChild(photoPathInput);
                        form.appendChild(deleteInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    }
});
</script>
</body>
</html>