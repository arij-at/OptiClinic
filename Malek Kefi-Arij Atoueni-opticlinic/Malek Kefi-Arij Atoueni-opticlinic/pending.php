<?php
require_once 'includes/header.php';
?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <div style="width:80px;height:80px;background:var(--teal-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;font-size:2.2rem;color:var(--teal)">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h2>Demande reçue !</h2>
                <p style="color:var(--gray-600)">Votre demande de rendez-vous a bien été enregistrée.</p>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Votre demande est en cours de traitement. Un email de confirmation vous sera envoyé une fois le créneau validé par notre équipe.
                    </div>
                    <p style="font-size:.9rem;color:var(--gray-600);margin-bottom:1.5rem">
                        Notre réceptionniste vous contactera dans les meilleurs délais. En cas d'urgence, appelez-nous au <strong>+216 71 234 567</strong>.
                    </p>
                    <a href="index.php" class="btn btn-primary w-100">
                        <i class="fas fa-home me-2"></i>Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
