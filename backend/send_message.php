<?php
header('Content-Type: application/json');
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $name = trim($input['name'] ?? '');
    $email = trim($input['email'] ?? '');
    $subject = trim($input['subject'] ?? '');
    $message = trim($input['message'] ?? '');
    
    if (empty($name)) $response['message'] = 'Nama harus diisi';
    elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $response['message'] = 'Email tidak valid';
    elseif (empty($message)) $response['message'] = 'Pesan harus diisi';
    else {
        try {
            $sql = "INSERT INTO messages (name, email, subject, message, created_at) VALUES (:name, :email, :subject, :message, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':name' => $name, ':email' => $email, ':subject' => $subject, ':message' => $message]);
            $response = ['success' => true, 'message' => 'Pesan berhasil dikirim. Terima kasih!'];
        } catch(PDOException $e) {
            $response['message'] = 'Gagal menyimpan pesan';
        }
    }
} else {
    $response['message'] = 'Method tidak diizinkan';
}
echo json_encode($response);
?>