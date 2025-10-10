/* =========================================================
   crm.js — Single-file CRM JS (refactored & ready-to-paste)
   ========================================================= */

const API_BASE_URL = 'api/';

/* --------------------------
   App State
---------------------------*/
let guests = [];
let campaigns = [];
let feedback = [];
let complaints = [];
let loyaltyPrograms = [];

let currentFeedbackType = 'all';
let currentComplaintType = 'all';

let editingGuestId = null;
let editingCampaignId = null;
let editingComplaintId = null;

let guestChartInstance = null;
let loyaltyChartInstance = null;

/* --------------------------
   Boot
---------------------------*/
document.addEventListener('DOMContentLoaded', async () => {
  attachStaticListeners();
  await loadAll();
});

/* --------------------------
   One-shot loader
---------------------------*/
/* --------------------------
   One-shot loader
---------------------------*/
async function loadAll() {
  try {
    const [
      dashRes,
      guestsRes,
      campaignsRes,
      feedbackRes,
      complaintsRes,
      loyaltyRes,
      loyaltyStatsRes   // ✅ new
    ] = await Promise.all([
      apiRequest('dashboard.php'),
      apiRequest('guests.php'),
      apiRequest('campaigns.php'),
      apiRequest('feedback.php?type=all'),
      apiRequest('complaints.php'),
      apiRequest('loyalty.php'),
      apiRequest('loyalty.php?stats=1')   // ✅ get totals
    ]);

    const stats = dashRes?.data || {};
    const loyaltyStats = loyaltyStatsRes?.data?.current || {};  // ✅ extract members

    guests = guestsRes?.data || [];
    campaigns = campaignsRes?.data || [];
    feedback = feedbackRes?.data || [];
    complaints = complaintsRes?.data || [];
    loyaltyPrograms = loyaltyRes?.data || [];

    // ✅ Merge loyalty members into dashboard stats
    updateStatCards({
      ...stats,
      loyalty_members: loyaltyStats.members
    });

    initializeCharts(stats);

    renderGuests();
    renderCampaigns();
    renderFeedback();
    renderComplaints();
    renderPrograms();

    await loadGuestOptions(); // prepare select options for complaint form

    showNotification('Dashboard loaded');
  } catch (err) {
    console.error('loadAll failed:', err);
    showNotification('Failed to load data', 'error');
  }
}


/* --------------------------
   Helpers / API
---------------------------*/
async function safeParseJSON(response) {
  const text = await response.text();
  try {
    return JSON.parse(text);
  } catch {
    return { success: false, error: 'Invalid JSON response', raw: text };
  }
}

async function apiRequest(endpoint, method = 'GET', data = null) {
  try {
    const config = { method, headers: {} };
    if (method !== 'GET') {
      config.headers['Content-Type'] = 'application/json';
      if (data !== null) config.body = JSON.stringify(data);
    }
    const url = API_BASE_URL + endpoint;
    const resp = await fetch(url, config);
    const result = await safeParseJSON(resp);

    if (!resp.ok) {
      const msg = result?.error || result?.message || `Request failed (${resp.status})`;
      throw new Error(msg);
    }
    return result;
  } catch (e) {
    console.error('API Error:', e);
    showNotification(e.message || 'API error', 'error');
    throw e;
  }
}

function showNotification(message, type = 'success') {
  let n = document.getElementById('notification');
  if (!n) {
    n = document.createElement('div');
    n.id = 'notification';
    n.style.cssText = `
      position: fixed; top: 20px; right: 20px; padding: 12px 24px;
      border-radius: 8px; color: white; font-weight: 500; z-index: 9999;
      opacity: 0; transition: opacity .3s ease;`;
    document.body.appendChild(n);
  }
  n.textContent = message;
  n.style.backgroundColor =
    type === 'success' ? '#10b981' :
    type === 'error'   ? '#ef4444' :
    type === 'warning' ? '#f59e0b' : '#6b7280';
  n.style.opacity = '1';
  clearTimeout(n._hideTimeout);
  n._hideTimeout = setTimeout(() => (n.style.opacity = '0'), 3000);
}

/* --------------------------
   Navigation
---------------------------*/
function showSection(sectionName) { 
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));

  const target = document.getElementById(sectionName);
  if (!target) return console.warn('Section not found:', sectionName);
  target.classList.add('active');

  document.querySelectorAll('.menu-item').forEach(item => {
    const on = item.getAttribute('onclick') || '';
    if (on.includes(`'${sectionName}'`) || on.includes(`"${sectionName}"`)) {
      item.classList.add('active');
    }
  });

  if (sectionName === 'dashboard') {
    loadDashboardData();
  } else if (sectionName === 'loyalty') {
    loadLoyaltyPrograms();
  } else if (sectionName === 'feedback') {
    loadFeedback();
  } else if (sectionName === 'complaints') {
    loadComplaints();
    loadComplaintStats();
  }
}

/* --------------------------
   Dashboard
---------------------------*/
async function loadDashboardData() {
  try {
    const res = await apiRequest('dashboard.php');
    const stats = res?.data || {};
    updateStatCards(stats);
    initializeCharts(stats);
  } catch (e) { console.error(e); }
}

function updateStatCards(stats) {
  // Dashboard stat cards: update values and growth rates by unique ID
  // Guests (skip growth rate update)
  const guestsH3 = document.querySelector('#stat-total-guests h3');
  if (guestsH3) guestsH3.textContent = stats.total_guests ?? 0;
  const guestsGrowth = document.getElementById('growthGuests');
  if (guestsGrowth) setGrowthRateById('growthGuests', null);

  // Loyalty Members
  const membersH3 = document.querySelector('#stat-loyalty-members h3');
  if (membersH3) membersH3.textContent = stats.loyalty_members ?? 0;
  const membersGrowth = document.getElementById('growthMembers');
  if (membersGrowth) setGrowthRateById('growthMembers', calcGrowthRate(stats.loyalty_members, stats.prev_loyalty_members));

  // Campaigns
  const campaignsH3 = document.querySelector('#stat-active-campaigns h3');
  if (campaignsH3) campaignsH3.textContent = stats.active_campaigns ?? 0;
  const campaignsGrowth = document.getElementById('growthCampaigns');
  if (campaignsGrowth) setGrowthRateById('growthCampaigns', calcGrowthRate(stats.active_campaigns, stats.prev_active_campaigns));

  // Avg Rating (no growth rate, just value)
  const avgRatingH3 = document.querySelector('#stat-avg-rating h3');
  if (avgRatingH3) avgRatingH3.textContent = stats.avg_rating ?? '0.0';
  const avgRatingGrowth = document.getElementById('growthAvgRating');
  if (avgRatingGrowth) setGrowthRateById('growthAvgRating', null);

   // Add complaint stat updates
  const complaintsH3 = document.querySelector('#stat-total-complaints h3');
  if (complaintsH3) complaintsH3.textContent = stats.total_complaints ?? 0;
  const complaintsGrowth = document.getElementById('growthComplaints');
  if (complaintsGrowth) setGrowthRateById('growthComplaints', calcGrowthRate(stats.total_complaints, stats.prev_total_complaints));

  // Resolved Complaints (dashboard stat card)
  const resolvedComplaintsH3 = document.querySelector('#stat-resolved-complaints h3');
  if (resolvedComplaintsH3) resolvedComplaintsH3.textContent = stats.resolved_complaints ?? 0;
}

// Helper to calculate growth rate percentage
function calcGrowthRate(current, previous) {
  if (typeof current !== 'number' || typeof previous !== 'number' || previous === 0) return null;
  return ((current - previous) / Math.abs(previous)) * 100;
}

// Helper to set growth rate in a stat card by span ID
function setGrowthRateById(spanId, growth) {
  const el = document.getElementById(spanId);
  if (!el) return;
  if (growth === null || isNaN(growth) || growth === 0) {
    el.textContent = '';
    el.style.display = 'none';
    return;
  }
  el.style.display = 'inline-block';
  if (growth > 0) {
    el.textContent = `+${Math.abs(growth).toFixed(1)}%`;
    el.className = 'stat-change positive';
  } else if (growth < 0) {
    el.textContent = `-${Math.abs(growth).toFixed(1)}%`;
    el.className = 'stat-change negative';
  }
}


