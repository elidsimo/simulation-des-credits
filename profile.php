<?php
session_start();
require_once "connexion.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations actuelles de l'utilisateur
$stmt = $conn->prepare("SELECT nom, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur introuvable.";
    exit();
}

$success = '';
$error = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $mot_de_passe_confirm = $_POST['mot_de_passe_confirm'];

    if (!empty($mot_de_passe)) {
        if ($mot_de_passe !== $mot_de_passe_confirm) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET nom = ?, email = ?, mot_de_passe = ? WHERE id = ?");
            $stmt->execute([$nom, $email, $hashed_password, $user_id]);
            $_SESSION['user_name'] = $nom;
            $success = "Profil mis à jour avec succès !";
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET nom = ?, email = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $user_id]);
        $_SESSION['user_name'] = $nom;
        $success = "Profil mis à jour avec succès !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - CreditSim</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            color: white;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(18px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Navigation retour */
        .back-nav {
            margin-bottom: 2rem;
        }

        .back-link {
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(-5px);
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #00d4ff, #3a7bd5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #00d4ff;
            margin-bottom: 0.5rem;
        }

        .header p {
            opacity: 0.9;
            font-size: 1rem;
        }

        /* Formulaire */
        .profile-form {
            display: grid;
            gap: 2rem;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 15px;
            border-left: 4px solid #00d4ff;
        }

        .section-title {
            color: #00d4ff;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: white;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px 12px 45px;
            border-radius: 12px;
            border: none;
            outline: none;
            font-size: 15px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transition: all 0.3s ease;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 0 0 2px rgba(0, 212, 255, 0.4);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
        }

        /* Messages */
        .message {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slideIn 0.5s ease;
        }

        .message.success {
            background: rgba(0, 255, 136, 0.15);
            border: 1px solid rgba(0, 255, 136, 0.3);
            color: #00ff88;
        }

        .message.error {
            background: rgba(255, 67, 54, 0.15);
            border: 1px solid rgba(255, 67, 54, 0.3);
            color: #ff4336;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Boutons */
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00d4ff, #3a7bd5);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(0, 212, 255, 0.5);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* Password strength indicator */
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }

        .strength-bar {
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak {
            background: #ff4336;
            width: 25%;
        }

        .strength-fair {
            background: #ff9800;
            width: 50%;
        }

        .strength-good {
            background: #4caf50;
            width: 75%;
        }

        .strength-strong {
            background: #00ff88;
            width: 100%;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .btn-group {
                flex-direction: column;
            }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h1><i class="fas fa-user-edit"></i> Mon Profil</h1>
            <p>Gérez vos informations personnelles et votre sécurité</p>
        </div>

        <?php if ($error): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success" id="successMessage">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="profileForm" class="profile-form">
            <!-- Informations personnelles -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-user-circle"></i>
                    Informations personnelles
                </h3>

                <div class="form-group">
                    <label class="form-label" for="nom">Nom complet</label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-user"></i>
                        <input type="text" id="nom" name="nom" class="form-input"
                            value="<?php echo htmlspecialchars($user['nom']); ?>" placeholder="Votre nom complet"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Adresse email</label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-input"
                            value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="votre@email.com"
                            required>
                    </div>
                </div>
            </div>

            <!-- Sécurité -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-shield-alt"></i>
                    Sécurité du compte
                </h3>

                <div class="form-group">
                    <label class="form-label" for="mot_de_passe">Nouveau mot de passe</label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-lock"></i>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-input"
                            placeholder="Laissez vide pour conserver l'actuel">
                    </div>
                    <div class="password-strength" id="passwordStrength" style="display: none;">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <span id="strengthText"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="mot_de_passe_confirm">Confirmer le mot de passe</label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-lock"></i>
                        <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" class="form-input"
                            placeholder="Confirmez votre nouveau mot de passe">
                    </div>
                </div>
            </div>

            <div class="btn-group">
                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                    <i class="fas fa-undo"></i>
                    Annuler
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>

    <script>
        // Validation du mot de passe en temps réel
        const passwordInput = document.getElementById('mot_de_passe');
        const passwordStrength = document.getElementById('passwordStrength');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');

        passwordInput.addEventListener('input', function () {
            const password = this.value;

            if (password.length === 0) {
                passwordStrength.style.display = 'none';
                return;
            }

            passwordStrength.style.display = 'block';

            let score = 0;
            let feedback = '';

            // Critères de validation
            if (password.length >= 8) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            // Mise à jour de l'affichage
            strengthFill.className = 'strength-fill';

            if (score <= 2) {
                strengthFill.classList.add('strength-weak');
                feedback = 'Mot de passe faible';
            } else if (score === 3) {
                strengthFill.classList.add('strength-fair');
                feedback = 'Mot de passe correct';
            } else if (score === 4) {
                strengthFill.classList.add('strength-good');
                feedback = 'Mot de passe fort';
            } else {
                strengthFill.classList.add('strength-strong');
                feedback = 'Mot de passe très fort';
            }

            strengthText.textContent = feedback;
        });

        // Validation du formulaire
        document.getElementById('profileForm').addEventListener('submit', function (e) {
            const password = document.getElementById('mot_de_passe').value;
            const confirmPassword = document.getElementById('mot_de_passe_confirm').value;
            const submitBtn = document.getElementById('submitBtn');

            if (password && password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return;
            }

            // Animation de chargement
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<div class="loading"></div> Mise à jour...';
        });

        // Fonction pour réinitialiser le formulaire
        function resetForm() {
            if (confirm('Êtes-vous sûr de vouloir annuler les modifications ?')) {
                location.reload();
            }
        }

        // Redirection après succès
        <?php if ($success): ?>
            setTimeout(function () {
                window.location.href = 'dash.html';
            }, 2000);
        <?php endif; ?>

        // Animation des inputs au focus
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function () {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function () {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>

</html>