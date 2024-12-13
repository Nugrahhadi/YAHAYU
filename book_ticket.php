<?php
session_start();
include("koneksi.php");

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah ada ID destinasi
if (!isset($_GET['id'])) {
    header("Location: destinations.php");
    exit;
}

// Ambil data destinasi
$destinasi_id = $_GET['id'];
$query = "SELECT * FROM destinasi WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $destinasi_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$destinasi = mysqli_fetch_assoc($result);

// Jika destinasi tidak ditemukan
if (!$destinasi) {
    header("Location: destinations.php");
    exit;
}

// Proses booking jika form disubmit
if (isset($_POST['submit'])) {
    $user_id = $_SESSION['user']['id'];
    $jumlah_tiket = $_POST['jumlah_tiket'];
    $tanggal_kunjungan = $_POST['tanggal_kunjungan'];
    $total_pembayaran = $destinasi['harga'] * $jumlah_tiket;
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $status = 'pending';

    $sql = "INSERT INTO bookings (user_id, destinasi_id, jumlah_tiket, tanggal_kunjungan, total_pembayaran, nama_lengkap, email, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "iiisdsss", $user_id, $destinasi_id, $jumlah_tiket, $tanggal_kunjungan, $total_pembayaran, $nama_lengkap, $email, $status);
    if (mysqli_stmt_execute($stmt)) {
        $booking_id = mysqli_insert_id($koneksi);
        header("Location: payment.php?booking_id=" . $booking_id);
        exit;
    } else {
        $error = "Terjadi kesalahan saat melakukan booking.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ticket - <?php echo htmlspecialchars($destinasi['nama_destinasi']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #FFD700;
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
            font-size: 24px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #006400;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background-color: white;
        }

        .jumlah-tiket {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .total-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .btn-pay {
            width: 100%;
            padding: 15px;
            background-color: #006400;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-pay:hover {
            background-color: #004d00;
        }

        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>BOOKING DETAILS</h2>

        <form action="" method="POST">
            <div class="form-section">
                <div class="form-group">
                    <label for="destinasi">Destinasi:</label>
                    <input type="text" id="destinasi" value="<?php echo htmlspecialchars($destinasi['nama_destinasi']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="tanggal_kunjungan">Waktu Kunjungan:</label>
                    <input type="date" id="tanggal_kunjungan" name="tanggal_kunjungan" required
                        min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="jumlah_tiket">Jumlah Tiket:</label>
                    <input type="number" id="jumlah_tiket" name="jumlah_tiket" min="1" max="10" value="1" required
                        onchange="updateTotal()">
                </div>

                <div class="total-section">
                    <label>Total Pembayaran:</label>
                    <div id="total_pembayaran">Rp <?php echo number_format($destinasi['harga'], 0, ',', '.'); ?></div>
                </div>
            </div>

            <div class="form-section">
                <h2>GUEST INFORMATION</h2>
                <div class="form-group">
                    <label for="nama_lengkap">Nama Pemesan:</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($_SESSION['user']['nama']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['email']); ?>" readonly>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <button type="submit" name="submit" class="btn-pay">Pay Now</button>
        </form>
    </div>

    <script>
        function updateTotal() {
            const hargaTiket = <?php echo $destinasi['harga']; ?>;
            const jumlahTiket = document.getElementById('jumlah_tiket').value;
            const total = hargaTiket * jumlahTiket;
            document.getElementById('total_pembayaran').textContent =
                'Rp ' + total.toLocaleString('id-ID');
        }
    </script>
</body>

</html>