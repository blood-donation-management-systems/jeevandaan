<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="profile-page">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-hand-holding-heart" style="color:var(--success);"></i> Donate Blood</h1>
            <a href="<?php echo APP_URL; ?>/user/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if (!$canDonate): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>You cannot donate yet.</strong> You must wait at least 90 days between donations.
                Last donation: <?php echo date('M d, Y', strtotime($user['last_donation_date'])); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-section" style="background:linear-gradient(135deg, #d4edda, #c3e6cb);">
            <h2 style="color:#155724;"><i class="fas fa-heart"></i> Your Blood Can Save Lives!</h2>
            <p style="color:#155724;font-size:1.1rem;margin-top:10px;">
                Register your willingness to donate. Organizations will contact you when your blood type is needed.
            </p>
        </div>

        <?php if ($canDonate): ?>
        <form method="POST" id="donateForm" onsubmit="return validateDonateForm()">
            <div class="form-section">
                <h2><i class="fas fa-tint"></i> Donation Details</h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Your Blood Group</label>
                        <input type="text" class="form-control" value="<?php echo $user['blood_group']; ?>" disabled style="background:#fff5f5;font-weight:bold;color:var(--primary);">
                    </div>
                    
                    <div class="form-group">
                        <label>Available Date * <small style="color:var(--secondary);">(Today or future)</small></label>
                        <input type="date" name="available_date" id="available_date" class="form-control" 
                               min="<?php echo date('Y-m-d'); ?>" required>
                        <small id="dateError" style="color:var(--danger);display:none;">
                            <i class="fas fa-exclamation-circle"></i> Past dates are not allowed
                        </small>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Preferred Location/Hospital * <small style="color:var(--secondary);">(must contain letters)</small></label>
                        <input type="text" name="location" id="location" class="form-control" 
                               placeholder="e.g. Bir Hospital, Kathmandu" required minlength="3">
                        <small id="locationError" style="color:var(--danger);display:none;">
                            <i class="fas fa-exclamation-circle"></i> Location cannot contain only numbers
                        </small>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Additional Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Any special requirements or preferred time..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-hand-holding-heart"></i> Register to Donate
                </button>
            </div>
        </form>
        <?php endif; ?>

        <?php if (!empty($offers)): ?>
        <div class="form-section">
            <h2><i class="fas fa-history"></i> Your Donation Offers</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offers as $offer): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($offer['available_date'])); ?></td>
                                <td><?php echo htmlspecialchars($offer['location']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $offer['status']; ?>">
                                        <?php echo ucfirst($offer['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($offer['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Real-time location validation
document.getElementById('location').addEventListener('input', function() {
    var value = this.value.trim();
    var errorMsg = document.getElementById('locationError');
    
    // Check if contains only numbers (with optional spaces/symbols)
    if (value && /^[\d\s\-_.,]+$/.test(value)) {
        errorMsg.style.display = 'block';
        this.style.borderColor = 'var(--danger)';
    } else {
        errorMsg.style.display = 'none';
        this.style.borderColor = '';
    }
});

// Real-time date validation
document.getElementById('available_date').addEventListener('change', function() {
    var selectedDate = new Date(this.value);
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    
    var errorMsg = document.getElementById('dateError');
    
    if (selectedDate < today) {
        errorMsg.style.display = 'block';
        this.style.borderColor = 'var(--danger)';
        this.value = '';
    } else {
        errorMsg.style.display = 'none';
        this.style.borderColor = '';
    }
});

// Form validation on submit
function validateDonateForm() {
    var location = document.getElementById('location').value.trim();
    var date = document.getElementById('available_date').value;
    var isValid = true;
    
    // Validate location (must contain letters)
    if (!location || /^[\d\s\-_.,]+$/.test(location)) {
        document.getElementById('locationError').style.display = 'block';
        document.getElementById('location').style.borderColor = 'var(--danger)';
        document.getElementById('location').focus();
        alert('Location must contain letters, not only numbers!');
        isValid = false;
    }
    
    // Validate date
    if (date) {
        var selectedDate = new Date(date);
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            document.getElementById('dateError').style.display = 'block';
            alert('Please select today or a future date!');
            isValid = false;
        }
    }
    
    return isValid;
}
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
