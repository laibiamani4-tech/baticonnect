<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telephone = $_POST['telephone'];
    $role = $_POST['role'];
    
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        $error = "Cet email est déjà utilisé";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (nom, email, password, telephone, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $email, $password, $telephone, $role]);
        $userId = $pdo->lastInsertId();
        
        if ($role === 'professionnel') {
            $specialite = $_POST['specialite'];
            $experience = $_POST['experience'];
            $localisation = $_POST['localisation'];
            $stmt2 = $pdo->prepare("INSERT INTO professionnels (user_id, specialite, experience, localisation) VALUES (?, ?, ?, ?)");
            $stmt2->execute([$userId, $specialite, $experience, $localisation]);
        } elseif ($role === 'fournisseur') {
            $nom_entreprise = $_POST['nom_entreprise'];
            $siret = $_POST['siret'];
            $adresse = $_POST['adresse'];
            $stmt2 = $pdo->prepare("INSERT INTO fournisseurs (user_id, nom_entreprise, siret, adresse) VALUES (?, ?, ?, ?)");
            $stmt2->execute([$userId, $nom_entreprise, $siret, $adresse]);
        }
        
        header('Location: login.php?success=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - BâtiConnect</title>
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
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
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
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .container {
            max-width: 600px;
            width: 100%;
            background: rgba(255,255,255,0.95);
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            animation: fadeInUp 0.6s ease;
        }
        
        .header {
            background: linear-gradient(135deg, #e67e22, #d35400);
            padding: 2rem;
            text-align: center;
        }
        
        .logo {
            font-size: 2rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.5rem;
        }
        
        .logo span {
            color: #1a1a2e;
        }
        
        .header p {
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
        }
        
        .form-container {
            padding: 2rem;
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
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #e67e22;
            font-size: 1rem;
        }
        
        .input-group input, .input-group select, .input-group textarea {
            width: 100%;
            padding: 0.9rem 1rem 0.9rem 2.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }
        
        .input-group textarea {
            padding-top: 0.9rem;
            resize: vertical;
        }
        
        .input-group input:focus, .input-group select:focus, .input-group textarea:focus {
            outline: none;
            border-color: #e67e22;
            box-shadow: 0 0 0 3px rgba(230,126,34,0.2);
        }
        
        .role-fields {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 15px;
            animation: fadeInUp 0.4s ease;
        }
        
        .role-fields.active {
            display: block;
        }
        
        .btn-register {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #e67e22, #d35400);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(230,126,34,0.4);
        }
        
        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 0.8rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            text-align: center;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }
        
        .login-link a {
            color: #e67e22;
            text-decoration: none;
            font-weight: 600;
        }
        
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            background: rgba(230,126,34,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape-1 { width: 300px; height: 300px; top: -100px; right: -100px; }
        .shape-2 { width: 200px; height: 200px; bottom: -50px; left: -50px; animation-delay: 1s; }
        .shape-3 { width: 150px; height: 150px; top: 50%; left: 20%; animation-delay: 2s; }
        
        @media (max-width: 600px) {
            .row {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            .container {
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <div class="container">
        <div class="header">
            <div class="logo">Bâti<span>Connect</span></div>
            <p><i class="fas fa-user-plus"></i> Rejoignez notre communauté</p>
        </div>
        
        <div class="form-container">
            <?php if (isset($error)): ?>
                <div class="error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" id="registerForm">
                <div class="row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nom complet</label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" name="nom" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Mot de passe</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" minlength="8" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Téléphone</label>
                        <div class="input-group">
                            <i class="fas fa-phone"></i>
                            <input type="tel" name="telephone" placeholder="0555123456">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user-tag"></i> Je suis</label>
                    <div class="input-group">
                        <i class="fas fa-briefcase"></i>
                        <select name="role" id="role" required onchange="toggleRoleFields()">
                            <option value="">-- Choisir --</option>
                            <option value="professionnel">👷 Professionnel (artisan, expert)</option>
                            <option value="fournisseur">🏪 Fournisseur (vendeur de matériaux)</option>
                        </select>
                    </div>
                </div>
                
                <div id="proFields" class="role-fields">
                    <h4 style="margin-bottom: 1rem; color: #e67e22;"><i class="fas fa-briefcase"></i> Informations professionnelles</h4>
                    <div class="form-group">
                        <label>Spécialité</label>
                        <div class="input-group">
                            <i class="fas fa-wrench"></i>
                            <input type="text" name="specialite" placeholder="Électricien, Maçon, Plombier...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Expérience / Description</label>
                        <div class="input-group">
                            <i class="fas fa-file-alt"></i>
                            <textarea name="experience" rows="3" placeholder="Décrivez votre parcours, vos compétences..."></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Localisation (Ville)</label>
                        <div class="input-group">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" name="localisation" placeholder="Alger, Oran, Sétif...">
                        </div>
                    </div>
                </div>
                
                <div id="fourFields" class="role-fields">
                    <h4 style="margin-bottom: 1rem; color: #e67e22;"><i class="fas fa-store"></i> Informations entreprise</h4>
                    <div class="form-group">
                        <label>Nom de l'entreprise</label>
                        <div class="input-group">
                            <i class="fas fa-building"></i>
                            <input type="text" name="nom_entreprise" placeholder="Matériaux Pro SNC">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>SIRET (optionnel)</label>
                        <div class="input-group">
                            <i class="fas fa-qrcode"></i>
                            <input type="text" name="siret" placeholder="41234567890123">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Adresse</label>
                        <div class="input-group">
                            <i class="fas fa-address-card"></i>
                            <input type="text" name="adresse" placeholder="Zone Industrielle, Sétif">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-register">
                    <i class="fas fa-check-circle"></i> Créer mon compte
                </button>
            </form>
            
            <div class="login-link">
                Déjà un compte ? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Se connecter</a>
            </div>
        </div>
    </div>
    
    <script>
        function toggleRoleFields() {
            const role = document.getElementById('role').value;
            const proFields = document.getElementById('proFields');
            const fourFields = document.getElementById('fourFields');
            
            proFields.classList.remove('active');
            fourFields.classList.remove('active');
            
            if (role === 'professionnel') {
                proFields.classList.add('active');
                document.querySelectorAll('#proFields input, #proFields textarea').forEach(el => el.required = true);
                document.querySelectorAll('#fourFields input').forEach(el => el.required = false);
            } else if (role === 'fournisseur') {
                fourFields.classList.add('active');
                document.querySelectorAll('#fourFields input').forEach(el => el.required = true);
                document.querySelectorAll('#proFields input, #proFields textarea').forEach(el => el.required = false);
            }
        }
    </script>
</body>
</html>