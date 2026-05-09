<?php
require_once 'includes/header.php';
?>

<!-- HERO -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-eyebrow">
                    <i class="fas fa-certificate"></i> <?php echo $lang_strings['hero_badge']; ?>
                </div>
                <h1>
                    <?php echo $lang_strings['hero_heading_part1']; ?><br>
                    <span class="accent"><?php echo $lang_strings['hero_heading_part2']; ?></span><br>
                    <?php echo $lang_strings['hero_heading_part3']; ?>
                </h1>
                <p class="hero-lead">
                    <?php echo $lang_strings['hero_lead']; ?>
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#intakeForm" class="btn-hero-primary">
                        <i class="fas fa-clipboard-list"></i> <?php echo $lang_strings['hero_button_start']; ?>
                    </a>
                    <a href="#services" class="btn-hero-secondary">
                        <i class="fas fa-info-circle"></i> <?php echo $lang_strings['hero_button_services']; ?>
                    </a>
                </div>
                <div class="hero-stats">
                    <div>
                        <div class="hero-stat-number">2 500<span style="color:var(--teal)">+</span></div>
                        <div class="hero-stat-label"><?php echo $lang_strings['stats_patients']; ?></div>
                    </div>
                    <div>
                        <div class="hero-stat-number">8<span style="color:var(--teal)">+</span></div>
                        <div class="hero-stat-label"><?php echo $lang_strings['stats_specialists']; ?></div>
                    </div>
                    <div>
                        <div class="hero-stat-number">98<span style="color:var(--teal)">%</span></div>
                        <div class="hero-stat-label"><?php echo $lang_strings['stats_satisfaction']; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-flex">
                <div class="hero-visual w-100">
                    <div class="hero-eye-graphic">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="hero-badge badge-tl">
                        <span class="badge-dot green"></span>
                        <?php echo $lang_strings['hero_badge']; ?>
                    </div>
                    <div class="hero-badge badge-br">
                        <span class="badge-dot gold"></span>
                        <?php echo $lang_strings['hero_quick_care']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS STRIP -->
<div class="stats-strip">
    <div class="container">
        <div class="row">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <span class="stat-number">2 500<span>+</span></span>
                    <span class="stat-label"><?php echo $lang_strings['stats_patients']; ?></span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <span class="stat-number">8<span>+</span></span>
                    <span class="stat-label"><?php echo $lang_strings['stats_specialists']; ?></span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <span class="stat-number">15<span>+</span></span>
                    <span class="stat-label"><?php echo $lang_strings['stats_experience']; ?></span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <span class="stat-number">98<span>%</span></span>
                    <span class="stat-label"><?php echo $lang_strings['stats_satisfaction']; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SERVICES -->
<section class="services-section" id="services">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag"><?php echo $lang_strings['services_tag']; ?></span>
            <h2 class="section-title"><?php echo $lang_strings['services_title']; ?></h2>
            <div class="section-divider"></div>
            <p class="section-subtitle"><?php echo $lang_strings['services_subtitle']; ?></p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-eye"></i></div>
                    <h5><?php echo $lang_strings['service_vision_exams']; ?></h5>
                    <p><?php echo $lang_strings['service_vision_exams_desc']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-glasses"></i></div>
                    <h5><?php echo $lang_strings['service_vision_correction']; ?></h5>
                    <p><?php echo $lang_strings['service_vision_correction_desc']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-stethoscope"></i></div>
                    <h5><?php echo $lang_strings['service_ocular_pathology']; ?></h5>
                    <p><?php echo $lang_strings['service_ocular_pathology_desc']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-child"></i></div>
                    <h5><?php echo $lang_strings['service_pediatric_ophthalmology']; ?></h5>
                    <p><?php echo $lang_strings['service_pediatric_ophthalmology_desc']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-laser"></i></div>
                    <h5><?php echo $lang_strings['service_refractive_surgery']; ?></h5>
                    <p><?php echo $lang_strings['service_refractive_surgery_desc']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-heartbeat"></i></div>
                    <h5><?php echo $lang_strings['service_followup']; ?></h5>
                    <p><?php echo $lang_strings['service_followup_desc']; ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag"><?php echo $lang_strings['process_tag']; ?></span>
            <h2 class="section-title"><?php echo $lang_strings['process_title']; ?></h2>
            <div class="section-divider"></div>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-3 col-sm-6">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <h5><?php echo $lang_strings['process_step1']; ?></h5>
                    <p><?php echo $lang_strings['process_step1_desc']; ?></p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="step-item">
                    <div class="step-number">2</div>
                    <h5><?php echo $lang_strings['process_step2']; ?></h5>
                    <p><?php echo $lang_strings['process_step2_desc']; ?></p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="step-item">
                    <div class="step-number">3</div>
                    <h5><?php echo $lang_strings['process_step3']; ?></h5>
                    <p><?php echo $lang_strings['process_step3_desc']; ?></p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="step-item">
                    <div class="step-number">4</div>
                    <h5><?php echo $lang_strings['process_step4']; ?></h5>
                    <p><?php echo $lang_strings['process_step4_desc']; ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TEAM -->
