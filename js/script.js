/* ============================================================
   LifeLink – Shared script  (index, login, signup, verification)
   ============================================================ */

/* ── Terms checkbox → enable signup button ── */
const termsBox = document.getElementById("termsAgree");
const signupBtn = document.getElementById("signupBtn");
if (termsBox && signupBtn) {
  termsBox.addEventListener("change", () => {
    signupBtn.disabled = !termsBox.checked;
  });
}

/* ── File upload previews (verification page) ── */
document
  .querySelectorAll('input[type="file"][data-preview]')
  .forEach((input) => {
    input.addEventListener("change", function () {
      const file = this.files[0];
      const prevId = this.dataset.preview;
      const statId = this.dataset.status;
      if (!file) return;
      document.getElementById(statId).textContent = file.name;
      if (file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = (e) => {
          const img = document.getElementById(prevId);
          img.src = e.target.result;
          img.style.display = "block";
          img.previousElementSibling.style.display = "none";
        };
        reader.readAsDataURL(file);
      }
    });
  });

/* ── Signup ── */
async function handleSignup(e) {
  e.preventDefault();
  const form = e.target;
  const payload = new FormData(form);
  const btn = form.querySelector('[type="submit"]');
  btn.disabled = true;
  btn.textContent = "Creating account…";

  try {
    const res = await fetch("php/signup.php", {
      method: "POST",
      body: payload,
    });
    const data = await res.json();
    if (data.success) {
      window.location.href = data.redirect;
    } else {
      showAlert(data.error || "Signup failed", "error");
      btn.disabled = false;
      btn.textContent = "Sign Up";
    }
  } catch {
    showAlert("Network error. Please try again.", "error");
    btn.disabled = false;
    btn.textContent = "Sign Up";
  }
}

/* ── Login ── */
async function handleLogin(e) {
  e.preventDefault();
  const form = e.target;
  const payload = new FormData(form);
  const btn = form.querySelector('[type="submit"]');
  btn.disabled = true;
  btn.textContent = "Signing in…";

  try {
    const res = await fetch("php/login.php", { method: "POST", body: payload });
    const data = await res.json();
    if (data.success) {
      window.location.href = data.redirect;
    } else {
      showAlert(data.error || "Invalid credentials", "error");
      btn.disabled = false;
      btn.textContent = "Sign In";
    }
  } catch {
    showAlert("Network error. Please try again.", "error");
    btn.disabled = false;
    btn.textContent = "Sign In";
  }
}

/* ── Red Cross modal ── */
function openRedCrossKeypad() {
  const modal = document.getElementById("redCrossModal");
  modal.style.display = "flex";
  document.getElementById("accessCodeInput").value = "";
  document.getElementById("redCrossInputStep").style.display = "block";
  document.getElementById("redCrossSuccessStep").style.display = "none";
}

function closeRedCrossModal() {
  document.getElementById("redCrossModal").style.display = "none";
}

async function verifyRedCrossCode() {
  const code = document.getElementById("accessCodeInput").value.trim();
  if (!code) return;

  const fd = new FormData();
  fd.append("code", code);

  try {
    const res = await fetch("php/verify_redcross.php", {
      method: "POST",
      body: fd,
    });
    const data = await res.json();
    if (data.success) {
      document.getElementById("redCrossInputStep").style.display = "none";
      document.getElementById("redCrossSuccessStep").style.display = "block";
      setTimeout(() => {
        window.location.href = data.redirect;
      }, 1500);
    } else {
      document.getElementById("accessCodeInput").style.borderColor = "#e74c3c";
      showAlert(data.error || "Invalid code", "error");
    }
  } catch {
    showAlert("Network error", "error");
  }
}

/* ── Verification upload ── */
async function handleVerification(e) {
  e.preventDefault();
  const form = e.target;
  const payload = new FormData(form);
  payload.append("front", document.getElementById("fileFront").files[0]);
  payload.append("back", document.getElementById("fileBack").files[0]);
  const cert = document.getElementById("fileCert").files[0];
  if (cert) payload.append("cert", cert);

  const btn = form.querySelector('[type="submit"]');
  btn.disabled = true;
  btn.textContent = "Uploading…";

  try {
    const res = await fetch("php/upload_verification.php", {
      method: "POST",
      body: payload,
    });
    const data = await res.json();
    if (data.success) {
      document.getElementById("successModal").style.display = "flex";
    } else {
      showAlert(data.error || "Upload failed", "error");
      btn.disabled = false;
      btn.textContent = "Submit For Verification";
    }
  } catch {
    showAlert("Network error", "error");
    btn.disabled = false;
    btn.textContent = "Submit For Verification";
  }
}

function closeModalAndRedirect() {
  window.location.href = "user.html";
}

/* ── Forgot password (UI-only – extend with email backend) ── */
function handleRecovery(e) {
  e.preventDefault();
  document.getElementById("recoveryForm").style.display = "none";
  document.getElementById("successState").style.display = "block";
}

/* ── Auth guard for protected pages ── */
async function guardPage(requiredRole) {
  try {
    const res = await fetch("php/check_auth.php");
    const data = await res.json();
    if (!data.authenticated) {
      window.location.href = data.redirect;
    } else if (requiredRole && data.role !== requiredRole) {
      window.location.href = "login.html";
    }
  } catch {
    window.location.href = "login.html";
  }
}

/* ── Tiny alert helper ── */
function showAlert(msg, type) {
  let el = document.getElementById("_ll_alert");
  if (!el) {
    el = document.createElement("div");
    el.id = "_ll_alert";
    el.style.cssText =
      "position:fixed;top:20px;left:50%;transform:translateX(-50%);" +
      "padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;z-index:9999;" +
      "box-shadow:0 4px 16px rgba(0,0,0,.15);transition:opacity .3s";
    document.body.appendChild(el);
  }
  el.textContent = msg;
  el.style.background = type === "error" ? "#C0392B" : "#27ae60";
  el.style.color = "#fff";
  el.style.opacity = "1";
  clearTimeout(el._timer);
  el._timer = setTimeout(() => {
    el.style.opacity = "0";
  }, 3500);
}
