/* ============================================================
   Life Link – User Dashboard | user.js  (backend-connected)
   ============================================================ */

const RARE_BLOOD_TYPES = ['A-', 'B-', 'O-', 'AB-', 'Rh-null'];
const RARE_LABELS = {
    'A-': 'A Negative (A−)', 'B-': 'B Negative (B−)', 'O-': 'O Negative (O−)',
    'AB-': 'AB Negative (AB−)', 'Rh-null': 'Rh-null (Golden Blood)'
};

/* ── Init ── */
window.onload = async function () {
    // Auth guard
    try {
        const r = await fetch('php/check_auth.php');
        const d = await r.json();
        if (!d.authenticated || d.role !== 'user') {
            window.location.href = 'login.html'; return;
        }
    } catch { window.location.href = 'login.html'; return; }

    // Load user data from session
    try {
        const r  = await fetch('php/session_data.php');
        const d  = await r.json();
        document.getElementById('display-firstname').innerText  = d.firstName  || 'User';
        document.getElementById('display-fullname').innerText   = d.fullName   || '';
        document.getElementById('display-email').innerText      = d.email      || '';
        document.getElementById('display-bloodgroup').innerText = d.bloodGroup || '—';
        document.getElementById('display-donations').innerText  = d.donations  || 0;
    } catch { /* keep placeholders */ }

    selectTab('request');

    document.querySelectorAll('.rare-modal-overlay').forEach(o => {
        o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
    });
};

/* ── Tab switching ── */
function selectTab(tab) {
    const donateBtn   = document.getElementById('donateBtn');
    const requestBtn  = document.getElementById('requestBtn');
    const donorForm   = document.getElementById('donorForm');
    const requestForm = document.getElementById('requestForm');

    if (tab === 'donate') {
        donateBtn.className  = 'action-btn active-red';
        requestBtn.className = 'action-btn inactive';
        donorForm.classList.add('visible');
        requestForm.classList.remove('visible');
    } else {
        requestBtn.className = 'action-btn active-red';
        donateBtn.className  = 'action-btn inactive';
        requestForm.classList.add('visible');
        donorForm.classList.remove('visible');
    }
}

/* ── Toast ── */
function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = 'toast show ' + (type || '');
    setTimeout(() => { t.className = 'toast'; }, 3500);
}

/* ── Rare blood ── */
function checkRareBloodDonor() {
    const val = document.getElementById('donorBloodGroup').value;
    if (!RARE_BLOOD_TYPES.includes(val)) return;
    const label = RARE_LABELS[val] || val;
    document.getElementById('rareDonorBadgeText').textContent = label;
    document.getElementById('rareDonorBloodText').textContent = label;
    document.getElementById('rareDonorContact').value = '';
    document.getElementById('rareDonorModal').classList.add('open');
}

function checkRareBloodRequest() {
    const val = document.getElementById('requestBloodGroup').value;
    if (!RARE_BLOOD_TYPES.includes(val)) return;
    const label = RARE_LABELS[val] || val;
    document.getElementById('rareRequestBadgeText').textContent  = label;
    document.getElementById('rareRequestBloodText').textContent  = label;
    document.getElementById('rareRequestBloodText2').textContent = label;
    document.getElementById('rareRequestContact').value = '';
    document.getElementById('rareRequestModal').classList.add('open');
}

function closeRareModal(id) { document.getElementById(id).classList.remove('open'); }

async function submitRareContact(type) {
    const inputId = type === 'donor' ? 'rareDonorContact' : 'rareRequestContact';
    const contact = document.getElementById(inputId).value.trim();
    const bg      = type === 'donor'
        ? document.getElementById('donorBloodGroup').value
        : document.getElementById('requestBloodGroup').value;

    if (!contact) {
        document.getElementById(inputId).style.borderColor = '#ef4444';
        return;
    }
    document.getElementById(inputId).style.borderColor = '';

    const fd = new FormData();
    fd.append('blood_group', bg);
    fd.append('contact', contact);
    fd.append('type', type);
    await fetch('php/rare_contact.php', { method: 'POST', body: fd });

    closeRareModal(type === 'donor' ? 'rareDonorModal' : 'rareRequestModal');
    showToast(type === 'donor' ? "Contact saved! We'll reach you when needed." : "We'll notify you when a match is found.", 'success');
}

