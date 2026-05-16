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
                <i class="fas fa-lock" style="color:var(--success);font-size:3rem;"></i>
                <h1>Reset Password</h1>
                <p>Create your new password</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> New Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="newPass" class="form-control" 
                               placeholder="Minimum 6 characters" required minlength="6">
                        <button type="button" class="password-toggle" onclick="togglePassword('newPass', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="confirm_password" id="confirmPass" class="form-control" 
                               placeholder="Re-enter password" required minlength="6">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPass', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success btn-block btn-lg">
                    <i class="fas fa-save"></i> Reset Password
                </button>
            </form>
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
