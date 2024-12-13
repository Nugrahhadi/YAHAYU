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
    <title>Add Destination - YaHaYu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        main {
            background: linear-gradient(to top, #FFE588, #FFCC00);
            min-height: 100vh;
            padding: 120px 20px 40px;
            font-family: 'Poppins', sans-serif;
        }

        .add-container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #1e8c45;
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 40px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            color: #1e8c45;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 1.1em;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #FFE588;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: white;
            color: #333;
            font-family: 'Poppins', sans-serif;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%231e8c45' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #1e8c45;
            box-shadow: 0 0 0 3px rgba(30, 140, 69, 0.2);
        }

        .file-input-container {
            margin-bottom: 20px;
        }

        .file-input-label {
            display: inline-block;
            padding: 12px 20px;
            background: #1e8c45;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            background: #156332;
            transform: translateY(-2px);
        }

        input[type="file"] {
            display: none;
        }

        .image-preview {
            margin: 15px 0;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .image-preview img {
            max-width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .image-preview img:hover {
            transform: scale(1.05);
        }

        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background: #1e8c45;
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        button[type="submit"]:hover {
            background: #156332;
            transform: translateY(-2px);
        }

        .preview-label {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .add-container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 2em;
            }

            .image-preview img {
                max-width: 120px;
                height: 120px;
            }
        }

        @media (max-width: 480px) {
            main {
                padding: 100px 15px 30px;
            }

            h1 {
                font-size: 1.8em;
            }

            .add-container {
                padding: 20px 15px;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav class="nav-header" role="navigation" aria-label="Main navigation">
            <div class="brand-logo">YaHaYu</div>

            <div class="hamburger" onclick="toggleMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <div class="nav-menu" id="navMenu">
                <a href="index.php" class="nav-item">Home</a>
                <div class="nav-item-with-icon" onclick="toggleDropdown(event)">
                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/c071fa65bfd4b98a705604af764ed18d0bca0822702f81df44640ac5a4aeb87d" class="nav-icon" alt="" />
                    <span class="nav-text">Destinations</span>
                    <div class="dropdown-menu" id="dropdownMenu" role="menu">
                        <a href="beaches.php?category=beaches" class="dropdown-item" role="menuitem">Beaches</a>
                        <a href="destinations.php?category=Deserts" class="dropdown-item" role="menuitem">Deserts</a>
                        <a href="destinations.php?category=air terjun" class="dropdown-item" role="menuitem">Waterfalls</a>
                        <a href="destinations.php?category=Cultural Sites" class="dropdown-item" role="menuitem">Cultural Sites</a>
                        <a href="destinations.php?category=Mountains" class="dropdown-item" role="menuitem">Mountains</a>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                            <a href="add_destinasi.php" class="dropdown-item" role="menuitem">Add Destination</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="myticket.php" class="nav-item">My Tickets</a>
                <?php endif; ?>
            </div>

            <div class="button-container">
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="buttonL">
                        <a href="profile.php">Profile</a>
                    </div>
                    <div class="buttonR">
                        <a href="logout.php">Logout</a>
                    </div>
                <?php else: ?>
                    <div class="buttonL">
                        <a href="login.php">Login</a>
                    </div>
                    <div class="buttonR">
                        <a href="register_pengguna.php">Sign Up</a>
                    </div>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <div class="add-container">
            <h1>Add New Destination</h1>
            <form action="add_destinasi.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama_destinasi">Destination Name</label>
                    <input type="text" id="nama_destinasi" name="nama_destinasi" required placeholder="Enter destination name">
                </div>

                <div class="form-group">
                    <label for="deskripsi">Description</label>
                    <textarea id="deskripsi" name="deskripsi" required placeholder="Describe the destination"></textarea>
                </div>

                <div class="form-group">
                    <label for="harga">Price (IDR)</label>
                    <input type="number" id="harga" name="harga" required placeholder="Enter price">
                </div>

                <div class="form-group">
                    <label for="kategori">Category</label>
                    <select id="kategori" name="kategori" required>
                        <option value="" disabled selected>Select a category</option>
                        <option value="pantai">Beaches</option>
                        <option value="gurun">Deserts</option>
                        <option value="air terjun">Waterfalls</option>
                        <option value="Cultural Sites">Cultural Sites</option>
                        <option value="pegunungan">Mountains</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Main Image</label>
                    <div class="file-input-container">
                        <label for="gambar" class="file-input-label">Choose Main Image</label>
                        <input type="file" id="gambar" name="gambar" required onchange="previewImage(this, 'mainImagePreview')">
                    </div>
                    <div id="mainImagePreview" class="image-preview"></div>
                </div>

                <div class="form-group">
                    <label>Additional Images (Max 2)</label>
                    <div class="file-input-container">
                        <label for="gambar_tambahan" class="file-input-label">Choose Additional Images</label>
                        <input type="file" id="gambar_tambahan" name="gambar_tambahan[]" multiple accept="image/*" onchange="previewMultipleImages(this, 'additionalImagePreview')">
                    </div>
                    <div id="additionalImagePreview" class="image-preview"></div>
                    <p class="preview-label">Preview will appear here</p>
                </div>

                <button type="submit" name="submit">Add Destination</button>
            </form>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <div class="brand-section">
                <a href="index.php" class="footer-logo">YaHaYu</a>
                <p>YaHaYu connects you to Brazil's hidden gems, offering easy online ticketing for unique travel experiences at top destinations across the country.</p>
            </div>

            <div class="links-section">
                <div class="links-column">
                    <h3>Quick Links</h3>
                    <a href="index.php">Home</a>
                    <a href="#destinations">Top Destinations</a>
                    <a href="#services">Our Services</a>
                    <a href="#about">About Us</a>
                </div>

                <div class="links-column">
                    <h3>Destinations</h3>
                    <a href="beaches.php">Beaches</a>
                    <a href="deserts.php">Deserts</a>
                    <a href="waterfalls.php">Waterfalls</a>
                    <a href="cultural-sites.php">Cultural Sites</a>
                    <a href="mountains.php">Mountains</a>
                </div>

                <div class="links-column contact-column">
                    <h3>Contact Us</h3>
                    <div class="contact-item">
                        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/d02e5e8c7df9f4016d0ff3e28c86a52193209ebc4bbd0391f58c5b66eedcc255" alt="Phone">
                        <a href="tel:+628121113141">+62 8121113141</a>
                    </div>
                    <div class="contact-item">
                        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/11eb0a8e4065b51bdab3e36a60f9ccc2bd492d97f87283099457649c5fd63249" alt="Email">
                        <a href="mailto:ahad@yahayu.ac.id">ahad@yahayu.ac.id</a>
                    </div>
                    <div class="contact-item">
                        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/fb297e302cb7e6e368853a8a1bc89afd264f17aa9c3be39ff3bf9ef96cbbd97a" alt="Location">
                        <span>Purbalingga, Central Java</span>
                    </div>
                    <div class="social-media">
                        <a href="#" class="social-link" aria-label="WhatsApp">
                            <img src="images/whatsapp.png" alt="WhatsApp">
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <img src="images/instagram.png" alt="Instagram">
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <img src="images/twitter.png" alt="Twitter">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider"></div>
        <p class="copyright">© 2024 YaHaYu. All Rights Reserved.</p>
    </footer>

    <script src="main.js"></script>

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