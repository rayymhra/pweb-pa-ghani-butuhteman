<?php
require 'config.php';

$success = "";
$error   = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name       = trim($_POST['name']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $confirm_pw = $_POST['confirm_password'];

    if ($password !== $confirm_pw) {
        $error = "Password dan konfirmasi password tidak sama!";
    } else {
        // Cek apakah email sudah terdaftar
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email sudah digunakan!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Default role = Client
            $role = "Client";
            $gender = "Lainnya"; // atau bisa ditambahkan input gender

            $stmt = $conn->prepare("INSERT INTO users (name, username, email, password, phone, gender, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $username = strtolower(str_replace(" ", "_", $name)); // auto generate username
            $phone = "-"; // sementara kosong

            $stmt->bind_param("sssssss", $name, $username, $email, $hashed_password, $phone, $gender, $role);

            if ($stmt->execute()) {
              $success = "Akun berhasil dibuat. Silakan login!";
                header("Location: login.php");
            } else {
                $error = "Terjadi kesalahan: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Let's Book Friends</title>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Josefin Sans', sans-serif;
      display: flex;
      min-height: 100vh;
      overflow: hidden;
    }

    .container {
      display: flex;
      width: 100%;
      height: 100vh;
    }

    /* Bagian gambar */
    .image-section {
      flex: 1.2;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #f3f4f6;
    }

    .image-section img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* Bagian form */
    .form-section {
      flex: 1;
      background: #fff;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      align-items: center;
      padding: 2rem;
    }

    .form-container {
      width: 100%;
      max-width: 380px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      flex-grow: 1;
    }

    .form-header h1 {
      font-size: 3rem;
      font-weight: 700;
      color: #06206C;
      margin-bottom: 0.5rem;
    }

    .form-header h1 span { color: #315DB4; display: inline; }

    .form-header p {
      color: #0F4457;
      margin-bottom: 1.5rem;
      font-size: 16px;
    }

    /* Notifikasi */
    .msg {
      text-align: center;
      padding: 0.8rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      font-size: 0.95rem;
      animation: fadeIn 0.4s ease-in-out;
    }
    .msg.success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .msg.error   { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-5px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Input */
    .form-input {
      width: 100%;
      padding: 0.85rem 1rem;
      margin-bottom: 1rem;
      border: none;
      border-left: 6px solid #4f79e0;
      background: #fff;
      box-shadow: 2px 3px 6px rgba(0, 0, 0, 0.15);
      font-size: 1rem;
      border-radius: 4px;
      color: #374151;
      outline: none;
      transition: 0.3s ease;
    }

    .form-input:focus {
      border-left-color: #2563eb;
      box-shadow: 2px 4px 8px rgba(37, 99, 235, 0.3);
    }

    /* Terms checkbox */
    .terms-check {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 0.85rem;
      color: #0F4457;
      margin-bottom: 1.5rem;
    }

    .button-group { display: flex; gap: 1rem; margin-bottom: 1rem; }

    .btn {
      flex: 1;
      padding: 0.8rem;
      font-size: 1rem;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .btn-signup { background: #FECE6A; color: #0F4457; }
    .btn-signup:hover { background: #FDBC3A; }

    .btn-login {
      background: #fff;
      color: #1d4ed8;
      border: 2px solid #1d4ed8;
    }
    .btn-login:hover { background: #1d4ed8; color: #fff; }

    .already {
      text-align: center;
      font-size: 0.85rem;
      color: #6b7280;
    }

    .already a { color: #1d4ed8; text-decoration: none; font-weight: 600; }
    .already a:hover { text-decoration: underline; }

    @media (max-width: 768px) {
      .container { flex-direction: column; }
      .image-section { height: 40vh; }
      .image-section img { height: 100%; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="image-section">
      <img src="assets/img/considerate-agency-UrzN-8K1PCE-unsplash.jpg" alt="Sign Up Illustration">
    </div>

    <div class="form-section">
      <div class="form-container">
        <div class="form-header">
          <h1>Daftar <span>Yuk!</span></h1>
          <p>Gabung dengan kami dan temukan teman untuk kalian sewa</p>
        </div>

        <?php if (!empty($success)) echo "<p class='msg success'>$success</p>"; ?>
        <?php if (!empty($error)) echo "<p class='msg error'>$error</p>"; ?>

        <form method="POST" action="">
          <input type="text" name="name" class="form-input" placeholder="Nama Lengkap" required>
          <input type="email" name="email" class="form-input" placeholder="Email" required>
          <input type="password" name="password" class="form-input" placeholder="Password" required>
          <input type="password" name="confirm_password" class="form-input" placeholder="Konfirmasi Password" required>

          <div class="terms-check">
            <input type="checkbox" id="terms" required>
            <label for="terms">
              I agree to the <a href="#">Terms & Conditions</a> and 
              <a href="#">Privacy Policy</a>
            </label>
          </div>

          <div class="button-group">
            <button type="submit" class="btn btn-signup">Daftar</button>
            <!-- <button type="button" class="btn btn-login" onclick="window.location.href='login.php'">Login</button> -->
          </div>

          <p class="already">
            Already have an account? <a href="login.php">Login here</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
