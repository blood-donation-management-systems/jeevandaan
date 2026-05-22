<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="profile-page">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-building"></i> Organization Profile</h1>
            <a href="<?php echo APP_URL; ?>/organization/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if (isset($flash)): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="verification-card <?php echo $org['is_verified'] ? 'verified' : 'pending'; ?>">
            <i class="fas <?php echo $org['is_verified'] ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
            <div>
                <h3><?php echo $org['is_verified'] ? 'Organization Verified' : 'Verification Pending'; ?></h3>
                <p><?php echo $org['is_verified'] ? 'You can view all donors and requests.' : 'Complete your profile and upload documents for verification.'; ?></p>
            </div>
        </div>

        <form method="POST" action="<?php echo APP_URL; ?>/organization/update-profile" enctype="multipart/form-data" id="orgProfileForm" onsubmit="return validateOrgProfile()">
            <div class="form-section">
                <h2><i class="fas fa-user"></i> Personal Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Full Name * <small style="color:var(--secondary);">(letters only, no @ symbols)</small></label>
                        <input type="text" name="full_name" id="org_full_name" class="form-control" value="<?php echo htmlspecialchars($org['full_name']); ?>" required>
                        <small id="orgNameError" style="color:var(--danger);display:none;"></small>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($org['email']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Phone (Nepal) * <small style="color:var(--secondary);">(10 digits, starts with 9)</small></label>
                        <input type="tel" name="phone" id="org_phone" class="form-control" value="<?php echo htmlspecialchars($org['phone'] ?? ''); ?>" placeholder="98XXXXXXXX" required maxlength="10">
                        <small id="orgPhoneError" style="color:var(--danger);display:none;"></small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-hospital"></i> Organization Details</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Organization Name * <small style="color:var(--secondary);">(no @ symbols)</small></label>
                        <input type="text" name="organization_name" id="org_name" class="form-control" value="<?php echo htmlspecialchars($org['organization_name'] ?? ''); ?>" required>
                        <small id="orgNameValidationError" style="color:var(--danger);display:none;"></small>
                    </div>
                    <div class="form-group">
                        <label>Organization ID *</label>
                        <input type="text" name="organization_id" class="form-control" value="<?php echo htmlspecialchars($org['organization_id'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Organization Type *</label>
                        <select name="organization_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="red_cross" <?php echo (($org['organization_type'] ?? '') === 'red_cross') ? 'selected' : ''; ?>>Red Cross Society</option>
                            <option value="hospital" <?php echo (($org['organization_type'] ?? '') === 'hospital') ? 'selected' : ''; ?>>Hospital</option>
                            <option value="blood_bank" <?php echo (($org['organization_type'] ?? '') === 'blood_bank') ? 'selected' : ''; ?>>Blood Bank</option>
                            <option value="ngo" <?php echo (($org['organization_type'] ?? '') === 'ngo') ? 'selected' : ''; ?>>NGO</option>
                            <option value="other" <?php echo (($org['organization_type'] ?? '') === 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Your Position * <small style="color:var(--secondary);">(no @ symbols)</small></label>
                        <input type="text" name="position" id="org_position" class="form-control" value="<?php echo htmlspecialchars($org['position'] ?? ''); ?>" required>
                        <small id="orgPositionError" style="color:var(--danger);display:none;"></small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-id-card"></i> Organization ID Document *</h2>
                <p style="color:var(--danger);margin-bottom:20px;">
                    <i class="fas fa-exclamation-circle"></i> ID document upload is REQUIRED for verification
                </p>
                <div class="upload-grid">
                    <div class="upload-box">
                        <label>Registration Certificate * <span style="color:var(--danger);">(Required)</span></label>
                        <label for="organization_id_document" class="upload-area" style="cursor:pointer;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                            <?php if (!empty($org['organization_id_document'])): ?>
                                <img src="<?php echo UPLOAD_URL; ?>id_cards/<?php echo $org['organization_id_document'] . '?t=' . time(); ?>" alt="Org ID" style="max-width:100%;max-height:150px;">
                                <span class="uploaded-badge"><i class="fas fa-check"></i></span>
                            <?php else: ?>
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Click to upload</span>
                            <?php endif; ?>
                        </div>
                        </label>
                        <input type="file" name="organization_id_document" id="organization_id_document" accept="image/*" style="position:absolute;width:1px;height:1px;opacity:0;" <?php echo empty($org['organization_id_document']) ? 'required' : ''; ?>>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-map-marker-alt"></i> Address</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Province *</label>
                        <select name="province" class="form-control" required>
                            <option value="">Select Province</option>
                            <?php foreach (['Koshi', 'Madhesh', 'Bagmati', 'Gandaki', 'Lumbini', 'Karnali', 'Sudurpashchim'] as $p): ?>
                                <option value="<?php echo $p; ?>" <?php echo (($org['province'] ?? '') === $p) ? 'selected' : ''; ?>><?php echo $p; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>District *</label>
                        <select name="district" class="form-control" required>
                            <option value="">Select District</option>
                            <?php foreach ($districts as $d): ?>
                                <option value="<?php echo $d['district']; ?>" <?php echo (($org['district'] ?? '') === $d['district']) ? 'selected' : ''; ?>><?php echo $d['district']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Municipality * <small style="color:var(--secondary);">(letters only)</small></label>
                        <input type="text" name="municipality" id="org_municipality" class="form-control" value="<?php echo htmlspecialchars($org['municipality'] ?? ''); ?>" required>
                        <small id="orgMunicipalityError" style="color:var(--danger);display:none;"></small>
                    </div>
                    <div class="form-group full-width">
                        <label>Full Address <small style="color:var(--secondary);">(no @ symbols)</small></label>
                        <textarea name="address" id="org_address" class="form-control" rows="2"><?php echo htmlspecialchars($org['address'] ?? ''); ?></textarea>
                        <small id="orgAddressError" style="color:var(--danger);display:none;"></small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-cog"></i> Preferences</h2>
                <div class="preferences-grid">
                    <label class="switch-label">
                        <input type="checkbox" name="receive_notifications" <?php echo ($org['receive_notifications'] ?? 1) ? 'checked' : ''; ?>>
                        <span class="switch"></span>
                        <span>Receive notifications</span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Profile
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function hasOnlyNumbers(str) {
    return /^[\d\s\-_.,()]+$/.test(str.trim());
}

function hasInvalidChars(str) {
    return /[@#$%^&*<>{}[\]\\|`~]/.test(str);
}

// Validation handlers
document.getElementById('org_full_name').addEventListener('input', function() {
    var err = document.getElementById('orgNameError');
    if (hasInvalidChars(this.value)) {
        err.textContent = '⚠️ Special characters not allowed';
        err.style.display = 'block';
        this.style.borderColor = 'var(--danger)';
    } else if (hasOnlyNumbers(this.value)) {
        err.textContent = '⚠️ Cannot be only numbers';
        err.style.display = 'block';
        this.style.borderColor = 'var(--danger)';
    } else {
        err.style.display = 'none';
        this.style.borderColor = '';
    }
});

document.getElementById('org_phone').addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '');
    var err = document.getElementById('orgPhoneError');
    if (this.value.length > 0 && (this.value.length !== 10 || !this.value.startsWith('9'))) {
        err.textContent = '⚠️ Phone must be 10 digits starting with 9';
        err.style.display = 'block';
        this.style.borderColor = 'var(--danger)';
    } else {
        err.style.display = 'none';
        this.style.borderColor = '';
    }
});

