/* ============================================================
   Life Link – Admin Dashboard  |  admin.js
   ============================================================ */

const API = "php/admin_api.php";

/* ── Page Navigation ── */
function showPage(id, btn) {
  document
    .querySelectorAll(".page")
    .forEach((s) => s.classList.remove("active"));
  document
    .querySelectorAll(".nav-item")
    .forEach((n) => n.classList.remove("active"));
  document.getElementById("page-" + id).classList.add("active");
  btn.classList.add("active");
}

function closeModal(id) {
  document.getElementById(id).classList.remove("open");
}

function showToast(msg) {
  const t = document.getElementById("toast");
  t.textContent = msg;
  t.classList.add("show");
  setTimeout(() => t.classList.remove("show"), 2600);
}

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".modal-overlay").forEach((o) => {
    o.addEventListener("click", (e) => {
      if (e.target === o) o.classList.remove("open");
    });
  });
  loadAll();
});

/* ── Helper: API fetch ── */
// async function apiFetch(action, method = "GET", body = null) {
//   const opts = { method };
//   if (body) opts.body = body;
//   const res = await fetch(`${API}?action=${action}`, opts);
//   return res.json();
// }
async function apiFetch(action, method = "GET", body = null) {
  const opts = { method, credentials: "same-origin" };
  if (body) opts.body = body;
  const res = await fetch(`${API}?action=${action}`, opts);
  return res.json();
}

function emptyRow(cols, msg) {
  return `<tr><td colspan="${cols}" style="text-align:center;color:#aaa;padding:24px">${msg}</td></tr>`;
}

function capitalize(s) {
  if (!s) return "";
  return s.charAt(0).toUpperCase() + s.slice(1);
}

function badgeClass(val) {
  const v = (val || "").toLowerCase();
  if (["active", "approved"].includes(v)) return "badge-active";
  if (["inactive", "rejected"].includes(v)) return "badge-inactive";
  return "badge-pending";
}

/* ── Load Everything ── */
async function loadAll() {
  await Promise.all([
    loadStats(),
    loadUsers(),
    loadCenters(),
    loadRequests(),
    loadDonors(),
    loadVerifications(),
  ]);
}

