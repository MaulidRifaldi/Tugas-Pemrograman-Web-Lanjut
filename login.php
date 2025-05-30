<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$kesalahan = ['username' => '', 'password' => '', 'umum' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '') $kesalahan['username'] = 'Username wajib diisi.';
    if ($password === '') $kesalahan['password'] = 'Password wajib diisi.';

    if (!$kesalahan['username'] && !$kesalahan['password']) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: dashboard.php");
                exit;
            } else {
                $kesalahan['umum'] = 'Username atau password salah.';
            }
        } catch (PDOException $e) {
            $kesalahan['umum'] = 'Kesalahan database: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Masuk - Sistem Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
<style>
body {
    font-family: 'Poppins', sans-serif;
    background-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
    margin: 0;
}
.kotak-login {
    background: rgba(255,255,255,0.1);
    border-radius: 15px;
    box-shadow: 0 8px 32px 0 rgba(0,0,0,0.37);
    width: 350px;
    padding: 2.5rem;
}
h2 {
    font-weight: 600;
    margin-bottom: 1.5rem;
    text-align: center;
}
.form-control {
    background: rgba(255,255,255,0.95);
    border-radius: 10px;
    border: none;
    color: #333;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: box-shadow 0.3s ease;
}
.form-control:focus {
    outline: none;
    box-shadow: 0 0 8px #764ba2;
}
.invalid-feedback {
    display: block;
    font-size: 0.85rem;
}
.btn-masuk {
    background: #764ba2;
    border: none;
    padding: 0.75rem;
    width: 100%;
    font-weight: 600;
    font-size: 1.1rem;
    border-radius: 10px;
    transition: background 0.3s ease;
    color: white;
    cursor: pointer;
}
.btn-masuk:hover {
    background: #5a3577;
    text-decoration: none;
}
.tautan-daftar {
    text-align: center;
    margin-top: 1rem;
}
.tautan-daftar a {
    color: #f1f1f1;
    text-decoration: underline;
}
.peringatan-umum {
    background: rgba(255, 99, 71, 0.85);
    padding: 0.75rem 1rem;
    border-radius: 10px;
    margin-bottom: 1rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-align: center;
}
</style>
</head>
<body>
<div class="kotak-login">
    <h2>Masuk Akun Anda</h2>
    <?php if ($kesalahan['umum']): ?>
        <div class="peringatan-umum"><?= htmlspecialchars($kesalahan['umum']) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
        <div class="form-group">
            <input type="text" name="username" placeholder="Nama pengguna" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
            class="form-control <?= $kesalahan['username'] ? 'is-invalid' : '' ?>" required autofocus />
            <div class="invalid-feedback"><?= htmlspecialchars($kesalahan['username']) ?></div>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Kata sandi" class="form-control <?= $kesalahan['password'] ? 'is-invalid' : '' ?>" required />
            <div class="invalid-feedback"><?= htmlspecialchars($kesalahan['password']) ?></div>
        </div>
        <button type="submit" class="btn-masuk">Masuk</button>
    </form>
    <p class="tautan-daftar">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
</div>
</body>
</html>