<section class="team-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag"><?php echo $lang_strings['team_tag']; ?></span>
            <h2 class="section-title"><?php echo $lang_strings['team_title']; ?></h2>
            <div class="section-divider"></div>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-lg-3 col-md-6">
                <div class="team-card">
                    <div class="team-avatar-placeholder"><i class="fas fa-user-md"></i></div>
                    <div class="team-info">
                        <h5>Dr. Sami Bouaziz</h5>
                        <div class="specialty">Ophtalmologue Chef</div>
                        <p style="font-size:.83rem;margin-top:.5rem;color:var(--gray-400)">20 ans d'expérience • Chirurgie réfractive</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="team-card">
                    <div class="team-avatar-placeholder"><i class="fas fa-user-md"></i></div>
                    <div class="team-info">
                        <h5>Dr. Leila Mansour</h5>
                        <div class="specialty">Pédiatrie Oculaire</div>
                        <p style="font-size:.83rem;margin-top:.5rem;color:var(--gray-400)">15 ans d'expérience • Strabisme</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="team-card">
                    <div class="team-avatar-placeholder"><i class="fas fa-user-md"></i></div>
                    <div class="team-info">
                        <h5>Dr. Anis Trabelsi</h5>
                        <div class="specialty">Glaucome & DMLA</div>
                        <p style="font-size:.83rem;margin-top:.5rem;color:var(--gray-400)">12 ans d'expérience • Rétinologie</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="team-card">
                    <div class="team-avatar-placeholder"><i class="fas fa-user-nurse"></i></div>
                    <div class="team-info">
                        <h5>Mme. Ines Kouki</h5>
                        <div class="specialty">Orthoptiste</div>
                        <p style="font-size:.83rem;margin-top:.5rem;color:var(--gray-400)">8 ans d'expérience • Rééducation visuelle</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- INTAKE FORM -->
