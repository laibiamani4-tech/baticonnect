<?php
header('Content-Type: application/json');
require_once 'config/database.php';

$search = $_GET['search'] ?? '';
$search = trim($search);

try {
    $sql = "SELECT p.*, f.nom_entreprise 
            FROM produits p
            JOIN fournisseurs f ON f.id = p.fournisseur_id
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (p.nom_produit LIKE :search OR p.categorie LIKE :search OR p.description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    $sql .= " ORDER BY p.date_ajout DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    echo json_encode($results);
    
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>