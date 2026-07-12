<?php
require_once '../backend/config.php';
if (isLoggedIn()) { header('Location: index.php'); exit(); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($password)) $error = 'Username dan password harus diisi!';
    else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id']; $_SESSION['username'] = $user['username'];
            header('Location: index.php'); exit();
        } else { $error = 'Username atau password salah!'; sleep(1); }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Login Admin</title><link rel="stylesheet" href="../css/admin.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"></head>
<body class="login-page">
<div class="login-container"><div class="login-box">
<h2><i class="fas fa-user-shield"></i> Admin Login</h2><p>Masuk untuk mengelola portofolio</p>
<?php if($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="POST"><div class="form-group"><label>Username</label><input type="text" name="username" required autofocus></div>
<div class="form-group"><label>Password</label><input type="password" name="password" required></div>
<button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</button></form>
<div class="back-to-site" style="text-align:center; margin-top:20px;"><a href="../index.html" style="color:var(--primary);"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a></div>
</div></div></body></html>