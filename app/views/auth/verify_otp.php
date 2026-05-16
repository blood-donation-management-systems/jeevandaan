<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<style>
.otp-input-wrapper {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin: 20px 0;
}
.otp-digit {
    width: 50px;
    height: 60px;
    text-align: center;
    font-size: 24px;
    font-weight: bold;
    border: 2px solid #ddd;
    border-radius: 8px;
}
.otp-digit:focus {
    border-color: var(--primary);
    outline: none;
}
</style>

<div class="auth-page">
    <div class="container">
        <div class="auth-card" style="max-width:480px;">
            <div class="auth-header">
                <i class="fas fa-envelope-open-text" style="color:var(--info);font-size:3rem;"></i>
                <h1>Enter OTP</h1>
                <p>We sent a 6-digit code to<br><strong><?php echo htmlspecialchars($email); ?></strong></p>
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

            <form method="POST" id="otpForm">
                <div class="form-group">
                    <label style="text-align:center;display:block;"><i class="fas fa-shield-alt"></i> Enter 6-Digit OTP</label>
                    <div class="otp-input-wrapper">
                        <input type="text" maxlength="1" class="otp-digit" data-index="0" pattern="[0-9]" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-digit" data-index="1" pattern="[0-9]" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-digit" data-index="2" pattern="[0-9]" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-digit" data-index="3" pattern="[0-9]" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-digit" data-index="4" pattern="[0-9]" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-digit" data-index="5" pattern="[0-9]" inputmode="numeric">
                    </div>
                    <input type="hidden" name="otp" id="otpHidden">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-check"></i> Verify OTP
                </button>
            </form>

            <div style="text-align:center;margin-top:20px;">
                <p style="color:var(--secondary);font-size:0.9rem;">Didn't receive OTP?</p>
                <a href="<?php echo APP_URL; ?>/auth/forgot-password?type=<?php echo $type; ?>" style="color:var(--primary);">
                    <i class="fas fa-redo"></i> Resend OTP
                </a>
            </div>

            <div style="text-align:center;margin-top:15px;">
                <small style="color:var(--secondary);">OTP expires in 10 minutes</small>
            </div>
        </div>
    </div>
</div>

<script>
const inputs = document.querySelectorAll('.otp-digit');
const hiddenInput = document.getElementById('otpHidden');

inputs.forEach((input, index) => {
    input.addEventListener('input', function(e) {
        if (this.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
        updateHidden();
    });
    
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !this.value && index > 0) {
            inputs[index - 1].focus();
        }
    });
    
    input.addEventListener('paste', function(e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text');
        const digits = pasted.replace(/\D/g, '').slice(0, 6);
        
        digits.split('').forEach((digit, i) => {
            if (inputs[i]) inputs[i].value = digit;
        });
        
        if (digits.length === 6) inputs[5].focus();
        updateHidden();
    });
});

function updateHidden() {
    let otp = '';
    inputs.forEach(input => otp += input.value);
    hiddenInput.value = otp;
}

document.getElementById('otpForm').addEventListener('submit', function(e) {
    if (hiddenInput.value.length !== 6) {
        e.preventDefault();
        alert('Please enter all 6 digits');
    }
});
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
