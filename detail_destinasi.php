<?php
session_start();
include("koneksi.php");

// Pastikan ID destinasi diterima melalui URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk mengambil data destinasi berdasarkan ID
    $query = "SELECT * FROM destinasi WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $destinasi = mysqli_fetch_assoc($result);

    if (!$destinasi) {
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}

$query_gambar = "SELECT gambar FROM destinasi_gambar WHERE destinasi_id = ?";
$stmt_gambar = mysqli_prepare($koneksi, $query_gambar);
mysqli_stmt_bind_param($stmt_gambar, "i", $id);
mysqli_stmt_execute($stmt_gambar);
$result_gambar = mysqli_stmt_get_result($stmt_gambar);
$gambar_tambahan = mysqli_fetch_all($result_gambar, MYSQLI_ASSOC);
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($destinasi['nama_destinasi']); ?> - YaHaYu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="detail_destinasi.css">
</head>

<body>
    <header class="hero">
        <nav class="nav-header" role="navigation" aria-label="Main navigation">
            <div class="brand-logo">YaHaYu</div>
            <div class="nav-menu">
                <a href="index.php" class="nav-item">Home</a>
                <div class="nav-item-with-icon" onclick="toggleDropdown()">
                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/c071fa65bfd4b98a705604af764ed18d0bca0822702f81df44640ac5a4aeb87d?placeholderIfAbsent=true&apiKey=820f30d49f024d318c8b29f3eaf6b5a7" class="nav-icon" alt="" aria-hidden="true" />
                    <span class="nav-text">Destinations</span>
                    <div class="dropdown-menu" id="dropdownMenu" role="menu">
                        <a href="beaches.php" class="dropdown-item" role="menuitem">Beaches</a>
                        <a href="desert.php" class="dropdown-item" role="menuitem">Deserts</a>
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

    <div class="container">
        <div class="image-grid">
            <!-- Left image -->
            <div class="grid-column side">
                <div class="grid-image">
                    <img src="<?php echo htmlspecialchars($destinasi['gambar']); ?>"
                        alt="<?php echo htmlspecialchars($destinasi['nama_destinasi']); ?>">
                </div>
            </div>

            <!-- Center image (larger) -->
            <div class="grid-column middle">
                <div class="grid-image">
                    <?php if (!empty($gambar_tambahan)): ?>
                        <img src="<?php echo htmlspecialchars($gambar_tambahan[0]['gambar']); ?>"
                            alt="Main view">
                    <?php else: ?>
                        <img src="<?php echo htmlspecialchars($destinasi['gambar']); ?>"
                            alt="<?php echo htmlspecialchars($destinasi['nama_destinasi']); ?>">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right image -->
            <div class="grid-column side">
                <div class="grid-image">
                    <?php if (!empty($gambar_tambahan[1])): ?>
                        <img src="<?php echo htmlspecialchars($gambar_tambahan[1]['gambar']); ?>"
                            alt="Additional view">
                    <?php else: ?>
                        <img src="<?php echo htmlspecialchars($destinasi['gambar']); ?>"
                            alt="<?php echo htmlspecialchars($destinasi['nama_destinasi']); ?>">
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h1><?php echo htmlspecialchars($destinasi['nama_destinasi']); ?></h1>

            <p class="description">
                <?php echo nl2br(htmlspecialchars($destinasi['deskripsi'])); ?>
            </p>

            <div class="info-boxes">
                <div class="info-box">08.00-18.00</div>
                <div class="info-box"><?php echo htmlspecialchars($destinasi['kategori']); ?></div>
                <div class="info-box">Rp. <?php echo number_format($destinasi['harga'], 0, ',', '.'); ?>,00</div>
            </div>

            <?php if (isset($_SESSION['user'])): ?>
                <a href="book_ticket.php?id=<?php echo $destinasi['id']; ?>" class="buy-button">Buy Ticket</a>
            <?php else: ?>
                <a href="login.php" class="buy-button">Login to Book</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer Section-->
    <footer>
        <div class="footer-container">
            <div class="brand-section">
                <h2>YaHaYu</h2>
                <p>YaHaYu connects you to Brazil's hidden gems, offering easy online ticketing for unique travel experiences at top destinations across the country.</p>
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
                    <a href="mountains,php">Mountains</a>
                </div>

                <div class="links-column contact-column">
                    <h3>Contact Us</h3>
                    <div class="contact-item">
                        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/d02e5e8c7df9f4016d0ff3e28c86a52193209ebc4bbd0391f58c5b66eedcc255?placeholderIfAbsent=true&apiKey=820f30d49f024d318c8b29f3eaf6b5a7" alt="Phone">
                        <span>+62 8121113141</span>
                    </div>
                    <div class="contact-item">
                        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/11eb0a8e4065b51bdab3e36a60f9ccc2bd492d97f87283099457649c5fd63249?placeholderIfAbsent=true&apiKey=820f30d49f024d318c8b29f3eaf6b5a7" alt="Email">
                        <span>ahad@yahayu.ac.id</span>
                    </div>
                    <div class="contact-item">
                        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/fb297e302cb7e6e368853a8a1bc89afd264f17aa9c3be39ff3bf9ef96cbbd97a?placeholderIfAbsent=true&apiKey=820f30d49f024d318c8b29f3eaf6b5a7" alt="Location">
                        <span>Purbalingga, Central Java</span>
                    </div>
                    <div class="social-media">
                        <img src="images/whatsapp.png" alt="WhatsApp">
                        <img src="images/instagram.png" alt="Instagram">
                        <img src="images/twitter.png" alt="Other">
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

<?php
mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>