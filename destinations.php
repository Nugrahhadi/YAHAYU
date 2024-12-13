<?php
session_start();
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
include("koneksi.php");

// Mengecek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['user']['role'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations - YaHaYu</title>
    <link rel="stylesheet" href="destinasi.css"> 
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
                        <a href="destinations.php?category=pantai" class="dropdown-item" role="menuitem">Beaches</a>
                        <a href="destinations.php?category=Deserts" class="dropdown-item" role="menuitem">Deserts</a>
                        <a href="destinations.php?category=air terjun" class="dropdown-item" role="menuitem">Waterfalls</a>
                        <a href="destinations.php?category=Cultural Sites" class="dropdown-item" role="menuitem">Cultural Sites</a>
                        <a href="destinations.php?category=Mountains" class="dropdown-item" role="menuitem">Mountains</a>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                            <a href="add_destinasi.php" class="dropdown-item" role="menuitem">Add Destination</a>
                        <?php endif; ?>
                    </div>

                </div>
                <a href="myticket.php" class="nav-item">My Tiket</a>
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
    </header>

    <main>
        <h1>Destinations</h1>

        <div class="destinations-list">
            <?php
            // Query untuk mengambil semua destinasi
            $query = "SELECT * FROM destinasi";
            $result = mysqli_query($koneksi, $query);

            // Mengecek apakah query berhasil
            if ($result) {
                // Menampilkan destinasi satu per satu
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="destination-card">';
                    echo '<h3>' . htmlspecialchars($row['nama_destinasi']) . '</h3>';
                    echo '<p><strong>Kategori:</strong> ' . htmlspecialchars($row['kategori']) . '</p>';
                    echo '<p>' . substr(htmlspecialchars($row['deskripsi']), 0, 100) . '...</p>';
                    echo '<p><strong>Harga:</strong> Rp ' . number_format($row['harga'], 0, ',', '.') . '</p>';
                    echo '<a href="detail_destinasi.php?id=' . $row['id'] . '" class="btn">Explore Now</a>';

                    // Menampilkan tombol hapus jika role admin
                    if ($role === 'admin') {
                        echo '<a href="delete_destinasi.php?id=' . $row['id'] . '" class="btn danger">Delete</a>';
                    }

                    echo '</div>';
                }
            } else {
                echo "<p>Destinasi tidak tersedia.</p>";
            }
            ?>
        </div>
    </main>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2024 YaHaYu. All Rights Reserved.</p>
    </footer>

</body>

</html>