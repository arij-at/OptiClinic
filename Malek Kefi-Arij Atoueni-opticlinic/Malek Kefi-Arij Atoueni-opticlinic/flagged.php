<?php
require_once 'includes/header.php';
?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <div style="width:80px;height:80px;background:var(--gold-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;font-size:2.2rem;color:var(--gold)">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2>Information importante</h2>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $lang_strings['flagged_message'] ?? 'Votre dossier a été marqué pour révision par notre équipe médicale. Nous vous contacterons prochainement.'; ?>
                    </div>
                    <a href="index.php" class="btn btn-primary w-100 mt-2">
                        <i class="fas fa-home me-2"></i>Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