function initializeCharts(stats) {
  try { guestChartInstance?.destroy(); loyaltyChartInstance?.destroy(); } catch {}

  const gctx = document.getElementById('guestChart');
  if (gctx && typeof Chart !== 'undefined' && Array.isArray(stats.guest_trends)) {
    guestChartInstance = new Chart(gctx, {
      type: 'line',
      data: {
        labels: stats.guest_trends.map(t => t.month),
        datasets: [{
          label: 'Guests',
          data: stats.guest_trends.map(t => t.count),
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59,130,246,.1)',
          borderWidth: 2, tension: .4
        }]
      },
      options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
  }

  const lctx = document.getElementById('loyaltyChart');
  if (lctx && typeof Chart !== 'undefined' && Array.isArray(stats.loyalty_distribution)) {
    
    const tierColors = {
      bronze: '#cd7f32',
      silver: '#c0c0c0',
      gold:   '#ffd700',
      platinum: '#4f46e5'
    };

    const labels = stats.loyalty_distribution.map(l => 
      (l.tier || '').replace(/^./, c => c.toUpperCase())
    );
    const values = stats.loyalty_distribution.map(l => l.members_count || 0);
    const bgColors = stats.loyalty_distribution.map(l => tierColors[l.tier] || '#999999');

    loyaltyChartInstance = new Chart(lctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: values,
          backgroundColor: bgColors
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { 
            position: 'bottom',
            labels: {
              color: '#fff',
              font: { size: 14 }
            }
          }
        }
      }
    });
  }
}

/* --------------------------
   Guests - FIXED
---------------------------*/
async function loadGuests() {
  try {
    const res = await apiRequest('guests.php');
    guests = res?.data || [];
    renderGuests();
    await loadGuestOptions(); // For complaint form dropdown
    showNotification('Guests loaded successfully');
  } catch (e) { 
    console.error('Load guests error:', e);
    showNotification('Failed to load guests', 'error');
  }
}

function renderGuests() {
  const list = document.getElementById('guestsList');
  if (!list) return;
  list.innerHTML = '';

  if (!guests || guests.length === 0) {
    list.innerHTML = '<p style="color: white; text-align: center; padding: 40px;">No guests found. Add your first guest!</p>';
    return;
  }

  guests.forEach(guest => {
    const avatarText = 'G';
    const guestId = guest.guest_id || guest.id;
    const historyTypeId = `purchaseType_${guestId}`;
    // Improved actions row: flex, wrap, gap, rounded, shadow, hover
    const card = document.createElement('div');
    card.className = 'guest-card';
    card.style.cssText = `
      background: rgba(255,255,255,0.05);
      border-radius: 14px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      margin-bottom: 18px;
      padding: 18px 18px 12px 18px;
      transition: box-shadow .2s;
    `;
    card.onmouseenter = () => { card.style.boxShadow = '0 6px 24px rgba(59,130,246,0.12)'; };
    card.onmouseleave = () => { card.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)'; };
    card.innerHTML = `
      <div class="guest-header" style="margin-bottom:8px;">
        <div class="guest-info" style="display:flex;align-items:center;gap:12px;">
          <div class="guest-avatar" style="background:#3b82f6;color:white;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:600;">${avatarText}</div>
          <div>
            <span class="loyalty-badge ${guest.loyalty_tier || ''}" style="background:#fbbf24;color:#222;padding:2px 10px;border-radius:8px;font-size:12px;font-weight:600;">${(guest.loyalty_tier || 'unknown').toUpperCase()}</span>
          </div>
        </div>
      </div>
      <div class="guest-details" style="margin-bottom:10px;">
        <div class="guest-detail" style="color:#fff;font-size:14px;"><span>📧</span><span style="margin-left:6px;">${escapeHtml(guest.email || '—')}</span></div>
        <div class="guest-detail" style="color:#fff;font-size:14px;"><span>📍</span><span style="margin-left:6px;">Unknown</span></div>
      </div>
      <div class="guest-actions"
        style="display:flex;flex-wrap:wrap;gap:7px;align-items:center;overflow-x:auto;padding:4px 0;">
        <button class="btn btn-secondary"
          onclick="viewGuest(${guestId})"
          style="padding:6px 14px;min-width:70px;border-radius:8px;font-size:13px;transition:.15s;background:#6b7280;color:#fff;border:none;cursor:pointer;">👁️ View</button>
        <select id="${historyTypeId}" style="padding:5px 10px;border-radius:8px;min-width:90px;font-size:13px;border:1px solid #e5e7eb;background:#fff;">
          <option value="lounge">Lounge</option>
          <option value="giftshop">Giftshop</option>
          <option value="room_dining">Room Dining</option>
          <option value="restaurant">Restaurant</option>
          <option value="all">All</option>
        </select>
        <button class="btn btn-primary"
          onclick="showPurchaseHistoryWithType(${guestId})"
          style="padding:6px 14px;min-width:110px;border-radius:8px;font-size:13px;transition:.15s;background:#f59e0b;color:#fff;border:none;cursor:pointer;">🛒 History</button>
        <button class="btn btn-primary"
          onclick="deleteGuest(${guestId})"
          style="padding:6px 14px;min-width:70px;border-radius:8px;font-size:13px;transition:.15s;background:#ef4444;color:#fff;border:none;cursor:pointer;">🗑️ Delete</button>
      </div>
    `;
    list.appendChild(card);
  });
}

// Helper to get selected type and call showPurchaseHistory
function showPurchaseHistoryWithType(guestId) {
  const sel = document.getElementById(`purchaseType_${guestId}`);
  const type = sel ? sel.value : 'lounge';
  showPurchaseHistory(guestId, type);
}


/* --------------------------
   Add Guest Modal & Functions
---------------------------*/

function showAddGuestModal() {
  // Reset form completely
  const form = document.getElementById('addGuestForm');
  if (form) form.reset();
  
  // Show modal
  const modal = document.getElementById('addGuestModal');
  if (modal) {
    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
  }
  
  // Focus on first input
  setTimeout(() => {
    const firstInput = document.getElementById('guestName');
    if (firstInput) firstInput.focus();
  }, 100);
}

async function addGuest(event) {
  event.preventDefault();
  
  // Get form data
  const guestData = {
    first_name: document.getElementById('guestFirstName')?.value.trim() || '',
    last_name: document.getElementById('guestLastName')?.value.trim() || '',
    email: document.getElementById('guestEmail')?.value.trim() || '',
    first_phone: document.getElementById('guestFirstPhone')?.value.trim() || '',
    second_phone: document.getElementById('guestSecondPhone')?.value.trim() || '',
    status: 'active'
  };

  // Validation
  if (!guestData.first_name) {
    showNotification('First name is required', 'error');
    document.getElementById('guestFirstName')?.focus();
    return;
  }
  if (!guestData.last_name) {
    showNotification('Last name is required', 'error');
    document.getElementById('guestLastName')?.focus();
    return;
  }
  if (!guestData.email) {
    showNotification('Email address is required', 'error');
    document.getElementById('guestEmail')?.focus();
    return;
  }
  if (!isValidEmail(guestData.email)) {
    showNotification('Please enter a valid email address', 'error');
    document.getElementById('guestEmail')?.focus();
    return;
  }
  if (!guestData.first_phone) {
    showNotification('First phone is required', 'error');
    document.getElementById('guestFirstPhone')?.focus();
    return;
  }

  try {
    const response = await apiRequest('guests.php', 'POST', guestData);
    
    if (response.success) {
      await loadGuests();
      closeModal('addGuestModal');
      showNotification('Guest added successfully!');
    } else {
      throw new Error(response.error || 'Failed to add guest');
    }
  } catch (e) { 
    console.error('Add guest error:', e);
    showNotification(e.message || 'Failed to add guest', 'error');
  }
}

