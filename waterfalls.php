<?php
session_start();
include("koneksi.php");
$role = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$query = "SELECT d.*, dg.gambar as additional_image 
          FROM destinasi d 
          LEFT JOIN destinasi_gambar dg ON d.id = dg.destinasi_id 
          WHERE d.kategori = 'air terjun'
          GROUP BY d.id";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Waterfalls</title>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <style>
        .button {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .delete-btn {
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <header class="hero">
        <nav class="nav-header" role="navigation" aria-label="Main navigation">
            <div class="brand-logo">YaHaYu</div>
            <div class="nav-menu">
                <a href="index.php" class="nav-item">Home</a>
                <div class="nav-item-with-icon" onclick="toggleDropdown()">
                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/c071fa65bfd4b98a705604af764ed18d0bca0822702f81df44640ac5a4aeb87d?placeholderIfAbsent=true&apiKey=820f30d49f024d318c8b29f3eaf6b5a7"
                        class="nav-icon"
                        alt=""
                        aria-hidden="true" />
                    <span class="nav-text">Destinations</span>
                    <div class="dropdown-menu" id="dropdownMenu" role="menu">
                        <a href="beaches.php" class="dropdown-item" role="menuitem">Beaches</a>
                        <a href="deserts.php" class="dropdown-item" role="menuitem">Deserts</a>
                        <a href="waterfalls.php" class="dropdown-item" role="menuitem">Waterfalls</a>
                        <a href="cultural-sites.php" class="dropdown-item" role="menuitem">Cultural Sites</a>
                        <a href="mountains.php" class="dropdown-item" role="menuitem">Mountains</a>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                            <a href="add_destinasi.php" class="dropdown-item" role="menuitem">Add Destination</a>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="myticket.php" class="nav-item">My Tiket</a>
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

    <section class="mountain-gallery" aria-label="Mountain Gallery">
        <h1 class="gallery-title">Waterfalls</h1>

        <?php
        if (mysqli_num_rows($result) > 0):
            while ($row = mysqli_fetch_assoc($result)):
                $main_image = '';
                if (!empty($row['gambar'])) {
                    if (strpos($row['gambar'], 'uploads/') === 0) {
                        $main_image = $row['gambar'];
                    } else {
                        $main_image = 'uploads/' . $row['gambar'];
                    }
                } else {
                    $main_image = 'images/default.png';
                }
        ?>
                <div class="container">
                    <div class="image">
                        <img src="<?php echo htmlspecialchars($main_image); ?>"
                            alt="<?php echo htmlspecialchars($row['nama_destinasi']); ?>"
                            onerror="this.src='images/default.png'"
                            loading="lazy" />
                    </div>
                    <div class="text-content">
                        <h3><?php echo htmlspecialchars($row['nama_destinasi']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?></p>
                        <p class="price">Rp. <?php echo number_format($row['harga'], 0, ',', '.'); ?>,00</p>
                    </div>
                    <div class="button">
                        <a href="detail_destinasi.php?id=<?php echo $row['id']; ?>">Explore More</a>
                        <?php if ($role === 'admin'): ?>
                            <a href="delete_destinasi.php?id=<?php echo $row['id']; ?>&source=<?php echo basename($_SERVER['PHP_SELF']); ?>"
                                class="delete-btn"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus destinasi ini?');">
                                Delete
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php
            endwhile;
        else:
            ?>
            <p class="no-data">No beach destinations available at the moment.</p>
        <?php endif; ?>
    </section>
    <!-- Footer Section-->
    <footer>
        <div class="footer-container">
            <div class="brand-section">
                <h2>YaHaYu</h2>
                <p>
                    YaHaYu connects you to Brazil's hidden gems, offering easy online
                    ticketing for unique travel experiences at top destinations across
                    the country.
                </p>
            </div>

            <div class="links-section">
                <div class="links-column">
                    <h3>Quick Links</h3>
                    <a href="index.php">Home</a>
                    <a href="#top-destinations">Top Destinations</a>
                    <a href="#discover">Discover</a>
                    <a href="#contact">Contact Us</a>
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
                        <img src="images/whatsapp.png" alt="WhatsApp" />
                        <img src="images/instagram.png" alt="Instagram" />
                        <img src="images//twitter.png" alt="Other" />
                    </div>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <p class="copyright">© 2024 YaHaYu. All Rights Reserved.</p>
    </footer>

    <script>
        function toggleDropdown() {
            const dropdownMenu = document.getElementById("dropdownMenu");
            if (dropdownMenu.style.display === "flex") {
                dropdownMenu.style.display = "none";
            } else {
                dropdownMenu.style.display = "flex";
            }
        }

        document.addEventListener("click", function(e) {
            const dropdown = document.getElementById("dropdownMenu");
            const trigger = document.querySelector(".nav-item-with-icon");
            if (!trigger.contains(e.target)) {
                dropdown.style.display = "none";
            }
        });
    </script>
</body>

</html>