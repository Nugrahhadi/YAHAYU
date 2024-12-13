<?php
session_start();
include("koneksi.php");

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Cek booking_id
if (!isset($_GET['booking_id'])) {
    header("Location: index.php");
    exit();
}

$booking_id = $_GET['booking_id'];
$user_id = $_SESSION['user']['id'];

// Ambil data booking
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

// Handle form submission
if (isset($_POST['process_payment'])) {
    $payment_token = 'PAY-' . time() . '-' . $booking_id;

    $update_query = "UPDATE bookings 
                    SET payment_status = 'paid',
                        payment_token = ?,
                        payment_time = CURRENT_TIMESTAMP,
                        status = 'confirmed'
                    WHERE id = ? AND user_id = ?";

    $update_stmt = $koneksi->prepare($update_query);
    $update_stmt->bind_param("sii", $payment_token, $booking_id, $user_id);

    if ($update_stmt->execute()) {
        header("Location: booking_sukses.php?booking_id=" . $booking_id);
        exit();
    } else {
        $error_message = "Terjadi kesalahan dalam proses pembayaran";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Pembayaran - YaHaYu</title>
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
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #FFDB4D;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .payment-container {
            display: flex;
            gap: 20px;
        }

        .payment-methods {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }

        .order-summary {
            width: 300px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            height: fit-content;
        }

        h2 {
            color: #006400;
            margin-bottom: 20px;
            text-align: center;
        }

        .payment-option {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-option:hover {
            border-color: #006400;
        }

        .payment-option.active {
            border-color: #006400;
            background-color: #f0f8f0;
        }

        .payment-option img {
            height: 30px;
            margin-right: 10px;
            vertical-align: middle;
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
            font-weight: bold;
        }

        .payment-button {
            background-color: #006400;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        .payment-button:hover {
            background-color: #004d00;
        }

        .payment-details {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            display: none;
        }

        .payment-details.active {
            display: block;
        }

        .account-number {
            font-family: monospace;
            font-size: 18px;
            padding: 10px;
            background: #fff;
            border: 1px dashed #006400;
            border-radius: 5px;
            text-align: center;
            margin: 10px 0;
        }

        .copy-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .copy-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Pembayaran</h2>

        <div class="payment-container">
            <div class="payment-methods">
                <h3>Pilih Metode Pembayaran</h3>

                <div class="payment-option" onclick="selectPayment('bca')">
                    <img src="path/to/bca-logo.png" alt="BCA"> Transfer Bank BCA
                </div>

                <div class="payment-option" onclick="selectPayment('mandiri')">
                    <img src="path/to/mandiri-logo.png" alt="Mandiri"> Transfer Bank Mandiri
                </div>

                <div class="payment-option" onclick="selectPayment('bni')">
                    <img src="path/to/bni-logo.png" alt="BNI"> Transfer Bank BNI
                </div>

                <div id="bca-details" class="payment-details">
                    <h4>Bank BCA</h4>
                    <p>No. Rekening:</p>
                    <div class="account-number">1234567890</div>
                    <p>a.n. YaHaYu Travel</p>
                    <button class="copy-button" onclick="copyToClipboard('1234567890')">Salin</button>
                </div>

                <div id="mandiri-details" class="payment-details">
                    <h4>Bank Mandiri</h4>
                    <p>No. Rekening:</p>
                    <div class="account-number">0987654321</div>
                    <p>a.n. YaHaYu Travel</p>
                    <button class="copy-button" onclick="copyToClipboard('0987654321')">Salin</button>
                </div>

                <div id="bni-details" class="payment-details">
                    <h4>Bank BNI</h4>
                    <p>No. Rekening:</p>
                    <div class="account-number">1122334455</div>
                    <p>a.n. YaHaYu Travel</p>
                    <button class="copy-button" onclick="copyToClipboard('1122334455')">Salin</button>
                </div>

                <form method="POST" action="" id="paymentForm">
                    <input type="hidden" name="selected_bank" id="selected_bank">
                    <button type="submit" name="process_payment" class="payment-button">Konfirmasi Pembayaran</button>
                </form>
            </div>

            <div class="order-summary">
                <h3>Ringkasan Pesanan</h3>
                <div class="detail-row">
                    <span>Destinasi:</span>
                    <span><?php echo htmlspecialchars($booking['nama_destinasi']); ?></span>
                </div>
                <div class="detail-row">
                    <span>Jumlah Tiket:</span>
                    <span><?php echo $booking['jumlah_tiket']; ?></span>
                </div>
                <div class="detail-row">
                    <span>Total Pembayaran:</span>
                    <span>Rp <?php echo number_format($booking['total_pembayaran'], 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedPayment = '';

        function selectPayment(bank) {
            // Remove active class from all options
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('active');
            });

            // Hide all payment details
            document.querySelectorAll('.payment-details').forEach(details => {
                details.style.display = 'none';
            });

            // Add active class to selected option
            event.currentTarget.classList.add('active');

            // Show selected payment details
            document.getElementById(bank + '-details').style.display = 'block';

            // Update hidden input
            document.getElementById('selected_bank').value = bank;
            selectedPayment = bank;
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Nomor rekening berhasil disalin!');
            });
        }

        // Validate form submission
        document.getElementById('paymentForm').onsubmit = function(e) {
            if (!selectedPayment) {
                e.preventDefault();
                alert('Silakan pilih metode pembayaran terlebih dahulu');
                return false;
            }
            return true;
        };
    </script>
</body>

</html>