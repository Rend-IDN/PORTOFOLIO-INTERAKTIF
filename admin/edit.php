<?php
require_once '../backend/config.php';
require_once '../backend/crud.php';
requireLogin();

$crud = new PortfolioCRUD($pdo);
$id = $_GET['id'] ?? 0;
$item = $crud->getById($id);
if (!$item) { header('Location: index.php'); exit(); }

$error = '';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 2 * 1024 * 1024;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? '';
    $project_url = trim($_POST['project_url'] ?? '');
    $image_url = $item['image_url'];
    
    if (empty($title)) $error = 'Judul project harus diisi!';
    elseif (empty($category)) $error = 'Kategori harus dipilih!';
    else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];
            if (!in_array($file_type, $allowed_types)) $error = 'Format file tidak didukung.';
            elseif ($file_size > $max_size) $error = 'Ukuran file terlalu besar. Maksimal 2MB.';
            else {
                $upload_dir = '../uploads/';
                if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
                $filename = time() . '_' . uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                    if ($image_url && file_exists('../' . $image_url)) unlink('../' . $image_url);
                    $image_url = 'uploads/' . $filename;
                } else $error = 'Gagal mengupload gambar.';
            }
        }
        
        if (empty($error)) {
            if ($crud->update($id, $title, $description, $category, $image_url, $project_url)) { header('Location: index.php?msg=updated'); exit(); }
            else $error = 'Gagal mengupdate data.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Edit Portfolio</title><link rel="stylesheet" href="../css/admin.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"></head>
<body>
<div class="admin-container">
    <nav class="admin-nav"><div class="nav-brand"><h2><i class="fas fa-portfolio"></i> Portfolio Admin</h2><small>Halo, <?= htmlspecialchars($_SESSION['username']) ?></small></div>
    <ul class="nav-menu"><li><a href="index.php"><i class="fas fa-dashboard"></i> Dashboard</a></li><li><a href="messages.php"><i class="fas fa-envelope"></i> Pesan</a></li><li><a href="tambah.php"><i class="fas fa-plus"></i> Tambah Portfolio</a></li><li><a href="../index.html"><i class="fas fa-home"></i> Lihat Website</a></li><li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li></ul></nav>
    <main class="admin-main"><div class="page-header"><h1><i class="fas fa-edit"></i> Edit Portfolio</h1><a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
    <?php if($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="form-container">
        <div class="form-group"><label>Judul Project *</label><input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" required></div>
        <div class="form-group"><label>Kategori *</label><select name="category" required><option value="">Pilih Kategori</option><option value="Web Development" <?= $item['category'] == 'Web Development' ? 'selected' : '' ?>>🌐 Web Development</option><option value="UI/UX Design" <?= $item['category'] == 'UI/UX Design' ? 'selected' : '' ?>>🎨 UI/UX Design</option><option value="Mobile App" <?= $item['category'] == 'Mobile App' ? 'selected' : '' ?>>📱 Mobile App</option><option value="Dashboard" <?= $item['category'] == 'Dashboard' ? 'selected' : '' ?>>📊 Dashboard</option><option value="E-commerce" <?= $item['category'] == 'E-commerce' ? 'selected' : '' ?>>🛒 E-commerce</option></select></div>
        <div class="form-group"><label>Deskripsi</label><textarea name="description" rows="5"><?= htmlspecialchars($item['description']) ?></textarea></div>
        <div class="form-group"><label>Gambar Saat Ini</label><?php if($item['image_url']): ?><div class="current-image"><img src="../<?= $item['image_url'] ?>" width="100" style="border-radius:5px;"></div><?php else: ?><p><i class="fas fa-image"></i> Tidak ada gambar</p><?php endif; ?><label>Ganti Gambar (opsional)</label><input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp"><small><i class="fas fa-info-circle"></i> Format: JPG, PNG, GIF, WEBP (Max 2MB)</small></div>
        <div class="form-group"><label>URL Project (opsional)</label><input type="url" name="project_url" value="<?= htmlspecialchars($item['project_url']) ?>" placeholder="https://example.com"></div>
        <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button><a href="index.php" class="btn btn-secondary">Batal</a></div>
    </form></main>
</div></body></html>