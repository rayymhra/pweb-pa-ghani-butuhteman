<?php
session_start();
require 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, name, username, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['role']    = $user['role'];

                switch ($user['role']) {
                    case "Admin": header("Location: admin_dashboard.php"); break;
                    case "Friend": header("Location: friend_dashboard.php"); break;
                    case "Client": header("Location: ../index.php"); break;
                    default: header("Location: index.php"); break;
                }
                exit();
            } else {
                $error = "Password salah! Silakan coba lagi.";
            }
        } else {
            $error = "Email tidak ditemukan!";
        }
    } else {
        $error = "Email dan password wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Let's Book Friends - Login</title>
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

    .form-header h1 span { color: #315DB4; display: block; }

    .form-header p {
      color: #0F4457;
      margin-bottom: 1.5rem;
      font-size: 1rem;
    }

    /* Notifikasi error */
    .alert {
      background: #fee2e2;
      color: #b91c1c;
      border: 1px solid #fca5a5;
      padding: 0.8rem 1rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      gap: 8px;
      animation: fadeIn 0.4s ease-in-out;
    }

    .alert strong { font-weight: 700; }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-5px); }
      to { opacity: 1; transform: translateY(0); }
    }

   .form-input {
  width: 100%;
  padding: 0.85rem 1rem;
  margin-bottom: 1rem;
  border: none;
  border-left: 6px solid #4f79e0; /* garis biru di kiri */
  background: #fff;
  box-shadow: 2px 3px 6px rgba(0, 0, 0, 0.15); /* efek shadow */
  font-size: 1rem;
  border-radius: 4px;
  color: #374151;
  outline: none;
  transition: 0.3s ease;
}

.form-input:focus {
  border-left-color: #2563eb; /* garis biru lebih terang saat fokus */
  box-shadow: 2px 4px 8px rgba(37, 99, 235, 0.3);
}


    .remember-section {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
      margin-bottom: 1.5rem;
      color: #0F4457;
    }

    .remember-section label { margin-top: 2px; }

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

    .btn-login { background: #fbbf24; color: #111827; }
    .btn-login:hover { background: #f59e0b; }

    .btn-signup {
      background: #fff;
      color: #1d4ed8;
      border: 2px solid #1d4ed8;
    }
    .btn-signup:hover { background: #1d4ed8; color: #fff; }

    .terms {
      text-align: center;
      font-size: 0.8rem;
      color: #6b7280;
    }

    .terms a { color: #1d4ed8; text-decoration: none; }
    .terms a:hover { text-decoration: underline; }

    /* Responsive */
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
      <img src="assets/img/login.png" alt="Login Illustration">
    </div>
    <div class="form-section">
      <div class="form-container">
        <div class="form-header">
          <h1>Let’s Book <span>Friends</span></h1>
          <p>Welcome back, please login to your account</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert">
            ⚠️ <strong>Error:</strong> <?= $error; ?>
          </div>
        <?php endif; ?>

        <form method="POST">
          <input type="email" name="email" class="form-input" placeholder="Your email" required>
          <input type="password" name="password" class="form-input" placeholder="Password" required>

          <div class="remember-section">
            <input type="checkbox" id="remember">
            <label for="remember">Remember me</label>
          </div>

          <div class="button-group">
            <button type="submit" class="btn btn-login">Login</button>
            <button type="button" class="btn btn-signup" onclick="window.location.href='register.php'">Sign Up</button>
          </div>
        </form>
      </div>

      <div class="terms">
        By signing up, you agree to <br><br>
        <a href="#">Terms & Conditions</a> and 
        <a href="#">Privacy Policy</a>
      </div>
    </div>
  </div>
</body>
</html>
