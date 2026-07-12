<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Perbaiki path
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/crud.php';

$response = ['success' => false, 'data' => [], 'message' => ''];

try {
    $crud = new PortfolioCRUD($pdo);
    $action = $_GET['action'] ?? '';
    
    switch($action) {
        case 'get_all':
            // image_url dari DB sudah relatif terhadap root project (mis. "uploads/foto.jpg"),
            // dan halaman yang memanggil API ini (index.html/portfolio.html) juga ada di root,
            // jadi path TIDAK perlu diubah/diberi prefix apapun di sini.
            $data = $crud->getAll();
            $response = ['success' => true, 'data' => $data, 'count' => count($data), 'message' => ''];
            break;
        case 'get_by_id':
            $id = $_GET['id'] ?? 0;
            if($id) { 
                $data = $crud->getById($id); 
                $response = $data ? ['success' => true, 'data' => $data, 'message' => ''] : ['success' => false, 'data' => [], 'message' => 'Data tidak ditemukan']; 
            }
            else $response['message'] = 'ID tidak valid';
            break;
        default:
            $response['message'] = 'Aksi tidak ditemukan. Gunakan: get_all, get_by_id';
    }
} catch(Exception $e) {
    $response['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
}
echo json_encode($response);
?>