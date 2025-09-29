<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"><title>Profil Teman</title>
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
.btn-custom { border-radius: 20px; padding: 6px 18px; font-weight: 500; }
.btn-simpan { background: #FECE6A; color: #333; }
.btn-booking { background: #315DB4; color: #fff; }
.box { background: #fff; border-radius: 12px; padding: 1rem 1.2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 1rem; }
.tag { background-color: #FECE6A; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 500; margin: 3px; display: inline-block; color: #333; }
.photo-grid { width: 100%; height: 250px; object-fit: cover; }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-dark" href="#">TemanKu</a>
  </div>
</nav>

<div class="container">
  <!-- PROFILE CARD -->
  <div class="profile-card shadow-sm">
    <div class="d-flex justify-content-between align-items-start">
      <div class="d-flex">
        <img src="https://via.placeholder.com/100" class="profile-img" alt="Profile">
        <div>
          <div class="profile-name">Mark Wayar</div>
          <div class="rating mb-1">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <small>(5/5)</small>
          </div>
          <div class="text-muted small">
            <i class="bi bi-geo-alt"></i> Jakarta, Sudirman <br>
            <i class="bi bi-gender-ambiguous"></i> Laki-laki
          </div>
        </div>
      </div>
      <div>
        <button class="btn btn-simpan btn-custom me-2"><i class="bi bi-bookmark"></i> Simpan</button>
        <!-- Tombol Booking buka modal -->
         <button class="btn btn-success btn-custom me-2" data-bs-toggle="modal" data-bs-target="#chatModal">
    <i class="bi bi-chat-dots-fill"></i> Chat
  </button>
        <button class="btn btn-booking btn-custom" data-bs-toggle="modal" data-bs-target="#bookingModal">
          <i class="bi bi-calendar-check"></i> Booking
        </button>
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
            <h6 class="fw-bold text-primary">Hobby</h6>
            <span class="tag">Mancing</span>
            <span class="tag">Traveling</span>
            <span class="tag">Ngopi</span>
          </div>
        </div>
        <div class="col-md-8">
          <div class="box">
            <h6 class="fw-bold text-primary">About</h6>
            <p>Gue suka mancing, siap nemenin lo mancing kapanpun. Tinggal infoin aja waktu dan lokasinya.</p>
            <hr>
            <h6 class="fw-bold text-primary">Statistik</h6>
            <p class="mb-1">Booking Dilakukan: 12</p>
            <p class="mb-0">Ulasan Diberikan: 8</p>
          </div>
        </div>
      </div>
    </div>

    <!-- PHOTOS -->
    <div class="tab-pane fade" id="photos">
      <div class="box">
        <h6 class="fw-bold text-primary mb-3">Photos</h6>
        <div class="row g-3">
          <div class="col-6 col-md-3">
            <img src="https://img.freepik.com/free-photo/portrait-smiling-young-man_1268-21877.jpg?semt=ais_hybrid&w=740&q=80" class="img-fluid photo-grid rounded">
          </div>
          <div class="col-6 col-md-3">
            <img src="https://img.freepik.com/free-photo/portrait-smiling-young-man_1268-21877.jpg?semt=ais_hybrid&w=740&q=80" class="img-fluid photo-grid rounded">
          </div>
          <div class="col-6 col-md-3">
            <img src="https://img.freepik.com/free-photo/portrait-smiling-young-man_1268-21877.jpg?semt=ais_hybrid&w=740&q=80" class="img-fluid photo-grid rounded">
          </div>
          <div class="col-6 col-md-3">
            <img src="https://img.freepik.com/free-photo/portrait-smiling-young-man_1268-21877.jpg?semt=ais_hybrid&w=740&q=80" class="img-fluid photo-grid rounded">
          </div>
        </div>
      </div>
    </div>

    <!-- KOMUNITAS -->
    <div class="tab-pane fade" id="community">
      <div class="box">
        <h6 class="fw-bold text-primary mb-3">Komunitas</h6>
        <p class="text-muted">Fitur komunitas teman ini belum tersedia.</p>
      </div>
    </div>

    <!-- RIWAYAT BOOKING -->
    <div class="tab-pane fade" id="history">
      <div class="box">
        <p class="text-muted">📅 Riwayat booking teman ini akan tampil di sini.</p>
      </div>
    </div>

    <!-- ULASAN -->
    <div class="tab-pane fade" id="reviews">
      <div class="box">
        <p class="text-muted">⭐ Ulasan teman ini akan tampil di sini.</p>
      </div>
    </div>
  </div>
</div>

<!-- MODAL BOOKING -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header" style="background:#315DB4; color:#fff;">
        <h5 class="modal-title" id="bookingModalLabel"><i class="bi bi-calendar-check"></i> Buat Booking</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formBooking">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Tanggal Booking</label>
            <input type="date" class="form-control" id="tglBooking" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Waktu Booking</label>
            <input type="time" class="form-control" id="jamBooking" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea class="form-control" id="catatanBooking" rows="3" placeholder="Tuliskan catatanmu..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-booking rounded-pill"><i class="bi bi-send"></i> Konfirmasi</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Tangani submit booking
document.getElementById("formBooking").addEventListener("submit", function(e){
  e.preventDefault();
  let tgl = document.getElementById("tglBooking").value;
  let jam = document.getElementById("jamBooking").value;

  if(tgl && jam){
    // Tutup modal
    let bookingModal = bootstrap.Modal.getInstance(document.getElementById("bookingModal"));
    bookingModal.hide();

    // SweetAlert sukses
    Swal.fire({
      icon: 'success',
      title: 'Booking Berhasil!',
      text: `Booking sudah dibuat untuk tanggal ${tgl} jam ${jam}.`,
      confirmButtonColor: '#315DB4'
    });
  }
});
</script>
</body>
</html>
