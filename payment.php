<?php
session_start();
include("koneksi.php");

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Ambil data booking dari database
if (isset($_GET['booking_id'])) {
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

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
    } else {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// Handle pembayaran sekarang
if (isset($_POST['pay_now'])) {
    $update_query = "UPDATE bookings 
                    SET status = 'confirmed',
                        payment_status = 'paid',
                        payment_time = CURRENT_TIMESTAMP
                    WHERE id = ? AND user_id = ?";

    $update_stmt = $koneksi->prepare($update_query);
    $update_stmt->bind_param("ii", $booking_id, $user_id);

    if ($update_stmt->execute()) {
        header("Location: proses_payment.php?booking_id=" . $booking_id);
        exit();
    } else {
        $error_message = "Terjadi kesalahan dalam proses pembayaran";
    }
}

// Handle bayar nanti
if (isset($_POST['pay_later'])) {
    $update_query = "UPDATE bookings 
                    SET status = 'pending',
                        payment_status = 'pending'
                    WHERE id = ? AND user_id = ?";

    $update_stmt = $koneksi->prepare($update_query);
    $update_stmt->bind_param("ii", $booking_id, $user_id);

    if ($update_stmt->execute()) {
        header("Location: myticket.php");
        exit();
    } else {
        $error_message = "Terjadi kesalahan dalam menyimpan tiket";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - YaHaYu</title>
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
        }

        .order-details {
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

        .button {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .pay-now {
            background-color: #006400;
            color: white;
        }

        .pay-now:hover {
            background-color: #004d00;
        }

        .pay-later {
            background-color: #FFA500;
            color: white;
        }

        .pay-later:hover {
            background-color: #FF8C00;
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Detail Pembayaran</h2>

        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="order-details">
            <div class="detail-row">
                <span>Destinasi:</span>
                <span><?php echo htmlspecialchars($booking['nama_destinasi']); ?></span>
            </div>
            <div class="detail-row">
                <span>Jumlah Tiket:</span>
                <span><?php echo $booking['jumlah_tiket']; ?></span>
            </div>
            <div class="detail-row">
                <span>Tanggal Kunjungan:</span>
                <span><?php echo date('d F Y', strtotime($booking['tanggal_kunjungan'])); ?></span>
            </div>
            <div class="detail-row">
                <span>Total Pembayaran:</span>
                <span>Rp <?php echo number_format($booking['total_pembayaran'], 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="button-container">
            <form method="POST" style="margin-bottom: 10px;">
                <button type="submit" name="pay_now" class="button pay-now">Bayar Sekarang</button>
            </form>

            <form method="POST">
                <button type="submit" name="pay_later" class="button pay-later">Bayar Nanti</button>
            </form>
        </div>
    </div>
</body>

</html>