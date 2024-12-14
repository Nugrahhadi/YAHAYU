<?php
session_start();
include("koneksi.php");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    $query = "SELECT foto_profil FROM akun WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    $profile_folder = "uploads/profile/";
    $profile_image = "images/default-avatar.jpg"; 

    if (!empty($user['foto_profil']) && file_exists($profile_folder . $user['foto_profil'])) {
        $profile_image = $profile_folder . $user['foto_profil'];
    }
    $_SESSION['user']['profile_picture'] = $profile_image;
}


$query = "SELECT * FROM destinasi LIMIT 3";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yahayu</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Top Destination Styles */
        .destinations-section {
            padding: 80px 5%;
            background: #FFCC00;
            position: relative;
            overflow: hidden;
        }

        .destinations-carousel {
            position: relative;
            max-width: 1400px;
            margin: 0 auto;
            overflow: hidden;
        }

        .destinations-header {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
        }

        .destinations-title {
            color: #367b54;
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            display: inline-block;
        }

        .destinations-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: #367b54;
            border-radius: 2px;
        }

        .destinations-subtitle {
            color: #666;
            font-size: 20px;
            font-weight: 500;
        }

        .destinations-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .destination-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 600px;
            display: flex;
            flex-direction: column;
        }

        .destination-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
            opacity: 1;
            z-index: 1;
        }

        .destination-card.active {
            transform: scale(1.05);
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.2);
            z-index: 2;
        }

        .destinations-section::before,
        .destinations-section::after {
            display: none;
        }

        .card-image {
            position: relative;
            height: 250px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .destinations-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }

        .destinations-image.main {
            position: relative;
            z-index: 1;
        }

        .destinations-image.overlay {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            z-index: 2;
        }

        .card-image:hover .destinations-image.main {
            opacity: 0;
        }

        .card-image:hover .destinations-image.overlay {
            opacity: 1;
        }

        .image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            z-index: 3;
        }

        .category-tag {
            background: #e8f5e9;
            color: #367b54;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .card-content {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex: 1;
            position: relative;
        }

        .card-content h3 {
            color: #367b54;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 12px;
            display: -webkit-box;
            overflow: hidden;
            height: 100px;
        }

        .description {
            color: #666;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 20px;
            height: 96px;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 15px;
            border-top: 1.5px solid #eee;
            position: absolute;
            bottom: 24px;
            left: 24px;
            right: 24px;
        }

        .price {
            font-size: 20px;
            font-weight: 700;
            color: #367b54;
        }

        .explore-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #367b54;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .explore-btn:hover {
            background: #2a6043;
            transform: translateX(5px);
        }

        .arrow {
            transition: transform 0.3s ease;
        }

        .explore-btn:hover .arrow {
            transform: translateX(5px);
        }

        @media (max-width: 1200px) {
            .destinations-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .destination-card {
                min-width: calc(50% - 20px);
                flex: 0 0 calc(50% - 20px);
            }
        }

        @media (max-width: 768px) {
            .destinations-section {
                padding: 60px 20px;
            }

            .destinations-container {
                grid-template-columns: 1fr;
            }

            .destinations-title {
                font-size: 36px;
            }

            .card-image {
                height: 200px;
            }

            .card-content {
                padding: 20px;
            }

            .card-footer {
                flex-direction: column;
                gap: 15px;
            }

            .explore-btn {
                width: 100%;
                justify-content: center;
            }

            .destination-card {
                min-width: calc(100% - 20px);
                flex: 0 0 calc(100% - 20px);
            }
        }
    </style>
</head>

