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
.forgot-link {
    text-align: right;
    margin-bottom: 15px;
}
.forgot-link a {
    color: var(--info);
    font-size: 0.9rem;
    text-decoration: none;
}
.forgot-link a:hover {
    text-decoration: underline;
}
</style>

<div class="auth-page">
    <div class="container">
        <div class="auth-card" style="max-width:480px;">
            <div class="auth-header">
                <i class="fas fa-hospital" style="color:var(--info);font-size:3rem;"></i>
                <h1>Organization Login</h1>
                <p>Red Cross / Hospital / Blood Bank</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo APP_URL; ?>/auth/organization-login">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" class="form-control" 
                           placeholder="Enter your email"
                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required autofocus>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="orgLoginPass" class="form-control" 
                               placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('orgLoginPass', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <!-- FORGOT PASSWORD LINK -->
                <div class="forgot-link">
                    <a href="<?php echo APP_URL; ?>/auth/forgot-password?type=organization">
                        <i class="fas fa-key"></i> Forgot Password?
                    </a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div style="text-align:center;margin:20px 0;">
                <p style="color:var(--secondary);margin-bottom:10px;">Don't have an account?</p>
                <a href="<?php echo APP_URL; ?>/auth/organization-register" class="btn btn-outline">
                    <i class="fas fa-hospital-user"></i> Register Organization
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
                <span>Continue with Google</span>
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
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
