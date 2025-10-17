<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO expenses (user_id, title, amount, category, date, description) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isdsss', $user_id, $title, $amount, $category, $date, $description);

    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: " . $connection->error;
    }
}
?>
<h2>Tambah Pengeluaran</h2>
<form method="POST">
  <input type="text" name="title" placeholder="Judul" required><br>
  <input type="number" name="amount" placeholder="Jumlah" required><br>
  <input type="text" name="category" placeholder="Kategori" required><br>
  <input type="date" name="date" required><br>
  <textarea name="description" placeholder="Deskripsi..."></textarea><br>
  <button type="submit">Simpan</button>
</form>