<section class="intake-section" id="intakeForm">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="form-wrapper">
                    <div class="form-header">
                        <h2><i class="fas fa-clipboard-list me-2" style="color:var(--teal)"></i><?php echo $lang_strings['intake_form']; ?></h2>
                        <p><?php echo $lang_strings['intake_description']; ?></p>
                    </div>
                    <div class="form-body">
                        <form id="mainIntakeForm" method="post" action="submit.php" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                            <!-- Section 1 -->
                            <div class="form-section-heading">
                                <div class="step-badge">1</div>
                                <h3><?php echo $lang_strings['your_information']; ?></h3>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="name" class="form-label"><?php echo $lang_strings['full_name']; ?> *</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Votre nom complet" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="dob" class="form-label"><?php echo $lang_strings['date_of_birth']; ?> *</label>
                                    <input type="date" class="form-control" id="dob" name="date_of_birth" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="phone" class="form-label"><?php echo $lang_strings['phone_number']; ?> *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+216 XX XXX XXX" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label"><?php echo $lang_strings['email_address']; ?> *</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="exemple@email.com" required>
                                </div>
                            </div>

                            <!-- Section 2 -->
                            <div class="form-section-heading">
                                <div class="step-badge">2</div>
                                <h3><?php echo $lang_strings['eye_right_now']; ?></h3>
                            </div>

                            <!-- Q1: Which Eye -->
                            <div class="question-card" data-question="which_eye">
                                <h5><i class="fas fa-eye me-2" style="color:var(--teal)"></i><?php echo $lang_strings['which_eye']; ?> *</h5>
                                <div class="d-flex flex-wrap justify-content-center">
                                    <button type="button" class="btn btn-outline-primary text-option-btn" data-value="left"><?php echo $lang_strings['left_eye']; ?></button>
                                    <button type="button" class="btn btn-outline-primary text-option-btn" data-value="right"><?php echo $lang_strings['right_eye']; ?></button>
                                    <button type="button" class="btn btn-outline-primary text-option-btn" data-value="both"><?php echo $lang_strings['both_eyes']; ?></button>
                                </div>
                                <input type="hidden" name="q_which_eye" id="q_which_eye">
                            </div>

                            <!-- Q2: How Long -->
                            <div class="question-card" data-question="how_long">
                                <h5><i class="fas fa-clock me-2" style="color:var(--teal)"></i><?php echo $lang_strings['how_long']; ?> *</h5>
                                <div class="d-flex flex-wrap justify-content-center">
                                    <button type="button" class="btn btn-outline-primary text-option-btn" data-value="today"><?php echo $lang_strings['started_today']; ?></button>
                                    <button type="button" class="btn btn-outline-primary text-option-btn" data-value="few_days"><?php echo $lang_strings['few_days']; ?></button>
                                    <button type="button" class="btn btn-outline-primary text-option-btn" data-value="week"><?php echo $lang_strings['about_week']; ?></button>
                                    <button type="button" class="btn btn-outline-primary text-option-btn" data-value="more"><?php echo $lang_strings['more_than_two_weeks']; ?></button>
                                </div>
                                <input type="hidden" name="q_how_long" id="q_how_long">
                            </div>

                            <!-- Q3: Pain Level -->
                            <div class="question-card" data-question="pain_level">
                                <h5><i class="fas fa-thermometer-half me-2" style="color:var(--teal)"></i><?php echo $lang_strings['pain_level']; ?> *</h5>
                                <?php $pain_images = ['pain0.jpg', 'pain1.jpg', 'pain2.jpg', 'pain3.png', 'pain4.png']; ?>
                                <div class="d-flex flex-wrap justify-content-center">
                                    <?php for ($i = 0; $i <= 4; $i++): ?>
                                        <button type="button" class="card option-card" data-value="<?php echo $i; ?>">
                                            <img src="assets/img/symptoms/<?php echo $pain_images[$i]; ?>" class="card-img-top" alt="<?php echo $lang_strings['pain_' . $i]; ?>">
                                            <div class="card-body">
                                                <p class="card-text"><?php echo $lang_strings['pain_' . $i]; ?></p>
                                            </div>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="q_pain_level" id="q_pain_level">
                            </div>

                            <!-- Q4: Eye Redness -->
                            <div class="question-card" data-question="redness">
                                <h5><i class="fas fa-tint me-2" style="color:var(--teal)"></i><?php echo $lang_strings['eye_redness_desc']; ?> *</h5>
                                <div class="d-flex flex-wrap justify-content-center">
                                    <?php for ($i = 0; $i <= 4; $i++): ?>
                                        <button type="button" class="card option-card" data-value="<?php echo $i; ?>">
                                            <img src="assets/img/symptoms/redness<?php echo $i+1; ?>.png" class="card-img-top" alt="<?php echo $lang_strings['redness_' . $i]; ?>">
                                            <div class="card-body">
                                                <p class="card-text"><?php echo $lang_strings['redness_' . $i]; ?></p>
                                            </div>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="q_redness" id="q_redness">
                            </div>

                            <!-- Q5: Discharge -->
                            <div class="question-card" data-question="discharge">
                                <h5><i class="fas fa-water me-2" style="color:var(--teal)"></i><?php echo $lang_strings['any_discharge']; ?> *</h5>
                                <div class="d-flex flex-wrap justify-content-center">
                                    <?php for ($i = 0; $i <= 4; $i++): ?>
                                        <button type="button" class="card option-card" data-value="<?php echo $i; ?>">
                                            <img src="assets/img/symptoms/<?php echo 'écoulement' . ($i+1); ?>.png" class="card-img-top" alt="<?php echo $lang_strings['discharge_' . $i]; ?>">
                                            <div class="card-body">
                                                <p class="card-text"><?php echo $lang_strings['discharge_' . $i]; ?></p>
                                            </div>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="q_discharge" id="q_discharge">
                            </div>

                            <!-- Q6: Vision -->
                            <div class="question-card" data-question="vision">
                                <h5><i class="fas fa-eye-slash me-2" style="color:var(--teal)"></i><?php echo $lang_strings['vision_right_now']; ?> *</h5>
                                <div class="d-flex flex-wrap justify-content-center">
                                    <?php
                                    $vision_labels = ['normal', 'slightly_blurry', 'very_blurry', 'distorted_wavy', 'dark_spot_missing'];
                                    for ($i = 0; $i <= 4; $i++): ?>
                                        <button type="button" class="card option-card" data-value="<?php echo $i; ?>">
                                            <img src="assets/img/symptoms/vision<?php echo $i+1; ?>.png" class="card-img-top" alt="<?php echo $lang_strings[$vision_labels[$i]]; ?>">
                                            <div class="card-body">
                                                <p class="card-text"><?php echo $lang_strings[$vision_labels[$i]]; ?></p>
                                            </div>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="q_vision" id="q_vision">
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="symptoms_description" class="form-label">
                                    <i class="fas fa-comment-medical me-1" style="color:var(--teal)"></i>
                                    <?php echo $lang_strings['describe_your_condition']; ?> <span style="color:var(--gray-400)">(optionnel)</span>
                                </label>
                                <textarea class="form-control" id="symptoms_description" name="symptoms_description" rows="3" placeholder="Décrivez vos symptômes en détail..."></textarea>
                            </div>

                            <button type="submit" class="btn-submit-form">
                                <i class="fas fa-paper-plane"></i>
                                <?php echo $lang_strings['submit']; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageQuestions = ['pain_level', 'redness', 'discharge', 'vision'];
    const textQuestions  = ['which_eye', 'how_long'];
    const allQuestions   = [...imageQuestions, ...textQuestions];

    imageQuestions.forEach(question => {
        const container   = document.querySelector(`[data-question="${question}"]`);
        const cards       = container.querySelectorAll('.option-card');
        const hiddenInput = document.getElementById(`q_${question}`);
        cards.forEach(card => {
            card.addEventListener('click', function() {
                cards.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                hiddenInput.value = this.dataset.value;
                container.classList.remove('border-danger');
            });
        });
    });

    textQuestions.forEach(question => {
        const container   = document.querySelector(`[data-question="${question}"]`);
        const buttons     = container.querySelectorAll('.text-option-btn');
        const hiddenInput = document.getElementById(`q_${question}`);
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                buttons.forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                hiddenInput.value = this.dataset.value;
                container.classList.remove('border-danger');
            });
        });
    });

    document.getElementById('mainIntakeForm').addEventListener('submit', function(e) {
        let allSelected = true;
        allQuestions.forEach(question => {
            const hiddenInput = document.getElementById(`q_${question}`);
            const container   = document.querySelector(`[data-question="${question}"]`);
            if (!hiddenInput.value) {
                allSelected = false;
                container.classList.add('border-danger');
                container.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                container.classList.remove('border-danger');
            }
        });
        if (!allSelected) {
            e.preventDefault();
            const msg = '<?php echo $lang === "fr" ? "Veuillez répondre à toutes les questions obligatoires." : "يرجى الإجابة على جميع الأسئلة الإلزامية."; ?>';
            alert(msg);
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
