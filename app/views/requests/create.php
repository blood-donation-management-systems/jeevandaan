<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="profile-page">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-plus-circle"></i> Create Blood Request</h1>
            <a href="<?php echo APP_URL; ?>/requests" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="requestForm" onsubmit="return validateRequestForm()">
            <div class="form-section">
                <h2><i class="fas fa-user-injured"></i> Patient Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Patient Name * <small style="color:var(--secondary);">(must contain letters)</small></label>
                        <input type="text" name="patient_name" id="patient_name" class="form-control" required minlength="2">
                        <small id="nameError" style="color:var(--danger);display:none;">
                            <i class="fas fa-exclamation-circle"></i> Name cannot contain only numbers
                        </small>
                    </div>
                    <div class="form-group">
                        <label>Patient Age</label>
                        <input type="number" name="patient_age" class="form-control" min="1" max="120">
                    </div>
                    <div class="form-group">
                        <label>Blood Group Required *</label>
                        <select name="blood_group" class="form-control" required>
                            <option value="">Select Blood Group</option>
                            <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg): ?>
                                <option value="<?php echo $bg; ?>"><?php echo $bg; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Units Required *</label>
                        <input type="number" name="units_required" class="form-control" value="1" min="1" max="10" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-hospital"></i> Hospital Details</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Hospital Name * <small style="color:var(--secondary);">(must contain letters)</small></label>
                        <input type="text" name="hospital_name" id="hospital_name" class="form-control" required minlength="3">
                        <small id="hospitalError" style="color:var(--danger);display:none;">
                            <i class="fas fa-exclamation-circle"></i> Hospital name cannot contain only numbers
                        </small>
                    </div>
                    <div class="form-group">
                        <label>Hospital District *</label>
                        <select name="hospital_district" class="form-control" required>
                            <option value="">Select District</option>
                            <?php foreach ($districts as $d): ?>
                                <option value="<?php echo $d['district']; ?>"><?php echo $d['district']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label>Hospital Address <small style="color:var(--secondary);">(must contain letters)</small></label>
                        <input type="text" name="hospital_address" id="hospital_address" class="form-control">
                        <small id="addressError" style="color:var(--danger);display:none;">
                            <i class="fas fa-exclamation-circle"></i> Address cannot contain only numbers
                        </small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-exclamation-triangle"></i> Urgency & Details</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Urgency Level *</label>
                        <select name="urgency" class="form-control" required>
                            <option value="low">🟢 Low - Can wait a few days</option>
                            <option value="medium">🟡 Medium - Needed within 1-2 days</option>
                            <option value="high">🔴 High - Needed urgently (within hours)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Required By (Date) <small style="color:var(--secondary);">(Today or future)</small></label>
                        <input type="datetime-local" name="required_by" id="required_by" class="form-control" 
                               min="<?php echo date('Y-m-d\TH:i'); ?>">
                        <small id="dateError" style="color:var(--danger);display:none;">
                            <i class="fas fa-exclamation-circle"></i> Past dates are not allowed
                        </small>
                    </div>
                    <div class="form-group full-width">
                        <label>Reason</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Surgery, accident, etc."></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Additional Notes</label>
                        <textarea name="additional_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-phone-alt"></i> Contact Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Contact Person Name * <small style="color:var(--secondary);">(must contain letters)</small></label>
                        <input type="text" name="contact_name" id="contact_name" class="form-control" 
                               value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required minlength="2">
                        <small id="contactNameError" style="color:var(--danger);display:none;">
                            <i class="fas fa-exclamation-circle"></i> Name cannot contain only numbers
                        </small>
                    </div>
                    <div class="form-group">
                        <label>Contact Phone * <small style="color:var(--secondary);">(10 digits, starts with 9)</small></label>
                        <input type="tel" name="contact_phone" id="contact_phone" class="form-control" 
                               placeholder="98XXXXXXXX" required pattern="[9][0-9]{9}" maxlength="10">
                        <small id="phoneError" style="color:var(--danger);display:none;">
                            <i class="fas fa-exclamation-circle"></i> Phone must be 10 digits starting with 9
                        </small>
                    </div>
                    <div class="form-group">
                        <label>Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" 
                               value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane"></i> Submit Blood Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validation function for text fields (must contain letters)
