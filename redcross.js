/* ============================================================
   Life Link – Red Cross Dashboard  |  redcross.js
   ============================================================ */

const API = "php/redcross_api.php";

/* ── Page Navigation ── */
function showPage(id, el) {
  document
    .querySelectorAll(".page")
    .forEach((p) => p.classList.remove("active"));
  document
    .querySelectorAll(".nav-item")
    .forEach((n) => n.classList.remove("active"));
  document.getElementById("page-" + id).classList.add("active");
  el.classList.add("active");
}

function showToast(msg) {
  const t = document.getElementById("toast");
  t.textContent = msg;
  t.classList.add("show");
  setTimeout(() => t.classList.remove("show"), 2600);
}

function closeModal(id) {
  document.getElementById(id).classList.remove("open");
}

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".modal-overlay").forEach((o) => {
    o.addEventListener("click", (e) => {
      if (e.target === o) o.classList.remove("open");
    });
  });
  loadAll();
});

/* ── Load Everything on Page Load ── */
async function loadAll() {
  await Promise.all([
    loadStats(),
    loadInventory(),
    loadAppointments(),
    loadBloodTesting(),
    loadBloodRequests(),
    loadRareBlood(),
    loadDonors(),
    loadExpiring(),
  ]);
}

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

/* ── Helper: empty row ── */
function emptyRow(cols, msg) {
  return `<tr><td colspan="${cols}" style="text-align:center;color:var(--text-muted);padding:24px">${msg}</td></tr>`;
}

