<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<style>
.password-wrapper { position: relative; }
.password-wrapper input { padding-right: 45px; }
.password-toggle {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: var(--secondary);
    font-size: 1.1rem; padding: 5px;
}
.password-toggle:hover { color: var(--primary); }

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
.terms-scroll-box strong { color: var(--info); }
.terms-checkbox-wrapper {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px;
    background: #e8f4f8;
    border: 2px solid var(--info);
    border-radius: 8px;
    margin-bottom: 20px;
}
.terms-checkbox-wrapper input {
    width: 18px; height: 18px; margin-top: 3px;
    accent-color: var(--info); cursor: pointer; flex-shrink: 0;
}
.terms-checkbox-wrapper label {
    font-size: 0.9rem; cursor: pointer; margin: 0 !important;
}
button:disabled { opacity: 0.5; cursor: not-allowed; }
</style>

<div class="auth-page">
    <div class="container">
        <div class="auth-card" style="max-width:550px;">
            <div class="auth-header">
                <i class="fas fa-hospital-user" style="color:var(--info);font-size:3rem;"></i>
                <h1>Register Organization</h1>
                <p>Red Cross / Hospital / Blood Bank / NGO</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo APP_URL; ?>/auth/organization-register" id="orgRegisterForm">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Your Full Name *</label>
                    <input type="text" name="full_name" class="form-control" 
                           placeholder="Your full name"
                           value="<?php echo htmlspecialchars($full_name ?? ''); ?>" required autofocus>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email *</label>
                    <input type="email" name="email" class="form-control" 
                           placeholder="Your email address"
                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-hospital"></i> Organization Name *</label>
                    <input type="text" name="organization_name" class="form-control" 
                           placeholder="Name of your organization"
                           value="<?php echo htmlspecialchars($organization_name ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password *</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="orgRegPass" class="form-control" 
                               placeholder="Minimum 6 characters" required minlength="6">
                        <button type="button" class="password-toggle" onclick="togglePassword('orgRegPass', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirm Password *</label>
                    <div class="password-wrapper">
                        <input type="password" name="confirm_password" id="orgRegConfirm" class="form-control" 
                               placeholder="Re-enter password" required minlength="6">
                        <button type="button" class="password-toggle" onclick="togglePassword('orgRegConfirm', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms and Conditions for Organizations -->
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;font-weight:600;color:var(--info);margin-bottom:10px;">
                        <i class="fas fa-file-contract"></i> Organization Terms and Conditions
                    </label>
                    <div class="terms-scroll-box">
                        <p><strong>1. Legitimate Use</strong><br>By registering as an organization on JeevanDaan, you agree to use this platform solely for legitimate blood donation management purposes. Misuse, fraudulent activities, or unauthorized commercial activities are strictly prohibited.</p>
                        
                        <p><strong>2. Organization Verification</strong><br>Your organization will be verified by JeevanDaan administrators. You must provide accurate organization details, valid registration certificates, and authentic identification documents.</p>
                        
                        <p><strong>3. Authorized Personnel</strong><br>Only authorized personnel from registered organizations (Red Cross Society, Hospitals, Blood Banks, recognized NGOs) are permitted to access donor information through this platform.</p>
                        
                        <p><strong>4. Donor Privacy</strong><br>You agree to maintain strict confidentiality of all donor information accessed through JeevanDaan. Donor data must NOT be shared with unauthorized parties, used for marketing, or sold to third parties.</p>
                        
                        <p><strong>5. Ethical Conduct</strong><br>Organizations must contact donors only for genuine blood donation requests. Harassment, spam, or coercion of donors is strictly forbidden and will result in immediate account suspension.</p>
                        
                        <p><strong>6. Data Security</strong><br>You are responsible for keeping your account credentials secure. Any activity performed through your account is your responsibility. Report any unauthorized access immediately.</p>
                        
                        <p><strong>7. Compliance with Laws</strong><br>You agree to comply with all applicable laws of Nepal regarding blood donation, healthcare, and data protection. JeevanDaan reserves the right to report violations to relevant authorities.</p>
                        
                        <p><strong>8. Account Termination</strong><br>JeevanDaan reserves the right to suspend or terminate any organization account that violates these terms, engages in suspicious activities, or misuses the platform.</p>
                        
                        <p><strong>9. No Commercial Sale</strong><br>Blood collected through donors registered on JeevanDaan must NEVER be sold for commercial profit. Blood donation is a humanitarian act and must remain so.</p>
                        
                        <p><strong>10. Reporting Obligations</strong><br>Organizations must maintain accurate records of blood collection, transfusions, and donor interactions, and must report to JeevanDaan when requested for audit purposes.</p>
                    </div>
                    
                    <div class="terms-checkbox-wrapper">
                        <input type="checkbox" name="agree_terms" id="agreeOrgTerms" required>
                        <label for="agreeOrgTerms">
                            I confirm that I represent a <strong>legitimate organization</strong> and agree to use JeevanDaan ethically as per the above Terms and Conditions. *
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-block btn-lg" id="orgSubmitBtn" disabled>
                    <i class="fas fa-hospital-user"></i> Create Organization Account
                </button>
            </form>

            <div style="text-align:center;margin:20px 0;">
                <p style="color:var(--secondary);margin-bottom:10px;">Already have an account?</p>
                <a href="<?php echo APP_URL; ?>/auth/organization-login" class="btn btn-outline">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>

            <div style="display:flex;align-items:center;margin:20px 0;gap:15px;">
                <hr style="flex:1;border:none;border-top:2px solid #eee;">
                <span style="color:var(--secondary);font-weight:500;">OR</span>
                <hr style="flex:1;border:none;border-top:2px solid #eee;">
            </div>

            <a href="<?php echo APP_URL; ?>/auth/google-organization" 
               class="btn btn-block btn-lg"
               style="background:#4285f4;color:white;border:none;display:flex;align-items:center;justify-content:center;gap:12px;padding:15px;">
                <i class="fab fa-google" style="font-size:1.2rem;"></i>
                <span>Sign up with Google</span>
            </a>

            <div style="text-align:center;margin-top:20px;">
                <a href="<?php echo APP_URL; ?>/auth/login" style="color:var(--secondary);font-size:0.9rem;">
                    <i class="fas fa-arrow-left"></i> Back to Login Options
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    var input = document.getElementById(inputId);
    var icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var termsCheckbox = document.getElementById('agreeOrgTerms');
    var submitBtn = document.getElementById('orgSubmitBtn');
    
    if (termsCheckbox && submitBtn) {
        termsCheckbox.addEventListener('change', function() {
            submitBtn.disabled = !this.checked;
        });
    }
});
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
