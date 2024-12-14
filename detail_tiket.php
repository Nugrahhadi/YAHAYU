<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$ticket_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$ticket_id) {
    header("Location: myticket.php");
    exit;
}

// Detail booking dan pemesan
$query = "SELECT b.*, d.nama_destinasi, d.harga, 
                 a.nama, a.email, a.nomor_telepon 
          FROM bookings b 
          JOIN destinasi d ON b.destinasi_id = d.id 
          JOIN akun a ON b.user_id = a.id 
          WHERE b.id = ? AND b.user_id = ?";

$stmt = mysqli_prepare($koneksi, $query);

if ($stmt === false) {
    die('Error in preparing statement: ' . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "ii", $ticket_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    header("Location: myticket.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tiket - YaHaYu</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            padding-top: 80px;
            background-color: #ffd700;
        }

        main {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .ticket-detail {
            background: #ffdb4d;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            animation: fadeIn 0.5s ease-out;
        }

        .detail-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        h1 {
            text-align: center;
            color: #1e8c45;
            font-size: 2.5rem;
            margin: 2rem 0;
        }

        h2 {
            color: #1e8c45;
            font-size: 1.8rem;
            margin: 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #1e8c45;
        }

        .detail-section p {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
            font-size: 1.1rem;
        }

        .ticket-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
        }

        .button {
            display: inline-block;
            padding: 1rem 2rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            transition: all 0.3s ease;
            background-color: #1e8c45;
            color: white;
            min-width: 200px;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
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
                        <a href="beaches.php?category=pantai" class="dropdown-item" role="menuitem">Beaches</a>
                        <a href="destinations.php?category=gurun" class="dropdown-item" role="menuitem">Deserts</a>
                        <a href="destinations.php?category=air terjun" class="dropdown-item" role="menuitem">Waterfalls</a>
                        <a href="destinations.php?category=Cultural Sites" class="dropdown-item" role="menuitem">Cultural Sites</a>
                        <a href="destinations.php?category=pegunungan" class="dropdown-item" role="menuitem">Mountains</a>
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
        <h1>Detail Tiket</h1>

        <?php if ($booking['status'] === 'confirmed' || $booking['status'] === 'paid'): ?>
            <div class="ticket-detail">
                <h2>Informasi Pemesan</h2>
                <div class="detail-section">
                    <p><strong>Nama Pemesan:</strong> <?php echo htmlspecialchars($booking['nama']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?></p>
                    <p><strong>Nomor HP:</strong> <?php echo htmlspecialchars($booking['nomor_telepon']); ?></p>
                </div>

                <h2>Informasi Tiket</h2>
                <div class="detail-section">
                    <p><strong>Destinasi:</strong> <?php echo htmlspecialchars($booking['nama_destinasi']); ?></p>
                    <p><strong>Tanggal Kunjungan:</strong> <?php echo date('d F Y', strtotime($booking['tanggal_kunjungan'])); ?></p>
                    <p><strong>Jumlah Tiket:</strong> <?php echo htmlspecialchars($booking['jumlah_tiket']); ?> orang</p>
                    <p><strong>Harga per Tiket:</strong> Rp <?php echo number_format($booking['harga'], 0, ',', '.'); ?></p>
                    <p><strong>Total Pembayaran:</strong> Rp <?php echo number_format($booking['total_pembayaran'], 0, ',', '.'); ?></p>
                </div>

                <div class="ticket-actions">
                    <a href="myticket.php" class="button">Kembali ke Daftar Tiket</a>
                </div>
            </div>
        <?php else: ?>
        <?php endif; ?>
    </main>

    <!-- Footer -->
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
                            <img src="Images/whatsapp.png" alt="WhatsApp">
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <img src="Images/instagram.png" alt="Instagram">
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <img src="Images/twitter.png" alt="Twitter">
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