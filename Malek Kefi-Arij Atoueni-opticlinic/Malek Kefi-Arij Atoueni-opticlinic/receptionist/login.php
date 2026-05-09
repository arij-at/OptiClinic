<?php
require_once '../includes/init.php';
require_once '../includes/db.php';
require_once '../models/UserModel.php';
require_once '../includes/lang.php';
$lang = $_SESSION['lang'] ?? 'fr';
require_once "../lang/$lang.php";
require_once '../includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email    = htmlspecialchars(strip_tags(trim($_POST['email'])));
    $password = $_POST['password'];
    $user = UserModel::findByEmail($pdo, $email);
    if ($user && $user['role'] === 'receptionist' && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Identifiants invalides. Veuillez réessayer.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiClinic — Espace Réceptionniste</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="login-page-wrapper">
    <div class="login-decorative d1"></div>
    <div class="login-decorative d2"></div>
    <div class="container d-flex justify-content-center">
        <div class="login-card">
            <a href="../index.php" style="position:absolute;top:1.2rem;left:1.5rem;font-size:.82rem;color:var(--gray-400)">
                <i class="fas fa-arrow-left me-1"></i> Accueil
            </a>

            <div class="login-card-icon receptionist">
                <i class="fas fa-headset"></i>
            </div>

            <h3>Connexion</h3>
            <span class="login-role-tag"><i class="fas fa-circle me-1" style="font-size:.5rem"></i>Espace Réceptionniste</span>

            <?php if ($error): ?>
                <div class="alert alert-danger mb-3"><i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <div class="mb-3">
                    <label class="form-label"><?php echo htmlspecialchars($lang_strings['email_address'], ENT_QUOTES, 'UTF-8'); ?></label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" class="form-control" placeholder="votre@email.com" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo htmlspecialchars($lang_strings['password'], ENT_QUOTES, 'UTF-8'); ?></label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-login"><?php echo htmlspecialchars($lang_strings['login'], ENT_QUOTES, 'UTF-8'); ?></button>
            </form>

            <div class="login-back-link">
                Médecin ? <a href="../doctor/login.php">Connexion espace médecin</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