/* --------------------------
   View Guest Modal & Functions
---------------------------*/

function viewGuest(id) {
  // Find guest using flexible ID matching
  const guest = guests.find(g => {
    const guestId = g.guest_id || g.id;
    return Number(guestId) === Number(id);
  });

  if (!guest) {
    console.error('Guest not found with ID:', id);
    showNotification('Guest not found', 'error');
    return;
  }

  // Populate form fields (readonly)
  document.getElementById('viewGuestFirstName').value = guest.first_name || '';
  document.getElementById('viewGuestLastName').value = guest.last_name || '';
  document.getElementById('viewGuestEmail').value = guest.email || '';
  document.getElementById('viewGuestFirstPhone').value = guest.first_phone || '';
  document.getElementById('viewGuestSecondPhone').value = guest.second_phone || '';
  document.getElementById('viewGuestStatus').value = guest.status || 'active';

  // Show modal
  const modal = document.getElementById('viewGuestModal');
  if (modal) {
    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
  }

  // Focus on first input
  setTimeout(() => {
    const firstInput = document.getElementById('viewGuestFirstName');
    if (firstInput) firstInput.focus();
  }, 100);
}

/* --------------------------
   Delete Guest Function
---------------------------*/

async function deleteGuest(id) {
  const guest = guests.find(g => {
    const guestId = g.guest_id || g.id;
    return Number(guestId) === Number(id);
  });
  
  const guestName = guest?.name || 'this guest';
  
  if (!confirm(`Are you sure you want to delete ${guestName}? This action cannot be undone.`)) {
    return;
  }
  
  try {
    const response = await apiRequest('guests.php', 'DELETE', { id });
    
    if (response.success) {
      await loadGuests();
      showNotification('Guest deleted successfully!');
    } else {
      throw new Error(response.error || 'Failed to delete guest');
    }
  } catch (e) { 
    console.error('Delete guest error:', e);
    showNotification(e.message || 'Failed to delete guest', 'error');
  }
}

/* --------------------------
   Modal Management
---------------------------*/

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (!modal) return;
  
  modal.classList.remove('active');
  modal.setAttribute('aria-hidden', 'true');
  
  // Reset forms and states
  if (modalId === 'addGuestModal') {
    const form = document.getElementById('addGuestForm');
    if (form) form.reset();
  } else if (modalId === 'viewGuestModal') {
    const form = document.getElementById('viewGuestForm');
    if (form) form.reset();
  }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
  if (event.target.classList.contains('modal') && event.target.classList.contains('active')) {
    const modalId = event.target.id;
    closeModal(modalId);
  }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    const activeModal = document.querySelector('.modal.active');
    if (activeModal) {
      closeModal(activeModal.id);
    }
  }
});

