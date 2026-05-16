<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-hand-holding-heart" style="font-size:3rem;color:var(--primary);"></i>
                <h1>Welcome to JeevanDaan</h1>
                <p>Choose how you want to continue</p>
            </div>
            
            <div class="login-options">
                <a href="<?php echo APP_URL; ?>/auth/user-login" class="login-option user">
                    <i class="fas fa-user"></i>
                    <h3>User</h3>
                    <p>Donate or request blood</p>
                </a>
                
                <a href="<?php echo APP_URL; ?>/auth/organization-login" class="login-option organization">
                    <i class="fas fa-hospital"></i>
                    <h3>Organization</h3>
                    <p>Red Cross / Hospital / Blood Bank</p>
                </a>
                
                <a href="<?php echo APP_URL; ?>/auth/admin-login" class="login-option admin">
                    <i class="fas fa-user-shield"></i>
                    <h3>Admin</h3>
                    <p>System Administrator</p>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
