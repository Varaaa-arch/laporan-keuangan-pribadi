<?php 
    session_start();
    if(!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }
    require '../config/db.php';
    $user_id = $_SESSION['user_id'];

    $result = $connection->query("SELECT * FROM expenses WHERE user_id = $user_id ORDER BY date DESC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Laporan Keuangan Pribadi</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f7fa;
      color: #2c3e50;
      margin: 0;
      padding: 0;
    }
    header {
      background-color: #2c3e50;
      color: white;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header h1 {
      margin: 0;
      font-size: 22px;
    }
    .logout-btn {
      background-color: #e74c3c;
      color: white;
      border: none;
      padding: 10px 16px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.2s;
    }
    .logout-btn:hover {
      background-color: #c0392b;
    }
    .container {
      width: 90%;
      margin: 20px auto;
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    form {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-bottom: 20px;
    }
    input, select {
      padding: 8px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    input[type="number"] {
      width: 200px;
    }
    input[type="text"] {
      flex: 1;
    }
    input[type="submit"] {
      background-color: #27ae60;
      color: white;
      border: none;
      cursor: pointer;
      transition: 0.2s;
    }
    input[type="submit"]:hover {
      background-color: #219150;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    th, td {
      border-bottom: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }
    th {
      background-color: #ecf0f1;
    }
    .summary {
      margin-top: 30px;
      padding: 20px;
      background-color: #ecf0f1;
      border-radius: 10px;
    }
    .summary p {
      margin: 8px 0;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <header>
    <h1>Sistem Laporan Keuangan Pribadi</h1>
    <form action="../auth/logout.php" method="post">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </header>

  <div class="container">
    <h2>Tambah Transaksi</h2>
    <form method="post">
      <input type="text" name="deskripsi" placeholder="Deskripsi" required>
      <input type="number" name="jumlah" placeholder="Jumlah (Rp)" required>
      <select name="tipe">
        <option value="pemasukan">Pemasukan</option>
        <option value="pengeluaran">Pengeluaran</option>
      </select>
      <input type="submit" name="tambah" value="Tambah Transaksi">
    </form>

    <?php
    $file = 'data_keuangan.json';
    if (!file_exists($file)) {
      file_put_contents($file, json_encode([]));
    }

    $data = json_decode(file_get_contents($file), true);

    if (isset($_POST['tambah'])) {
      $transaksi = [
        "deskripsi" => $_POST['deskripsi'],
        "jumlah" => (int)$_POST['jumlah'],
        "tipe" => $_POST['tipe'],
        "tanggal" => date("Y-m-d H:i:s")
      ];
      $data[] = $transaksi;
      file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
      echo "<p style='color:green;'>Transaksi berhasil ditambahkan.</p>";
    }

    $total_pemasukan = 0;
    $total_pengeluaran = 0;
    foreach ($data as $d) {
      if ($d['tipe'] === 'pemasukan') $total_pemasukan += $d['jumlah'];
      else $total_pengeluaran += $d['jumlah'];
    }

    $saldo = $total_pemasukan - $total_pengeluaran;
    ?>

    <h2>Data Transaksi</h2>
    <table>
      <tr>
        <th>Tanggal</th>
        <th>Deskripsi</th>
        <th>Tipe</th>
        <th>Jumlah (Rp)</th>
      </tr>
      <?php foreach ($data as $d): ?>
        <tr>
          <td><?= $d['tanggal'] ?></td>
          <td><?= htmlspecialchars($d['deskripsi']) ?></td>
          <td><?= ucfirst($d['tipe']) ?></td>
          <td>Rp<?= number_format($d['jumlah'], 0, ',', '.') ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <div class="summary">
      <h3>Ringkasan Keuangan</h3>
      <p>Total Pemasukan: <strong>Rp<?= number_format($total_pemasukan, 0, ',', '.') ?></strong></p>
      <p>Total Pengeluaran: <strong>Rp<?= number_format($total_pengeluaran, 0, ',', '.') ?></strong></p>
      <p>Saldo Akhir: <strong>Rp<?= number_format($saldo, 0, ',', '.') ?></strong></p>

      <?php
      if ($total_pengeluaran > $total_pemasukan) {
        echo "<p style='color:red;'>Kamu defisit bulan ini. Segera atur ulang pengeluaranmu.</p>";
      } elseif ($total_pengeluaran >= ($total_pemasukan * 0.8)) {
        echo "<p style='color:orange;'>Keuangan stabil, tapi sebaiknya mulai sisihkan untuk tabungan.</p>";
      } else {
        echo "<p style='color:green;'>Keuangan sangat sehat. Kamu bisa mulai berinvestasi.</p>";
      }
      ?>
    </div>

    <canvas id="chart" height="100"></canvas>

    <script>
      const ctx = document.getElementById('chart');
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Pemasukan', 'Pengeluaran'],
          datasets: [{
            data: [<?= $total_pemasukan ?>, <?= $total_pengeluaran ?>],
            backgroundColor: ['#2ecc71', '#e74c3c'],
          }]
        },
        options: {
          plugins: {
            legend: { position: 'bottom' },
            title: { display: true, text: 'Proporsi Pemasukan vs Pengeluaran' }
          }
        }
      });
    </script>
  </div>
</body>
</html>