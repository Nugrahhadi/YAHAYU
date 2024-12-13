<?php
session_start();
include("koneksi.php");

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['booking_id'])) {
    header("Location: index.php");
    exit();
}

$booking_id = $_GET['booking_id'];
$user_id = $_SESSION['user']['id'];

$query = "SELECT b.*, d.nama_destinasi 
          FROM bookings b 
          JOIN destinasi d ON b.destinasi_id = d.id 
          WHERE b.id = ? AND b.user_id = ?";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - YaHaYu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #FFD700;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background-color: #FFDB4D;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #006400;
            margin-bottom: 20px;
            text-align: center;
            font-size: 24px;
        }

        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .booking-details {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: bold;
        }

        .home-button {
            background-color: #006400;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: block;
            text-align: center;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
        }

        .home-button:hover {
            background-color: #004d00;
        }

        .buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .print-button {
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: block;
            text-align: center;
            font-size: 16px;
            flex: 1;
            cursor: pointer;
        }

        .print-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="success-message">
            <h2>Pembayaran Berhasil!</h2>
            <p>Terima kasih telah melakukan pemesanan di YaHaYu</p>
        </div>

        <div class="booking-details">
            <div class="detail-row">
                <span class="detail-label">Nomor Booking:</span>
                <span><?php echo $booking['payment_token']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Destinasi:</span>
                <span><?php echo htmlspecialchars($booking['nama_destinasi']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Nama Pemesan:</span>
                <span><?php echo htmlspecialchars($booking['nama_lengkap']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span><?php echo htmlspecialchars($booking['email']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Jumlah Tiket:</span>
                <span><?php echo $booking['jumlah_tiket']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal Kunjungan:</span>
                <span><?php echo date('d F Y', strtotime($booking['tanggal_kunjungan'])); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Pembayaran:</span>
                <span>Rp <?php echo number_format($booking['total_pembayaran'], 0, ',', '.'); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span style="color: #2e7d32; font-weight: bold;">SUKSES</span>
            </div>
        </div>

        <div class="buttons">
            <a href="javascript:window.print()" class="print-button">Cetak Tiket</a>
            <a href="index.php" class="home-button">Kembali ke Beranda</a>
        </div>
    </div>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>