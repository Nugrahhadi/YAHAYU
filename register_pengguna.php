<?php
session_start();
include("koneksi.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nomor_telepon = $_POST['nomor_telepon'];

    $tanggal = $_POST['tanggal'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];

    if (!checkdate($bulan, $tanggal, $tahun)) {
        $error_message = "Tanggal lahir tidak valid!";
    } else {
        $tanggal_lahir = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' . str_pad($tanggal, 2, '0', STR_PAD_LEFT);

        if ($password !== $confirm_password) {
            $error_message = "Password dan konfirmasi password tidak cocok.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Cek apakah email sudah terdaftar
            $check_email = "SELECT id FROM akun WHERE email = ?";
            $stmt_check = mysqli_prepare($koneksi, $check_email);
            mysqli_stmt_bind_param($stmt_check, "s", $email);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);

            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                $error_message = "Email sudah terdaftar. Gunakan email lain.";
            } else {
                $role = "peserta";
                $sql = "INSERT INTO akun (username, nama, email, password, nomor_telepon, tanggal_lahir, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($koneksi, $sql);
                mysqli_stmt_bind_param($stmt, "sssssss", $username, $nama, $email, $password_hash, $nomor_telepon, $tanggal_lahir, $role);

                if (mysqli_stmt_execute($stmt)) {
                    $user_id = mysqli_insert_id($koneksi);
                    $_SESSION['user'] = [
                        'id' => $user_id,
                        'username' => $username,
                        'nama' => $nama,
                        'email' => $email,
                        'nomor_telepon' => $nomor_telepon,
                        'tanggal_lahir' => $tanggal_lahir,
                        'role' => $role
                    ];

                    // Redirect ke halaman utama
                    header("Location: index.php");
                    exit;
                } else {
                    $error_message = "Error: " . mysqli_error($koneksi);
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_stmt_close($stmt_check);
        }
    }
    mysqli_close($koneksi);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - YaHaYu</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding-top: 80px;
            background: url('background.png') no-repeat center center;
            background-size: cover;
        }

        .form-section {
            width: 100%;
            max-width: 500px;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            margin: 2rem auto;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .form-section h1 {
            color: #1E8C45;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1E8C45;
            box-shadow: 0 0 0 2px rgba(30, 140, 69, 0.1);
        }

        .date-group {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }


        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            padding: 5px;
        }

        .toggle-password img {
            width: 20px;
            height: 20px;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .toggle-password:hover img {
            opacity: 1;
        }

        .register-btn {
            width: 100%;
            padding: 1rem;
            background: #1E8C45;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 1rem;
        }

        .register-btn:hover {
            background: #166835;
        }

        .form-footer {
            margin-top: 1.5rem;
            text-align: center;
            color: #666;
        }

        .form-footer a {
            color: #1E8C45;
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }


        @media (max-width: 992px) {
            .form-section {
                width: 90%;
                max-width: 500px;
            }

        }

        @media (max-width: 768px) {
            .form-section {
                width: 90%;
                margin: 1rem;
                padding: 2rem 1.5rem;
            }

            .date-group {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .form-section h1 {
                font-size: 1.75rem;
            }

            .form-group input {
                padding: 0.6rem 1rem;
            }
        }
    </style>
</head>

<body>
    <nav class="nav-header" role="navigation" aria-label="Main navigation">
        <div class="brand-logo">YaHaYu</div>

        <!-- Hamburger Menu -->
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
            <div class="buttonL">
                <a href="login.php">Login</a>
            </div>
            <div class="buttonR">
                <a href="register_pengguna.php">Sign Up</a>
            </div>
        </div>
    </nav>

    <div class="register-container">
        <div class="form-section">
            <h1>REGISTER</h1>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="register_pengguna.php" method="POST">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="nomor_telepon">Nomor Telepon</label>
                    <input type="text" id="nomor_telepon" name="nomor_telepon" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <div class="date-group">
                        <input type="number" name="tanggal" placeholder="Tanggal" min="1" max="31" value="<?php echo htmlspecialchars(explode('-', $profile['tanggal_lahir'])[2]); ?>" required>
                        <input type="number" name="bulan" placeholder="Bulan" min="1" max="12" value="<?php echo htmlspecialchars(explode('-', $profile['tanggal_lahir'])[1]); ?>" required>
                        <input type="number" name="tahun" placeholder="Tahun" min="1900" max="2024" value="<?php echo htmlspecialchars(explode('-', $profile['tanggal_lahir'])[0]); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <div class="password-container">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <span class="error-message" id="confirmError"></span>
                </div>

                <button type="submit" class="register-btn">Create Account</button>
                <div class="form-footer">
                    Already have an account? <a href="login.php">Sign in</a>
                </div>
            </form>
        </div>
    </div>
    <script src="main.js"></script>
</body>

</html>