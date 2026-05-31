<?php
header('Content-Type: application/json');
require_once 'config/database.php';

$search = $_GET['search'] ?? '';
$search = trim($search);

try {
    // Récupérer les professionnels avec leurs photos
    $sql = "SELECT p.*, u.nom, u.telephone, DATE(u.date_inscription) as date_inscription 
            FROM professionnels p 
            JOIN users u ON u.id = p.user_id 
            WHERE u.role = 'professionnel' AND u.statut = 'actif'";
    
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (u.nom LIKE :search OR p.specialite LIKE :search OR p.localisation LIKE :search OR p.experience LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    $sql .= " ORDER BY u.date_inscription DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    // S'assurer que la photo est définie
    foreach ($results as &$pro) {
        if (empty($pro['photo'])) {
            $pro['photo'] = 'uploads/default-avatar.jpg';
        }
    }
    
    echo json_encode($results);
    
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>