/* ── Validation ── */
function validateAge(input) {
    const err = document.getElementById('ageError');
    if (input.value && parseInt(input.value) < 18) {
        err.classList.add('show'); input.classList.add('input-error');
    } else {
        err.classList.remove('show'); input.classList.remove('input-error');
    }
}

function validateWeight(input) {
    const err = document.getElementById('weightError');
    if (input.value && parseFloat(input.value) < 45) {
        err.classList.add('show'); input.classList.add('input-error');
    } else {
        err.classList.remove('show'); input.classList.remove('input-error');
    }
}

/* ── Donor form submit ── */
async function submitDonorForm() {
    document.querySelectorAll('.error-msg').forEach(e => e.classList.remove('show'));
    let valid = true;

    const bg    = document.getElementById('donorBloodGroup').value;
    const loc   = document.getElementById('donorLocation').value.trim();
    const age   = parseInt(document.getElementById('donorAge').value);
    const wt    = parseFloat(document.getElementById('donorWeight').value);
    const units = parseInt(document.getElementById('donorUnits').value);
    const last  = document.getElementById('donorLastDate').value;

    if (!bg || !loc) { showToast('Please fill in all required fields.', 'error'); return; }
    if (isNaN(age) || age < 18) { document.getElementById('ageError').classList.add('show'); valid = false; }
    if (isNaN(wt)  || wt  < 45) { document.getElementById('weightError').classList.add('show'); valid = false; }
    if (isNaN(units) || units < 400 || units > 500) { document.getElementById('unitsError').classList.add('show'); valid = false; }
    if (!valid) return;

    const fd = new FormData();
    fd.append('blood_group', bg); fd.append('location', loc);
    fd.append('age', age); fd.append('weight', wt);
    fd.append('units_ml', units); fd.append('last_donation', last);

    try {
        const res  = await fetch('php/submit_donation.php', { method: 'POST', body: fd });
        const data = await res.json();
        data.success ? showToast(data.message, 'success') : showToast(data.error, 'error');
    } catch { showToast('Network error', 'error'); }
}

/* ── Request form submit ── */
async function submitRequestForm() {
    const bg      = document.getElementById('requestBloodGroup').value;
    const loc     = document.getElementById('requestLocation').value.trim();
    const date    = document.getElementById('requestDate').value;
    const units   = parseInt(document.getElementById('requestUnits').value);
    const urgency = document.getElementById('requestUrgency').value;
    const errEl   = document.getElementById('reqUnitsError');

    errEl.classList.remove('show');
    if (!bg || !loc || !date || !urgency) { showToast('Please fill in all required fields.', 'error'); return; }
    if (isNaN(units) || units < 400 || units > 2000) { errEl.classList.add('show'); return; }

    const fd = new FormData();
    fd.append('blood_group', bg); fd.append('location', loc);
    fd.append('date_needed', date); fd.append('units_ml', units); fd.append('urgency', urgency);

    try {
        const res  = await fetch('php/submit_request.php', { method: 'POST', body: fd });
        const data = await res.json();
        data.success ? showToast(data.message, 'success') : showToast(data.error, 'error');
    } catch { showToast('Network error', 'error'); }
}

/* ── Urgency style ── */
function updateUrgencyStyle() {
    const sel  = document.getElementById('requestUrgency');
    const hint = document.getElementById('urgencyHint');
    const val  = sel.value;
    sel.className = val ? 'urgency-' + val : '';
    if (val === 'normal')     { hint.style.display='block'; hint.innerHTML='<span class="urgency-badge normal">● Normal</span> Scheduled or routine transfusion'; }
    else if (val === 'emergency') { hint.style.display='block'; hint.innerHTML='<span class="urgency-badge emergency">● Emergency</span> Required within 24 hours'; }
    else if (val === 'critical')  { hint.style.display='block'; hint.innerHTML='<span class="urgency-badge critical">● Critical</span> Immediate — life-threatening'; }
    else { hint.style.display='none'; hint.innerHTML=''; }
}
