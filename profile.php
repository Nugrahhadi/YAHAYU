<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$query = "SELECT username, nama, email, nomor_telepon, tanggal_lahir, foto_profil FROM akun WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$profile = mysqli_fetch_assoc($result);

//update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nomor_telepon = $_POST['nomor_telepon'];
    $tanggal_lahir = $_POST['tanggal_lahir'];

    //update data ke database
    $update_query = "UPDATE akun SET username = ?, nama = ?, email = ?, nomor_telepon = ?, tanggal_lahir = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($koneksi, $update_query);
    mysqli_stmt_bind_param($update_stmt, "sssssi", $username, $nama, $email, $nomor_telepon, $tanggal_lahir, $user_id);

    if (mysqli_stmt_execute($update_stmt)) {
        $_SESSION['user']['nama'] = $nama;
        $_SESSION['user']['email'] = $email;
        header("Location: profile.php?succes=1");
        exit;
    } else {
        $error = "Gagal memperbarui profile. Coba lagi!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - YaHaYu</title>
    <link rel="stylesheet" href="style.css">
    <style>
        main {
            background: linear-gradient(to top, #FFCC00, #FFDD55);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            padding-top: 30px;
        }

        /* Profile title */
        .profile-title {
            color: #367B54;
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
            margin-top: 10px;
            text-align: center;
        }

        /* Profile container */
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 40px;
            padding: 40px;
            align-items: flex-start;
        }

        .profile-image-section {
            flex: 0 0 350px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profile-image-section h1 {
            padding-top: 10px;
        }

        .profile-image {
            width: 350px;
            height: 350px;
            border-radius: 25px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-image:hover {
            transform: scale(1.02);
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .profile-image img:hover {
            transform: scale(1.05);
        }

        .profile-details {
            flex: 1;
            background-color: rgba(255, 204, 0, 0.3);
            border-radius: 25px;
            padding: 30px;
            margin-top: 10px;
        }

        .detail-item {
            margin-bottom: 25px;
        }

        .detail-item:hover {
            transform: translateX(10px);
        }

        .detail-label {
            color: #367B54;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .detail-value {
            color: #367B54;
            font-size: 16px;
            background-color: rgba(255, 255, 255, 0.5);
            padding: 10px 15px;
            border-radius: 8px;
        }

        /* profile button */
        .edit-profile-btn {
            background-color: #367B54;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            float: right;
            transition: background-color 0.3s;
        }

        .edit-profile-btn:hover {
            background-color: #2a6043;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(54, 123, 84, 0.3);
        }

        .success-message,
        .error-message {
            max-width: 1000px;
            margin: 20px auto;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .edit-form {
            max-width: 1000px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 25px;
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #367B54;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #367B54;
            border-radius: 8px;
            font-size: 16px;
        }

        .change-photo-btn {
            width: 100%;
            background-color: #367B54;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .change-photo-btn:hover {
            background-color: #2a6043;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(54, 123, 84, 0.2);
        }

        .upload-form {
            text-align: center;
            margin-top: 10px;
        }

        /* Button delete foto */
        .delete-photo-btn {
            width: 100%;
            background-color: #d9534f;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .delete-photo-btn:hover {
            background-color: #c9302c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(217, 53, 45, 0.2);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
                align-items: center;
            }

            .profile-image-section {
                flex: 0 0 auto;
                width: 100%;
                max-width: 300px;
            }

            .profile-details {
                width: 100%;
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
        <main>
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" id="successAlert1">
                    <p>Profil berhasil diperbarui!</p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success']) && $_GET['success'] == '2'): ?>
                <div class="success-message" id="successAlert2">
                    <p>Foto profil berhasil diperbarui!</p>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="error-message">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <div class="profile-container">
                <div class="profile-image-section">
                    <h1 class="profile-title">PROFIL</h1>
                    <div class="profile-image">
                        <img src="<?php echo !empty($profile['foto_profil']) ? htmlspecialchars($profile['foto_profil']) : 'images/default-avatar.png'; ?>"
                            alt="Profile Picture">
                    </div>
                    <form action="update_profile_picture.php" method="POST" enctype="multipart/form-data" class="upload-form">
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*" style="display: none;">
                        <button type="button" onclick="document.getElementById('profile_picture').click()" class="change-photo-btn">
                            Ganti Foto Profil
                        </button>
                        <button type="submit" name="delete_photo" value="1" class="delete-photo-btn">
                            Hapus Foto Profil
                        </button>
                    </form>
                </div>

                <div class="profile-details">
                    <div class="detail-item">
                        <div class="detail-label">Username:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($profile['username']); ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Nama Lengkap:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($profile['nama']); ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Email:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($profile['email']); ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Nomor Telepon:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($profile['nomor_telepon']); ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Tanggal Lahir:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($profile['tanggal_lahir']); ?></div>
                    </div>

                    <form action="update_profile.php" method="GET">
                        <button type="submit" name="edit" value="1" class="edit-profile-btn">Edit Profil</button>
                    </form>
                </div>
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
                const successAlert1 = document.getElementById('successAlert1');
                const successAlert2 = document.getElementById('successAlert2');

                function hideAlert(alertElement) {
                    if (alertElement) {
                        setTimeout(function() {
                            alertElement.style.opacity = '0';
                            setTimeout(function() {
                                alertElement.style.display = 'none';
                                const url = new URL(window.location.href);
                                url.searchParams.delete('success');
                                window.history.replaceState({}, '', url);
                            }, 200);
                        }, 1000);
                    }
                }

                hideAlert(successAlert1);
                hideAlert(successAlert2);
            });

            document.getElementById('profile_picture').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.querySelector('.profile-image img').src = e.target.result;
                    }
                    reader.readAsDataURL(file);

                    this.closest('form').submit();
                }
            });
        </script>
</body>

</html>