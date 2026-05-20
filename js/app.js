/**
 * JeevanDaan - Main JavaScript
 */

// Portal Modal
function openPortal() {
    document.getElementById('loginPortal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closePortal() {
    document.getElementById('loginPortal').classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal on outside click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('loginPortal');
    if (e.target === modal) {
        closePortal();
    }
});

// Close modal on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePortal();
    }
});

// Mobile Menu Toggle
function toggleMobileMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('mobile-active');
}

// File Upload Preview
document.addEventListener('DOMContentLoaded', function() {
    const uploadInputs = document.querySelectorAll('.upload-box input[type="file"]');
    
    uploadInputs.forEach(function(input) {
        const uploadArea = input.previousElementSibling;
        
        // Click to upload
        uploadArea.addEventListener('click', function() {
            input.click();
        });
        
        // Preview on change
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    uploadArea.innerHTML = '<img src="' + e.target.result + '" alt="Preview"><span class="uploaded-badge"><i class="fas fa-check"></i> Selected</span>';
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // Province-District Filter
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    
    if (provinceSelect && districtSelect) {
        const allOptions = Array.from(districtSelect.options);
        
        provinceSelect.addEventListener('change', function() {
            const selectedProvince = this.value;
            
            // Clear district
            districtSelect.innerHTML = '<option value="">Select District</option>';
            
            // Filter and add matching districts
            allOptions.forEach(function(option) {
                if (option.dataset.province === selectedProvince || !option.value) {
                    districtSelect.appendChild(option.cloneNode(true));
                }
            });
        });
    }
    
    // Flash messages auto-hide
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 300);
        }, 5000);
    });
});

// Form Validation
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            field.style.borderColor = '#dc3545';
            isValid = false;
        } else {
            field.style.borderColor = '#e9ecef';
        }
    });
    
    return isValid;
}

// Phone Number Validation (Nepal)
function validatePhone(phone) {
    const pattern = /^(98|97|96|95|94)\d{8}$/;
    return pattern.test(phone);
}

// Age Calculator
function calculateAge(birthDate) {
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    
    return age;
}

// Donation Eligibility Check
function checkEligibility(weight, birthDate, lastDonation) {
    const age = calculateAge(birthDate);
    const messages = [];
    let eligible = true;
    
    if (weight < 45) {
        messages.push('Weight must be at least 45 kg');
        eligible = false;
    }
    
    if (age < 18) {
        messages.push('You must be at least 18 years old');
        eligible = false;
    }
    
    if (age > 65) {
        messages.push('Maximum age for donation is 65 years');
        eligible = false;
    }
    
    if (lastDonation) {
        const last = new Date(lastDonation);
        const today = new Date();
        const diffDays = Math.floor((today - last) / (1000 * 60 * 60 * 24));
        
        if (diffDays < 90) {
            messages.push('You must wait ' + (90 - diffDays) + ' more days before donating again');
            eligible = false;
        }
    }
    
    return { eligible, messages };
}

console.log('JeevanDaan loaded successfully!');
