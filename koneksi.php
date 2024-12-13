<?php
$koneksi = mysqli_connect("localhost", "root", "", "wisata_brazil");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