['org_name', 'org_position', 'org_municipality', 'org_address'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', function() {
            var err = document.getElementById(id.replace('org_', 'org') + 'Error') || document.getElementById('orgNameValidationError');
            if (hasInvalidChars(this.value)) {
                if (err) {
                    err.textContent = '⚠️ Special characters not allowed';
                    err.style.display = 'block';
                }
                this.style.borderColor = 'var(--danger)';
            } else {
                if (err) err.style.display = 'none';
                this.style.borderColor = '';
            }
        });
    }
});

document.querySelectorAll('input[type="file"]').forEach(function(input) {
    input.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            var uploadArea = this.previousElementSibling;
            reader.onload = function(e) {
                uploadArea.innerHTML = '<img src="' + e.target.result + '" style="max-width:100%;max-height:150px;"><span class="uploaded-badge"><i class="fas fa-check"></i></span>';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});

function validateOrgProfile() {
    var errors = [];
    
    var name = document.getElementById('org_full_name').value.trim();
    var phone = document.getElementById('org_phone').value;
    
    if (hasInvalidChars(name) || hasOnlyNumbers(name)) {
        errors.push('Invalid full name');
    }
    
    if (phone.length !== 10 || !phone.startsWith('9')) {
        errors.push('Phone must be 10 digits starting with 9');
    }
    
    // Check required upload
    var docInput = document.getElementById('organization_id_document');
    var hasDoc = <?php echo !empty($org['organization_id_document']) ? 'true' : 'false'; ?>;
    
    if (!hasDoc && (!docInput.files || docInput.files.length === 0)) {
        errors.push('Organization ID document is required');
    }
    
    if (errors.length > 0) {
        alert('Please fix the following errors:\n\n• ' + errors.join('\n• '));
        return false;
    }
    
    return true;
}
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
