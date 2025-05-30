<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/db.php';

$username = $_SESSION['username'] ?? 'Pengguna';
$role = $_SESSION['role'] ?? 'user';

$adminCount = $userCount = 0;

if ($role === 'admin') {
    $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    foreach ($stmt as $countRow) {
        if ($countRow['role'] === 'admin') $adminCount = $countRow['count'];
        if ($countRow['role'] === 'user') $userCount = $countRow['count'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #fff;
            margin: 0;
        }
        .container {
            max-width: 600px;
            margin-top: 20px;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.37);
        }
        .navbar {
            background: rgba(0, 0, 0, 0.5);
        }
        .navbar-brand, .navbar-text {
            color: #fff;
        }
        .alert {
            font-weight: 600;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand navbar-dark">
    <a class="navbar-brand" href="#">Dashboard</a>
    <div class="navbar-nav ml-auto">
        <span class="navbar-text mr-3">Selamat datang, <?= htmlspecialchars($username) ?> (<?= htmlspecialchars(ucfirst($role)) ?>)</span>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</nav>
<div class="container">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title"><?= ucfirst($role) ?> Dashboard</h4>
            <p class="card-text">Ini adalah halaman dashboard yang dilindungi. Hanya pengguna yang sudah login yang dapat mengakses halaman ini.</p>

            <?php if ($role === 'admin'): ?>
                <div class="alert alert-info">Panel Admin: Anda dapat mengelola sistem di sini.</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Admin</h5>
                                <p class="card-text" style="font-size: 1.5rem;"><?= $adminCount ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card text-white bg-secondary">
                            <div class="card-body">
                                <h5 class="card-title">Total Pengguna</h5>
                                <p class="card-text" style="font-size: 1.5rem;"><?= $userCount ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-secondary">Anda telah login sebagai pengguna biasa.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>