<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/functions.php';
$lang = $_SESSION['lang'] ?? 'fr';
require_once __DIR__ . '/../lang/' . $lang . '.php';

$dir = ($lang === 'ar') ? 'rtl' : 'ltr';
$lang_attr = ($lang === 'ar') ? 'ar' : 'fr';
$currentPath = $_SERVER['REQUEST_URI'];
$showLangToggle = !preg_match('#/(doctor|receptionist)(/|$)#', $currentPath);
$assetPrefix = preg_match('#/(doctor|receptionist)(/|$)#', $_SERVER['SCRIPT_NAME']) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang_attr; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiClinic — Soins Oculaires Professionnels</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo $assetPrefix; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="<?php echo $assetPrefix; ?>index.php">
            <span class="brand-dot"><i class="fas fa-eye" style="font-size:.85rem"></i></span>
            Opti<span class="brand-dot">Clinic</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <?php if ($showLangToggle): ?>
            <form method="post" class="m-0">
                <input type="hidden" name="toggle_lang" value="1">
                <button type="submit" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-globe me-1"></i><?php echo $lang === 'fr' ? 'العربية' : 'Français'; ?>
                </button>
            </form>
            <?php endif; ?>

            <?php if (isset($_SESSION['role']) && !$showLangToggle): ?>
                <?php if ($_SESSION['role'] === 'receptionist'): ?>
                    <a href="<?php echo $assetPrefix; ?>receptionist/dashboard.php" class="btn btn-nav-primary">
                        <i class="fas fa-th-large me-1"></i><?php echo $lang_strings['dashboard']; ?>
                    </a>
                    <a href="<?php echo $assetPrefix; ?>receptionist/logout.php" class="btn btn-nav-secondary">
                        <i class="fas fa-sign-out-alt me-1"></i><?php echo $lang_strings['logout']; ?>
                    </a>
                <?php elseif ($_SESSION['role'] === 'doctor'): ?>
                    <a href="<?php echo $assetPrefix; ?>doctor/dashboard.php" class="btn btn-nav-primary">
                        <i class="fas fa-th-large me-1"></i><?php echo $lang_strings['dashboard']; ?>
                    </a>
                    <a href="<?php echo $assetPrefix; ?>doctor/logout.php" class="btn btn-nav-secondary">
                        <i class="fas fa-sign-out-alt me-1"></i><?php echo $lang_strings['logout']; ?>
                    </a>
                <?php endif; ?>
            <?php elseif (!isset($_SESSION['role'])): ?>
                <a href="<?php echo $assetPrefix; ?>receptionist/login.php" class="btn btn-nav-secondary">
                    <i class="fas fa-headset me-1"></i>Réceptionniste
                </a>
                <a href="<?php echo $assetPrefix; ?>doctor/login.php" class="btn btn-nav-primary">
                    <i class="fas fa-user-md me-1"></i>Médecin
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php
if (isset($_POST['toggle_lang'])) {
    $_SESSION['lang'] = $lang === 'fr' ? 'ar' : 'fr';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}
?>
