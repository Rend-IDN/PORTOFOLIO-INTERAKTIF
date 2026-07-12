<?php
require_once '../backend/config.php';
require_once '../backend/crud.php';
requireLogin();

$crud = new PortfolioCRUD($pdo);
$portfolio_items = $crud->getAll();

// Hitung pesan belum dibaca
$stmt = $pdo->query("SELECT COUNT(*) as unread FROM messages WHERE is_read = 0");
$unread_messages = $stmt->fetch()['unread'];

// Hapus data jika ada request delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $item = $crud->getById($_GET['delete']);
    if ($crud->delete($_GET['delete'])) {
        if ($item && $item['image_url'] && file_exists('../' . $item['image_url'])) {
            unlink('../' . $item['image_url']);
        }
        header('Location: index.php?msg=deleted');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Portofolio</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .dashboard-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 10px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card h3 { font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem; }
        .stat-card p { color: var(--gray); }
        .welcome-banner { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem; }
        .btn-refresh { background: var(--success); color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; }
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
                <li><a href="index.php" class="active"><i class="fas fa-dashboard"></i> Dashboard</a></li>
                <li><a href="messages.php"><i class="fas fa-envelope"></i> Pesan <?php if($unread_messages > 0): ?><span style="background: var(--accent); padding: 2px 8px; border-radius: 20px; font-size: 0.7rem;"><?= $unread_messages ?></span><?php endif; ?></a></li>
                <li><a href="tambah.php"><i class="fas fa-plus"></i> Tambah Portfolio</a></li>
                <li><a href="../index.html"><i class="fas fa-home"></i> Lihat Website</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
        
        <main class="admin-main">
            <div class="welcome-banner">
                <h2><i class="fas fa-chart-line"></i> Selamat Datang di Dashboard</h2>
                <p>Kelola portfolio website Anda di sini. Tambah, edit, atau hapus project portfolio.</p>
            </div>
            
            <div class="dashboard-stats">
                <div class="stat-card" onclick="window.location.href='index.php'">
                    <h3><?= count($portfolio_items) ?></h3>
                    <p><i class="fas fa-folder-open"></i> Total Portfolio</p>
                </div>
                <div class="stat-card" onclick="window.location.href='index.php'">
                    <h3><?= count(array_unique(array_column($portfolio_items, 'category'))) ?></h3>
                    <p><i class="fas fa-tags"></i> Kategori</p>
                </div>
                <div class="stat-card" onclick="window.location.href='messages.php'">
                    <h3><?= $unread_messages ?></h3>
                    <p><i class="fas fa-envelope"></i> Pesan Belum Dibaca</p>
                    <?php if($unread_messages > 0): ?><span style="background: var(--accent); color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.7rem;">New</span><?php endif; ?>
                </div>
                <div class="stat-card">
                    <h3><?= date('d/m/Y') ?></h3>
                    <p><i class="fas fa-calendar"></i> Tanggal</p>
                </div>
            </div>
            
            <div class="page-header">
                <h1><i class="fas fa-list"></i> Manajemen Portfolio</h1>
                <div>
                    <button onclick="location.reload()" class="btn-refresh"><i class="fas fa-sync-alt"></i> Refresh</button>
                    <a href="tambah.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Portfolio</a>
                </div>
            </div>
            
            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php if($_GET['msg'] == 'added') echo 'Portfolio berhasil ditambahkan!'; if($_GET['msg'] == 'updated') echo 'Portfolio berhasil diupdate!'; if($_GET['msg'] == 'deleted') echo 'Portfolio berhasil dihapus!'; ?></div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>ID</th><th>Gambar</th><th>Judul</th><th>Kategori</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php if(count($portfolio_items) > 0): ?>
                            <?php foreach($portfolio_items as $item): ?>
                            <tr>
                                <td style="text-align: center;"><?= $item['id'] ?></td>
                                <td style="text-align: center;">
                                    <?php if($item['image_url'] && file_exists('../' . $item['image_url'])): ?>
                                        <img src="../<?= $item['image_url'] ?>" alt="<?= htmlspecialchars($item['title']) ?>" width="50" height="50" style="object-fit: cover; border-radius: 5px;">
                                    <?php else: ?>
                                        <div style="width:50px; height:50px; background:#eee; display:flex; align-items:center; justify-content:center; border-radius:5px;"><i class="fas fa-image" style="color:#ccc;"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($item['title']) ?></strong></td>
                                <td><span style="background: rgba(102,126,234,0.1); padding: 0.2rem 0.5rem; border-radius: 5px;"><?= htmlspecialchars($item['category']) ?></span></td>
                                <td class="description-cell"><?= htmlspecialchars(substr($item['description'] ?? '', 0, 50)) ?>...</td>
                                <td class="action-buttons">
                                    <a href="edit.php?id=<?= $item['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="javascript:void(0)" onclick="confirmDelete(<?= $item['id'] ?>)" class="btn-delete"><i class="fas fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center"><i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem; display: block;"></i>Belum ada data portfolio. <a href="tambah.php" style="color: var(--primary);">Tambah sekarang</a></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script>
        function confirmDelete(id) { if(confirm('Apakah Anda yakin ingin menghapus portfolio ini?')) { window.location.href = 'index.php?delete=' + id; } }
        setTimeout(() => { const alert = document.querySelector('.alert'); if(alert) { alert.style.transition = 'opacity 0.5s'; alert.style.opacity = '0'; setTimeout(() => alert.remove(), 500); } }, 3000);
    </script>
</body>
</html>