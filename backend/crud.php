<?php
require_once 'config.php';

class PortfolioCRUD {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function create($title, $description, $category, $image_url, $project_url) {
        $sql = "INSERT INTO portfolio_items (title, description, category, image_url, project_url) VALUES (:title, :description, :category, :image_url, :project_url)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':title' => $title, ':description' => $description, ':category' => $category, ':image_url' => $image_url, ':project_url' => $project_url]);
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM portfolio_items ORDER BY created_at DESC");
        // Kembalikan path apa adanya dari database (relatif terhadap root project, mis. "uploads/foto.jpg").
        // JANGAN prepend '../' di sini - biarkan pemanggil (admin/*.php atau backend/api.php)
        // yang menentukan prefix path sesuai lokasi file mereka masing-masing.
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM portfolio_items WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function update($id, $title, $description, $category, $image_url, $project_url) {
        $sql = "UPDATE portfolio_items SET title = :title, description = :description, category = :category, image_url = :image_url, project_url = :project_url WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id, ':title' => $title, ':description' => $description, ':category' => $category, ':image_url' => $image_url, ':project_url' => $project_url]);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM portfolio_items WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    public function getByCategory($category) {
        $stmt = $this->pdo->prepare("SELECT * FROM portfolio_items WHERE category = :category ORDER BY created_at DESC");
        $stmt->execute([':category' => $category]);
        return $stmt->fetchAll();
    }
}
?>