<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<style>
.google-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto;
}
.terms-scroll-box {
    max-height: 200px;
    overflow-y: auto;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #f9f9f9;
    font-size: 0.85rem;
    line-height: 1.6;
    margin-bottom: 15px;
}
.terms-scroll-box p { margin-bottom: 12px; }
.terms-scroll-box strong { color: var(--primary); }
.terms-checkbox-wrapper {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px;
    background: #fff5f5;
    border: 2px solid var(--primary);
    border-radius: 8px;
    margin-bottom: 20px;
}
.terms-checkbox-wrapper input {
    width: 18px; height: 18px; margin-top: 3px;
    accent-color: var(--primary); cursor: pointer; flex-shrink: 0;
}
button:disabled { opacity: 0.5; cursor: not-allowed; }
</style>

<div class="auth-page">
    <div class="container">
        <div class="auth-card" style="max-width:550px;">
            <div class="auth-header" style="text-align:center;">
                <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <h1 style="margin-top:15px;">One Last Step</h1>
                <p>Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong>!<br>
                Please read and accept our Terms & Conditions to continue.</p>
            </div>

            <form method="POST" action="<?php echo APP_URL; ?>/auth/accept-google-terms" id="termsForm">
                <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($user_type); ?>">
                
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;font-weight:600;color:var(--primary);margin-bottom:10px;">
                        <i class="fas fa-file-contract"></i> Terms and Conditions
                    </label>
                    <div class="terms-scroll-box">
                        <?php if ($user_type === 'user'): ?>
                            <p><strong>1. Acceptance of Terms</strong><br>By accessing and using JeevanDaan's Blood Donation Management System, you agree to be bound by these Terms and Conditions.</p>
                            <p><strong>2. Eligibility Requirements</strong><br>To donate blood through JeevanDaan, you must be at least 18 years of age, weigh at least 50 kg, be in good general health, and not have donated blood in the last 3 months.</p>
                            <p><strong>3. Identity Verification</strong><br>All users must complete identity verification by submitting valid government-issued identification (citizenship card or passport).</p>
                            <p><strong>4. Medical Screening</strong><br>All donated blood will undergo comprehensive testing including blood group, HIV, Hepatitis B and C, and hemoglobin testing.</p>
                            <p><strong>5. Privacy and Data Protection</strong><br>Your data will be stored securely, used only for blood donation management, shared only with authorised Red Cross personnel, and never sold to third parties.</p>
                            <p><strong>6. Donor Responsibilities</strong><br>Provide accurate health information, disclose medical conditions, follow pre and post-donation guidelines, and keep contact information up to date.</p>
                            <p><strong>7. Donation Interval Responsibility</strong><br>If you have previously donated blood and attempt to donate again before the required minimum gap of <strong>3 months (90 days)</strong>, this is your responsibility. JeevanDaan will not be held liable for any health complications.</p>
                            <p><strong>8. Limitation of Liability</strong><br>JeevanDaan is not liable for unavailability of specific blood types, delays in processing, or system downtime.</p>
                        <?php else: ?>
                            <p><strong>1. Legitimate Use</strong><br>By registering as an organization on JeevanDaan, you agree to use this platform solely for legitimate blood donation management purposes.</p>
                            <p><strong>2. Organization Verification</strong><br>Your organization will be verified by JeevanDaan administrators. You must provide accurate organization details and valid registration certificates.</p>
                            <p><strong>3. Authorized Personnel</strong><br>Only authorized personnel from registered organizations are permitted to access donor information through this platform.</p>
                            <p><strong>4. Donor Privacy</strong><br>You agree to maintain strict confidentiality of all donor information accessed through JeevanDaan.</p>
                            <p><strong>5. Ethical Conduct</strong><br>Organizations must contact donors only for genuine blood donation requests. Harassment or coercion of donors is strictly forbidden.</p>
                            <p><strong>6. Data Security</strong><br>You are responsible for keeping your account credentials secure.</p>
                            <p><strong>7. Compliance with Laws</strong><br>You agree to comply with all applicable laws of Nepal regarding blood donation, healthcare, and data protection.</p>
                            <p><strong>8. No Commercial Sale</strong><br>Blood collected through donors registered on JeevanDaan must NEVER be sold for commercial profit.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="terms-checkbox-wrapper">
                        <input type="checkbox" name="agree_terms" id="googleAgreeTerms" required>
                        <label for="googleAgreeTerms" style="cursor:pointer;margin:0;">
                            I have read and agree to the <strong>Terms and Conditions</strong> stated above. *
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" id="acceptBtn" disabled>
                    <i class="fas fa-check"></i> Accept & Continue
                </button>
            </form>

            <div style="text-align:center;margin-top:20px;">
                <a href="<?php echo APP_URL; ?>/auth/decline-google-terms" style="color:var(--secondary);font-size:0.9rem;">
                    <i class="fas fa-times"></i> Decline & go back to Login
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('googleAgreeTerms').addEventListener('change', function() {
    document.getElementById('acceptBtn').disabled = !this.checked;
});
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
