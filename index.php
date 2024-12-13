<?php
session_start();
include("koneksi.php");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
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

    <section class="destinations-section" id="destinations" role="region" aria-labelledby="destinations-heading">
        <div class="destinations-header">
            <h2 id="destinations-heading" class="destinations-title">TOP DESTINATIONS</h2>
            <p class="destinations-subtitle">Where Will Your Next Adventure Take You?</p>
        </div>
        <div class="destinations-grid">
            <?php
            $query = "SELECT * FROM destinasi LIMIT 3";
            $result = mysqli_query($koneksi, $query);

            while ($row = mysqli_fetch_assoc($result)):
            ?>
                <div class="destination-card" onclick="window.location.href='detail_destinasi.php?id=<?php echo $row['id']; ?>'">
                    <img src="<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_destinasi']); ?>" class="destinations-image">
                </div>
            <?php endwhile; ?>
        </div>
    </section>

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