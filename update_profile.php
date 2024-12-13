<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Ambil data profil pengguna dari database
$query = "SELECT username, nama, email, nomor_telepon, tanggal_lahir FROM akun WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$profile = mysqli_fetch_assoc($result);

// Update form profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nomor_telepon = $_POST['nomor_telepon'];
    $tanggal = $_POST['tanggal'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];

    if (!checkdate($bulan, $tanggal, $tahun)) {
        $error = "Tanggal lahir tidak valid!";
    } else {
        $tanggal_lahir = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' . str_pad($tanggal, 2, '0', STR_PAD_LEFT);

        // Cek apakah ada password baru
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        // Validasi password dan konfirmasi password
        if (!empty($password) && $password !== $confirm_password) {
            $error = "Password dan konfirmasi password tidak cocok!";
        } else {
            // Jika password baru diisi, enkripsi password dan update
            if (!empty($password)) {
                $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                $update_query = "UPDATE akun SET username = ?, nama = ?, email = ?, nomor_telepon = ?, tanggal_lahir = ?, password = ? WHERE id = ?";
                $update_stmt = mysqli_prepare($koneksi, $update_query);
                mysqli_stmt_bind_param($update_stmt, "ssssssi", $username, $nama, $email, $nomor_telepon, $tanggal_lahir, $password_hashed, $user_id);
            } else {
                // Jika tidak mengganti password, update hanya data lainnya
                $update_query = "UPDATE akun SET username = ?, nama = ?, email = ?, nomor_telepon = ?, tanggal_lahir = ? WHERE id = ?";
                $update_stmt = mysqli_prepare($koneksi, $update_query);
                mysqli_stmt_bind_param($update_stmt, "sssssi", $username, $nama, $email, $nomor_telepon, $tanggal_lahir, $user_id);
            }

            if (mysqli_stmt_execute($update_stmt)) {
                $_SESSION['user']['nama'] = $nama;
                $_SESSION['user']['email'] = $email;
                header("Location: profile.php?success=1");
                exit;
            } else {
                $error = "Gagal memperbarui profil. Coba lagi!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - YaHaYu</title>
    <link rel="stylesheet" href="style.css">
    <style>
        main {
            background: linear-gradient(to top, #FFE588, #FFCC00);
            min-height: 100vh;
            padding: 120px 20px 40px;
            font-family: 'Poppins', sans-serif;
        }

        .update-container {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.9);
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
            font-size: 1em;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #FFE588;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: white;
            color: #333;
        }

        input:focus {
            outline: none;
            border-color: #1e8c45;
            box-shadow: 0 0 0 3px rgba(30, 140, 69, 0.2);
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

        .error-message {
            background: #fff3f3;
            color: #dc3545;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            color: #1e8c45;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-button:hover {
            color: #156332;
        }

        .back-button:before {
            content: '←';
            margin-right: 8px;
        }

        /* Hover effects for inputs */
        .form-group {
            position: relative;
        }

        .form-group:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #1e8c45;
            transition: width 0.3s ease;
            transform: translateX(-50%);
        }

        .form-group:hover:after {
            width: 100%;
        }

        .date-group {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .update-container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 2em;
            }

            input {
                padding: 10px 14px;
            }
        }

        @media (max-width: 480px) {
            main {
                padding: 100px 15px 30px;
            }

            h1 {
                font-size: 1.8em;
            }

            .update-container {
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
                        <a href="deserts.php?category=Deserts" class="dropdown-item" role="menuitem">Deserts</a>
                        <a href="waterfalls.php?category=air terjun" class="dropdown-item" role="menuitem">Waterfalls</a>
                        <a href="cultural-sites.php?category=Cultural Sites" class="dropdown-item" role="menuitem">Cultural Sites</a>
                        <a href="mountains.php?category=Mountains" class="dropdown-item" role="menuitem">Mountains</a>
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
        <div class="update-container">
            <a href="profile.php" class="back-button">Kembali ke Profil</a>
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" id="successAlert">
                    <p>Profil berhasil diperbarui!</p>
                </div>
            <?php endif; ?>
            <h1>Update Profile</h1>

            <?php if (isset($error)): ?>
                <div class="error-message">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form action="update_profile.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($profile['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($profile['nama']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profile['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="nomor_telepon">Nomor Telepon</label>
                    <input type="tel" id="nomor_telepon" name="nomor_telepon" value="<?php echo htmlspecialchars($profile['nomor_telepon']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <div class="date-group">
                        <input type="number" name="tanggal" placeholder="Tanggal" min="1" max="31" value="<?php echo htmlspecialchars(explode('-', $profile['tanggal_lahir'])[2]); ?>" required>
                        <input type="number" name="bulan" placeholder="Bulan" min="1" max="12" value="<?php echo htmlspecialchars(explode('-', $profile['tanggal_lahir'])[1]); ?>" required>
                        <input type="number" name="tahun" placeholder="Tahun" min="1900" max="2024" value="<?php echo htmlspecialchars(explode('-', $profile['tanggal_lahir'])[0]); ?>" required>
                    </div>
                </div>

                <!-- password and confirmation -->
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi password baru">
                </div>

                <button type="submit" name="submit">Perbarui Profil</button>
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
                    <a href="culturalSites.php">Cultural Sites</a>
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
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.getElementById('successAlert');
            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.opacity = '0';
                    setTimeout(function() {
                        successAlert.style.display = 'none';
                        const url = new URL(window.location.href);
                        url.searchParams.delete('success');
                        window.history.replaceState({}, '', url);
                    }, 300);
                }, 3000);
            }
        });
    </script>
</body>

</html>