function isOnlyNumbers(value) {
    var trimmed = value.trim();
    return trimmed && /^[\d\s\-_.,()]+$/.test(trimmed);
}

// Real-time validation for text fields
['patient_name', 'hospital_name', 'hospital_address', 'contact_name'].forEach(function(fieldId) {
    var field = document.getElementById(fieldId);
    if (field) {
        field.addEventListener('input', function() {
            var errorId = fieldId.replace('_name', 'Error').replace('_address', 'Error').replace('hospital_address', 'addressError').replace('contact_name', 'contactNameError');
            
            // Specific error mapping
            var errorMap = {
                'patient_name': 'nameError',
                'hospital_name': 'hospitalError',
                'hospital_address': 'addressError',
                'contact_name': 'contactNameError'
            };
            
            var errorMsg = document.getElementById(errorMap[fieldId]);
            
            if (this.value.trim() && isOnlyNumbers(this.value)) {
                if (errorMsg) errorMsg.style.display = 'block';
                this.style.borderColor = 'var(--danger)';
            } else {
                if (errorMsg) errorMsg.style.display = 'none';
                this.style.borderColor = '';
            }
        });
    }
});

// Phone validation
document.getElementById('contact_phone').addEventListener('input', function() {
    // Allow only numbers
    this.value = this.value.replace(/\D/g, '');
    
    var errorMsg = document.getElementById('phoneError');
    
    if (this.value.length > 0 && (this.value.length !== 10 || !this.value.startsWith('9'))) {
        errorMsg.style.display = 'block';
        this.style.borderColor = 'var(--danger)';
    } else {
        errorMsg.style.display = 'none';
        this.style.borderColor = '';
    }
});

// Date validation
document.getElementById('required_by').addEventListener('change', function() {
    var selectedDate = new Date(this.value);
    var now = new Date();
    var errorMsg = document.getElementById('dateError');
    
    if (selectedDate < now) {
        errorMsg.style.display = 'block';
        this.style.borderColor = 'var(--danger)';
        this.value = '';
    } else {
        errorMsg.style.display = 'none';
        this.style.borderColor = '';
    }
});

// Form submit validation
function validateRequestForm() {
    var isValid = true;
    var errors = [];
    
    // Validate patient name
    var patientName = document.getElementById('patient_name').value.trim();
    if (!patientName || isOnlyNumbers(patientName)) {
        errors.push('Patient name must contain letters');
        document.getElementById('nameError').style.display = 'block';
        isValid = false;
    }
    
    // Validate hospital name
    var hospitalName = document.getElementById('hospital_name').value.trim();
    if (!hospitalName || isOnlyNumbers(hospitalName)) {
        errors.push('Hospital name must contain letters');
        document.getElementById('hospitalError').style.display = 'block';
        isValid = false;
    }
    
    // Validate hospital address (if provided)
    var hospitalAddress = document.getElementById('hospital_address').value.trim();
    if (hospitalAddress && isOnlyNumbers(hospitalAddress)) {
        errors.push('Hospital address must contain letters');
        document.getElementById('addressError').style.display = 'block';
        isValid = false;
    }
    
    // Validate contact name
    var contactName = document.getElementById('contact_name').value.trim();
    if (!contactName || isOnlyNumbers(contactName)) {
        errors.push('Contact name must contain letters');
        document.getElementById('contactNameError').style.display = 'block';
        isValid = false;
    }
    
    // Validate phone
    var phone = document.getElementById('contact_phone').value;
    if (phone.length !== 10 || !phone.startsWith('9')) {
        errors.push('Phone must be 10 digits starting with 9');
        document.getElementById('phoneError').style.display = 'block';
        isValid = false;
    }
    
    // Validate required_by date (if provided)
    var requiredBy = document.getElementById('required_by').value;
    if (requiredBy) {
        var selectedDate = new Date(requiredBy);
        var now = new Date();
        if (selectedDate < now) {
            errors.push('Required by date cannot be in the past');
            document.getElementById('dateError').style.display = 'block';
            isValid = false;
        }
    }
    
    if (!isValid) {
        alert('Please fix the following errors:\n\n• ' + errors.join('\n• '));
    }
    
    return isValid;
}
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
