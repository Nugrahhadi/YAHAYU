<?php
session_start();
include("koneksi.php");

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Ambil data tiket user
$user_id = $_SESSION['user']['id'];
$query = "SELECT b.*, d.nama_destinasi, d.harga, d.kategori, d.gambar, 
                 a.nama as nama_akun, a.email as email_akun, a.nomor_telepon 
          FROM bookings b 
          JOIN destinasi d ON b.destinasi_id = d.id 
          JOIN akun a ON b.user_id = a.id 
          WHERE b.user_id = ? 
          ORDER BY b.created_at DESC";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tickets = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            padding-top: 80px;
            background: linear-gradient(to bottom, #ffd700, #ffdb4d);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h1 {
            text-align: center;
            color: #1e8c45;
            font-size: 2.5em;
            margin-bottom: 40px;
            text-transform: uppercase;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .no-tickets {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 600px;
        }

        .no-tickets p {
            font-size: 1.2em;
            color: #666;
            margin-bottom: 20px;
        }

        .tickets-container {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .ticket-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            /* display: flex; */
            flex-direction: row;
            padding-bottom: 30px;
        }

        .ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .ticket-header {
            background: #1e8c45;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex: 1;
        }

        .ticket-header h3 {
            margin: 0;
            font-size: 1.4em;
            font-weight: 600;
        }

        .ticket-status {
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 0.9em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #ffd700;
            color: #333;
        }

        .status-paid {
            background: #4CAF50;
            color: white;
        }

        .status-expired {
            background: #ff6b6b;
            color: white;
        }

        .status-used {
            background: #78909c;
            color: white;
        }

        /* .ticket-info {
            padding: 25px;
        } */
        .ticket-info {
            flex: 2;
            padding: 25px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #1e8c45;
            font-weight: 600;
            font-size: 1.1em;
        }

        .payment-info {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px dashed #ffd700;
        }

        /* .ticket-actions {
            padding: 20px;
            text-align: center;
            background: rgba(248, 249, 250, 0.5);
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        } */

        .ticket-actions {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            cursor: pointer;
        }

        .btn-pay {
            background: #1e8c45;
            color: white;
            box-shadow: 0 4px 15px rgba(30, 140, 69, 0.3);
        }

        .btn-pay:hover {
            background: #167c3a;
            transform: translateY(-2px);
        }

        .btn-detail {
            background: #ffd700;
            color: #333;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        .btn-detail:hover {
            background: #ffc800;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px 15px;
            }

            .tickets-container {
                display: flex;
                flex-direction: column;
                gap: 30px;
            }

            .ticket-header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .ticket-status {
                width: 100%;
                text-align: center;
            }

            h1 {
                font-size: 2em;
                margin-bottom: 30px;
            }

            .btn {
                width: 100%;
                margin: 5px 0;
            }
        }

        @media (max-width: 480px) {
            .ticket-info {
                padding: 15px;
            }

            .detail-row {
                flex-direction: column;
                gap: 5px;
            }

            .detail-label {
                font-size: 1em;
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
    <div class="container">
        <h1>MY TICKETS</h1>

        <?php if (empty($tickets)): ?>
            <div class="no-tickets">
                <p>Anda belum memiliki tiket. Silakan pesan tiket terlebih dahulu.</p>
            </div>
        <?php else: ?>
            <div class="tickets-container">
                <?php foreach ($tickets as $ticket): ?>
                    <div class="ticket-card">
                        <div class="ticket-header">
                            <h3><?php echo htmlspecialchars($ticket['nama_destinasi']); ?></h3>
                            <span class="ticket-status status-<?php echo $ticket['status']; ?>">
                                <?php
                                switch ($ticket['status']) {
                                    case 'pending':
                                        echo 'Menunggu Pembayaran';
                                        break;
                                    case 'paid':
                                        echo 'Tiket Aktif';
                                        break;
                                    case 'expired':
                                        echo 'Kadaluarsa';
                                        break;
                                    case 'used':
                                        echo 'Sudah Digunakan';
                                        break;
                                }
                                ?>
                            </span>
                        </div>

                        <div class="ticket-info">
                            <div class="detail-row">
                                <span class="detail-label">Tanggal Kunjungan:</span>
                                <span><?php echo date('d F Y', strtotime($ticket['tanggal_kunjungan'])); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Jumlah Tiket:</span>
                                <span><?php echo $ticket['jumlah_tiket']; ?> orang</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Total Pembayaran:</span>
                                <span>Rp <?php echo number_format($ticket['total_pembayaran'], 0, ',', '.'); ?></span>
                            </div>

                            <?php if ($ticket['status'] == 'paid'): ?>
                                <div class="payment-info">
                                    <div class="detail-row">
                                        <span class="detail-label">Status Pembayaran:</span>
                                        <span class="status-paid">Lunas</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Waktu Pembayaran:</span>
                                        <span><?php echo date('d/m/Y H:i', strtotime($ticket['payment_time'])); ?></span>
                                    </div>

                                    <div class="qr-code">
                                        <img src="generate_qr.php?id=<?php echo $ticket['id']; ?>" alt="QR Code Tiket">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="ticket-actions">
                            <?php if ($ticket['status'] == 'pending'): ?>
                                <a href="payment.php?booking_id=<?php echo $ticket['id']; ?>" class="btn btn-pay">
                                    Bayar Sekarang
                                </a>
                            <?php endif; ?>
                            <?php if ($ticket['status'] == 'confirmed' || $ticket['status'] == 'paid'): ?>
                                <a href="detail_tiket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-detail">
                                    Lihat Detail Tiket
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

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