// --- Lounge Order History Modal ---
async function showPurchaseHistory(guestId, type = 'lounge') {
  try {
    // Fetch purchase history from API
    let endpoint = `guests.php?guest_id=${guestId}&history_type=${type}`;
    const res = await apiRequest(endpoint);
    let orders = res.data;

    let html = '';
    if (type === 'all') {
      // Show all types
      html = Object.entries(orders).map(([key, arr]) => {
        let label = {
          lounge: 'Lounge Orders',
          giftshop: 'Giftshop Sales',
          room_dining: 'Room Dining Orders',
          restaurant: 'Restaurant Orders'
        }[key] || key;
        return `
          <h4 style="color:#fbbf24;margin-top:24px;">${label}</h4>
          ${arr.length === 0 ? '<p style="color:white;">No records found.</p>' : `
            <table style="width:100%;color:white;border-collapse:collapse;margin-bottom:16px;">
              <thead>
                <tr>
                  <th style="border-bottom:1px solid #fff;padding:6px;">Order/Sale #</th>
                  <th style="border-bottom:1px solid #fff;padding:6px;">Type</th>
                  <th style="border-bottom:1px solid #fff;padding:6px;">Total</th>
                  <th style="border-bottom:1px solid #fff;padding:6px;">Date</th>
                  <th style="border-bottom:1px solid #fff;padding:6px;">Status</th>
                </tr>
              </thead>
              <tbody>
                ${arr.map(o => `
                  <tr>
                    <td style="padding:6px;">${o.order_id || o.sale_id}</td>
                    <td style="padding:6px;">${escapeHtml(o.order_type || o.payment_method || '-')}</td>
                    <td style="padding:6px;">₱${Number(o.total_amount).toFixed(2)}</td>
                    <td style="padding:6px;">${o.order_date || o.sale_date ? new Date(o.order_date || o.sale_date).toLocaleString() : '-'}</td>
                    <td style="padding:6px;">${escapeHtml(o.status || '-')}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          `}
        `;
      }).join('');
    } else {
      // Single type
      if (!orders.length) {
        html = '<p style="color:white;text-align:center;padding:24px;">No purchase history found for this guest.</p>';
      } else {
        html = `
          <table style="width:100%;color:white;border-collapse:collapse;">
            <thead>
              <tr>
                <th style="border-bottom:1px solid #fff;padding:6px;">Order/Sale #</th>
                <th style="border-bottom:1px solid #fff;padding:6px;">Type</th>
                <th style="border-bottom:1px solid #fff;padding:6px;">Total</th>
                <th style="border-bottom:1px solid #fff;padding:6px;">Date</th>
                <th style="border-bottom:1px solid #fff;padding:6px;">Status</th>
              </tr>
            </thead>
            <tbody>
              ${orders.map(o => `
                <tr>
                  <td style="padding:6px;">${o.order_id || o.sale_id}</td>
                  <td style="padding:6px;">${escapeHtml(o.order_type || o.payment_method || '-')}</td>
                  <td style="padding:6px;">₱${Number(o.total_amount).toFixed(2)}</td>
                  <td style="padding:6px;">${o.order_date || o.sale_date ? new Date(o.order_date || o.sale_date).toLocaleString() : '-'}</td>
                  <td style="padding:6px;">${escapeHtml(o.status || '-')}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `;
      }
    }

    showPurchaseHistoryModal(html);
  } catch (e) {
    showPurchaseHistoryModal('<p style="color:white;text-align:center;padding:24px;">Failed to load purchase history.</p>');
  }
}

function showPurchaseHistoryModal(contentHtml) {
  let modal = document.getElementById('purchaseHistoryModal');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'purchaseHistoryModal';
    modal.className = 'modal';
    modal.innerHTML = `
      <div class="modal-content" style="max-width:700px;" role="dialog" aria-modal="true" aria-labelledby="purchaseHistoryTitle">
        <h3 id="purchaseHistoryTitle"></h3>
        <div id="purchaseHistoryContent"></div>
        <div class="modal-actions">
          <button type="button" onclick="closeModal('purchaseHistoryModal')" class="btn-secondary">Close</button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
  }
  // Set the modal title based on the selected type
  let type = 'lounge'; // default
  const lastSel = document.querySelector('[id^="purchaseType_"]');
  if (lastSel) type = lastSel.value;
  let title = 'Purchase History';
  if (type === 'giftshop') title = 'Giftshop Purchase History';
  else if (type === 'room_dining') title = 'Room Dining Purchase History';
  else if (type === 'restaurant') title = 'Restaurant Purchase History';
  else if (type === 'lounge') title = 'Lounge Purchase History';
  else if (type === 'all') title = 'All Purchase History';
  document.getElementById('purchaseHistoryTitle').textContent = title;
  document.getElementById('purchaseHistoryContent').innerHTML = contentHtml;
  modal.classList.add('active');
  modal.setAttribute('aria-hidden', 'false');
}

/* --------------------------
   Helper Functions
---------------------------*/

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function escapeHtml(text) {
  if (typeof text !== 'string') return text;
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Load guest options for complaint form dropdown
async function loadGuestOptions() {
  try {
    if (!Array.isArray(guests) || !guests.length) {
      const res = await apiRequest('guests.php');
      guests = res?.data || [];
    }

    const select = document.getElementById('complaintGuestId');
    if (!select) return;

    select.innerHTML = '';
    const noneOpt = document.createElement('option');
    noneOpt.value = '';
    noneOpt.textContent = '-- Select guest (or leave blank to type name) --';
    select.appendChild(noneOpt);

    guests.forEach(g => {
      const opt = document.createElement('option');
      const guestId = g.guest_id || g.id;
      opt.value = guestId;
      // Add guest profile info as data attributes
      opt.textContent = `${g.first_name || ''} ${g.last_name || ''} (${g.email || 'no email'})`;
      opt.setAttribute('data-profile', JSON.stringify({
        guest_id: guestId,
        first_name: g.first_name || '',
        last_name: g.last_name || '',
        email: g.email || '',
        status: g.status || '',
        loyalty_tier: g.loyalty_tier || ''
      }));
      select.appendChild(opt);
    });

    // Optional: On change, show guest profile info somewhere in the form
    select.onchange = function() {
      const selected = select.options[select.selectedIndex];
      const profile = selected.getAttribute('data-profile');
      if (profile) {
        // You can parse and display this profile info in your complaint modal as needed
        // Example: console.log(JSON.parse(profile));
      }
    };
  } catch (err) {
    console.error('Failed to load guests for complaint select:', err);
  }
}

/* --------------------------
   Initialize Guest Management
---------------------------*/

// Auto-load guests when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  // Load guests if we're on the guests section
  const guestsSection = document.getElementById('guests');
  if (guestsSection) {
    loadGuests();
  }
});

/* --------------------------
   Expose functions to global scope
------

/* --------------------------
   Campaigns - FIXED
---------------------------*/
async function loadCampaigns() {
  try {
    campaigns = (await apiRequest('campaigns.php'))?.data || [];
    renderCampaigns();
  } catch (e) {
    console.error(e);
  }
}

function renderCampaigns() {
  const wrap = document.getElementById('campaignsList');
  if (!wrap) return;
  wrap.innerHTML = '';

  if (!campaigns.length) {
    wrap.innerHTML = '<p style="color:white;text-align:center;padding:40px;">No campaigns available.</p>';
    updateCampaignStats();
    return;
  }

  campaigns.forEach(c => {
    const div = document.createElement('div');
    div.className = 'campaign-card';
    div.innerHTML = `
      <div class="campaign-header">
        <div class="campaign-info">
          <div class="campaign-icon">📧</div>
          <div class="campaign-details">
            <h3>${escapeHtml(c.name || '—')}</h3>
            <div class="campaign-meta">
              <span class="status-badge ${c.status || ''}">
                ${(c.status || 'draft').replace(/^./, m=>m.toUpperCase())}
              </span>
              <span>Target: ${escapeHtml(c.target_audience || '—')}</span>
              <span>Type: ${escapeHtml(c.type || '—')}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="campaign-content">
        <p><strong>Description:</strong> ${escapeHtml(c.description || 'No description')}</p>
        <div class="campaign-stats">
          <div class="campaign-stat"><h4>${c.sent_count || 0}</h4><p>Sent</p></div>
          <div class="campaign-stat"><h4>${c.open_rate || 0}%</h4><p>Opened</p></div>
          <div class="campaign-stat"><h4>${c.click_rate || 0}%</h4><p>Clicked</p></div>
        </div>
      </div>
      <div class="campaign-actions">
        <button class="btn btn-secondary" onclick="editCampaign(${c.id})" style="margin-right: 8px; padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer;">✏️ Edit</button>
        <button class="btn btn-primary" onclick="viewCampaign(${c.id})" style="margin-right: 8px; padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer;">👁️ View</button>
        <button class="btn btn-danger" onclick="deleteCampaign(${c.id})" style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer;">🗑️ Delete</button>
      </div>`;
    wrap.appendChild(div);
  });

  updateCampaignStats();
}

function updateCampaignStats() {
  const totalSent = campaigns.reduce((sum, c) => sum + (Number(c.sent_count) || 0), 0);
  const totalOpened = campaigns.reduce((sum, c) =>
    sum + Math.round(((c.open_rate || 0) / 100) * (c.sent_count || 0)), 0);
  const totalClicked = campaigns.reduce((sum, c) =>
    sum + Math.round(((c.click_rate || 0) / 100) * (c.sent_count || 0)), 0);

  const clickRate = totalSent > 0 ? ((totalClicked / totalSent) * 100).toFixed(1) : 0;

  if (document.getElementById('statTotalSent'))
    document.getElementById('statTotalSent').textContent = totalSent;
  if (document.getElementById('statOpened'))
    document.getElementById('statOpened').textContent = totalOpened;
  if (document.getElementById('statClicked'))
    document.getElementById('statClicked').textContent = totalClicked;
  if (document.getElementById('statClickRate'))
    document.getElementById('statClickRate').textContent = clickRate + '%';
}

function showCreateCampaignModal() {
  document.getElementById('createCampaignForm')?.reset();
  editingCampaignId = null;
  document.getElementById('createCampaignTitle').textContent = "Create New Campaign";
  document.getElementById('campaignSaveBtn').style.display = "inline-block";
  document.getElementById('campaignCancelBtn').textContent = "Cancel";
  document.getElementById('campaignExtraStats').style.display = "none";
  document.getElementById('campaignAdminFields').style.display = "block";
  toggleCampaignFields(true);
  document.getElementById('createCampaignModal')?.classList.add('active');
  // Add this line:
  populateCampaignAudienceTiers();
}

function editCampaign(id) {
  const c = campaigns.find(x => Number(x.id) === Number(id));
  if (!c) return showNotification('Campaign not found', 'error');
  
  editingCampaignId = id;

  document.getElementById('campaignName').value = c.name || '';
  document.getElementById('campaignDescription').value = c.description || '';
  document.getElementById('campaignType').value = c.type || '';
  document.getElementById('campaignAudience').value = c.target_audience || '';
  document.getElementById('campaignMessage').value = c.message || '';

  const validStatuses = ['draft','scheduled','active','completed'];
  const safeStatus = validStatuses.includes(c.status) ? c.status : 'draft';
  document.getElementById('campaignStatus').value = safeStatus;

  document.getElementById('campaignSchedule').value = c.schedule || '';

  // Populate admin fields for edit
  document.getElementById('campaignSentCount').value = c.sent_count || 0;
  document.getElementById('campaignOpenRate').value = c.open_rate || 0;
  document.getElementById('campaignClickRate').value = c.click_rate || 0;

  document.getElementById('createCampaignTitle').textContent = "Edit Campaign";
  document.getElementById('campaignSaveBtn').style.display = "inline-block";
  document.getElementById('campaignCancelBtn').textContent = "Cancel";
  document.getElementById('campaignExtraStats').style.display = "none";

  document.getElementById('campaignAdminFields').style.display = "block";

  toggleCampaignFields(true);
  document.getElementById('createCampaignModal')?.classList.add('active');
}

async function createCampaign(e) {
  e?.preventDefault();

  const data = {
    name: document.getElementById('campaignName')?.value.trim() || '',
    description: document.getElementById('campaignDescription')?.value.trim() || '',
    type: document.getElementById('campaignType')?.value || '',
    target_audience: document.getElementById('campaignAudience')?.value || '',
    message: document.getElementById('campaignMessage')?.value.trim() || '',
    status: document.getElementById('campaignStatus')?.value || 'draft',
    schedule: document.getElementById('campaignSchedule')?.value || null,
    sent_count: Number(document.getElementById('campaignSentCount')?.value) || 0,
    open_rate: Number(document.getElementById('campaignOpenRate')?.value) || 0,
    click_rate: Number(document.getElementById('campaignClickRate')?.value) || 0
  };

  const validStatuses = ['draft','scheduled','active','completed'];
  if (!validStatuses.includes(data.status)) {
    data.status = 'draft';
  }

  if (!data.name || !data.type || !data.target_audience || !data.message) {
    return showNotification('Please fill in all required campaign fields', 'error');
  }

  try {
    if (editingCampaignId) {
      data.id = editingCampaignId;
      await apiRequest('campaigns.php', 'PUT', data);
      showNotification('Campaign updated successfully!');
      editingCampaignId = null;
    } else {
      await apiRequest('campaigns.php', 'POST', data);
      showNotification('Campaign created successfully!');
    }
    
    await loadCampaigns();
    closeModal('createCampaignModal');
    document.getElementById('createCampaignForm')?.reset();
    document.getElementById('campaignAdminFields').style.display = "none";
  } catch (err) {
    console.error(err);
    showNotification('Failed to save campaign', 'error');
  }
}

function viewCampaign(id) {
  const c = campaigns.find(x => Number(x.id) === Number(id));
  if (!c) return showNotification('Campaign not found', 'error');
  
  editingCampaignId = null;

  document.getElementById('campaignName').value = c.name || '';
  document.getElementById('campaignDescription').value = c.description || '';
  document.getElementById('campaignType').value = c.type || '';
  document.getElementById('campaignAudience').value = c.target_audience || '';
  document.getElementById('campaignMessage').value = c.message || '';
  document.getElementById('campaignStatus').value = c.status || 'draft';
  document.getElementById('campaignSchedule').value = c.schedule || '';

  document.getElementById('campaignSentCount').value = c.sent_count || 0;
  document.getElementById('campaignOpenRate').value = c.open_rate || 0;
  document.getElementById('campaignClickRate').value = c.click_rate || 0;

  document.getElementById('createCampaignTitle').textContent = "View Campaign";
  document.getElementById('campaignSaveBtn').style.display = "none";
  document.getElementById('campaignCancelBtn').textContent = "Close";
  document.getElementById('campaignExtraStats').style.display = "block";
  document.getElementById('campaignAdminFields').style.display = "block";

  toggleCampaignFields(false);
  document.getElementById('createCampaignModal')?.classList.add('active');
}

function toggleCampaignFields(enable) {
  const fields = [
    'campaignName',
    'campaignDescription', 
    'campaignType',
    'campaignAudience',
    'campaignMessage',
    'campaignStatus',
    'campaignSchedule',
    'campaignSentCount',
    'campaignOpenRate',
    'campaignClickRate'
  ];

  fields.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.disabled = !enable;
  });
}

/* --------------------------
   Feedback - FIXED
---------------------------*/
async function loadFeedback() {
  try {
    feedback = (await apiRequest('feedback.php?type=all'))?.data || [];
    renderFeedback();
    updateFeedbackStats();
  } catch (e) { console.error(e); }
}

function renderFeedback() {
  const list = document.getElementById('feedbackList');
  if (!list) return;
  list.innerHTML = '';

  feedback = feedback.map(f => ({
    ...f,
    type: f.type && String(f.type).trim() !== '' ? f.type : 'review'
  }));

  let filtered = feedback;
  if (currentFeedbackType !== 'all') {
    filtered = feedback.filter(f => f.type === currentFeedbackType);
  }

  if (!filtered.length) {
    list.innerHTML = '<p style="color: white; text-align: center; padding: 40px;">No feedback available.</p>';
    updateFeedbackStats();
    return;
  }

  filtered.forEach(item => {
    const avatar = (item.guest_name || '')
      .split(' ')
      .map(n => n?.[0] || '')
      .join('')
      .toUpperCase() || 'G';

    // Add guest profile link if available
    let guestProfileHtml = '';
    if (item.guest_profile_url && item.guest_id) {
      // Instead of <a href=...>, use a button to open the modal
      guestProfileHtml = `<button onclick="viewGuest(${item.guest_id})" style="color:#3b82f6;text-decoration:underline;font-size:13px;margin-left:8px;background:none;border:none;cursor:pointer;">View Profile</button>`;
    }

    const card = document.createElement('div');
    card.className = 'feedback-card';
    card.innerHTML = `
      <div class="feedback-header">
        <div class="feedback-avatar">${avatar}</div>
        <div class="feedback-info">
          <h3>${escapeHtml(item.guest_name || '—')}${guestProfileHtml}</h3>
          <div class="feedback-meta">
            <span class="feedback-type ${item.type || ''}">${(item.type || '').toUpperCase()}</span>
            <span>${item.created_at ? new Date(item.created_at).toLocaleDateString() : ''}</span>
          </div>
        </div>
      </div>
      <div class="stars">${generateStars(item.rating || 0)}</div>
      <div class="feedback-message">${escapeHtml(item.comment || '')}</div>
      ${
        item.reply
          ? `<div class="feedback-reply" style="background:gray;padding:12px;border-radius:8px;margin-top:12px;border-left:4px solid #3b82f6;"><strong>Reply:</strong> ${escapeHtml(item.reply)}</div>`
          : ''
      }
      <div class="feedback-actions">
        <button class="btn btn-secondary" onclick="replyToFeedback(${item.id})" style="margin-right: 8px; padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer;">💬 Reply</button>
        <button class="btn btn-success" onclick="updateFeedbackStatus(${item.id}, 'approved')" style="margin-right: 8px; padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer;">✅ Approve</button>
        <button class="btn btn-danger" onclick="deleteFeedback(${item.id})" style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer;">🗑️ Delete</button>
      </div>`;
    list.appendChild(card);
  });

  updateFeedbackStats();
}

function updateFeedbackStats() {
  // Only count reviews, remove service_feedback
  const totalReviews = feedback.filter(f => f.type === 'review').length;

  const rated = feedback.filter(f => f.rating && !isNaN(f.rating));
  const avgRating = rated.length
    ? (rated.reduce((sum, f) => sum + Number(f.rating), 0) / rated.length).toFixed(1)
    : 0;

  const totalFeedback = feedback.length;
  const resolved = feedback.filter(f => f.status === 'approved' || f.status === 'rejected').length;
  const resolutionRate = totalFeedback > 0 ? Math.round((resolved / totalFeedback) * 100) : 0;

  // Growth rates (dummy, you should replace with real previous values if available)
  const prevTotalReviews = window.prevTotalReviews ?? totalReviews;
  const prevAvgRating = window.prevAvgRating ?? avgRating;

  if (document.getElementById('averageRating')) document.getElementById('averageRating').textContent = avgRating;
  if (document.getElementById('totalReviews')) document.getElementById('totalReviews').textContent = totalReviews;

  if (document.getElementById('statAverageRating')) document.getElementById('statAverageRating').textContent = avgRating;
  if (document.getElementById('statTotalReviews')) {
    document.getElementById('statTotalReviews').textContent = totalReviews;
    setGrowthRateById('growthTotalReviews', calcGrowthRate(totalReviews, prevTotalReviews));
  }
  if (document.getElementById('statResolutionRate')) document.getElementById('statResolutionRate').textContent = resolutionRate + '%';
  // Save current as previous for next call
  window.prevTotalReviews = totalReviews;
  window.prevAvgRating = avgRating;
}


function generateStars(rating) {
  let s = '';
  for (let i = 1; i <= 5; i++) s += i <= rating ? '⭐' : '☆';
  return s;
}

function showFeedbackType(type) {
  currentFeedbackType = type;
  document.querySelectorAll('#feedback .tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('#feedback .tab-btn').forEach(b => {
    const on = b.getAttribute('onclick') || '';
    if (on.includes(`'${type}'`) || on.includes(`"${type}"`)) b.classList.add('active');
  });
  renderFeedback();
}
function replyToFeedback(id) {
  // Find feedback item
  const item = feedback.find(f => Number(f.id) === Number(id));
  if (!item) return showNotification('Feedback not found', 'error');

  // Show reply modal and set up form
  const modal = document.getElementById('replyModal');
  if (!modal) return;

  document.getElementById('replyFeedbackId').value = item.id;
  document.getElementById('replyMessage').value = item.reply || '';

  modal.classList.add('active');
  modal.setAttribute('aria-hidden', 'false');

  // Attach submit handler (detach first to avoid duplicates)
  const form = document.getElementById('replyForm');
  if (form) {
    form.onsubmit = async function(e) {
      e.preventDefault();
      await sendFeedbackReply();
    };
  }
}

async function sendFeedbackReply() {
  const id = document.getElementById('replyFeedbackId').value;
  const reply = document.getElementById('replyMessage').value.trim();
  if (!id || !reply) {
    showNotification('Reply cannot be empty', 'error');
    return;
  }
  try {
    await apiRequest('feedback.php', 'PUT', { id, reply });
    await loadFeedback();
    closeModal('replyModal');
    showNotification('Reply sent successfully!');
  } catch (e) {
    showNotification('Failed to send reply', 'error');
  }
}

async function updateFeedbackStatus(id, status = 'approved') {
  try {
    await apiRequest('feedback.php', 'PUT', { id, status });
    await loadFeedback();
    showNotification('Feedback approved!');
  } catch (e) {
    showNotification('Failed to approve feedback', 'error');
  }
}

async function deleteFeedback(id) {
  if (!confirm('Are you sure you want to delete this feedback?')) return;
  try {
    await apiRequest('feedback.php', 'DELETE', { id });
    await loadFeedback();
    showNotification('Feedback deleted successfully!');
  } catch (e) {
    showNotification('Failed to delete feedback', 'error');
  }
}
function showComplaintType(type) {
  // Only allow 'all' and 'complaint'
  currentComplaintType = type === 'complaint' ? 'complaint' : 'all';
  document.querySelectorAll('#complaints .tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('#complaints .tab-btn').forEach(b => {
    const on = b.getAttribute('onclick') || '';
    if (on.includes(`'${type}'`) || on.includes(`"${type}"`)) b.classList.add('active');
  });
  renderComplaints();
}

/* --------------------------
   Complaints - Updated for guest_id - FIXED
---------------------------*/
async function loadComplaints() {
  try {
    complaints = (await apiRequest('complaints.php'))?.data || [];
    renderComplaints();
  } catch (e) { console.error(e); }
}

async function loadComplaintStats() {
  try {
    const res = await apiRequest('complaints.php?stats=1');
    if (!res?.data) return;

    const stats = res.data;
    // Always set stat card values to 0 or 0% if falsy
    const setIf = (id, val, isPercent = false) => {
      const el = document.getElementById(id);
      if (el) el.textContent = (val !== undefined && val !== null && val !== "") ? (isPercent ? val + '%' : val) : (isPercent ? '0%' : '0');
    };

    setIf('statTotalComplaints', stats.total_complaints);
    setIf('statResolvedComplaints', stats.resolved_complaints);
    setIf('statActiveComplaints', stats.active_complaints);
    setIf('statSuggestions', stats.total_suggestions);
    setIf('statCompliments', stats.total_compliments);
    setIf('statResolutionRate', stats.resolution_rate, true);
  } catch (err) {
    console.error("Failed to load complaint stats:", err);
  }
}

function renderComplaints() {
  const list = document.getElementById('complaintsList');
  if (!list) return;
  list.innerHTML = '';

  let filtered;
  if (currentComplaintType === 'all') {
    filtered = complaints;
  } else if (
    currentComplaintType === 'complaint' ||
    currentComplaintType === 'compliment' ||
    currentComplaintType === 'suggestion'
  ) {
    filtered = complaints.filter(c => c.type === currentComplaintType);
  } else {
    // fallback: filter by status if needed (for future status tabs)
    filtered = complaints.filter(c => c.status === currentComplaintType);
  }

  if (!filtered.length) {
    list.innerHTML = '<p style="color: white; text-align: center; padding: 40px;">No complaints available.</p>';
    updateComplaintStats();
    return;
  }

  filtered.forEach(item => {
    const avatar = (item.guest_name || '')
      .split(' ')
      .map(n => n?.[0] || '')
      .join('')
      .toUpperCase() || 'G';

    const card = document.createElement('div');
    card.className = 'feedback-card';
    card.innerHTML = `
      <div class="feedback-header">
        <div class="feedback-avatar">${avatar}</div>
        <div class="feedback-info">
          <h3>${escapeHtml(item.guest_name || '—')}</h3>
          <div class="feedback-meta">
            <span class="status-badge ${item.status || ''}">
              ${(item.status || '').replace(/^./, m => m.toUpperCase())}
            </span>
            <span>${item.created_at ? new Date(item.created_at).toLocaleDateString() : ''}</span>
          </div>
        </div>
      </div>
      <div class="feedback-rating">
        ${item.rating ? generateStars(item.rating) : 'No Rating'}
      </div>
      <div class="feedback-message">
        ${escapeHtml(item.comment || item.message || '')}
      </div>
      <div class="feedback-actions">
        <button class="btn btn-secondary" onclick="editComplaint(${item.id})" style="margin-right: 8px; padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer;">✏️ Edit Status</button>
        <button class="btn btn-primary" onclick="replyToComplaint(${item.id})" style="margin-right: 8px; padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer;">💬 Respond</button>
        <button class="btn btn-danger" onclick="deleteComplaint(${item.id})" style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer;">🗑️ Delete</button>
      </div>`;
    list.appendChild(card);
  });

  updateComplaintStats();
}

async function loadGuestOptions() {
  try {
    if (!Array.isArray(guests) || !guests.length) {
      const res = await apiRequest('guests.php');
      guests = res?.data || [];
    }

    const select = document.getElementById('complaintGuestId');
    if (!select) return;

    select.innerHTML = '';
    const noneOpt = document.createElement('option');
    noneOpt.value = '';
    noneOpt.textContent = '-- Select guest (or leave blank to type name) --';
    select.appendChild(noneOpt);

    guests.forEach(g => {
      const opt = document.createElement('option');
      opt.value = g.guest_id || g.id;
      // Add guest profile info as data attributes
      opt.textContent = `${g.first_name || ''} ${g.last_name || ''} (${g.email || 'no email'})`;
      opt.setAttribute('data-profile', JSON.stringify({
        guest_id: g.guest_id || g.id,
        first_name: g.first_name || '',
        last_name: g.last_name || '',
        email: g.email || '',
        status: g.status || '',
        loyalty_tier: g.loyalty_tier || ''
      }));
      select.appendChild(opt);
    });

    // Optional: On change, show guest profile info somewhere in the form
    select.onchange = function() {
      const selected = select.options[select.selectedIndex];
      const profile = selected.getAttribute('data-profile');
      if (profile) {
        // You can parse and display this profile info in your complaint modal as needed
        // Example: console.log(JSON.parse(profile));
      }
    };
  } catch (err) {
    console.error('Failed to load guests for complaint select', err);
  }
}

function showCreateComplaintModal() {
  loadGuestOptions();
  document.getElementById('createComplaintForm')?.reset();
  const modal = document.getElementById('createComplaintModal');
  if (modal) modal.classList.add('active');
}

async function createComplaint(e) {
  e?.preventDefault();

  const select = document.getElementById('complaintGuestId');
  const manualNameInput = document.getElementById('complaintGuestName');
  let guest_id = select?.value || null;
  let guest_name = '';

  if (guest_id) {
    const g = guests.find(x => {
      const guestId = x.id || x.guest_id;
      return String(guestId) === String(guest_id);
    });
    
    if (g) {
      guest_name = g.name || '';
      if (!guest_name && g.first_name) {
        guest_name = `${g.first_name} ${g.last_name || ''}`.trim();
      }
      if (!guest_name) {
        guest_name = (manualNameInput?.value || '').trim();
      }
    }
  } else {
    guest_name = (manualNameInput?.value || '').trim();
  }

  const comment = (document.getElementById('complaintComment')?.value || '').trim();
  const status = document.getElementById('complaintStatus')?.value || 'pending';
  const type = document.getElementById('complaintType')?.value || 'complaint';

  if (!guest_name) return showNotification('Guest name is required (select a guest or type a name)', 'error');
  if (!comment) return showNotification('Comment is required', 'error');

  const payload = { guest_id: guest_id || null, guest_name, comment, status, type };

  try {
    await apiRequest('complaints.php', 'POST', payload);
    await loadComplaints();
    closeModal('createComplaintModal');
    document.getElementById('createComplaintForm')?.reset();
    showNotification('Complaint submitted successfully!');
  } catch (err) {
    console.error('Failed adding complaint:', err);
    showNotification(err?.message || 'Failed to submit complaint', 'error');
  }
}

function editComplaint(id) {
  const item = complaints.find(c => Number(c.id) === Number(id));
  if (!item) return showNotification('Complaint not found', 'error');

  editingComplaintId = id;
  document.getElementById('editComplaintId').value = item.id || '';
  document.getElementById('editComplaintGuestName').value = item.guest_name || '';
  document.getElementById('editComplaintComment').value = item.comment || '';
  document.getElementById('editComplaintType').value = item.type || 'complaint';
  document.getElementById('editComplaintRating').value = item.rating || '';
  document.getElementById('editComplaintStatus').value = item.status || 'pending';

  document.getElementById('editComplaintModal')?.classList.add('active');
}

async function updateComplaint(e) {
  e?.preventDefault();
  const data = {
    id: document.getElementById('editComplaintId')?.value,
    type: document.getElementById('editComplaintType')?.value || 'complaint',
    rating: document.getElementById('editComplaintRating')?.value || null,
    status: document.getElementById('editComplaintStatus')?.value || 'pending'
  };

  if (!data.id) return showNotification('Missing complaint ID', 'error');

  try {
    await apiRequest('complaints.php', 'PUT', data);
    await loadComplaints();
    closeModal('editComplaintModal');
    editingComplaintId = null;
    showNotification('Complaint updated successfully!');
  } catch (e) {
    console.error(e);
    showNotification('Failed to update complaint', 'error');
  }
}

async function replyToComplaint(id) {
  const item = complaints.find(c => Number(c.id) === Number(id));
  if (!item) return;
  const reply = prompt(`Respond to ${item.guest_name || 'Guest'}'s complaint:`);
  if (!reply?.trim()) return;

  try {
    await apiRequest('complaints.php', 'PUT', { id, status: 'resolved', reply: reply.trim() });
    await loadComplaints();
    showNotification('Response sent and complaint marked as resolved!');
  } catch (e) { console.error(e); }
}

async function deleteComplaint(id) {
  if (!confirm('Are you sure you want to delete this complaint?')) return;
  try {
    await apiRequest('complaints.php', 'DELETE', { id });
    await loadComplaints();
    showNotification('Complaint deleted successfully!');
  } catch (e) { console.error(e); }
}

function updateComplaintStats() {
  const totalSuggestions = complaints.filter(c => c.type === 'suggestion').length;
  const totalCompliments = complaints.filter(c => c.type === 'compliment').length;
  const totalComplaints = complaints.filter(c => c.type === 'complaint').length;
  const activeComplaints = complaints.filter(c => c.type === 'complaint' && c.status !== 'resolved' && c.status !== 'dismissed').length;
  const resolved = complaints.filter(c => c.status === 'resolved').length;
  const resolutionRate = totalComplaints > 0 ? Math.round((resolved / totalComplaints) * 100) : 0;

  // Growth rates (dummy, you should replace with real previous values if available)
  const prevTotalComplaints = window.prevTotalComplaints ?? totalComplaints;
  const prevActiveComplaints = window.prevActiveComplaints ?? activeComplaints;

  // Always set stat card values to 0 or 0% if falsy
  const setIf = (id, val, isPercent = false) => {
    const el = document.getElementById(id);
    if (el) el.textContent = (val !== undefined && val !== null && val !== "") ? (isPercent ? val + '%' : val) : (isPercent ? '0%' : '0');
  };

  setIf('statSuggestions', totalSuggestions);
  setGrowthRateById('growthSuggestions', null);
  setIf('statCompliments', totalCompliments);
  setGrowthRateById('growthCompliments', null);
  setIf('statActiveComplaints', activeComplaints);
  setGrowthRateById('growthActiveComplaints', calcGrowthRate(activeComplaints, prevActiveComplaints));
  setIf('statResolutionRate', resolutionRate, true);
  setGrowthRateById('growthResolutionRateComplaints', null);
  // Save current as previous for next call
  window.prevTotalComplaints = totalComplaints;
  window.prevActiveComplaints = activeComplaints;
}


/* --------------------------
   Loyalty Programs (unchanged)
---------------------------*/
async function loadLoyaltyPrograms() {
  try {
    // get all programs
    loyaltyPrograms = (await apiRequest('loyalty.php'))?.data || [];

    // get stats (total members, etc.)
    const statsRes = await apiRequest('loyalty.php?stats=1');
    const stats = statsRes?.data?.current || {
      members: 0,
      points_redeemed: 0,
      rewards_given: 0,
      revenue_impact: 0
    };

    // ✅ Update the loyalty stats cards with CORRECT IDs
    const statTotalMembers = document.getElementById('statTotalMembers');
    const statPointsRedeemed = document.getElementById('statPointsRedeemed');
    const statRewardsGiven = document.getElementById('statRewardsGiven');
    const statRevenueImpact = document.getElementById('statRevenueImpact');

  if (statTotalMembers) statTotalMembers.textContent = stats.members ?? 0;
  setGrowthRateById('growthMembers', calcGrowthRate(stats.members, stats.prev_members));
  if (statPointsRedeemed) statPointsRedeemed.textContent = stats.points_redeemed ?? 0;
  setGrowthRateById('growthPoints', calcGrowthRate(stats.points_redeemed, stats.prev_points_redeemed));
  if (statRewardsGiven) statRewardsGiven.textContent = stats.rewards_given ?? 0;
  setGrowthRateById('growthRewards', calcGrowthRate(stats.rewards_given, stats.prev_rewards_given));
  if (statRevenueImpact) statRevenueImpact.textContent = `$${(stats.revenue_impact ?? 0).toLocaleString()}`;
  setGrowthRateById('growthRevenue', calcGrowthRate(stats.revenue_impact, stats.prev_revenue_impact));

    // ✅ Also update dashboard loyalty members if we're on dashboard
    const dashboardLoyaltyMembers = document.querySelector('.stat-card:nth-child(2) h3');
    if (dashboardLoyaltyMembers) {
      dashboardLoyaltyMembers.textContent = stats.members ?? 0;
    }

    // render loyalty program cards
    renderPrograms(stats);
    
    console.log('Loyalty stats loaded:', stats); // Debug log
    
  } catch (e) {
    console.error("loadLoyaltyPrograms error:", e);
    showNotification('Failed to load loyalty programs', 'error');
  }
}

// ✅ Also update the renderPrograms function to show total at top
function renderPrograms(stats = {}) {
  const container = document.querySelector('.loyalty-programs');
  if (!container) return console.error('Loyalty programs container not found');
  container.innerHTML = '';

  // ✅ Show total members prominently at the top


  if (!Array.isArray(loyaltyPrograms) || !loyaltyPrograms.length) {
    const noPrograms = document.createElement('p');
    noPrograms.style.cssText = 'color: white; text-align: center; padding: 40px; font-size: 16px;';
    noPrograms.textContent = 'No loyalty programs available. Create your first program!';
    container.appendChild(noPrograms);
    return;
  }

  loyaltyPrograms.forEach(p => {
    const card = document.createElement('div');
    card.className = `program-card ${p.tier || ''}`;
    card.style.cssText = `
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 20px;
      color: white;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    `;

    let benefits = [];
    if (typeof p.benefits === 'string') {
      benefits = p.benefits.split(',').map(b => b.trim()).filter(Boolean);
    } else if (Array.isArray(p.benefits)) {
      benefits = p.benefits;
    }

    card.innerHTML = `
      <div class="program-header" style="display: flex; align-items: center; margin-bottom: 16px;">
        <div class="program-icon" style="font-size: 32px; margin-right: 16px;">${getTierIcon(p.tier)}</div>
        <div class="program-info">
          <h3 style="margin: 0; font-size: 24px; font-weight: 600;">${escapeHtml(p.name || '—')}</h3>
          <p style="margin: 4px 0 0 0; opacity: 0.8; font-size: 16px;">${Number(p.members_count) || 0} members</p>
        </div>
      </div>
      <div class="program-details">
        <div class="points-info" style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 8px; margin-bottom: 16px;">
          <span style="font-size: 16px;">Points per ₱1 Spent: <strong style="color: #fbbf24;">${p.points_rate || 0}x</strong></span>
          <span style="font-size: 16px; margin-left: 16px;">Discount Rate: <strong style="color: #10b981;">${p.discount_rate ? (p.discount_rate + '%') : '0%'}</strong></span>
        </div>
        ${benefits.length > 0 ? `
          <div class="benefits-section">
            <h4 style="margin: 0 0 12px 0; font-size: 18px; color: #fbbf24;">Benefits:</h4>
            <ul class="benefits-list" style="list-style: none; padding: 0; margin: 0;">
              ${benefits.map(b => `<li style="padding: 4px 0; font-size: 14px;">• ${escapeHtml(b)}</li>`).join('')}
            </ul>
          </div>
        ` : ''}
      </div>
    `;

    // Add hover effect
    card.addEventListener('mouseenter', () => {
      card.style.transform = 'translateY(-4px)';
      card.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
    });
    
    card.addEventListener('mouseleave', () => {
      card.style.transform = 'translateY(0)';
      card.style.boxShadow = 'none';
    });

    container.appendChild(card);
  });
}

function updateLoyaltyStatCards(stats) {
  const cards = document.querySelectorAll('#loyalty .stat-card');
  if (cards[0]) {
    cards[0].querySelector('h3').textContent = stats.members ?? 0;
    setGrowthRate(cards[0], calcGrowthRate(stats.members, stats.prev_members));
  }
  if (cards[1]) {
    cards[1].querySelector('h3').textContent = stats.points_redeemed ?? 0;
    setGrowthRate(cards[1], calcGrowthRate(stats.points_redeemed, stats.prev_points_redeemed));
  }
  if (cards[2]) {
    cards[2].querySelector('h3').textContent = stats.rewards_given ?? 0;
    setGrowthRate(cards[2], calcGrowthRate(stats.rewards_given, stats.prev_rewards_given));
  }
  if (cards[3]) {
    cards[3].querySelector('h3').textContent = `$${stats.revenue_impact ?? 0}`;
    setGrowthRate(cards[3], calcGrowthRate(stats.revenue_impact, stats.prev_revenue_impact));
  }
}


function getTierIcon(tier) {
  switch ((tier || '').toLowerCase()) {
    case 'platinum': return '💎';
    case 'gold': return '🏆';
    case 'silver': return '🥈';
    case 'bronze': return '🥉';
    default: return '⭐';
  }
}

function showCreateProgramModal() {
  document.getElementById('createProgramModal')?.classList.add('active');
}

async function createProgram(e) {
  e?.preventDefault();

  const name = document.getElementById('programName')?.value.trim();
  const tier = document.getElementById('programTier')?.value.trim();
  const pointsRate = document.getElementById('programPointsRate')?.value.trim();
  const benefits = document.getElementById('programBenefits')?.value.trim();
  const membersCount = document.getElementById('programMembersCount')?.value.trim();
  const discountRate = document.getElementById('programDiscountRate')?.value.trim(); // <-- ADD THIS LINE

  if (!name || !tier || !pointsRate) {
    return showNotification('Please fill in all required fields', 'error');
  }

  const payload = {
    name,
    tier,
    points_rate: parseFloat(pointsRate),
    benefits,
    members_count: membersCount ? parseInt(membersCount, 10) : 0,
    discount_rate: discountRate ? parseFloat(discountRate) : 0.0 // <-- ADD THIS LINE
  };

  try {
    await apiRequest('loyalty.php', 'POST', payload);

    loyaltyPrograms = [];
    await loadLoyaltyPrograms();

    closeModal('createProgramModal');
    document.getElementById('createProgramForm')?.reset();
    showNotification('Loyalty program created successfully!');
  } catch (e) {
    console.error(e);
    showNotification('Failed to create program: ' + (e.message || ''), 'error');
  }
}


/* --------------------------
   Modals & listeners
---------------------------*/
function closeModal(id) {
  const m = document.getElementById(id);
  if (!m) return;
  m.classList.remove('active');

  if (id === 'createCampaignModal') {
    editingCampaignId = null;
    const title = m.querySelector('h3');
    if (title) title.textContent = 'Create New Campaign';
    document.getElementById('createCampaignForm')?.reset();
  } else if (id === 'viewGuestModal') {
    const form = document.getElementById('viewGuestForm');
    if (form) form.reset();
  } else if (id === 'addGuestModal') {
    document.getElementById('addGuestForm')?.reset();
  } else if (id === 'editComplaintModal') {
    editingComplaintId = null;
    document.getElementById('editComplaintForm')?.reset();
  } else if (id === 'createComplaintModal') {
     
      document.getElementById('createComplaintForm')?.reset();
  } else if (id === 'createProgramModal') {
    document.getElementById('createProgramForm')?.reset();
  }
}

function closeAllModals() {
  document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
  editingGuestId = null; editingCampaignId = null; editingComplaintId = null;
}

function attachStaticListeners() {
  detachListener('#addGuestForm', 'submit', addGuest);
  detachListener('#createCampaignForm', 'submit', createCampaign);
  detachListener('#createProgramForm', 'submit', createProgram);
  detachListener('#createComplaintForm', 'submit', createComplaint);
  detachListener('#editComplaintForm', 'submit', updateComplaint);

  document.querySelector('#addGuestForm')?.addEventListener('submit', addGuest);
  document.querySelector('#createCampaignForm')?.addEventListener('submit', createCampaign);
  document.querySelector('#createProgramForm')?.addEventListener('submit', createProgram);
  document.querySelector('#createComplaintForm')?.addEventListener('submit', createComplaint);
  document.querySelector('#editComplaintForm')?.addEventListener('submit', updateComplaint);

  window.addEventListener('click', e => {
    document.querySelectorAll('.modal').forEach(m => {
      if (m.classList.contains('active') && e.target === m) m.classList.remove('active');
    });
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeAllModals();
  });
}

function detachListener(selector, event, handler) {
  const el = document.querySelector(selector);
  if (el) el.removeEventListener(event, handler);
}

/* --------------------------
   Utilities
---------------------------*/
function escapeHtml(str) {
  if (typeof str !== 'string') return str;
  return str.replace(/[&<>"']/g, function (m) {
    return ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[m];
  });
}

/* --------------------------
   Expose to window
---------------------------*/
window.showSection = showSection;
window.loadAll = loadAll;

window.showAddGuestModal = showAddGuestModal;
window.addGuest = addGuest;
window.viewGuest = viewGuest;
window.deleteGuest = deleteGuest;
window.filterGuests = filterGuests;

window.showCreateCampaignModal = showCreateCampaignModal;
window.createCampaign = createCampaign;
window.editCampaign = editCampaign;
window.deleteCampaign = deleteCampaign;
window.viewCampaign = viewCampaign;


window.showFeedbackType = showFeedbackType;
window.replyToFeedback = replyToFeedback;
window.updateFeedbackStatus = updateFeedbackStatus;
window.deleteFeedback = deleteFeedback;

window.showCreateProgramModal = showCreateProgramModal;
window.createProgram = createProgram;

window.closeModal = closeModal;
window.closeAllModals = closeAllModals;

window.showCreateComplaintModal = showCreateComplaintModal;
window.createComplaint = createComplaint;
window.editComplaint = editComplaint;
window.updateComplaint = updateComplaint;
window.replyToComplaint = replyToComplaint;
window.deleteComplaint = deleteComplaint;

window.loadGuestOptions = loadGuestOptions;
window.showPurchaseHistory = showPurchaseHistory;