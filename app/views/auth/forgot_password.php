<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="auth-page">
    <div class="container">
        <div class="auth-card" style="max-width:480px;">
            <div class="auth-header">
                <i class="fas fa-key" style="color:var(--warning);font-size:3rem;"></i>
                <h1>Forgot Password</h1>
                <p>Enter your email to receive an OTP</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" class="form-control" 
                           placeholder="Enter your registered email"
                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required autofocus>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-paper-plane"></i> Send OTP
                </button>
            </form>

            <div style="text-align:center;margin-top:20px;">
                <a href="<?php echo APP_URL; ?>/auth/<?php echo $type === 'user' ? 'user-login' : 'organization-login'; ?>" 
                   style="color:var(--secondary);font-size:0.9rem;">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
