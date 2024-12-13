<?php
session_start();
include("koneksi.php");

// Cek apakah pengguna sudah login dan memiliki role admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

function upload_foto($file)
{
    $target_dir = "uploads/";
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $newFileName;

    if ($file["size"] > 10485760) {
        return ["status" => false, "message" => "File terlalu besar (max 10mb)"];
    }

    // Validasi tipe file
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        return ["status" => false, "message" => "Hanya file JPG, JPEG, PNG & GIF yang diizinkan"];
    }

    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["status" => true, "file_name" => $target_file];
    } else {
        return ["status" => false, "message" => "Gagal mengupload file"];
    }
}

// Proses jika form disubmit
if (isset($_POST['submit'])) {
    // Ambil data dari form
    $nama_destinasi = $_POST['nama_destinasi'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];

    // Upload gambar utama
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $upload_result = upload_foto($_FILES['gambar']);

        if ($upload_result['status']) {
            $gambar_path = $upload_result['file_name'];

            // Insert data destinasi
            $sql = "INSERT INTO destinasi (nama_destinasi, deskripsi, harga, kategori, gambar) VALUES (?, ?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($koneksi, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssss", $nama_destinasi, $deskripsi, $harga, $kategori, $gambar_path);

                if (mysqli_stmt_execute($stmt)) {
                    $destinasi_id = mysqli_insert_id($koneksi);

                    // Upload gambar tambahan
                    $success = true;
                    if (isset($_FILES['gambar_tambahan'])) {
                        for ($i = 0; $i < count($_FILES['gambar_tambahan']['name']); $i++) {
                            if ($_FILES['gambar_tambahan']['error'][$i] == 0) {
                                $file = [
                                    "name" => $_FILES['gambar_tambahan']['name'][$i],
                                    "type" => $_FILES['gambar_tambahan']['type'][$i],
                                    "tmp_name" => $_FILES['gambar_tambahan']['tmp_name'][$i],
                                    "error" => $_FILES['gambar_tambahan']['error'][$i],
                                    "size" => $_FILES['gambar_tambahan']['size'][$i]
                                ];

                                $upload_result = upload_foto($file);

                                if ($upload_result['status']) {
                                    $gambar_tambahan_path = $upload_result['file_name'];
                                    $sql_tambahan = "INSERT INTO destinasi_gambar (destinasi_id, gambar) VALUES (?, ?)";
                                    $stmt_tambahan = mysqli_prepare($koneksi, $sql_tambahan);
                                    mysqli_stmt_bind_param($stmt_tambahan, "is", $destinasi_id, $gambar_tambahan_path);

                                    if (!mysqli_stmt_execute($stmt_tambahan)) {
                                        $success = false;
                                    }
                                    mysqli_stmt_close($stmt_tambahan);
                                }
                            }
                        }
                    }

                    if ($success) {
                        echo "<script>
                            alert('Destinasi berhasil ditambahkan!');
                            window.location.href='destinations.php';
                        </script>";
                    }
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            echo "Error: " . $upload_result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Destination</title>
    <style>
        /* ... style yang sudah ada ... */
        .image-preview {
            margin: 10px 0;
            display: flex;
            gap: 10px;
        }

        .image-preview img {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>

<body>
    <h1>Tambah Destinasi</h1>
    <form action="add_destinasi.php" method="POST" enctype="multipart/form-data">
        <label for="nama_destinasi">Nama Destinasi:</label><br>
        <input type="text" id="nama_destinasi" name="nama_destinasi" required><br><br>

        <label for="deskripsi">Deskripsi:</label><br>
        <textarea id="deskripsi" name="deskripsi" rows="4" cols="50" required></textarea><br><br>

        <label for="harga">Harga:</label><br>
        <input type="number" id="harga" name="harga" required><br><br>

        <label for="kategori">Kategori:</label><br>
        <select id="kategori" name="kategori" required>
            <option value="pantai">Beaches</option>
            <option value="gurun">Deserts</option>
            <option value="air terjun">Waterfallst</option>
            <option value="Cultural Sites">Cultural Sites</option>
            <option value="pegunungan">Mountains</option>
        </select><br><br>

        <label for="gambar">Upload Gambar Utama:</label><br>
        <input type="file" id="gambar" name="gambar" required onchange="previewImage(this, 'mainImagePreview')"><br>
        <div id="mainImagePreview" class="image-preview"></div><br>

        <label for="gambar_tambahan">Upload Gambar Tambahan (Max 3):</label><br>
        <input type="file" id="gambar_tambahan" name="gambar_tambahan[]" multiple accept="image/*" onchange="previewMultipleImages(this, 'additionalImagePreview')"><br>
        <div id="additionalImagePreview" class="image-preview"></div><br>

        <button type="submit" name="submit">Tambah Destinasi</button>
    </form>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            preview.innerHTML = '';

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewMultipleImages(input, previewId) {
            const preview = document.getElementById(previewId);
            preview.innerHTML = '';

            if (input.files) {
                const maxFiles = 3;
                const files = Array.from(input.files).slice(0, maxFiles);

                files.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });

                if (input.files.length > maxFiles) {
                    alert('Maksimal 3 gambar tambahan yang diperbolehkan');
                }
            }
        }
    </script>
</body>

</html>