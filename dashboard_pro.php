<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'professionnel') {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Récupérer les données du professionnel
$stmt = $pdo->prepare("SELECT * FROM professionnels WHERE user_id = ?");
$stmt->execute([$userId]);
$pro = $stmt->fetch();

// Récupérer les données de l'utilisateur
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

// Mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $telephone = trim($_POST['telephone']);
    $specialite = trim($_POST['specialite']);
    $experience = trim($_POST['experience']);
    $localisation = trim($_POST['localisation']);
    
    // Mise à jour des infos utilisateur
    $pdo->prepare("UPDATE users SET nom = ?, telephone = ? WHERE id = ?")->execute([$nom, $telephone, $userId]);
    
    // Mise à jour des infos professionnelles
    $pdo->prepare("UPDATE professionnels SET specialite = ?, experience = ?, localisation = ? WHERE user_id = ?")->execute([$specialite, $experience, $localisation, $userId]);
    
    // Gestion de la photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $allowed)) {
            $photoName = 'pro_' . $userId . '_' . time() . '.' . $ext;
            $photoPath = $uploadDir . $photoName;
            
            // Supprimer l'ancienne photo
            if (!empty($pro['photo']) && file_exists($pro['photo'])) {
                unlink($pro['photo']);
            }
            
            move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
            $pdo->prepare("UPDATE professionnels SET photo = ? WHERE user_id = ?")->execute([$photoPath, $userId]);
            
            // Recharger les données
            $stmt = $pdo->prepare("SELECT * FROM professionnels WHERE user_id = ?");
            $stmt->execute([$userId]);
            $pro = $stmt->fetch();
        }
    }
    
    // Recharger les données utilisateur
    $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch();
    
    header('Location: dashboard_pro.php?updated=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace - Professionnel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
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
        }
        
        .logo { font-size: 1.5rem; font-weight: 800; }
        .logo span { color: #e67e22; }
        
        .user-info { display: flex; align-items: center; gap: 1rem; }
        .user-avatar {
            width: 40px; height: 40px;
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
        }
        
        .btn-logout:hover { background: #dc2626; }
        
        .container {
            max-width: 1200px;
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
        
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 2rem;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            animation: fadeInUp 0.5s ease;
        }
        
        .card:hover { transform: translateY(-5px); }
        
        .card-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 2px solid #e67e22;
            padding-bottom: 0.5rem;
        }
        
        .profile-photo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .profile-photo img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #e67e22;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .photo-upload {
            margin-top: 1rem;
            text-align: center;
        }
        
        .photo-upload label {
            display: inline-block;
            background: #e67e22;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.8rem;
        }
        
        .photo-upload label:hover { background: #d35400; }
        
        .photo-upload input {
            display: none;
        }
        
        .preview-content { margin-bottom: 1rem; }
        
        .preview-item {
            margin-bottom: 1rem;
            padding: 0.8rem;
            background: #f8f9fa;
            border-radius: 12px;
        }
        
        .preview-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #e67e22;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .preview-value {
            font-size: 0.95rem;
            color: #333;
            margin-top: 0.2rem;
            word-break: break-word;
        }
        
        .empty-value {
            color: #999;
            font-style: italic;
        }
        
        .form-group {
            margin-bottom: 1.2rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
            font-size: 0.85rem;
        }
        
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }
        
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #e67e22;
            box-shadow: 0 0 0 3px rgba(230,126,34,0.2);
        }
        
        .btn-save {
            background: linear-gradient(135deg, #e67e22, #d35400);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230,126,34,0.4);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1rem;
            text-align: center;
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
        
        @media (max-width: 800px) {
            .grid-2 { grid-template-columns: 1fr; }
            .container { padding: 0 1rem; }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Bâti<span>Connect</span></div>
        <div class="user-info">
            <div class="user-avatar"><?= htmlspecialchars(substr($user['nom'] ?? '?', 0, 1)) ?></div>
            <span><?= htmlspecialchars($user['nom'] ?? '') ?></span>
            <form action="logout.php" method="POST" style="margin: 0;">
                <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
            </form>
        </div>
    </header>
    
    <div class="container">
        <h1 class="dashboard-title"><i class="fas fa-user-hard-hat"></i> Mon espace professionnel</h1>
        <p class="dashboard-subtitle">Gérez votre profil et apparaissez dans les recherches des clients</p>
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="success"><i class="fas fa-check-circle"></i> Votre profil a été mis à jour avec succès !</div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><i class="fas fa-eye"></i> 124</div>
                <div class="stat-label">Vues de votre profil</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><i class="fas fa-phone"></i> 18</div>
                <div class="stat-label">Contacts reçus</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= date('d/m/Y', strtotime($user['date_inscription'] ?? 'now')) ?></div>
                <div class="stat-label">Membre depuis</div>
            </div>
        </div>
        
        <div class="grid-2">
            <!-- Aperçu public -->
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-globe" style="color:#e67e22;"></i>
                    Aperçu public
                </div>
                
                <div class="profile-photo">
                    <?php 
                    $photoPath = !empty($pro['photo']) ? $pro['photo'] : 'uploads/default-avatar.jpg';
                    ?>
                    <img src="<?= htmlspecialchars($photoPath) ?>" alt="Photo de profil" id="previewPhoto" onerror="this.src='uploads/default-avatar.jpg'">
                </div>
                
                <div class="preview-content">
                    <div class="preview-item">
                        <div class="preview-label">Nom complet</div>
                        <div class="preview-value"><?= htmlspecialchars($user['nom'] ?? 'Non renseigné') ?></div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Spécialité</div>
                        <div class="preview-value"><?= htmlspecialchars($pro['specialite'] ?? 'Non renseignée') ?></div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Expérience</div>
                        <div class="preview-value"><?= !empty($pro['experience']) ? nl2br(htmlspecialchars($pro['experience'])) : '<span class="empty-value">Aucune expérience renseignée</span>' ?></div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Localisation</div>
                        <div class="preview-value"><?= htmlspecialchars($pro['localisation'] ?? 'Non renseignée') ?></div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Téléphone</div>
                        <div class="preview-value"><?= htmlspecialchars($user['telephone'] ?? 'Non renseigné') ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire modification -->
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-edit" style="color:#e67e22;"></i>
                    Modifier mon profil
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nom complet</label>
                        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Téléphone de contact</label>
                        <input type="tel" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" placeholder="0555123456">
                        <small style="color:#888; font-size:0.7rem;">Ce numéro sera visible par les clients</small>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-camera"></i> Photo de profil</label>
                        <div class="photo-upload">
                            <label for="photoUpload">
                                <i class="fas fa-upload"></i> Choisir une photo
                            </label>
                            <input type="file" name="photo" id="photoUpload" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <small style="color:#888; font-size:0.7rem;">Format: JPG, PNG, GIF. Taille max: 5MB</small>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-wrench"></i> Spécialité</label>
                        <input type="text" name="specialite" value="<?= htmlspecialchars($pro['specialite'] ?? '') ?>" required>
                        <small style="color:#888; font-size:0.7rem;">Ex: Électricien, Maçon, Plombier...</small>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-file-alt"></i> Expérience / Description</label>
                        <textarea name="experience" rows="5" placeholder="Décrivez votre parcours, vos compétences, vos années d'expérience..."><?= htmlspecialchars($pro['experience'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Localisation (Ville)</label>
                        <input type="text" name="localisation" value="<?= htmlspecialchars($pro['localisation'] ?? '') ?>" placeholder="Alger, Oran, Sétif...">
                    </div>
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('previewPhoto');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>