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
</style>

<div class="auth-page">
    <div class="container">
        <div class="auth-card" style="max-width:480px;">
            <div class="auth-header">
                <i class="fas fa-user-shield" style="color:var(--warning);font-size:3rem;"></i>
                <h1>Admin Login</h1>
                <p>System Administrator Access</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" class="form-control" 
                           placeholder="Enter username" required autofocus>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="adminPass" class="form-control" 
                               placeholder="Enter password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('adminPass', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

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
