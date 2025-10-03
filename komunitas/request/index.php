<?php
include 'koneksi.php';

// handle approve/reject
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == "approve") {
        mysqli_query($conn, "UPDATE community_requests SET status='approved' WHERE id=$id");
    } elseif ($action == "reject") {
        mysqli_query($conn, "UPDATE community_requests SET status='rejected' WHERE id=$id");
    }

    header("Location: request.php");
    exit;
}

// handle form submit
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['communityName'];
    $category = $_POST['category'];
    $desc = $_POST['description'];
    $by = $_POST['requestedBy'];
    $contact = $_POST['contactInfo'];

    $sql = "INSERT INTO community_requests 
            (community_name, category, description, requested_by, contact_info) 
            VALUES ('$name','$category','$desc','$by','$contact')";

    if (mysqli_query($conn, $sql)) {
        $msg = "Request berhasil dikirim! Status: Pending";
    } else {
        $msg = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Komunitas - Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    
    <style>
        :root {
            --primary-color: #FECE6A;
            --secondary-color: #FECE6A;
            --accent-color: #FECE6A;
            --text-dark: #2C2C2C;
            --text-light: #5A5A5A;
        }
        body {
            background: linear-gradient(135deg, #FECE6A 0%, #FECE6A 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-custom {
            background: #FECE6A(244, 196, 48, 0.95);
        }
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .request-card { background: #F8F9FA; border-radius: 15px; border-left: 5px solid #DEE2E6; margin-bottom: 1rem; }
        .btn-approve { background: linear-gradient(135deg, #28A745, #20C997); border: none; color: white; font-weight: 500; }
        .btn-reject { background: linear-gradient(135deg, #DC3545, #E74C3C); border: none; color: white; font-weight: 500; }
        .status-pending { background: #FECE6A; padding: 5px 10px; border-radius: 10px; color:#856404; }
        .status-approved { background: #28A745; padding: 5px 10px; border-radius: 10px; color:white; }
        .status-rejected { background: #DC3545; padding: 5px 10px; border-radius: 10px; color:white; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                  <img src="pp butuh tmn.jpg" alt="Logo" width="40" height="40" class="me-2">
                  BUTUH TEMAN
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="main-container p-4">
            <!-- Page Header -->
            <div class="page-header p-4 mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-5 fw-bold text-dark mb-2">
                            <i class="bi bi-plus-circle-fill text-warning me-2"></i>
                            Request Komunitas Baru
                        </h1>
                        <p class="lead text-muted mb-0">Kelola permintaan pembuatan komunitas dari user</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex justify-content-end align-items-center">
                            <span class="badge bg-light text-dark fs-6 px-3 py-2">
                                <i class="bi bi-clock me-1"></i>
                                <?php echo date("d-m-Y"); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" id="requestTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="form-tab" data-bs-toggle="tab" data-bs-target="#form-pane" type="button">
                        <i class="bi bi-plus-lg me-2"></i>Buat Request
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list-pane" type="button">
                        <i class="bi bi-list-ul me-2"></i>Daftar Request
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="requestTabContent">
                <!-- Form Tab -->
                <div class="tab-pane fade show active" id="form-pane" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <?php if (!empty($msg)) { ?>
                                <div class="alert alert-info"><?php echo $msg; ?></div>
                            <?php } ?>
                            <form method="post" action="">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Komunitas</label>
                                        <input type="text" class="form-control" name="communityName" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Kategori</label>
                                        <select class="form-select" name="category" required>
                                            <option value="">Pilih kategori</option>
                                            <option value="teknologi">🖥️ Teknologi</option>
                                            <option value="olahraga">⚽ Olahraga</option>
                                            <option value="seni">🎨 Seni & Budaya</option>
                                            <option value="bisnis">💼 Bisnis</option>
                                            <option value="pendidikan">📚 Pendidikan</option>
                                            <option value="hobi">🎮 Hobi</option>
                                            <option value="lainnya">📂 Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Komunitas</label>
                                    <textarea class="form-control" name="description" rows="4" required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Diminta oleh</label>
                                        <input type="text" class="form-control" name="requestedBy" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Kontak Email</label>
                                        <input type="email" class="form-control" name="contactInfo" required>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-custom btn-lg">
                                        <i class="bi bi-send me-2"></i>Submit Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- List Tab -->
                <div class="tab-pane fade" id="list-pane" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-dark mb-0">
                            <i class="bi bi-list-check me-2"></i>Daftar Request
                        </h5>
                    </div>
                    <div id="requestContainer">
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM community_requests ORDER BY id DESC");
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "
                                <div class='card request-card'>
                                    <div class='card-body'>
                                        <div class='d-flex justify-content-between align-items-start mb-3'>
                                            <div>
                                                <h5 class='card-title text-dark mb-1'>
                                                    <i class='bi bi-collection text-warning me-2'></i>".$row['community_name']."
                                                </h5>
                                                <div class='d-flex align-items-center text-muted small'>
                                                    <span class='me-3'><i class='bi bi-tag me-1'></i>".$row['category']."</span>
                                                    <span class='me-3'><i class='bi bi-person me-1'></i>".$row['requested_by']."</span>
                                                    <span><i class='bi bi-calendar me-1'></i>".$row['date_created']."</span>
                                                </div>
                                            </div>
                                            <span class='status-".$row['status']."'>".$row['status']."</span>
                                        </div>
                                        
                                        <p class='card-text text-muted mb-3'>".$row['description']."</p>
                                        <div class='text-muted small mb-3'>
                                            <i class='bi bi-envelope me-1'></i><strong>Kontak:</strong> ".$row['contact_info']."
                                        </div>";
                                if ($row['status'] == 'pending') {
                                    echo "
                                    <div class='d-flex gap-2'>
                                        <a href='request.php?action=approve&id=".$row['id']."' class='btn btn-approve btn-sm'>
                                            <i class='bi bi-check-lg me-1'></i>Approve
                                        </a>
                                        <a href='request.php?action=reject&id=".$row['id']."' class='btn btn-reject btn-sm'>
                                            <i class='bi bi-x-lg me-1'></i>Reject
                                        </a>
                                    </div>";
                                }
                                echo "</div></div>";
                            }
                        } else {
                            echo "<p class='text-muted'>Belum ada request.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
