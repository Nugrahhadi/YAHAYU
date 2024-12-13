<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    mysqli_begin_transaction($koneksi);

    try {
        //Hapus gambar tambahan dari folder dan database
        $query_gambar_tambahan = "SELECT gambar FROM destinasi_gambar WHERE destinasi_id = ?";
        $stmt_gambar = mysqli_prepare($koneksi, $query_gambar_tambahan);
        mysqli_stmt_bind_param($stmt_gambar, "i", $id);
        mysqli_stmt_execute($stmt_gambar);
        $result_gambar = mysqli_stmt_get_result($stmt_gambar);

        while ($row_gambar = mysqli_fetch_assoc($result_gambar)) {
            if ($row_gambar['gambar']) {
                $gambar_path = $row_gambar['gambar'];
                if (file_exists($gambar_path)) {
                    unlink($gambar_path);
                }
            }
        }

        // Hapus record gambar tambahan dari database
        $delete_tambahan = "DELETE FROM destinasi_gambar WHERE destinasi_id = ?";
        $stmt_delete_tambahan = mysqli_prepare($koneksi, $delete_tambahan);
        mysqli_stmt_bind_param($stmt_delete_tambahan, "i", $id);
        mysqli_stmt_execute($stmt_delete_tambahan);

        // Hapus gambar utama destinasi
        $query = "SELECT gambar FROM destinasi WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row && $row['gambar']) {
            $gambar_path = $row['gambar'];
            if (file_exists($gambar_path)) {
                unlink($gambar_path);
            }
        }

        // Hapus data destinasi
        $query_delete = "DELETE FROM destinasi WHERE id = ?";
        $stmt_delete = mysqli_prepare($koneksi, $query_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $id);
        mysqli_stmt_execute($stmt_delete);

        mysqli_commit($koneksi);

        $_SESSION['success_message'] = "Destinasi berhasil dihapus.";
        header("Location: " . $_SERVER['HTTP_REFERER'] ?? 'index.php');
        exit;
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['error_message'] = "Terjadi kesalahan saat menghapus destinasi: " . $e->getMessage();
        header("Location: " . $_SERVER['HTTP_REFERER'] ?? 'index.php');
        exit;
    }

    if (isset($stmt_gambar)) mysqli_stmt_close($stmt_gambar);
    if (isset($stmt_delete_tambahan)) mysqli_stmt_close($stmt_delete_tambahan);
    if (isset($stmt)) mysqli_stmt_close($stmt);
    if (isset($stmt_delete)) mysqli_stmt_close($stmt_delete);
}

mysqli_close($koneksi);
