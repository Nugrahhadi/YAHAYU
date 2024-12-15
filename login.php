<?php
session_start();
include("koneksi.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Email dan password harus diisi.";
    } else {
        $sql = "SELECT * FROM akun WHERE email = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user'] = [
                    'id' => $row['id'],
                    'email' => $row['email'],
                    'username' => $row['username'],
                    'nama' => $row['nama'],
                    'nomor_telepon' => $row['nomor_telepon'],
                    'role' => $row['role'],
                ];

                header("Location: index.php");
                exit();
            } else {
                $error_message = "Password yang Anda masukkan salah.";
            }
        } else {
            $error_message = "Email tidak ditemukan.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($koneksi);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - YaHaYu</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding-top: 80px;
            background: url('background.png') no-repeat center center;
            background-size: cover;
        }

        .login-box {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            margin: 1rem;
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .login-box h1 {
            color: #1E8C45;
            margin-bottom: 2rem;
            font-size: 2rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 25px;
            border: 1px solid #ddd;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1E8C45;
            box-shadow: 0 0 0 2px rgba(30, 140, 69, 0.1);
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

        .forgot-password {
            display: block;
            text-align: right;
            color: #1E8C45;
            font-size: 0.9rem;
            text-decoration: none;
            margin: 1rem 0;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #166835;
        }

        .login-btn {
            width: 100%;
            padding: 1rem;
            background: #1E8C45;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-btn:hover {
            background: #166835;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .register-link {
            margin-top: 1.5rem;
            color: #666;
        }

        .register-link a {
            color: #1E8C45;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .alert-error {
            background-color: #ff6b6b;
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 1rem;
                padding-top: 100px;
            }

            .login-box {
                padding: 2rem 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .login-box h1 {
                font-size: 1.75rem;
            }

            .form-group input {
                padding: 0.6rem 1rem;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
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

    <div class="login-container">
        <div class="login-box">
            <h1>LOGIN</h1>
            <?php if (isset($error_message)): ?>
                <div class="alert-error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        placeholder="Enter your email" required />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password"
                            placeholder="Enter your password" required />
                    </div>
                </div>
                <a href="#" class="forgot-password">Forgot Password?</a>
                <button type="submit" class="login-btn">Login</button>
            </form>
            <p class="register-link">Don't have an account? <a href="register_pengguna.php">Register Now!</a></p>
        </div>
    </div>

    <script src="main.js"></script>
</body>

</html>