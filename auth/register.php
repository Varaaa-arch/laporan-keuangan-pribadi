<?php
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  if (!empty($username) && !empty($password)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $check = $connection->prepare("SELECT * FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
      echo "<script>alert('Username sudah digunakan!');</script>";
    } else {
      $stmt = $connection->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
      $stmt->bind_param("ss", $username, $hashed);

      if ($stmt->execute()) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
      } else {
        echo "<script>alert('Gagal menyimpan data!');</script>";
      }

      $stmt->close();
    }
  } else {
    echo "<script>alert('Isi semua field terlebih dahulu!');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun Keuangan</title>
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
    <h2>Daftar Akun Baru</h2>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="submit" value="Register">
    </form>
    <div class="link">
      Sudah punya akun? <a href="login.php">Login</a>
    </div>
  </div>
</body>
</html>