/* ══════════════════════════════════════════════════════════
   OVERVIEW STATS
══════════════════════════════════════════════════════════ */
async function loadStats() {
  try {
    const d = await apiFetch("stats");
    const vals = document.querySelectorAll("#page-overview .stat-value");
    if (vals[0]) vals[0].textContent = d.users ?? 0;
    if (vals[1]) vals[1].textContent = d.centers ?? 0;
    if (vals[2]) vals[2].textContent = d.donors ?? 0;
    if (vals[3]) vals[3].textContent = d.requests ?? 0;

    // Activity items
    const acts = document.querySelectorAll(".activity-item .count");
    if (acts[0]) acts[0].textContent = d.new_today ?? 0;
    if (acts[1]) acts[1].textContent = d.pending ?? 0;
    if (acts[2]) acts[2].textContent = d.active_requests ?? 0;
  } catch (e) {
    console.error("Stats error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   MANAGE USERS
══════════════════════════════════════════════════════════ */
async function loadUsers() {
  try {
    const data = await apiFetch("users");
    const tbody = document.querySelector("#users-table tbody");
    if (!tbody) return;
    tbody.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = emptyRow(6, "No users found.");
      return;
    }

    data.forEach((r) => {
      const joined = r.created_at ? r.created_at.split(" ")[0] : "—";
      tbody.innerHTML += `
        <tr data-id="${r.id}" data-db-id="${r.id}">
          <td>${r.full_name}</td>
          <td>${r.email || "—"}</td>
          <td>${capitalize(r.role)}</td>
          <td><span class="badge ${badgeClass(r.status)}">${capitalize(r.status)}</span></td>
          <td>${joined}</td>
          <td>
            <button class="action-btn" onclick="openEdit('users','${r.id}',this)">
              <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
            </button>
            <button class="action-btn" onclick="openDelete('${r.id}','users-table')">
              <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg> Delete
            </button>
          </td>
        </tr>`;
    });
  } catch (e) {
    console.error("Users error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   RED CROSS CENTERS
══════════════════════════════════════════════════════════ */
async function loadCenters() {
  try {
    const data = await apiFetch("centers");
    const tbody = document.querySelector("#redcross-table tbody");
    if (!tbody) return;
    tbody.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = emptyRow(5, "No centers found.");
      return;
    }

    data.forEach((r) => {
      tbody.innerHTML += `
        <tr data-id="${r.id}" data-db-id="${r.id}">
          <td>${r.name}</td>
          <td>${r.location || "—"}</td>
          <td>${r.contact || "—"}</td>
          <td><span class="badge ${badgeClass(r.status)}">${capitalize(r.status)}</span></td>
          <td>
            <button class="action-btn" onclick="openEdit('redcross','${r.id}',this)">
              <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
            </button>
            <button class="action-btn" onclick="openDelete('${r.id}','redcross-table')">
              <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg> Delete
            </button>
          </td>
        </tr>`;
    });
  } catch (e) {
    console.error("Centers error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   MANAGE REQUESTS
══════════════════════════════════════════════════════════ */
async function loadRequests() {
  try {
    const data = await apiFetch("requests");
    const tbody = document.querySelector("#request-table tbody");
    if (!tbody) return;
    tbody.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = emptyRow(6, "No requests found.");
      return;
    }

    data.forEach((r) => {
      const date = r.created_at ? r.created_at.split(" ")[0] : "—";
      tbody.innerHTML += `
        <tr data-id="${r.id}" data-db-id="${r.id}">
          <td>${r.full_name}</td>
          <td>${r.blood_group}</td>
          <td>${r.units_ml}</td>
          <td><span class="badge ${badgeClass(r.status)}">${capitalize(r.status)}</span></td>
          <td>${date}</td>
          <td>
            <button class="action-btn" onclick="openEdit('request','${r.id}',this)">
              <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
            </button>
            <button class="action-btn" onclick="openDelete('${r.id}','request-table')">
              <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg> Delete
            </button>
          </td>
        </tr>`;
    });
  } catch (e) {
    console.error("Requests error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   MANAGE DONORS
══════════════════════════════════════════════════════════ */
async function loadDonors() {
  try {
    const data = await apiFetch("donors");
    const tbody = document.querySelector("#donors-table tbody");
    if (!tbody) return;
    tbody.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = emptyRow(6, "No donors found.");
      return;
    }

    data.forEach((r) => {
      tbody.innerHTML += `
        <tr data-id="${r.id}" data-db-id="${r.id}">
          <td>${r.full_name}</td>
          <td>${r.blood_group || "—"}</td>
          <td>${r.donations || 0}</td>
          <td><span class="badge ${badgeClass(r.status)}">${capitalize(r.status)}</span></td>
          <td>${r.last_donation || "—"}</td>
          <td>
            <button class="action-btn" onclick="openEdit('donors','${r.id}',this)">
              <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
            </button>
            <button class="action-btn" onclick="openDelete('${r.id}','donors-table')">
              <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg> Delete
            </button>
          </td>
        </tr>`;
    });
  } catch (e) {
    console.error("Donors error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   VERIFICATIONS
══════════════════════════════════════════════════════════ */
async function loadVerifications() {
  try {
    const data = await apiFetch("verifications");
    const tbody = document.querySelector("#verification-table tbody");
    if (!tbody) return;
    tbody.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = emptyRow(5, "No verifications pending.");
      return;
    }

    data.forEach((r) => {
      const submitted = r.submitted_at ? r.submitted_at.split(" ")[0] : "—";
      const hasCert = r.cert_path ? "ID + Certificate" : "ID Verification";
      tbody.innerHTML += `
        <tr data-id="${r.id}" data-db-id="${r.id}" data-user-id="${r.user_id}">
          <td>${r.full_name}</td>
          <td>${hasCert}</td>
          <td><span class="badge ${badgeClass(r.status)}">${capitalize(r.status)}</span></td>
          <td>${submitted}</td>
          <td>
            <button class="btn-pill btn-pill-accept" onclick="verifyAction('${r.id}','${r.user_id}','approved')">Accept</button>
            <button class="btn-pill btn-pill-reject" onclick="verifyAction('${r.id}','${r.user_id}','rejected')">Reject</button>
          </td>
        </tr>`;
    });
  } catch (e) {
    console.error("Verifications error", e);
  }
}

/* ── Verify Action ── */
async function verifyAction(id, userId, status) {
  try {
    const fd = new FormData();
    fd.append("id", id);
    fd.append("user_id", userId);
    fd.append("status", status);
    await apiFetch("verify_action", "POST", fd);
    await loadVerifications();
    await loadStats();
    showToast("Verification " + status + ".");
  } catch (e) {
    showToast("Error updating verification.");
  }
}

/* ══════════════════════════════════════════════════════════
   EDIT MODAL
══════════════════════════════════════════════════════════ */
const editFields = {
  users: [
    { label: "Name", col: 0, type: "text" },
    { label: "Email", col: 1, type: "email" },
    {
      label: "Role",
      col: 2,
      type: "select",
      options: ["user", "redcross", "admin"],
    },
    {
      label: "Status",
      col: 3,
      type: "select",
      options: ["active", "inactive", "pending"],
      isBadge: true,
    },
  ],
  redcross: [
    { label: "Center Name", col: 0, type: "text" },
    { label: "Location", col: 1, type: "text" },
    { label: "Contact", col: 2, type: "text" },
    {
      label: "Status",
      col: 3,
      type: "select",
      options: ["active", "inactive"],
      isBadge: true,
    },
  ],
  request: [
    { label: "Patient", col: 0, type: "text" },
    { label: "Blood Group", col: 1, type: "text" },
    { label: "Units", col: 2, type: "number" },
    {
      label: "Status",
      col: 3,
      type: "select",
      options: ["pending", "approved", "rejected"],
      isBadge: true,
    },
  ],
  donors: [
    { label: "Name", col: 0, type: "text" },
    { label: "Blood Group", col: 1, type: "text" },
    { label: "Total Donation", col: 2, type: "number" },
    {
      label: "Status",
      col: 3,
      type: "select",
      options: ["active", "inactive"],
      isBadge: true,
    },
  ],
};

let _editContext = null;

function openEdit(type, rowId, btn) {
  const row = btn.closest("tr");
  const cells = row.querySelectorAll("td");
  const fields = editFields[type];
  if (!fields) return;
  const container = document.getElementById("modal-fields");
  container.innerHTML = "";

  fields.forEach((f) => {
    const cell = cells[f.col];
    const val = f.isBadge
      ? cell.querySelector(".badge")?.textContent.trim().toLowerCase()
      : cell.textContent.trim();
    const div = document.createElement("div");
    div.className = "form-group";
    div.innerHTML = `<label class="form-label">${f.label}</label>`;
    if (f.type === "select") {
      const sel = document.createElement("select");
      sel.className = "form-select";
      sel.dataset.col = f.col;
      sel.dataset.isBadge = f.isBadge || false;
      f.options.forEach((o) => {
        const opt = document.createElement("option");
        opt.value = o;
        opt.textContent = capitalize(o);
        if (o === val) opt.selected = true;
        sel.appendChild(opt);
      });
      div.appendChild(sel);
    } else {
      div.innerHTML += `<input class="form-input" type="${f.type}" data-col="${f.col}" value="${val}"/>`;
    }
    container.appendChild(div);
  });

  _editContext = {
    type,
    row,
    dbId: row.dataset.dbId,
    userId: row.dataset.userId,
  };
  document.getElementById("edit-modal").classList.add("open");
}

async function saveEdit() {
  if (!_editContext) return;
  const { type, row, dbId, userId } = _editContext;
  const cells = row.querySelectorAll("td");
  const fd = new FormData();
  fd.append("id", dbId);

  let role = "",
    status = "";

  document
    .querySelectorAll("#modal-fields input, #modal-fields select")
    .forEach((inp) => {
      const col = parseInt(inp.dataset.col);
      const isBadge = inp.dataset.isBadge === "true";
      const val = inp.value.trim();

      if (isBadge) {
        const b = cells[col].querySelector(".badge");
        if (b) {
          b.textContent = capitalize(val);
          b.className = "badge " + badgeClass(val);
        }
        status = val;
        fd.append("status", val);
      } else {
        cells[col].textContent = val;
        if (type === "users" && col === 2) {
          role = val;
          fd.append("role", val);
        }
      }
    });

  try {
    let action = "";
    if (type === "users") action = "update_user";
    if (type === "redcross") action = "update_center";
    if (type === "request") action = "update_request";
    if (type === "donors") {
      /* donor update via users table */ action = "update_user";
      fd.append("role", "user");
    }
    if (action) await apiFetch(action, "POST", fd);
    showToast("Record updated successfully.");
  } catch (e) {
    showToast("Error saving changes.");
  }
  closeModal("edit-modal");
}

/* ══════════════════════════════════════════════════════════
   DELETE MODAL
══════════════════════════════════════════════════════════ */
let _deleteContext = null;

function openDelete(rowId, tableId) {
  _deleteContext = { rowId, tableId };
  document.getElementById("delete-modal").classList.add("open");
}

async function confirmDelete() {
  if (!_deleteContext) return;
  const table = document.getElementById(_deleteContext.tableId);
  const row = table?.querySelector(`tr[data-id="${_deleteContext.rowId}"]`);

  if (row) {
    const dbId = row.dataset.dbId;
    const fd = new FormData();
    fd.append("id", dbId);
    try {
      if (_deleteContext.tableId === "users-table")
        await apiFetch("delete_user", "POST", fd);
      if (_deleteContext.tableId === "redcross-table")
        await apiFetch("delete_center", "POST", fd);
      if (_deleteContext.tableId === "request-table")
        await apiFetch("delete_request", "POST", fd);
      if (_deleteContext.tableId === "donors-table")
        await apiFetch("delete_user", "POST", fd);
    } catch (e) {}
    row.remove();
  }

  closeModal("delete-modal");
  showToast("Record deleted.");
  await loadStats();
  _deleteContext = null;
}
