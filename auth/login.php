<?php
session_start();
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  if (!empty($username) && !empty($password)) {
    $stmt = $connection->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();

      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        echo "<script>
          alert('Login berhasil! Selamat datang, {$user['username']}');
          window.location='../dashboard/index.php';
        </script>";
      } else {
        echo "<script>alert('Password salah!');</script>";
      }
    } else {
      echo "<script>alert('Username tidak ditemukan!');</script>";
    }
    $stmt->close();
  } else {
    echo "<script>alert('Isi semua kolom terlebih dahulu!');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Keuangan</title>
  <style>
    :root {
      --primary: #3498db;
      --primary-dark: #2980b9;
      --background: #f6f8fa;
      --card-bg: #ffffff;
      --text-dark: #2c3e50;
      --text-gray: #7f8c8d;
      --radius: 12px;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: var(--background);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .card {
      background: var(--card-bg);
      padding: 40px 35px;
      border-radius: var(--radius);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 350px;
      text-align: center;
    }

    h2 {
      color: var(--text-dark);
      margin-bottom: 20px;
      font-weight: 600;
    }

    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #dcdcdc;
      border-radius: var(--radius);
      transition: 0.3s;
      font-size: 14px;
    }

    input:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 5px rgba(52,152,219,0.3);
    }

    input[type="submit"] {
      background: var(--primary);
      color: #fff;
      font-weight: 500;
      border: none;
      cursor: pointer;
      transition: 0.3s;
    }

    input[type="submit"]:hover {
      background: var(--primary-dark);
      transform: scale(1.02);
    }

    .link {
      margin-top: 15px;
      color: var(--text-gray);
      font-size: 14px;
    }

    .link a {
      color: var(--primary-dark);
      font-weight: 500;
      text-decoration: none;
    }

    .link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Login Keuangan</h2>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="submit" value="Login">
    </form>
    <div class="link">
      Belum punya akun? <a href="register.php">Daftar di sini</a>
    </div>
  </div>
</body>
</html>
