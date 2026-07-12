<?php
// admin/check_notifications.php
require_once '../backend/config.php';
requireLogin();

header('Content-Type: application/json');

$stmt = $pdo->query("SELECT COUNT(*) as unread FROM messages WHERE is_read = 0");
$unread = $stmt->fetch()['unread'];

echo json_encode(['unread' => $unread]);
?>