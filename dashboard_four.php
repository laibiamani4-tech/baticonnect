<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'fournisseur') {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM fournisseurs WHERE user_id = ?");
$stmt->execute([$userId]);
$fournisseur = $stmt->fetch();
$fournisseurId = $fournisseur['id'];

$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

$produits = $pdo->prepare("SELECT * FROM produits WHERE fournisseur_id = ? ORDER BY date_ajout DESC");
$produits->execute([$fournisseurId]);
$produitsList = $produits->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nom_produit = $_POST['nom_produit'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $categorie = $_POST['categorie'];
    $stock = $_POST['stock'];
    $telephone_contact = $_POST['telephone_contact'];
    $email_contact = $_POST['email_contact'];
    $facebook_link = $_POST['facebook_link'];
    $localisation = $_POST['localisation'];
    
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = $uploadDir . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }
    
    $stmt = $pdo->prepare("INSERT INTO produits (fournisseur_id, nom_produit, description, prix, image, categorie, stock, telephone_contact, email_contact, facebook_link, localisation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fournisseurId, $nom_produit, $description, $prix, $image, $categorie, $stock, $telephone_contact, $email_contact, $facebook_link, $localisation]);
    
    header('Location: dashboard_four.php?added=1');
    exit;
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM produits WHERE id = ? AND fournisseur_id = ?")->execute([$_GET['delete'], $fournisseurId]);
    header('Location: dashboard_four.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace - Fournisseur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        header {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: white;
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 800;
        }
        
        .logo span {
            color: #e67e22;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: #e67e22;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-logout:hover {
            background: #dc2626;
        }
        
        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            animation: fadeInUp 0.4s ease;
        }
        
        .dashboard-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 0.5rem;
        }
        
        .dashboard-subtitle {
            color: #666;
            margin-bottom: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.2rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: #e67e22;
        }
        
        .stat-label {
            font-size: 0.7rem;
            color: #666;
            text-transform: uppercase;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            animation: fadeInUp 0.5s ease;
        }
        
        .form-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e67e22;
        }
        
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
            font-size: 0.85rem;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }
        
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #e67e22;
            box-shadow: 0 0 0 3px rgba(230,126,34,0.2);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #e67e22, #d35400);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230,126,34,0.4);
        }
        
        .btn-danger {
            background: #dc2626;
        }
        
        .btn-danger:hover {
            background: #b91c1c;
            box-shadow: 0 5px 15px rgba(220,38,38,0.4);
        }
        
        .products-table {
            background: white;
            border-radius: 20px;
            overflow-x: auto;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f8f9fa;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
        }
        
        td {
            padding: 1rem;
            border-top: 1px solid #eee;
            vertical-align: middle;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .badge {
            display: inline-block;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .badge-ciment { background: #e8f8f5; color: #1abc9c; }
        .badge-fer { background: #fef3e2; color: #e67e22; }
        .badge-carrelage { background: #e8eaf6; color: #3f51b5; }
        .badge-peinture { background: #fce4ec; color: #e91e63; }
        .badge-platre { background: #fff3e0; color: #ff9800; }
        
        @media (max-width: 800px) {
            .row {
                grid-template-columns: 1fr;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .container {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Bâti<span>Connect</span></div>
        <div class="user-info">
            <div class="user-avatar"><?= substr($fournisseur['nom_entreprise'], 0, 1) ?></div>
            <span><?= htmlspecialchars($fournisseur['nom_entreprise']) ?></span>
            <form action="logout.php" method="POST" style="margin: 0;">
                <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
            </form>
        </div>
    </header>
    
    <div class="container">
        <h1 class="dashboard-title"><i class="fas fa-store"></i> Mon espace fournisseur</h1>
        <p class="dashboard-subtitle">Gérez votre catalogue de produits et vos informations</p>
        
        <?php if (isset($_GET['added'])): ?>
            <div class="success"><i class="fas fa-check-circle"></i> Produit ajouté avec succès !</div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= count($produitsList) ?></div>
                <div class="stat-label">Produits</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><i class="fas fa-eye"></i> 89</div>
                <div class="stat-label">Vues totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><i class="fas fa-phone"></i> 12</div>
                <div class="stat-label">Contacts</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= array_sum(array_column($produitsList, 'stock')) ?></div>
                <div class="stat-label">Stock total</div>
            </div>
        </div>
        
        <!-- Formulaire ajout produit -->
        <div class="form-card">
            <div class="form-title">
                <i class="fas fa-plus-circle" style="color:#e67e22;"></i>
                Ajouter un nouveau produit
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="row">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Nom du produit *</label>
                        <input type="text" name="nom_produit" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-chart-line"></i> Catégorie</label>
                        <select name="categorie">
                            <option value="ciment">Ciment</option>
                            <option value="fer">Fer à béton</option>
                            <option value="carrelage">Carrelage</option>
                            <option value="peinture">Peinture</option>
                            <option value="platre">Plâtre</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description</label>
                    <textarea name="description" rows="3" placeholder="Description détaillée du produit..."></textarea>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label><i class="fas fa-dollar-sign"></i> Prix (DA) *</label>
                        <input type="number" step="0.01" name="prix" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-boxes"></i> Stock disponible</label>
                        <input type="number" name="stock" value="0">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label><i class="fas fa-image"></i> Photo du produit</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Téléphone de contact</label>
                        <input type="tel" name="telephone_contact" placeholder="0555123456">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email de contact</label>
                        <input type="email" name="email_contact" placeholder="contact@entreprise.com">
                    </div>
                    <div class="form-group">
                        <label><i class="fab fa-facebook"></i> Facebook (lien)</label>
                        <input type="url" name="facebook_link" placeholder="https://facebook.com/...">
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Localisation (Adresse/Ville)</label>
                    <input type="text" name="localisation" placeholder="Zone Industrielle, Sétif">
                </div>
                <button type="submit" class="btn-submit"><i class="fas fa-plus"></i> Ajouter le produit</button>
            </form>
        </div>
        
        <!-- Liste des produits -->
        <div class="form-title" style="margin-top: 1rem;">
            <i class="fas fa-list"></i>
            Mes produits (<?= count($produitsList) ?>)
        </div>
        
        <div class="products-table">
            <?php if (empty($produitsList)): ?>
                <div style="padding: 2rem; text-align: center; color: #888;">
                    <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                    Aucun produit pour le moment. Ajoutez votre premier produit ci-dessus.
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr><th>Image</th><th>Produit</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Contact</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produitsList as $p): ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($p['image'] ?? 'https://via.placeholder.com/60') ?>" class="product-image" onerror="this.src='https://via.placeholder.com/60'"></td>
                            <td><strong><?= htmlspecialchars($p['nom_produit']) ?></strong><br><small><?= htmlspecialchars(substr($p['description'], 0, 50)) ?>...</small></td>
                            <td><span class="badge badge-<?= $p['categorie'] ?>"><?= ucfirst($p['categorie']) ?></span></td>
                            <td><?= number_format($p['prix'], 0) ?> DA</td>
                            <td><?= $p['stock'] ?> unités</td>
                            <td>
                                <?php if ($p['telephone_contact']): ?><i class="fas fa-phone"></i> <?= $p['telephone_contact'] ?><br><?php endif; ?>
                                <?php if ($p['email_contact']): ?><i class="fas fa-envelope"></i> <?= substr($p['email_contact'], 0, 20) ?><?php endif; ?>
                            </td>
                            <td>
                                <a href="?delete=<?= $p['id'] ?>" class="btn-submit btn-danger" style="padding: 0.3rem 0.8rem; font-size: 0.8rem; text-decoration: none;" onclick="return confirm('Supprimer ce produit ?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>