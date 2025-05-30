<?php
session_start();
require_once 'config/db.php';

$kesalahan = [];
$sukses = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if ($username === '') $kesalahan[] = 'Username wajib diisi.';
    if ($password === '') $kesalahan[] = 'Password wajib diisi.';
    if ($password !== $confirm_password) $kesalahan[] = 'Password dan konfirmasi tidak cocok.';
    if (!in_array($role, ['admin', 'user'])) $kesalahan[] = 'Peran tidak valid.';

    if (!$kesalahan) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $kesalahan[] = 'Username sudah terdaftar.';
            } else {
                $hashPass = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $hashPass, $role])) {
                    $sukses = 'Registrasi berhasil! <a href="login.php">Masuk sekarang</a>.';
                } else {
                    $kesalahan[] = 'Terjadi kesalahan, coba lagi.';
                }
            }
        } catch (PDOException $e) {
            $kesalahan[] = 'Kesalahan database: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Daftar Akun Baru</title>
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
.kotak-daftar {
    background: rgba(255,255,255,0.1);
    border-radius: 15px;
    box-shadow: 0 8px 32px 0 rgba(0,0,0,0.37);
    width: 350px;
    padding: 2.5rem;
}
h2 {
    font-weight: 600;
    text-align: center;
    margin-bottom: 1rem;
}
.btn-daftar {
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
.btn-daftar:hover {
    background: #5a3577;
    text-decoration: none;
}
.form-control {
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
}
.alert-danger ul {
    margin-bottom: 0;
}
.tautan-masuk a {
    color: #f1f1f1;
    text-decoration: underline;
}
</style>
</head>
<body>
<div class="kotak-daftar">
    <h2>Buat Akun Baru</h2>
    <?php if ($kesalahan): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($kesalahan as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($sukses): ?>
        <div class="alert alert-success"><?= $sukses ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
        <div class="form-group">
            <input type="text" name="username" placeholder="Nama pengguna" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Kata sandi" class="form-control" required />
        </div>
        <div class="form-group">
            <input type="password" name="confirm_password" placeholder="Konfirmasi kata sandi" class="form-control" required />
        </div>
        <div class="form-group">
            <label for="role">Pilih Peran</label>
            <select name="role" id="role" class="form-control" required>
                <option value="user" <?= (($_POST['role'] ?? '') === 'user') ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <button type="submit" class="btn-daftar">Daftar</button>
    </form>
    <p class="tautan-masuk" style="text-align:center; margin-top:1rem;">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
</div>
</body>
</html>