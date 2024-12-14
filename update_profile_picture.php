<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user']['id'];

    if (isset($_POST['delete_photo'])) {
        $default_photo = 'images/default-avatar.jpg'; 

        $update_query = "UPDATE akun SET foto_profil = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_query);
        mysqli_stmt_bind_param($update_stmt, "si", $default_photo, $user_id);

        if (mysqli_stmt_execute($update_stmt)) {
            $_SESSION['user']['foto_profil'] = $default_photo;
            header("Location: profile.php?success=2");
            exit;
        } else {
            $error = "Gagal menghapus foto profil. Coba lagi!";
        }
    }

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_picture']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            $newname = 'profile_' . $user_id . '_' . time() . '.' . $filetype;
            $upload_path = 'uploads/profiles/' . $newname;

            if (!file_exists('uploads/profiles')) {
                mkdir('uploads/profiles', 0777, true);
            }

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                $query = "UPDATE akun SET foto_profil = ? WHERE id = ?";
                $stmt = mysqli_prepare($koneksi, $query);
                mysqli_stmt_bind_param($stmt, "si", $upload_path, $user_id);

                if (mysqli_stmt_execute($stmt)) {
                    header("Location: profile.php?success=2");
                    exit;
                }
            }
        }
    }

    header("Location: profile.php?error=1");
    exit;
}
