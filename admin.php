<?php
include("koneksi.php");

// Akun admin
$admin_email = "admin@brazil.com";
$admin_password = password_hash("Admin123_", PASSWORD_DEFAULT);
$role = "admin";
$username = "admin";
$nama = "Admin Utama";
$nomor_telepon = "081256786543";
$tanggal_lahir = "1999-01-02";

$sql = "INSERT INTO akun (email, password, role, username, nama, nomor_telepon, tanggal_lahir) VALUES ('$admin_email', '$admin_password', '$role', '$username', '$nama', '$nomor_telepon', '$tanggal_lahir')";

if (mysqli_query($koneksi, $sql)) {
    echo "Akun admin berhasil ditambahkan.";
} else {
    echo "Error: " . mysqli_error($koneksi);
}

mysqli_close($koneksi);
