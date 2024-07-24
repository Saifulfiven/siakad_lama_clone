<?php
$dbs = [
  'server' => 'localhost',
  'username' => 'root',
  'password' => '',
  'database' => 'siakad'
];

// Buat koneksi
$conn = new mysqli($dbs['server'], $dbs['username'], $dbs['password'], $dbs['database']);
// Cek Koneksi
if ($conn->connect_error) {
    die("Koneksi ke database bermasalah: " . $conn->connect_error);
}
?>