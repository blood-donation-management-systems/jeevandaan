<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="profile-page">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-user-circle"></i> My Profile</h1>
            <a href="<?php echo APP_URL; ?>/user/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if (isset($flash)): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="verification-card <?php echo $user['is_verified'] ? 'verified' : 'pending'; ?>">
            <i class="fas <?php echo $user['is_verified'] ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
            <div>
                <h3><?php echo $user['is_verified'] ? 'Account Verified' : 'Verification Pending'; ?></h3>
                <p><?php echo $user['is_verified'] ? 'You can donate blood and access all features.' : 'Please upload your Nagarikta for verification.'; ?></p>
            </div>
        </div>
        

        <form method="POST" action="<?php echo APP_URL; ?>/user/update-profile" enctype="multipart/form-data">
            <div class="form-section">
                <h2><i class="fas fa-user"></i> Personal Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" pattern="[A-Za-z\s\.]+" title="Name can only contain letters, spaces, and dots" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Phone (Nepal) *</label>
                        <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="98XXXXXXXX" pattern="[0-9]{10}" maxlength="10" title="Phone must be exactly 10 digits" required>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth *</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?php echo $user['date_of_birth']; ?>" max="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Gender *</label>
                        <?php $isVerified = ($user['verification_status'] ?? '') === 'approved'; ?>
                        <select name="gender" class="form-control" <?php echo $isVerified ? 'disabled' : ''; ?> required>
                            <option value="">Select</option>
                            <option value="male" <?php echo $user['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo $user['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo $user['gender'] === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <?php if ($isVerified): ?>
                            <small style="color:var(--secondary);font-size:11px;"><i class="fas fa-lock"></i> Locked after verification — contact admin to change</small>
                            <input type="hidden" name="gender" value="<?php echo htmlspecialchars($user['gender']); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Blood Group *</label>
                        <select name="blood_group" class="form-control" <?php echo $isVerified ? 'disabled' : ''; ?> required>
                            <option value="">Select</option>
                            <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg): ?>
                                <option value="<?php echo $bg; ?>" <?php echo $user['blood_group'] === $bg ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($isVerified): ?>
                            <small style="color:var(--secondary);font-size:11px;"><i class="fas fa-lock"></i> Locked after verification — contact admin to change</small>
                            <input type="hidden" name="blood_group" value="<?php echo htmlspecialchars($user['blood_group']); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Weight (kg) *</label>
                        <input type="number" name="weight" class="form-control" value="<?php echo $user['weight']; ?>" min="50" max="200" step="0.1" title="Minimum weight for blood donation is 50 kg" oninvalid="this.setCustomValidity('Minimum weight for blood donation is 50 kg')" oninput="this.setCustomValidity('')" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-map-marker-alt"></i> Address</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Province *</label>
                        <select name="province" id="province" class="form-control" required>
                            <option value="">Select Province</option>
                            <?php foreach (['Koshi', 'Madhesh', 'Bagmati', 'Gandaki', 'Lumbini', 'Karnali', 'Sudurpashchim'] as $p): ?>
                                <option value="<?php echo $p; ?>" <?php echo ($user['province'] ?? '') === $p ? 'selected' : ''; ?>><?php echo $p; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>District *</label>
                        <select name="district" id="district" class="form-control" required>
                            <option value="">Select District</option>
                            <?php foreach ($districts as $d): ?>
                                <option value="<?php echo $d['district']; ?>" data-province="<?php echo $d['province']; ?>" <?php echo ($user['district'] ?? '') === $d['district'] ? 'selected' : ''; ?>><?php echo $d['district']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Municipality *</label>
                        <input type="text" name="municipality" class="form-control" value="<?php echo htmlspecialchars($user['municipality'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Ward No. *</label>
                        <input type="number" name="ward_no" class="form-control" value="<?php echo $user['ward_no']; ?>" min="1" max="33" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Tole/Street</label>
                        <input type="text" name="tole" class="form-control" value="<?php echo htmlspecialchars($user['tole'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-heartbeat"></i> Health Information</h2>
                <div class="form-group">
                    <label>Last Donation Date</label>
                    <input type="date" name="last_donation_date" class="form-control" value="<?php echo $user['last_donation_date']; ?>">
                </div>

                <div class="health-checkboxes">
                    <label class="health-warning">Do you have any of these conditions?</label>
                    <div class="checkbox-grid">
                        <label class="checkbox-label">
                            <input type="checkbox" name="has_hiv" <?php echo $user['has_hiv'] ? 'checked' : ''; ?>>
                            <span>HIV/AIDS</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="has_hepatitis_b" <?php echo $user['has_hepatitis_b'] ? 'checked' : ''; ?>>
                            <span>Hepatitis B</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="has_hepatitis_c" <?php echo $user['has_hepatitis_c'] ? 'checked' : ''; ?>>
                            <span>Hepatitis C</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="has_diabetes" <?php echo $user['has_diabetes'] ? 'checked' : ''; ?>>
                            <span>Diabetes</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="has_hypertension" <?php echo $user['has_hypertension'] ? 'checked' : ''; ?>>
                            <span>Hypertension</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Other Diseases</label>
                    <textarea name="other_diseases" class="form-control" rows="3"><?php echo htmlspecialchars($user['other_diseases'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-id-card"></i> Identity Verification</h2>
                <p style="color:var(--secondary);margin-bottom:20px;">Upload clear photos of your Nagarikta (Citizenship)</p>

                <div class="upload-grid">
                    <div class="upload-box">
                        <label>Nagarikta Front *</label>
                        <div class="upload-area" onclick="document.getElementById('citizenship_front').click()">
                            <?php if ($user['citizenship_front']): ?>
                                <img src="<?php echo UPLOAD_URL; ?>id_cards/<?php echo $user['citizenship_front']; ?>" alt="Front" style="max-width:100%;max-height:150px;">
                                <span class="uploaded-badge"><i class="fas fa-check"></i></span>
                            <?php else: ?>
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Click to upload</span>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="citizenship_front" id="citizenship_front" accept="image/*" style="display:none;">
                    </div>

                    <div class="upload-box">
                        <label>Nagarikta Back *</label>
                        <div class="upload-area" onclick="document.getElementById('citizenship_back').click()">
                            <?php if ($user['citizenship_back']): ?>
                                <img src="<?php echo UPLOAD_URL; ?>id_cards/<?php echo $user['citizenship_back']; ?>" alt="Back" style="max-width:100%;max-height:150px;">
                                <span class="uploaded-badge"><i class="fas fa-check"></i></span>
                            <?php else: ?>
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Click to upload</span>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="citizenship_back" id="citizenship_back" accept="image/*" style="display:none;">
                    </div>

                    <div class="upload-box">
                        <label>Donation Certificate (Optional)</label>
                        <div class="upload-area" onclick="document.getElementById('donation_certificate').click()">
                            <?php if ($user['donation_certificate']): ?>
                                <img src="<?php echo UPLOAD_URL; ?>certificates/<?php echo $user['donation_certificate']; ?>" alt="Certificate" style="max-width:100%;max-height:150px;">
                                <span class="uploaded-badge"><i class="fas fa-check"></i></span>
                            <?php else: ?>
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Click to upload</span>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="donation_certificate" id="donation_certificate" accept="image/*" style="display:none;">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-cog"></i> Preferences</h2>
                <div class="preferences-grid">
                    <label class="switch-label">
                        <input type="checkbox" name="willing_to_donate" <?php echo $user['willing_to_donate'] ? 'checked' : ''; ?>>
                        <span class="switch"></span>
                        <span>I am willing to donate blood</span>
                    </label>
                    <label class="switch-label">
                        <input type="checkbox" name="receive_notifications" <?php echo $user['receive_notifications'] ? 'checked' : ''; ?>>
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
// File upload preview
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
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>