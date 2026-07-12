<?php
require_once '../backend/config.php';
require_once '../backend/crud.php';
requireLogin();

$error = '';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 2 * 1024 * 1024;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? '';
    $project_url = trim($_POST['project_url'] ?? '');
    
    if (empty($title)) $error = 'Judul project harus diisi!';
    elseif (empty($category)) $error = 'Kategori harus dipilih!';
    else {
        $image_url = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];
            if (!in_array($file_type, $allowed_types)) $error = 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.';
            elseif ($file_size > $max_size) $error = 'Ukuran file terlalu besar. Maksimal 2MB.';
            else {
                // Pastikan direktori uploads ada
                $upload_dir = '../uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $filename = time() . '_' . uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $target_path = $upload_dir . $filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image_url = 'uploads/' . $filename;
                } else {
                    $error = 'Gagal mengupload gambar.';
                }
            }
        }
        
        if (empty($error)) {
            $crud = new PortfolioCRUD($pdo);
            if ($crud->create($title, $description, $category, $image_url, $project_url)) {
                header('Location: index.php?msg=added');
                exit();
            } else {
                $error = 'Gagal menambahkan data.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Portfolio</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Tambahan style untuk memastikan tampilan */
        body { background: #f5f6fa; }
        .admin-container { display: flex; min-height: 100vh; background: #f5f6fa; }
        .admin-nav { width: 250px; background: #2c3e50; color: white; padding: 20px 0; min-height: 100vh; }
        .nav-brand { padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .nav-brand h2 { color: white; }
        .nav-brand small { color: #a0aec0; }
        .nav-menu { list-style: none; padding: 0; }
        .nav-menu li a { display: block; padding: 12px 20px; color: rgba(255,255,255,0.8); text-decoration: none; transition: 0.3s; }
        .nav-menu li a:hover, .nav-menu li a.active { background: rgba(255,255,255,0.1); border-left: 3px solid #667eea; color: white; }
        .nav-menu li a i { margin-right: 10px; width: 20px; }
        .admin-main { flex: 1; padding: 20px; background: #f5f6fa; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #e2e8f0; }
        .page-header h1 { font-size: 1.8rem; color: #2c3e50; }
        .form-container { background: white; padding: 30px; border-radius: 10px; max-width: 800px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #667eea; outline: none; }
        .form-actions { display: flex; gap: 10px; margin-top: 30px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; font-size: 14px; }
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #5a67d8; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn-secondary:hover { background: #7f8c8d; }
        .alert { padding: 12px 20px; border-radius: 5px; margin-bottom: 20px; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        small { color: #6c757d; font-size: 12px; }
        small i { margin-right: 5px; }
    </style>
</head>
<body>
<div class="admin-container">
    <nav class="admin-nav">
        <div class="nav-brand">
            <h2><i class="fas fa-portfolio"></i> Portfolio Admin</h2>
            <small>Halo, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></small>
        </div>
        <ul class="nav-menu">
            <li><a href="index.php"><i class="fas fa-dashboard"></i> Dashboard</a></li>
            <li><a href="messages.php"><i class="fas fa-envelope"></i> Pesan</a></li>
            <li><a href="tambah.php" class="active"><i class="fas fa-plus"></i> Tambah Portfolio</a></li>
            <li><a href="../index.html"><i class="fas fa-home"></i> Lihat Website</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    <main class="admin-main">
        <div class="page-header">
            <h1><i class="fas fa-plus"></i> Tambah Portfolio Baru</h1>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <?php if($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="form-container">
            <div class="form-group">
                <label>Judul Project *</label>
                <input type="text" name="title" required placeholder="Masukkan judul project">
            </div>
            <div class="form-group">
                <label>Kategori *</label>
                <select name="category" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Web Development">🌐 Web Development</option>
                    <option value="UI/UX Design">🎨 UI/UX Design</option>
                    <option value="Mobile App">📱 Mobile App</option>
                    <option value="Dashboard">📊 Dashboard</option>
                    <option value="E-commerce">🛒 E-commerce</option>
                </select>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" rows="5" placeholder="Jelaskan tentang project ini..."></textarea>
            </div>
            <div class="form-group">
                <label>Gambar Project</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                <small><i class="fas fa-info-circle"></i> Format: JPG, PNG, GIF, WEBP (Max 2MB)</small>
            </div>
            <div class="form-group">
                <label>URL Project (opsional)</label>
                <input type="url" name="project_url" placeholder="https://example.com/project">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </main>
</div>
</body>
</html>