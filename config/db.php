<?php 
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'expense_db';

    $connection = mysqli_connect($host, $user, $pass, $db);

    if ($connection -> connect_error) {
        die("Koneksi gagal:" . mysqli_connect_error());
    }
?>