<body>
    <header class="hero">
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
                        <a href="beaches.php?category=pantai" class="dropdown-item" role="menuitem">Beaches</a>
                        <a href="deserts.php?category=gurun" class="dropdown-item" role="menuitem">Deserts</a>
                        <a href="waterfalls.php?category=air terjun" class="dropdown-item" role="menuitem">Waterfalls</a>
                        <a href="cultural-sites.php?category=Cultural Sites" class="dropdown-item" role="menuitem">Cultural Sites</a>
                        <a href="mountains.php?category=pegunungan" class="dropdown-item" role="menuitem">Mountains</a>
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
                    <div class="buttonP">
                        <a href="profile.php">
                            <img src="<?php echo htmlspecialchars($_SESSION['user']['profile_picture']); ?>" alt="Profile" class="profile-icon">
                        </a>
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

        <div class="hero-content">
            <p class="hero-tagline">
                Hey, Let's Jump into The
                <span style="color: rgba(30, 140, 69, 1)">Green</span>
                and
                <span style="color: rgba(255, 99, 71, 1)">Yellow</span>
                Country with Yahayu!
            </p>
            <div class="hero-brand">YaHaYu</div>
            <div class="hero-motto">Journey, Joy, Jump!</div>
            <button class="hero-cta" onclick="scrollToDestinations()">Start Your Journey!</button>
        </div>
    </header>

    <!-- Top Destination -->
    <section class="destinations-section" id="destinations">
        <div class="destinations-header">
            <h2 class="destinations-title">TOP DESTINATIONS</h2>
            <p class="destinations-subtitle">Where Will Your Next Adventure Take You?</p>
        </div>

        <div class="destinations-container">
            <?php
            $query = "SELECT * FROM destinasi LIMIT 3";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)):
                $query_gambar = "SELECT gambar FROM destinasi_gambar WHERE destinasi_id = ? LIMIT 1";
                $stmt_gambar = mysqli_prepare($koneksi, $query_gambar);
                mysqli_stmt_bind_param($stmt_gambar, "i", $row['id']);
                mysqli_stmt_execute($stmt_gambar);
                $result_gambar = mysqli_stmt_get_result($stmt_gambar);
                $gambar_tambahan = mysqli_fetch_assoc($result_gambar);
            ?>
                <div class="destination-card">
                    <div class="card-image">
                        <img src="<?php echo htmlspecialchars($row['gambar']); ?>"
                            alt="<?php echo htmlspecialchars($row['nama_destinasi']); ?>"
                            class="destinations-image main">
                        <?php if ($gambar_tambahan): ?>
                            <img src="<?php echo htmlspecialchars($gambar_tambahan['gambar']); ?>"
                                alt="Additional view of <?php echo htmlspecialchars($row['nama_destinasi']); ?>"
                                class="destinations-image overlay">
                        <?php endif; ?>
                        <div class="image-overlay">
                            <span class="category-tag"><?php echo htmlspecialchars($row['kategori']); ?></span>
                        </div>
                    </div>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($row['nama_destinasi']); ?></h3>
                        <p class="description"><?php echo nl2br(htmlspecialchars(substr($row['deskripsi'], 0, 100))); ?>...</p>
                        <div class="card-footer">
                            <div class="price">Rp. <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>
                            <a href="detail_destinasi.php?id=<?php echo $row['id']; ?>" class="explore-btn">
                                Explore More
                                <span class="arrow">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php
                mysqli_stmt_close($stmt_gambar);
            endwhile;
            mysqli_stmt_close($stmt);
            ?>
        </div>
    </section>

    <!-- Service -->
    <section class="service-section">
        <div class="boxes-container">
            <div class="features-box">
                <div class="feature-item">
                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/f49a5a4182832d85dd0ef06634574623088c17f42b6c5a9536f1503910ec8a3b" class="feature-icon" alt="Online ticket booking icon" />
                    <h2 class="feature-title">Pemesanan Tiket <br> Wisata Online</h2>
                </div>

                <div class="feature-item">
                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/2466cca79d23c3193066e24d1787c623ad5768a8d677642a12bf5a3366ca7044" class="feature-icon" alt="Hidden destinations icon" />
                    <h2 class="feature-title">Destinasi Wisata <br> Tersembunyi & Unik</h2>
                </div>

                <div class="feature-item">
                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/34e796a095c5d3580c861bd9fbaf76d641b3fc8c390c39d4550b73f95fa33e0e" class="feature-icon" alt="Multiple destinations icon" />
                    <h2 class="feature-title">Beragam Pilihan <br> Destinasi</h2>
                </div>

                <div class="feature-item">
                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/d469ce4109af54fe2445c866f8d3bcbb3527a7169c3aed92d523d4992a5f7d8d" class="feature-icon" alt="Easy payment icon" />
                    <h2 class="feature-title">Kemudahan Akses <br> dan Pembayaran</h2>
                </div>
            </div>

            <div class="about-box">
                <h1 class="about-heading">Get to Know Us</h1>
                <p class="about-description">
                    YaHaYu adalah platform penghubung Anda dengan destinasi wisata unik di Brasil.<br /><br />
                    Bekerja sama dengan berbagai mitra destinasi terkemuka, YaHaYu memudahkan Anda untuk membeli tiket secara online dengan cara yang praktis dan efisien.
                </p>
            </div>
        </div>
    </section>

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
</body>

</html>