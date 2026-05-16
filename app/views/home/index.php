<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Save life through
            Donating Blood<br></h1>
            <p>Every drop counts. Join thousands of donors across Nepal saving lives through blood donation.</p>
            <div class="hero-buttons">
                <a href="<?php echo APP_URL; ?>/auth/user-login" class="btn btn-light btn-lg">
                    <i class="fas fa-hand-holding-heart"></i> Become a Donor
                </a>
                <a href="<?php echo APP_URL; ?>/requests" class="btn btn-lg" style="background:white;color:var(--primary);border:2px solid white;">
                    <i class="fas fa-tint"></i> Find Blood
                </a>
            </div>
        </div>
        <div class="hero-image">
            <img src="<?php echo APP_URL; ?>/images/logo.png" alt="Blood Donation" style="max-width:100%;height:auto;max-height:400px;">
        </div>
    </div>
</section>

<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>1000+</h3>
                <p>Registered Donors</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-tint"></i>
                <h3>500+</h3>
                <p>Lives Saved</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-hospital"></i>
                <h3>77</h3>
                <p>Districts</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-heart"></i>
                <h3>24/7</h3>
                <p>Support</p>
            </div>
        </div>
    </div>
</section>

<section class="urgent-section">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Urgent Blood Requests</h2>
            <a href="<?php echo APP_URL; ?>/requests" class="btn btn-outline">View All</a>
        </div>
        
        <?php if (!empty($requests)): ?>
            <div class="requests-grid">
                <?php foreach ($requests as $request): ?>
                    <div class="request-card <?php echo $request['urgency']; ?>">
                        <div class="request-header">
                            <span class="blood-badge large"><?php echo $request['blood_group']; ?></span>
                            <span class="urgency-badge <?php echo $request['urgency']; ?>">
                                <?php echo ucfirst($request['urgency']); ?>
                            </span>
                        </div>
                        <div class="request-body">
                            <h3><?php echo htmlspecialchars($request['patient_name']); ?></h3>
                            <p><i class="fas fa-hospital"></i> <?php echo htmlspecialchars($request['hospital_name']); ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($request['hospital_district']); ?></p>
                            <p><i class="fas fa-flask"></i> <?php echo $request['units_required']; ?> unit(s)</p>
                        </div>
                        <div class="request-footer">
                            <a href="<?php echo APP_URL; ?>/requests/details/<?php echo $request['id']; ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-hand-holding-heart"></i> Help Now
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-check-circle"></i>
                <p>No urgent requests at the moment</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="how-it-works">
    <div class="container">
        <h2 class="section-title">How It Works</h2>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <i class="fas fa-user-plus"></i>
                <h3>Register</h3>
                <p>Create your account</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <i class="fas fa-id-card"></i>
                <h3>Verify</h3>
                <p>Upload Nagarikta</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <i class="fas fa-bell"></i>
                <h3>Get Notified</h3>
                <p>Receive alerts</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <i class="fas fa-heart"></i>
                <h3>Save Lives</h3>
                <p>Donate blood</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Save Lives?</h2>
            <p>One donation can save up to 3 lives!</p>
            <a href="<?php echo APP_URL; ?>/auth/user-login" class="btn btn-light btn-lg">
                <i class="fas fa-sign-in-alt"></i> Get Started
            </a>
        </div>
    </div>
</section>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