/* ══════════════════════════════════════════════════════════
   DASHBOARD STATS
══════════════════════════════════════════════════════════ */
async function loadStats() {
  try {
    const d = await apiFetch("stats");
    const vals = document.querySelectorAll("#page-dashboard .stat-value");
    if (vals[0]) vals[0].textContent = d.donors ?? 0;
    if (vals[1]) vals[1].textContent = d.units ?? 0;
    if (vals[2]) vals[2].textContent = d.requests ?? 0;
    if (vals[3]) vals[3].textContent = d.expiring ?? 0;
  } catch (e) {
    console.error("Stats error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   INVENTORY
══════════════════════════════════════════════════════════ */
const compatMap = {
  "A+": ["A+", "AB+"],
  "A-": ["A+", "A-", "AB+", "AB-"],
  "B+": ["B+", "AB+"],
  "B-": ["B+", "B-", "AB+", "AB-"],
  "O+": ["A+", "B+", "O+", "AB+"],
  "O-": ["A+", "A-", "B+", "B-", "O+", "O-", "AB+", "AB-"],
  "AB+": ["AB+"],
  "AB-": ["AB+", "AB-"],
  "Rh-null": ["All Types"],
};

async function loadInventory() {
  try {
    const data = await apiFetch("inventory");
    const tbody = document.querySelector("#tbl-inventory tbody");
    if (!tbody) return;

    // Also update dashboard bar chart
    const barList = document.querySelector(".bar-list");

    tbody.innerHTML = "";
    if (barList) barList.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = emptyRow(6, "No inventory data.");
      return;
    }

    const maxUnits = Math.max(...data.map((r) => r.units_available), 1);

    data.forEach((r) => {
      const id = r.id;
      const bg = r.blood_group;
      const units = r.units_available;
      const storage = r.storage_date || "—";
      const expire = r.expire_date || "—";
      const compat = (compatMap[bg] || [bg])
        .map((t) => `<span class="compat-tag">${t}</span>`)
        .join("");
      const isGolden = bg === "Rh-null";

      tbody.innerHTML += `
        <tr data-id="${id}" data-db-id="${id}">
          <td>${
            isGolden
              ? `<strong style="color:var(--crimson)">${bg} <span style="font-size:10px;background:#fef3c7;color:#92400e;padding:1px 6px;border-radius:99px;font-weight:600;margin-left:4px;border:1px solid #f59e0b">Golden</span></strong>`
              : bg
          }</td>
          <td>${units}</td>
          <td>${storage}</td>
          <td>${expire}</td>
          <td><div class="compat-tags">${compat}</div></td>
          <td>
            <button class="action-btn" onclick="openAdd('${id}')">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg> Add
            </button>
            <button class="action-btn" onclick="openEdit('inventory','${id}',this)">
              <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Update
            </button>
          </td>
        </tr>`;

      // Dashboard bar
      if (barList) {
        const pct = Math.round((units / maxUnits) * 100);
        barList.innerHTML += `
          <div class="bar-row">
            <span class="blood-type" style="${bg === "Rh-null" ? "font-size:9px;width:44px" : ""}">${bg}</span>
            <div class="bar-track"><div class="bar-fill" style="width:${pct}%"></div></div>
            <span class="units">${units} Unit${units !== 1 ? "s" : ""}</span>
          </div>`;
      }
    });
  } catch (e) {
    console.error("Inventory error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   APPOINTMENTS
══════════════════════════════════════════════════════════ */
async function loadAppointments() {
  try {
    const data = await apiFetch("appointments");
    const tbody = document.querySelector("#tbl-appointments tbody");
    if (!tbody) return;
    tbody.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = emptyRow(6, "No appointments found.");
      return;
    }

    data.forEach((r) => {
      const statusClass =
        r.status === "scheduled"
          ? "badge-green"
          : r.status === "completed"
            ? "badge-green"
            : "badge-red";
      tbody.innerHTML += `
        <tr data-id="${r.id}" data-db-id="${r.id}">
          <td>${r.full_name}</td>
          <td>${r.blood_group || "—"}</td>
          <td>${r.requested_date || "—"}</td>
          <td>${r.last_donation || "—"}</td>
          <td><span class="badge ${statusClass}">${capitalize(r.status)}</span></td>
          <td>
            <button class="action-btn" onclick="openEdit('appointments','${r.id}',this)">
              <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
            </button>
            <button class="action-btn" onclick="openDelete('${r.id}','tbl-appointments')">
              <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg> Delete
            </button>
          </td>
        </tr>`;
    });
  } catch (e) {
    console.error("Appointments error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   BLOOD TESTING
══════════════════════════════════════════════════════════ */
async function loadBloodTesting() {
  try {
    const data = await apiFetch("blood_testing");
    const tbody = document.querySelector("#tbl-bloodtesting tbody");
    if (!tbody) return;
    tbody.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = emptyRow(5, "No blood testing records.");
      return;
    }

    data.forEach((r) => {
      const rowId = `bt${r.id}`;
      const statusHtml =
        r.status === "pending"
          ? `<button class="action-btn" onclick="testingAction(${r.id},'accepted')">
             <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Accept
           </button>
           <button class="action-btn" onclick="testingAction(${r.id},'rejected')">
             <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Reject
           </button>`
          : `<span class="badge ${r.status === "accepted" ? "badge-green" : "badge-red"}">${capitalize(r.status)}</span>`;

      tbody.innerHTML += `
        <tr data-id="${rowId}" data-db-id="${r.id}">
          <td><strong style="font-weight:500">${r.full_name}</strong></td>
          <td>${r.claimed_group || "—"}</td>
          <td>${r.tested_group || "—"}</td>
          <td>
            <button class="disease-expand-btn" onclick="toggleDisease('${rowId}')">
              <svg viewBox="0 0 24 24" style="width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2.2">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>
              </svg> View Disease Panel
            </button>
          </td>
          <td>${statusHtml}</td>
        </tr>
        <tr class="disease-drawer" id="drawer-${rowId}">
          <td colspan="5">
            <div class="disease-panel">
              <div class="dpanel">
                <div class="dpanel-header">Hemoglobin Level</div>
                <div class="dpanel-body">
                  <div class="disease-row">
                    <div class="disease-row-label">Hemoglobin<span>Normal: 13.5–17.5 g/dL</span></div>
                    <div class="disease-input-wrap">
                      <input type="number" step="0.1" value="${r.hemoglobin || 14.5}" id="hb-${rowId}" onchange="updateHbBadge('${rowId}')"/>
                      <span style="font-size:11px;color:var(--text-muted)">g/dL</span>
                      <span class="disease-val dv-normal" id="hb-badge-${rowId}">Normal</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="dpanel">
                <div class="dpanel-header">Infectious Diseases</div>
                <div class="dpanel-body">
                  ${[
                    "hiv",
                    "hepatitis_b",
                    "hepatitis_c",
                    "syphilis",
                    "malaria",
                    "covid",
                  ]
                    .map((d) => {
                      const val = r[d] || "negative";
                      const bg = val === "positive" ? "#fee2e2" : "#d1fae5";
                      const col = val === "positive" ? "#991b1b" : "#065f46";
                      return `<div class="disease-row">
                      <div class="disease-row-label">${d.replace("_", " ").replace(/\b\w/g, (c) => c.toUpperCase())}</div>
                      <select class="disease-val" style="background:${bg};color:${col};border:none;font-size:12px;padding:3px 8px;border-radius:99px;font-weight:600" onchange="updateSelectColor(this)">
                        <option value="negative" ${val === "negative" ? "selected" : ""}>Negative</option>
                        <option value="positive" ${val === "positive" ? "selected" : ""}>Positive</option>
                      </select>
                    </div>`;
                    })
                    .join("")}
                </div>
              </div>
              <div class="dpanel">
                <div class="dpanel-header">Vital Signs</div>
                <div class="dpanel-body">
                  <div class="disease-row">
                    <div class="disease-row-label">Blood Pressure<span>Normal: 90/60–120/80 mmHg</span></div>
                    <div class="disease-input-wrap"><input type="text" value="${r.blood_pressure || "118/76"}" style="width:76px"/><span style="font-size:11px;color:var(--text-muted)">mmHg</span></div>
                  </div>
                  <div class="disease-row">
                    <div class="disease-row-label">Pulse Rate<span>Normal: 60–100 bpm</span></div>
                    <div class="disease-input-wrap"><input type="number" value="${r.pulse_rate || 78}" style="width:60px"/><span style="font-size:11px;color:var(--text-muted)">bpm</span></div>
                  </div>
                  <div class="disease-row">
                    <div class="disease-row-label">Temperature<span>Normal: 36.1–37.2 °C</span></div>
                    <div class="disease-input-wrap"><input type="number" step="0.1" value="${r.temperature || 36.8}" style="width:60px"/><span style="font-size:11px;color:var(--text-muted)">°C</span></div>
                  </div>
                </div>
              </div>
              <div class="dpanel">
                <div class="dpanel-header">Weight & General Health</div>
                <div class="dpanel-body">
                  <div class="disease-row">
                    <div class="disease-row-label">Weight<span>Minimum 50 kg to donate</span></div>
                    <div class="disease-input-wrap"><input type="number" value="${r.weight || 68}" style="width:60px"/><span style="font-size:11px;color:var(--text-muted)">kg</span></div>
                  </div>
                  <div class="disease-row" style="align-items:flex-start;flex-direction:column;gap:6px">
                    <div class="disease-row-label" style="font-size:12px;font-weight:600;color:var(--crimson)">Health Declaration</div>
                    <textarea class="gh-textarea" rows="3">${r.health_declaration || ""}</textarea>
                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>`;
    });
  } catch (e) {
    console.error("Blood testing error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   BLOOD REQUESTS
══════════════════════════════════════════════════════════ */
async function loadBloodRequests() {
  try {
    const data = await apiFetch("blood_requests");
    const tbody = document.querySelector("#tbl-bloodrequest tbody");
    if (!tbody) return;
    tbody.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = emptyRow(5, "No blood requests.");
      return;
    }

    data.forEach((r) => {
      const urgencyClass =
        r.urgency === "critical"
          ? "badge-red"
          : r.urgency === "emergency"
            ? "badge-yellow"
            : "badge-green";
      const statusClass =
        r.status === "approved"
          ? "badge-green"
          : r.status === "rejected"
            ? "badge-red"
            : "badge-yellow";
      tbody.innerHTML += `
        <tr data-id="${r.id}" data-db-id="${r.id}">
          <td>${r.full_name}</td>
          <td>${r.blood_group}</td>
          <td>${r.units_ml}</td>
          <td><span class="badge ${statusClass}">${capitalize(r.status)}</span></td>
          <td>
            <button class="action-btn" onclick="requestAction(${r.id},'approved')">
              <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Approve
            </button>
            <button class="action-btn" onclick="openAssign(${r.id})">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg> Assign
            </button>
          </td>
        </tr>`;
    });
  } catch (e) {
    console.error("Blood requests error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   RARE BLOOD REGISTRY
══════════════════════════════════════════════════════════ */
async function loadRareBlood() {
  try {
    const data = await apiFetch("rare_blood");
    const tbody = document.querySelector("#tbl-rareblood tbody");
    if (!tbody) return;
    tbody.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = emptyRow(5, "No rare blood registry entries.");
      return;
    }

    data.forEach((r) => {
      const isRare = r.blood_group === "Rh-null";
      const badge = r.type === "donor" ? "badge-green" : "badge-yellow";
      const badgeTxt = r.type === "donor" ? "Available" : "Rare — On Call";
      tbody.innerHTML += `
        <tr data-id="${r.id}">
          <td>${r.full_name}</td>
          <td ${isRare ? 'style="font-weight:600;color:var(--crimson)"' : ""}>${r.blood_group}</td>
          <td>—</td>
          <td>${r.contact || "—"}</td>
          <td><span class="badge ${badge}">${badgeTxt}</span></td>
        </tr>`;
    });

    // Update dashboard rare blood alert
    const alertBox = document.querySelector(".card .alert-item");
    if (alertBox) {
      const urgent = data.filter((r) => r.type === "request");
      if (urgent.length) {
        alertBox.innerHTML = `
          <div class="alert-type">${urgent[0].blood_group} Urgent Needed</div>
          ${urgent.map((r) => `<div class="alert-names">${r.full_name}</div>`).join("")}`;
      } else {
        alertBox.innerHTML = `<div class="alert-type" style="color:var(--text-muted)">No urgent requests</div>`;
      }
    }
  } catch (e) {
    console.error("Rare blood error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   DONOR RECORDS
══════════════════════════════════════════════════════════ */
async function loadDonors() {
  try {
    const data = await apiFetch("donors");
    const tbody = document.querySelector("#tbl-donors tbody");
    if (!tbody) return;
    tbody.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = emptyRow(6, "No donor records.");
      return;
    }

    data.forEach((r) => {
      const eligible = r.donations > 0 ? "badge-green" : "badge-red";
      const eligibleText = r.donations > 0 ? "Eligible" : "Not Eligible";
      tbody.innerHTML += `
        <tr data-id="${r.id}" data-db-id="${r.id}">
          <td>${r.full_name}</td>
          <td>${r.blood_group || "—"}</td>
          <td>${r.donations || 0}</td>
          <td>${r.last_donation || "—"}</td>
          <td><span class="badge ${eligible}">${eligibleText}</span></td>
          <td>
            <button class="action-btn" onclick="viewHistory(${r.id})">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 15"/></svg> View History
            </button>
          </td>
        </tr>`;
    });
  } catch (e) {
    console.error("Donors error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   EXPIRY MONITORING
══════════════════════════════════════════════════════════ */
async function loadExpiring() {
  try {
    const data = await apiFetch("expiring");
    const tbody = document.querySelector("#tbl-expire tbody");
    if (!tbody) return;
    tbody.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = emptyRow(5, "No expiring units.");
      return;
    }

    data.forEach((r) => {
      tbody.innerHTML += `
        <tr data-id="${r.id}" data-db-id="${r.id}">
          <td>${r.blood_group}</td>
          <td>${r.units_available}</td>
          <td>${r.expire_date}</td>
          <td><span class="days-left-warning">${r.days_left} Days</span></td>
          <td>
            <button class="action-btn" onclick="removeExpiry(${r.id})">
              <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg> Remove Expiry
            </button>
          </td>
        </tr>`;
    });
  } catch (e) {
    console.error("Expiring error", e);
  }
}

/* ══════════════════════════════════════════════════════════
   ACTIONS (with real API calls)
══════════════════════════════════════════════════════════ */
async function testingAction(dbId, status) {
  try {
    const fd = new FormData();
    fd.append("id", dbId);
    fd.append("status", status);
    await apiFetch("update_testing", "POST", fd);
    await loadBloodTesting();
    showToast("Sample " + status + ".");
  } catch (e) {
    showToast("Error updating record.");
  }
}

async function requestAction(dbId, status) {
  try {
    const fd = new FormData();
    fd.append("id", dbId);
    fd.append("status", status);
    await apiFetch("update_request", "POST", fd);
    await loadBloodRequests();
    showToast("Request " + status + ".");
  } catch (e) {
    showToast("Error updating request.");
  }
}

async function removeExpiry(dbId) {
  try {
    const fd = new FormData();
    fd.append("id", dbId);
    await apiFetch("remove_expiry", "POST", fd);
    await loadExpiring();
    await loadStats();
    showToast("Expiry record removed.");
  } catch (e) {
    showToast("Error removing expiry.");
  }
}

/* ── Disease Panel ── */
function toggleDisease(rowId) {
  const d = document.getElementById("drawer-" + rowId);
  const open = d.classList.contains("open");
  document
    .querySelectorAll(".disease-drawer")
    .forEach((x) => x.classList.remove("open"));
  if (!open) d.classList.add("open");
}

function updateSelectColor(sel) {
  if (sel.value === "positive") {
    sel.style.background = "#fee2e2";
    sel.style.color = "#991b1b";
  } else {
    sel.style.background = "#d1fae5";
    sel.style.color = "#065f46";
  }
}

function updateHbBadge(rowId) {
  const v = parseFloat(document.getElementById("hb-" + rowId).value);
  const b = document.getElementById("hb-badge-" + rowId);
  if (isNaN(v)) {
    b.textContent = "—";
    b.className = "disease-val";
    return;
  }
  if (v < 12) {
    b.textContent = "Low";
    b.className = "disease-val dv-pos";
  } else if (v > 17.5) {
    b.textContent = "High";
    b.className = "disease-val dv-warn";
  } else {
    b.textContent = "Normal";
    b.className = "disease-val dv-normal";
  }
}

/* ── Edit Modal ── */
const editConfig = {
  appointments: [
    { label: "Donor Name", col: 0, type: "text" },
    { label: "Blood Group", col: 1, type: "text" },
    { label: "Requested Date", col: 2, type: "date" },
    { label: "Last Donation", col: 3, type: "date" },
    {
      label: "Status",
      col: 4,
      type: "select",
      options: ["scheduled", "pending", "completed", "cancelled"],
      isBadge: true,
    },
  ],
  inventory: [
    { label: "Blood Group", col: 0, type: "text" },
    { label: "Units Available", col: 1, type: "number" },
    { label: "Storage Date", col: 2, type: "date" },
    { label: "Expire Date", col: 3, type: "date" },
  ],
};

let _editCtx = null;

function openEdit(type, rowId, btn) {
  const row = btn.closest("tr");
  const cells = row.querySelectorAll("td");
  const fields = editConfig[type];
  if (!fields) return;
  document.getElementById("edit-modal-title").textContent = "Edit Record";
  const c = document.getElementById("edit-modal-fields");
  c.innerHTML = "";
  fields.forEach((f) => {
    const cell = cells[f.col];
    const val = f.isBadge
      ? cell.querySelector(".badge")?.textContent.trim()
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
        const op = document.createElement("option");
        op.value = o;
        op.textContent = capitalize(o);
        if (o === val?.toLowerCase()) op.selected = true;
        sel.appendChild(op);
      });
      div.appendChild(sel);
    } else {
      div.innerHTML += `<input class="form-input" type="${f.type}" data-col="${f.col}" value="${val}"/>`;
    }
    c.appendChild(div);
  });
  _editCtx = { row, type, dbId: row.dataset.dbId };
  document.getElementById("edit-modal").classList.add("open");
}

async function saveEdit() {
  if (!_editCtx) return;
  const { row, type, dbId } = _editCtx;
  const cells = row.querySelectorAll("td");
  const fd = new FormData();
  fd.append("id", dbId);

  document
    .querySelectorAll("#edit-modal-fields input, #edit-modal-fields select")
    .forEach((i) => {
      const col = parseInt(i.dataset.col);
      const isBadge = i.dataset.isBadge === "true";
      const val = i.value.trim();
      if (isBadge) {
        const b = cells[col].querySelector(".badge");
        if (b) {
          b.textContent = capitalize(val);
        }
        fd.append("status", val);
      } else {
        cells[col].textContent = val;
        if (col === 1 && type === "inventory")
          fd.append("units_available", val);
        if (col === 2 && type === "inventory") fd.append("storage_date", val);
        if (col === 3 && type === "inventory") fd.append("expire_date", val);
      }
    });

  try {
    const action =
      type === "inventory" ? "update_inventory" : "update_appointment";
    await apiFetch(action, "POST", fd);
    showToast("Record updated successfully.");
  } catch (e) {
    showToast("Error saving changes.");
  }
  closeModal("edit-modal");
}

/* ── Delete Modal ── */
let _deleteCtx = null;

function openDelete(rowId, tableId) {
  _deleteCtx = { rowId, tableId };
  document.getElementById("delete-modal").classList.add("open");
}

async function confirmDelete() {
  if (!_deleteCtx) return;
  const tbl = document.getElementById(_deleteCtx.tableId);
  const row = tbl?.querySelector(`tr[data-id="${_deleteCtx.rowId}"]`);
  if (row) {
    const dbId = row.dataset.dbId;
    try {
      const fd = new FormData();
      fd.append("id", dbId);
      await apiFetch("delete_appointment", "POST", fd);
    } catch (e) {}
    row.remove();
  }
  closeModal("delete-modal");
  showToast("Record deleted.");
  _deleteCtx = null;
}

/* ── Add Units Modal ── */
let _addRowId = null;

function openAdd(rowId) {
  _addRowId = rowId;
  document.getElementById("add-units-input").value = 1;
  document.getElementById("add-modal").classList.add("open");
}

function adjUnits(d) {
  const i = document.getElementById("add-units-input");
  i.value = Math.max(1, (parseInt(i.value) || 1) + d);
}

async function confirmAdd() {
  const n = parseInt(document.getElementById("add-units-input").value);
  if (!n || n < 1) {
    showToast("Enter a valid number.");
    return;
  }
  try {
    const fd = new FormData();
    fd.append("id", _addRowId);
    fd.append("units", n);
    await apiFetch("add_units", "POST", fd);
    await loadInventory();
    await loadStats();
    showToast(n + " unit(s) added.");
  } catch (e) {
    showToast("Error adding units.");
  }
  closeModal("add-modal");
  _addRowId = null;
}

/* ── Assign Modal ── */
let _assignRowId = null;

function openAssign(rowId) {
  _assignRowId = rowId;
  document.getElementById("assign-donor-input").value = "";
  document.getElementById("assign-notes-input").value = "";
  document.getElementById("assign-modal").classList.add("open");
}

function confirmAssign() {
  const d = document.getElementById("assign-donor-input").value.trim();
  if (!d) {
    showToast("Please enter a donor name.");
    return;
  }
  closeModal("assign-modal");
  showToast("Assigned to " + d + ".");
  _assignRowId = null;
}

/* ── Donor History Modal ── */
async function viewHistory(userId) {
  document.getElementById("history-modal-title").textContent =
    "Donation History";
  const list = document.getElementById("history-list");
  list.innerHTML =
    '<p style="text-align:center;color:var(--text-muted);padding:18px">Loading…</p>';
  document.getElementById("history-modal").classList.add("open");
  try {
    const res = await fetch(`php/redcross_api.php?action=donors`);
    const data = await res.json();
    const donor = data.find((d) => d.id == userId);
    if (!donor || !donor.donations) {
      list.innerHTML =
        '<p style="color:var(--text-muted);text-align:center;padding:18px">No donation history found.</p>';
      return;
    }
    document.getElementById("history-modal-title").textContent =
      donor.full_name + " — Donation History";
    list.innerHTML = `
      <div class="history-item">
        <div class="h-date">Last Donation: ${donor.last_donation || "—"}</div>
        <div class="h-type">${donor.blood_group || "—"} Blood</div>
        <span class="h-badge">${donor.donations} total donation(s)</span>
      </div>`;
  } catch (e) {
    list.innerHTML =
      '<p style="color:var(--text-muted);text-align:center;padding:18px">Could not load history.</p>';
  }
}

/* ── Utility ── */
function capitalize(s) {
  if (!s) return "";
  return s.charAt(0).toUpperCase() + s.slice(1);
}
