<?php
require_once '../backend/config.php';
requireLogin();

// Tandai sebagai sudah dibaca
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = :id");
    $stmt->execute([':id' => $_GET['read']]);
    header('Location: messages.php');
    exit();
}

// Hapus pesan
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    header('Location: messages.php?msg=deleted');
    exit();
}

// Ambil semua pesan
$stmt = $pdo->query("SELECT * FROM messages ORDER BY is_read ASC, created_at DESC");
$messages = $stmt->fetchAll();

// Hitung pesan belum dibaca
$stmt = $pdo->query("SELECT COUNT(*) as unread FROM messages WHERE is_read = 0");
$unread_count = $stmt->fetch()['unread'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .message-card { background: white; border-radius: 10px; margin-bottom: 1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; transition: all 0.3s; }
        .message-card.unread { border-left: 4px solid var(--accent); background: #fffef7; }
        .message-header { padding: 1rem; background: #f8f9fa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; }
        .message-info { display: flex; gap: 1.5rem; flex-wrap: wrap; }
        .message-info span { font-size: 0.9rem; color: var(--gray); }
        .message-info i { margin-right: 0.3rem; color: var(--primary); }
        .message-subject { font-weight: bold; font-size: 1.1rem; margin-bottom: 0.5rem; }
        .message-body { padding: 1rem; color: var(--dark); line-height: 1.6; }
        .message-actions { padding: 0.8rem 1rem; border-top: 1px solid #eee; display: flex; gap: 1rem; flex-wrap: wrap; }
        .badge-unread { background: var(--accent); color: white; padding: 0.2rem 0.8rem; border-radius: 20px; font-size: 0.7rem; }
        .badge-read { background: var(--gray); color: white; padding: 0.2rem 0.8rem; border-radius: 20px; font-size: 0.7rem; }
        .btn-small { padding: 5px 12px; font-size: 0.8rem; }
        .empty-state { text-align: center; padding: 3rem; color: var(--gray); }
        .empty-state i { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }
        .refresh-btn { position: fixed; bottom: 20px; right: 20px; background: var(--primary); color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 2px 10px rgba(0,0,0,0.2); transition: all 0.3s; z-index: 100; }
        .refresh-btn:hover { transform: rotate(180deg); background: var(--accent); }
    </style>
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <div class="nav-brand"><h2><i class="fas fa-portfolio"></i> Portfolio Admin</h2><small>Halo, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></small></div>
            <ul class="nav-menu">
                <li><a href="index.php"><i class="fas fa-dashboard"></i> Dashboard</a></li>
                <li><a href="messages.php" class="active"><i class="fas fa-envelope"></i> Pesan <?php if($unread_count > 0): ?><span style="background: var(--accent); padding: 2px 8px; border-radius: 20px; font-size: 0.7rem;"><?= $unread_count ?></span><?php endif; ?></a></li>
                <li><a href="tambah.php"><i class="fas fa-plus"></i> Tambah Portfolio</a></li>
                <li><a href="../index.html"><i class="fas fa-home"></i> Lihat Website</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
        
        <main class="admin-main">
            <div class="page-header">
                <h1><i class="fas fa-envelope"></i> Pesan Masuk</h1>
                <?php if($unread_count > 0): ?><span style="background: var(--accent); color: white; padding: 5px 15px; border-radius: 20px;"><i class="fas fa-bell"></i> <?= $unread_count ?> pesan baru</span><?php endif; ?>
            </div>
            
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?><div class="alert alert-success">✅ Pesan berhasil dihapus!</div><?php endif; ?>
            
            <?php if(count($messages) > 0): ?>
                <?php foreach($messages as $msg): ?>
                    <div class="message-card <?= $msg['is_read'] ? '' : 'unread' ?>">
                        <div class="message-header">
                            <div class="message-info">
                                <span><i class="fas fa-user"></i> <?= htmlspecialchars($msg['name']) ?></span>
                                <span><i class="fas fa-envelope"></i> <?= htmlspecialchars($msg['email']) ?></span>
                                <span><i class="fas fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></span>
                                <?php if(!$msg['is_read']): ?><span class="badge-unread"><i class="fas fa-circle"></i> Belum dibaca</span><?php else: ?><span class="badge-read"><i class="fas fa-check-circle"></i> Sudah dibaca</span><?php endif; ?>
                            </div>
                        </div>
                        <div class="message-body">
                            <div class="message-subject"><?= htmlspecialchars($msg['subject'] ?: '(Tanpa Subjek)') ?></div>
                            <p style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                        </div>
                        <div class="message-actions">
                            <?php if(!$msg['is_read']): ?><a href="?read=<?= $msg['id'] ?>" class="btn-edit btn-small"><i class="fas fa-check"></i> Tandai sudah dibaca</a><?php endif; ?>
                            <a href="mailto:<?= $msg['email'] ?>?subject=Balasan: <?= urlencode($msg['subject']) ?>" class="btn-primary btn-small" style="background: var(--success);"><i class="fas fa-reply"></i> Balas Email</a>
                            <a href="?delete=<?= $msg['id'] ?>" class="btn-delete btn-small" onclick="return confirm('Hapus pesan ini?')"><i class="fas fa-trash"></i> Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state"><i class="fas fa-inbox"></i><h3>Belum ada pesan</h3><p>Belum ada pengunjung yang mengirim pesan melalui formulir kontak.</p></div>
            <?php endif; ?>
        </main>
    </div>
    <a href="messages.php" class="refresh-btn" title="Refresh"><i class="fas fa-sync-alt"></i></a>
</body>
</html>