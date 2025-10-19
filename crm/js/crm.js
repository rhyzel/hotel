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

/* --------------------------
   Guest Complaint & Feedback Functions
---------------------------*/


// Generic modal close function
function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (!modal) return;

  // Hide with both display and class
  modal.classList.remove('active');
  modal.style.display = 'none';
  modal.setAttribute('aria-hidden', 'true');

  // Reset form if exists
  const form = modal.querySelector('form');
  if (form) {
    form.reset();
  }

  // Special handling for dynamically created modals
  if (['purchaseHistoryModal', 'pointsEarningModal'].includes(modalId)) {
    setTimeout(() => modal.remove(), 300); // Remove after animation
  }
}

let editingGuestId = null;
let editingCampaignId = null;
let editingComplaintId = null;

let guestChartInstance = null;
let loyaltyChartInstance = null;

/* --------------------------
   Boot
---------------------------*/
document.addEventListener('DOMContentLoaded', async () => {
  attachSearchListeners();
  attachFormHandlers();
  await loadAll();
});



/* --------------------------
   Search Functionality
---------------------------*/
function attachSearchListeners() {
  const searchInput = document.getElementById('guestSearch');
  if (searchInput) {
    // Add debounced search
    let searchTimeout;
    searchInput.addEventListener('input', (e) => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        filterGuests();
      }, 300); // Wait 300ms after user stops typing
    });

    // Clear search when X is clicked
    searchInput.addEventListener('search', () => {
      filterGuests();
    });
  }
}
function attachFormHandlers() {
  // Attach complaint form handler
  const complaintForm = document.getElementById('createComplaintForm');
  if (complaintForm) {
    // Clear the form when cancel is clicked
    const complaintCancelBtn = complaintForm.querySelector('button[type="button"]');
    if (complaintCancelBtn) {
      complaintCancelBtn.onclick = (e) => {
        e.preventDefault();
        complaintForm.reset();
        const modal = document.getElementById('createComplaintModal');
        if (modal) {
          modal.style.display = 'none';
          modal.classList.remove('active');
        }
        showSection('guests'); // Return to guests section
      };
    }

    complaintForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      // Validate required fields
      if (!complaintForm.checkValidity()) {
        showNotification('Please fill in all required fields', 'error');
        return;
      }
      
      const formData = {
        guest_id: document.getElementById('complaintGuestId').value,
        guest_name: document.getElementById('complaintGuestName').value,
        comment: document.getElementById('complaintComment').value,
        //type: document.getElementById('complaintType').value,//
        status: document.getElementById('complaintStatus').value || 'pending'
      };

      try {
        const submitBtn = complaintForm.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        
        const response = await apiRequest('complaints.php', 'POST', formData);
        
if (response.success) {
  showNotification('Complaint submitted successfully');
  complaintForm.reset();

  // Refresh complaints
  const complaintsRes = await apiRequest('complaints.php');
  complaints = complaintsRes?.data || [];

  // ✅ Close modal first
  closeModal('createComplaintModal');

  // ✅ Small delay to ensure modal animation/DOM update finishes
  setTimeout(() => {
    showSection('guests');
  }, 200);
}
 else {
          throw new Error(response.error || 'Failed to submit complaint');
        }
      } catch (err) {
        console.error('Submit complaint error:', err);
        showNotification(err.message || 'Failed to submit complaint', 'error');
      } finally {
        const submitBtn = complaintForm.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = false;
      }
    });
  }

  // Attach feedback form handler
  const feedbackForm = document.getElementById('createFeedbackForm');
  if (feedbackForm) {
    feedbackForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      // Validate all required fields
      if (!feedbackForm.checkValidity()) {
        showNotification('Please fill in all required fields', 'error');
        return;
      }

      // Get all form values
      const guestId = document.getElementById('feedbackGuestId').value;
      const guestName = document.getElementById('feedbackGuestName').value;
      const rating = document.getElementById('feedbackRating').value;
      const comment = document.getElementById('feedbackComment').value;

      // Additional validation
      if (!guestId || !guestName) {
        showNotification('Guest information is missing', 'error');
        return;
      }

      if (!rating) {
        showNotification('Please select a rating', 'error');
        return;
      }

      const formData = {
        guest_id: guestId,
        guest_name: guestName,
        rating: rating,
        comment: comment,
        type: 'review',
        date: new Date().toISOString().split('T')[0]
      };

      try {
        const submitBtn = feedbackForm.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        
        const response = await apiRequest('feedback.php', 'POST', formData);
        
        if (response.success) {
          showNotification('Feedback submitted successfully');
          
          // Update data in background
          const feedbackRes = await apiRequest('feedback.php?type=all');
          feedback = feedbackRes?.data || [];
          
          // Reset form
          feedbackForm.reset();
          
          // Close modal
          const modal = document.getElementById('createFeedbackModal');
          if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
          }
          
          // Return to guests section and ensure it's displayed
          showSection('guests');
          
          // Refresh guest list
          renderGuests();
        } else {
          throw new Error(response.error || 'Failed to submit feedback');
        }
      } catch (err) {
        console.error('Submit feedback error:', err);
        showNotification(err.message || 'Failed to submit feedback', 'error');
      } finally {
        const submitBtn = feedbackForm.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = false;
      }
    });
  }
}


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
      loyaltyStatsRes
    ] = await Promise.all([
      apiRequest('dashboard.php'),
      apiRequest('guests.php'),
      apiRequest('campaigns.php'),
      apiRequest('feedback.php?type=all'),
      apiRequest('complaints.php'),
      apiRequest('loyalty.php'),
      apiRequest('loyalty.php?stats=1')
    ]);

    guests = guestsRes?.data || [];
    campaigns = campaignsRes?.data || [];
    feedback = feedbackRes?.data || [];
    complaints = complaintsRes?.data?.items || [];
    // Update active complaints count
    const activeComplaintsH3 = document.querySelector('#stat-active-complaints h3');
    if (activeComplaintsH3) {
        activeComplaintsH3.textContent = complaintsRes?.data?.active_count || '0';
    }
    loyaltyPrograms = loyaltyRes?.data || [];

    // --- Fix: Always compute loyalty_members from guests ---
    let loyaltyMembers = 0;
    if (Array.isArray(guests)) {
      loyaltyMembers = guests.filter(g => g.loyalty_tier && g.loyalty_tier !== '').length;
    }

    // Compute avg rating from feedback data so dashboard matches feedback
    let avgRating = 0;
    if (Array.isArray(feedback) && feedback.length) {
      // feedback items may have 'rating' field (number or string)
      const ratings = feedback.map(f => Number(f.rating)).filter(r => !isNaN(r));
      if (ratings.length) {
        avgRating = ratings.reduce((a,b) => a + b, 0) / ratings.length;
        // round to 1 decimal place
        avgRating = Math.round(avgRating * 10) / 10;
      }
    }

    // Use loyaltyMembers and avgRating for dashboard stat
    const stats = {
      ...(dashRes?.data || {}),
      loyalty_members: loyaltyMembers,
      avg_rating: avgRating
    };

    updateStatCards(stats);
    initializeCharts(stats);

    renderGuests();
    renderCampaigns();
    renderFeedback();
    renderComplaints();
    renderPrograms();

    await loadGuestOptions();

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
      position: fixed; top: 20px; right: 20px; padding: 16px 24px;
      border-radius: 12px; color: white; font-weight: 500; z-index: 9999;
      opacity: 0; transition: all .3s ease;
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);`;
    document.body.appendChild(n);
  }
  n.textContent = message;
  n.style.background = 
    type === 'success' ? 'rgba(16, 185, 129, 0.85)' :
    type === 'error'   ? 'rgba(239, 68, 68, 0.85)' :
    type === 'warning' ? 'rgba(245, 158, 11, 0.85)' : 'rgba(107, 114, 128, 0.85)';
  n.style.opacity = '1';
  clearTimeout(n._hideTimeout);
  n._hideTimeout = setTimeout(() => (n.style.opacity = '0'), 3000);
}

/* --------------------------
   Form Handling Functions
---------------------------*/
// Form handling now uses closeModal() directly

// Attach form handlers

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
  }

  // Update active complaints count on every section change if the element exists
  const activeComplaintsH3 = document.querySelector('#stat-active-complaints h3');
  if (activeComplaintsH3) {
    apiRequest('complaints.php').then(response => {
      if (response?.success) {
        activeComplaintsH3.textContent = response.data?.active_count || '0';
      }
    }).catch(err => console.error('Error updating active complaints:', err));
  }
}

/* --------------------------
   Dashboard
---------------------------*/
async function loadDashboardData() {
  try {
    // Fetch dashboard basics and server stats
    const [dashRes, statsRes] = await Promise.all([
      apiRequest('dashboard.php'),
      apiRequest('stats.php')
    ]);

    // Also fetch feedback to compute avg rating consistently
    let avgRating = statsRes?.data?.avg_rating ?? null;
    try {
      const feedbackRes = await apiRequest('feedback.php?type=all');
      const fb = feedbackRes?.data || [];
      if (Array.isArray(fb) && fb.length) {
        const ratings = fb.map(f => Number(f.rating)).filter(r => !isNaN(r));
        if (ratings.length) {
          avgRating = Math.round((ratings.reduce((a,b) => a+b,0) / ratings.length) * 10) / 10;
        }
      }
    } catch (e) { /* ignore feedback fetch errors, keep server stat if present */ }

    const stats = {
      ...(dashRes?.data || {}),
      ...(statsRes?.data || {}),
      avg_rating: avgRating
    };
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
  try { 
    guestChartInstance?.destroy(); 
    loyaltyChartInstance?.destroy(); 
  } catch {}

  const gctx = document.getElementById('guestChart');
  if (gctx && typeof Chart !== 'undefined' && Array.isArray(stats.guest_trends)) {

    // Optional gradient background (can keep or remove)
    const gradient = gctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(255,255,255,0.4)');   // Light white (top)
    gradient.addColorStop(1, 'rgba(255,255,255,0.05)');  // Faint white (bottom)

    guestChartInstance = new Chart(gctx, {
      type: 'line',
      data: {
        labels: stats.guest_trends.map(t => t.month),
        datasets: [{
          label: 'Guests',
          data: stats.guest_trends.map(t => t.count),
          borderColor: '#ffffff', // white line
          backgroundColor: gradient, // white gradient fill
          borderWidth: 2,
          tension: 0.4,
          fill: true,
          pointBackgroundColor: '#ffffff', // white points
          pointBorderColor: '#ffffff',
          pointHoverBackgroundColor: '#ffffff',
          pointHoverBorderColor: '#60a5fa', // optional blue glow when hovered
          pointRadius: 4,
          pointHoverRadius: 6,
        }]
      },
      options: { 
        responsive: true, 
        plugins: { 
          legend: { display: false } 
        }, 
        scales: { 
          y: { 
            beginAtZero: true,
            grid: { color: 'rgba(255,255,255,0.1)' },
            ticks: { color: '#fff' } 
          },
          x: {
            ticks: { color: '#fff' },
            grid: { color: 'rgba(255,255,255,0.05)' }
          }
        } 
      }
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
async function loadComplaints() {
  try {
    const res = await apiRequest('complaints.php');
    if (res?.success) {
      complaints = res.data.items || [];
      // Update active complaints count in stats
      const activeComplaintsH3 = document.querySelector('#stat-active-complaints h3');
      if (activeComplaintsH3) {
        activeComplaintsH3.textContent = res.data.active_count || '0';
      }
      renderComplaints();
      showNotification('Complaints loaded successfully');
    }
  } catch (e) {
    console.error('Load complaints error:', e);
    showNotification('Failed to load complaints', 'error');
  }
}

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

function filterGuests() {
  const searchTerm = document.getElementById('guestSearch').value.toLowerCase().trim();
  
  if (!searchTerm) {
    renderGuestsList(guests);
    return;
  }

  const filteredGuests = guests.filter(guest => {
    const searchableFields = [
      `${guest.first_name} ${guest.last_name}`, // Full name
      guest.first_name,                         // First name only
      guest.last_name,                          // Last name only
      guest.email,                              // Email
      guest.first_phone,                        // First phone
      guest.second_phone,                       // Second phone
      guest.loyalty_tier,                       // Loyalty tier
      guest.status                              // Status
    ];

    // Search all fields and return true if any match
    return searchableFields.some(field => 
      (field || '').toLowerCase().includes(searchTerm)
    );
  });

  renderGuestsList(filteredGuests);
}

function renderGuests() {
  renderGuestsList(guests);
}

function renderGuestsList(guestsToRender) {
  const list = document.getElementById('guestsList');
  if (!list) return;
  list.innerHTML = '';

  if (!guestsToRender || guestsToRender.length === 0) {
    list.innerHTML = '<p style="color: white; text-align: center; padding: 40px;">No guests found.</p>';
    return;
  }

  guestsToRender.forEach(guest => {
  const avatarText = 'G';
    const guestId = guest.guest_id || guest.id;
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
          <div class="guest-avatar" style="background:#3b82f6;color:white;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:600;">G</div>
          <div>
            <span class="loyalty-badge ${guest.loyalty_tier || ''}" style="background:#fbbf24;color:#222;padding:2px 10px;border-radius:8px;font-size:12px;font-weight:600;">${(guest.loyalty_tier || 'unknown').toUpperCase()}</span>
          </div>
        </div>
      </div>
      <div class="guest-details" style="margin-bottom:10px;">
        <div class="guest-detail" style="color:#fff;font-size:14px;"><span>📧</span><span style="margin-left:6px;">${escapeHtml(guest.email || '—')}</span></div>
      </div>
      <div class="guest-actions"
        style="display:flex;flex-wrap:wrap;gap:7px;align-items:center;overflow-x:auto;padding:4px 0;">
        <button class="btn btn-secondary"
          onclick="viewGuest(${guestId})"
          style="padding:6px 14px;min-width:70px;border-radius:8px;font-size:13px;transition:.15s;background:rgba(0, 0, 0, 0.3);color:#fff;border:none;cursor:pointer;">View</button>
        <button class="btn btn-secondary"
          onclick="showPurchaseHistoryWithType(${guestId})"
          style="padding:6px 14px;min-width:110px;border-radius:8px;font-size:13px;transition:.15s;background:rgba(0, 0, 0, 0.3);color:#fff;border:none;cursor:pointer;">History</button>
        <button class="btn btn-secondary"
          onclick="addComplaintForGuest(${guestId}, '${escapeHtml(guest.first_name)} ${escapeHtml(guest.last_name)}')"
          style="padding:6px 14px;min-width:100px;border-radius:8px;font-size:13px;transition:.15s;background:rgba(0, 0, 0, 0.3);color:#fff;border:none;cursor:pointer;">Complaint</button>
        <button class="btn btn-secondary"
          onclick="addFeedbackForGuest(${guestId}, '${escapeHtml(guest.first_name)} ${escapeHtml(guest.last_name)}')"
          style="padding:6px 14px;min-width:100px;border-radius:8px;font-size:13px;transition:.15s;background:rgba(0, 0, 0, 0.3);color:#fff;border:none;cursor:pointer;">Feedback</button>
      </div>
    `;
    list.appendChild(card);
  });
}

// Helper to always show billing history
function showPurchaseHistoryWithType(guestId) {
  showPurchaseHistory(guestId, 'billing');
}


/* --------------------------
   Add Guest Modal & Functions
---------------------------*/

// Guest addition has been moved to the Reservation module

async function addGuest(event) {
  event.preventDefault();
  
  // Get form data
  const loyaltyTier = document.getElementById('guestLoyaltyTier')?.value || 'bronze';
  const autoLoyaltyTier = document.getElementById('guestAutoLoyaltyTier')?.checked ?? true;

  const guestData = {
    first_name: document.getElementById('guestFirstName')?.value.trim() || '',
    last_name: document.getElementById('guestLastName')?.value.trim() || '',
    email: document.getElementById('guestEmail')?.value.trim() || '',
    first_phone: document.getElementById('guestFirstPhone')?.value.trim() || '',
    second_phone: document.getElementById('guestSecondPhone')?.value.trim() || '',
    status: 'active',
    loyalty_tier: loyaltyTier,
    auto_loyalty_tier: autoLoyaltyTier
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
  document.getElementById('viewGuestStatus').value = guest.status || 'regular';
  
  // Update the status field style based on status
const statusField = document.getElementById('viewGuestStatus');
if (statusField) {
  const status = guest.status || 'regular';
  statusField.value = status;
  statusField.style.color = '#fff';
  statusField.style.fontWeight = status === 'vip' ? '600' : '400';
  }

  // Show modal with both display and class
  const modal = document.getElementById('viewGuestModal');
  if (modal) {
    modal.style.display = 'flex';
    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
  }

  // Focus on first input
  setTimeout(() => {
    const firstInput = document.getElementById('viewGuestFirstName');
    if (firstInput) firstInput.focus();
  }, 100);
}

// Guest deletion has been removed - guests are managed through the Reservation module

/* --------------------------
   Modal Management
---------------------------*/

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (!modal) return;
  
  // Hide the modal
  modal.classList.remove('active');
  modal.style.display = 'none';
  modal.setAttribute('aria-hidden', 'true');
  
  // Show guests section when closing complaint or feedback modals
  if (modalId === 'createComplaintModal' || modalId === 'createFeedbackModal') {
    // Show guests section
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    const guestsSection = document.getElementById('guests');
    if (guestsSection) {
      guestsSection.classList.add('active');
    }
    
    // Update menu highlighting
    document.querySelectorAll('.menu-item').forEach(item => {
      item.classList.remove('active');
      if (item.getAttribute('onclick')?.includes('guests')) {
        item.classList.add('active');
      }
    });
  }
  
  // Reset forms and states based on modal type
  switch(modalId) {
    case 'createComplaintModal':
      const complaintForm = document.getElementById('createComplaintForm');
      if (complaintForm) {
        complaintForm.reset();
        // Clear guest-specific fields
        document.getElementById('complaintGuestId').value = '';
        document.getElementById('complaintGuestName').value = '';
        document.getElementById('complaintComment').value = '';
      }
      break;
      
    case 'createFeedbackModal':
      const feedbackForm = document.getElementById('createFeedbackForm');
      if (feedbackForm) {
        feedbackForm.reset();
      }
      break;
      
    case 'viewGuestModal':
      const viewForm = document.getElementById('viewGuestForm');
      if (viewForm) {
        viewForm.reset();
      }
      break;
  }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
  if (event.target.classList.contains('modal') && event.target.classList.contains('active')) {
    const modalId = event.target.id;
    closeModal(modalId);
  }
});

/* --------------------------
   Guest Complaint & Feedback
---------------------------*/
function addComplaintForGuest(guestId, guestName) {
  // Set the selected guest in the complaint form
  const guestSelect = document.getElementById('complaintGuestId');
  if (guestSelect) {
    guestSelect.value = guestId;
  }

  // Pre-fill the guest name
  const guestNameInput = document.getElementById('complaintGuestName');
  if (guestNameInput) {
    guestNameInput.value = guestName;
  }

  // Show complaint modal without changing section
  const modal = document.getElementById('createComplaintModal');
  if (modal) {
    // First reset any existing form data
    const form = modal.querySelector('form');
    if (form) {
      form.reset();
      // Re-set the guest info after reset
      document.getElementById('complaintGuestId').value = guestId;
      document.getElementById('complaintGuestName').value = guestName;
    }
    // Use flex to center modal
    modal.style.display = 'flex';
    modal.classList.add('active');
  }

  // Focus on the comment field
  setTimeout(() => {
    const commentField = document.getElementById('complaintComment');
    if (commentField) commentField.focus();
  }, 100);
}

function addFeedbackForGuest(guestId, guestName) {
  // Show feedback form modal without changing section
  const modal = document.getElementById('createFeedbackModal');
  if (modal) {
    // Set the guest information
    document.getElementById('feedbackGuestId').value = guestId;
    document.getElementById('feedbackGuestName').value = guestName;
    
    modal.style.display = 'flex';
    modal.classList.add('active');
    
    // Focus on rating field
    setTimeout(() => {
      const ratingField = document.getElementById('feedbackRating');
      if (ratingField) ratingField.focus();
    }, 100);
  }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    const activeModal = document.querySelector('.modal.active');
    if (activeModal) {
      closeModal(activeModal.id);
    }
  }
});

// --- billing Order History Modal ---
async function showPurchaseHistory(guestId, type = 'billing') {
  try {
    let endpoint = `guests.php?guest_id=${guestId}&history_type=${type}`;
    const res = await apiRequest(endpoint);
    let orders = res.data;

    let html = '';
    if (type === 'billing') {
      if (!orders.length) {
        html = '<p style="color:white;text-align:center;padding:24px;">No billing history found for this guest.</p>';
      } else {
        html = `
            <div style="max-height:400px;overflow-y:auto;scrollbar-width:none;-ms-overflow-style:none;position:relative;">
              <style>
                .sticky-header {
                  position: sticky;
                  top: 0;
                  z-index: 10;
                  background: #1a1a1a;
                }
                .sticky-header th {
                  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
              </style>
              <table style="width:100%;color:white;border-collapse:collapse;table-layout:fixed;">
                <thead class="sticky-header">
                  <tr>
                    <th style="border-bottom:1px solid #fff;padding:12px 8px;font-size:13px;background:#1a1a1a;">Order ID</th>
                    <th style="border-bottom:1px solid #fff;padding:12px 8px;font-size:13px;background:#1a1a1a;">Order Type</th>
                    <th style="border-bottom:1px solid #fff;padding:12px 8px;font-size:13px;background:#1a1a1a;">Item Name</th>
                    <th style="border-bottom:1px solid #fff;padding:12px 8px;font-size:13px;background:#1a1a1a;">Total</th>
                    <th style="border-bottom:1px solid #fff;padding:12px 8px;font-size:13px;background:#1a1a1a;">Option</th>
                    <th style="border-bottom:1px solid #fff;padding:12px 8px;font-size:13px;background:#1a1a1a;">Method</th>
                    <th style="border-bottom:1px solid #fff;padding:12px 8px;font-size:13px;background:#1a1a1a;">Partial</th>
                    <th style="border-bottom:1px solid #fff;padding:12px 8px;font-size:13px;background:#1a1a1a;">Remain</th>
                    <th style="border-bottom:1px solid #fff;padding:12px 8px;font-size:13px;background:#1a1a1a;">Created</th>
                  </tr>
                </thead>
                <tbody>
                  ${orders.map(o => `
                    <tr>
                      <td style="padding:4px;font-size:13px;word-break:break-all;">${o.order_id ?? '-'}</td>
                      <td style="padding:4px;font-size:13px;word-break:break-all;">${escapeHtml(o.order_type || '-')}</td>
                      <td style="padding:4px;font-size:13px;word-break:break-word;">${escapeHtml(o.item_name || '-')}</td>
                      <td style="padding:4px;font-size:13px;">₱${isNaN(Number(o.total_amount)) ? '0.00' : Number(o.total_amount).toFixed(2)}</td>
                      <td style="padding:4px;font-size:13px;">${escapeHtml(o.payment_option || '-')}</td>
                      <td style="padding:4px;font-size:13px;">${escapeHtml(o.payment_method || '-')}</td>
                      <td style="padding:4px;font-size:13px;">₱${isNaN(Number(o.partial_payment)) ? '0.00' : Number(o.partial_payment).toFixed(2)}</td>
                      <td style="padding:4px;font-size:13px;">₱${isNaN(Number(o.remaining_amount)) ? '0.00' : Number(o.remaining_amount).toFixed(2)}</td>
                      <td style="padding:4px;font-size:13px;">${o.created_at ? new Date(o.created_at).toLocaleString() : '-'}</td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            </div>
        `;
      }
    } else if (type === 'all') {
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
  // Remove existing modal if present
  let modal = document.getElementById('purchaseHistoryModal');
  if (modal) {
    modal.remove();
  }

  // Create fresh modal
  modal = document.createElement('div');
  modal.id = 'purchaseHistoryModal';
  modal.className = 'modal';
  modal.style.display = 'none'; // Start hidden
  modal.innerHTML = `
    <div class="modal-content" style="max-width:700px;">
      <h3 id="purchaseHistoryTitle">Purchase History</h3>
      <div id="purchaseHistoryContent">${contentHtml}</div>
      <div class="modal-actions">
        <button type="button" onclick="closeModal('purchaseHistoryModal')" class="btn-secondary">Close</button>
      </div>
    </div>
  `;
  document.body.appendChild(modal);

  // Show with both display and class
  requestAnimationFrame(() => {
    modal.style.display = 'flex';
    modal.classList.add('active');
  });
}

/* Fix loyalty program modals */
function showPointsEarningModal(program) {
  // Remove any existing modal
  let existingModal = document.getElementById('pointsEarningModal');
  if (existingModal) {
    existingModal.remove();
  }

  // Create fresh modal
  const modal = document.createElement('div');
  modal.id = 'pointsEarningModal';
  modal.className = 'modal';
  modal.style.display = 'none'; // Start hidden
  modal.innerHTML = `
    <div class="modal-content" style="max-width:600px;">
      <h3 id="pointsEarningTitle">${escapeHtml(program.name)} — Points Earning</h3>
      <div id="pointsEarningContent"></div>
      <div class="modal-actions">
        <button type="button" onclick="closeModal('pointsEarningModal')" class="btn-secondary">Close</button>
      </div>
    </div>
  `;
  document.body.appendChild(modal);
  
  // Show with both display and class
  requestAnimationFrame(() => {
    modal.style.display = 'flex';
    modal.classList.add('active');
  });
}

async function showPointsEarningModal(program) {
  try {
    const res = await apiRequest(`loyalty.php?points_earning=1&tier=${program.tier}`);
    const guestsPoints = res?.data || [];
    const totalPoints = guestsPoints.reduce((sum, g) => sum + Number(g.total_points_earned || 0), 0);

    // Create modal if it doesn't exist, or clear existing content
    let modal = document.getElementById('pointsEarningModal');
    if (modal) {
      modal.remove(); // Remove existing modal
    }

    // Create fresh modal
    modal = document.createElement('div');
    modal.id = 'pointsEarningModal';
    modal.className = 'modal';
    modal.innerHTML = `
      <div class="modal-content" style="max-width:700px;">
        <h3>${escapeHtml(program.name)} — Points Earning</h3>
        
        <div style="margin:16px 0;padding:12px;background:rgba(255,255,255,0.1);border-radius:8px;">
          <div style="display:flex;justify-content:space-between;color:white;margin-bottom:8px;">
            <div><strong>Tier:</strong> ${escapeHtml(program.tier)}</div>
            <div><strong>Total Points:</strong> ${totalPoints.toLocaleString()}</div>
          </div>
          <div style="display:flex;justify-content:space-between;color:white;">
            <div><strong>Points Rate:</strong> ${program.points_rate}x per ₱1</div>
            <div><strong>Members:</strong> ${guestsPoints.length}</div>
          </div>
        </div>

        <div style="max-height:400px;overflow-y:auto;scrollbar-width:none;-ms-overflow-style:none;position:relative;">
          <style>
            .points-table-wrapper::-webkit-scrollbar {
              display: none;
            }
            .sticky-header {
              position: sticky;
              top: 0;
              background: #1a1a1a;
              z-index: 10;
            }
            .sticky-header th {
              box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
          </style>
          <div class="points-table-wrapper">
            <table style="width:100%;border-collapse:collapse;">
              <thead class="sticky-header">
                <tr>
                  <th style="text-align:left;padding:12px 8px;color:white;border-bottom:1px solid rgba(255,255,255,0.2);background:#1a1a1a;">Guest Name</th>
                  <th style="text-align:left;padding:12px 8px;color:white;border-bottom:1px solid rgba(255,255,255,0.2);background:#1a1a1a;">Email</th>
                  <th style="text-align:right;padding:12px 8px;color:white;border-bottom:1px solid rgba(255,255,255,0.2);background:#1a1a1a;">Points</th>
                </tr>
              </thead>
              <tbody>
                ${guestsPoints.map(g => `
                  <tr>
                    <td style="padding:8px;color:#fff;">${escapeHtml(g.guest_name || '')}</td>
                    <td style="padding:8px;color:#ccc;">${escapeHtml(g.email || '')}</td>
                    <td style="padding:8px;color:#fbbf24;text-align:right;">${Number(g.total_points_earned || 0).toLocaleString()}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        </div>
        
        <div class="modal-actions" style="margin-top:16px;">
          <button type="button" onclick="closeModal('pointsEarningModal')" class="btn-secondary">Close</button>
        </div>
      </div>
    `;

    document.body.appendChild(modal);

    // Show modal with animation
    requestAnimationFrame(() => {
      modal.style.display = 'flex';
      modal.classList.add('active');
    });

  } catch (err) {
    console.error('Error showing points modal:', err);
    showNotification('Failed to load points data', 'error');
  }
}

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
      loyaltyStatsRes
    ] = await Promise.all([
      apiRequest('dashboard.php'),
      apiRequest('guests.php'),
      apiRequest('campaigns.php'),
      apiRequest('feedback.php?type=all'),
      apiRequest('complaints.php'),
      apiRequest('loyalty.php'),
      apiRequest('loyalty.php?stats=1')
    ]);

    guests = guestsRes?.data || [];
    campaigns = campaignsRes?.data || [];
    feedback = feedbackRes?.data || [];
    complaints = complaintsRes?.data?.items || [];
    // Update active complaints count
    const activeComplaintsH3 = document.querySelector('#stat-active-complaints h3');
    if (activeComplaintsH3) {
        activeComplaintsH3.textContent = complaintsRes?.data?.active_count || '0';
    }
    loyaltyPrograms = loyaltyRes?.data || [];

    // --- Fix: Always compute loyalty_members from guests ---
    let loyaltyMembers = 0;
    if (Array.isArray(guests)) {
      loyaltyMembers = guests.filter(g => g.loyalty_tier && g.loyalty_tier !== '').length;
    }

    // Compute avg rating from feedback data so dashboard matches feedback
    let avgRating = 0;
    if (Array.isArray(feedback) && feedback.length) {
      // feedback items may have 'rating' field (number or string)
      const ratings = feedback.map(f => Number(f.rating)).filter(r => !isNaN(r));
      if (ratings.length) {
        avgRating = ratings.reduce((a,b) => a + b, 0) / ratings.length;
        // round to 1 decimal place
        avgRating = Math.round(avgRating * 10) / 10;
      }
    }

    // Use loyaltyMembers and avgRating for dashboard stat
    const stats = {
      ...(dashRes?.data || {}),
      loyalty_members: loyaltyMembers,
      avg_rating: avgRating
    };

    updateStatCards(stats);
    initializeCharts(stats);

    renderGuests();
    renderCampaigns();
    renderFeedback();
    renderComplaints();
    renderPrograms();

    await loadGuestOptions();

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
      position: fixed; top: 20px; right: 20px; padding: 16px 24px;
      border-radius: 12px; color: white; font-weight: 500; z-index: 9999;
      opacity: 0; transition: all .3s ease;
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);`;
    document.body.appendChild(n);
  }
  n.textContent = message;
  n.style.background = 
    type === 'success' ? 'rgba(16, 185, 129, 0.85)' :
    type === 'error'   ? 'rgba(239, 68, 68, 0.85)' :
    type === 'warning' ? 'rgba(245, 158, 11, 0.85)' : 'rgba(107, 114, 128, 0.85)';
  n.style.opacity = '1';
  clearTimeout(n._hideTimeout);
  n._hideTimeout = setTimeout(() => (n.style.opacity = '0'), 3000);
}

/* --------------------------
   Form Handling Functions
---------------------------*/
// Form handling now uses closeModal() directly

// Attach form handlers

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
  }

  // Update active complaints count on every section change if the element exists
  const activeComplaintsH3 = document.querySelector('#stat-active-complaints h3');
  if (activeComplaintsH3) {
    apiRequest('complaints.php').then(response => {
      if (response?.success) {
        activeComplaintsH3.textContent = response.data?.active_count || '0';
      }
    }).catch(err => console.error('Error updating active complaints:', err));
  }
}

/* --------------------------
   Dashboard
---------------------------*/
async function loadDashboardData() {
  try {
    // Fetch dashboard basics and server stats
    const [dashRes, statsRes] = await Promise.all([
      apiRequest('dashboard.php'),
      apiRequest('stats.php')
    ]);

    // Also fetch feedback to compute avg rating consistently
    let avgRating = statsRes?.data?.avg_rating ?? null;
    try {
      const feedbackRes = await apiRequest('feedback.php?type=all');
      const fb = feedbackRes?.data || [];
      if (Array.isArray(fb) && fb.length) {
        const ratings = fb.map(f => Number(f.rating)).filter(r => !isNaN(r));
        if (ratings.length) {
          avgRating = Math.round((ratings.reduce((a,b) => a+b,0) / ratings.length) * 10) / 10;
        }
      }
    } catch (e) { /* ignore feedback fetch errors, keep server stat if present */ }

    const stats = {
      ...(dashRes?.data || {}),
      ...(statsRes?.data || {}),
      avg_rating: avgRating
    };
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
  try { 
    guestChartInstance?.destroy(); 
    loyaltyChartInstance?.destroy(); 
  } catch {}

  const gctx = document.getElementById('guestChart');
  if (gctx && typeof Chart !== 'undefined' && Array.isArray(stats.guest_trends)) {

    // Optional gradient background (can keep or remove)
    const gradient = gctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(255,255,255,0.4)');   // Light white (top)
    gradient.addColorStop(1, 'rgba(255,255,255,0.05)');  // Faint white (bottom)

    guestChartInstance = new Chart(gctx, {
      type: 'line',
      data: {
        labels: stats.guest_trends.map(t => t.month),
        datasets: [{
          label: 'Guests',
          data: stats.guest_trends.map(t => t.count),
          borderColor: '#ffffff', // white line
          backgroundColor: gradient, // white gradient fill
          borderWidth: 2,
          tension: 0.4,
          fill: true,
          pointBackgroundColor: '#ffffff', // white points
          pointBorderColor: '#ffffff',
          pointHoverBackgroundColor: '#ffffff',
          pointHoverBorderColor: '#60a5fa', // optional blue glow when hovered
          pointRadius: 4,
          pointHoverRadius: 6,
        }]
      },
      options: { 
        responsive: true, 
        plugins: { 
          legend: { display: false } 
        }, 
        scales: { 
          y: { 
            beginAtZero: true,
            grid: { color: 'rgba(255,255,255,0.1)' },
            ticks: { color: '#fff' } 
          },
          x: {
            ticks: { color: '#fff' },
            grid: { color: 'rgba(255,255,255,0.05)' }
          }
        } 
      }
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
async function loadComplaints() {
  try {
    const res = await apiRequest('complaints.php');
    if (res?.success) {
      complaints = res.data.items || [];
      // Update active complaints count in stats
      const activeComplaintsH3 = document.querySelector('#stat-active-complaints h3');
      if (activeComplaintsH3) {
        activeComplaintsH3.textContent = res.data.active_count || '0';
      }
      renderComplaints();
      showNotification('Complaints loaded successfully');
    }
  } catch (e) {
    console.error('Load complaints error:', e);
    showNotification('Failed to load complaints', 'error');
  }
}

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

function filterGuests() {
  const searchTerm = document.getElementById('guestSearch').value.toLowerCase().trim();
  
  if (!searchTerm) {
    renderGuestsList(guests);
    return;
  }

  const filteredGuests = guests.filter(guest => {
    const searchableFields = [
      `${guest.first_name} ${guest.last_name}`, // Full name
      guest.first_name,                         // First name only
      guest.last_name,                          // Last name only
      guest.email,                              // Email
      guest.first_phone,                        // First phone
      guest.second_phone,                       // Second phone
      guest.loyalty_tier,                       // Loyalty tier
      guest.status                              // Status
    ];

    // Search all fields and return true if any match
    return searchableFields.some(field => 
      (field || '').toLowerCase().includes(searchTerm)
    );
  });

  renderGuestsList(filteredGuests);
}

function renderGuests() {
  renderGuestsList(guests);
}

function renderGuestsList(guestsToRender) {
  const list = document.getElementById('guestsList');
  if (!list) return;
  list.innerHTML = '';

  if (!guestsToRender || guestsToRender.length === 0) {
    list.innerHTML = '<p style="color: white; text-align: center; padding: 40px;">No guests found.</p>';
    return;
  }

  guestsToRender.forEach(guest => {
  const avatarText = 'G';
    const guestId = guest.guest_id || guest.id;
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
          <div class="guest-avatar" style="background:#3b82f6;color:white;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:600;">G</div>
          <div>
            <span class="loyalty-badge ${guest.loyalty_tier || ''}" style="background:#fbbf24;color:#222;padding:2px 10px;border-radius:8px;font-size:12px;font-weight:600;">${(guest.loyalty_tier || 'unknown').toUpperCase()}</span>
          </div>
        </div>
      </div>
      <div class="guest-details" style="margin-bottom:10px;">
        <div class="guest-detail" style="color:#fff;font-size:14px;"><span>📧</span><span style="margin-left:6px;">${escapeHtml(guest.email || '—')}</span></div>
      </div>
      <div class="guest-actions"
        style="display:flex;flex-wrap:wrap;gap:7px;align-items:center;overflow-x:auto;padding:4px 0;">
        <button class="btn btn-secondary"
          onclick="viewGuest(${guestId})"
          style="padding:6px 14px;min-width:70px;border-radius:8px;font-size:13px;transition:.15s;background:rgba(0, 0, 0, 0.3);color:#fff;border:none;cursor:pointer;">View</button>
        <button class="btn btn-secondary"
          onclick="showPurchaseHistoryWithType(${guestId})"
          style="padding:6px 14px;min-width:110px;border-radius:8px;font-size:13px;transition:.15s;background:rgba(0, 0, 0, 0.3);color:#fff;border:none;cursor:pointer;">History</button>
        <button class="btn btn-secondary"
          onclick="addComplaintForGuest(${guestId}, '${escapeHtml(guest.first_name)} ${escapeHtml(guest.last_name)}')"
          style="padding:6px 14px;min-width:100px;border-radius:8px;font-size:13px;transition:.15s;background:rgba(0, 0, 0, 0.3);color:#fff;border:none;cursor:pointer;">Complaint</button>
        <button class="btn btn-secondary"
          onclick="addFeedbackForGuest(${guestId}, '${escapeHtml(guest.first_name)} ${escapeHtml(guest.last_name)}')"
          style="padding:6px 14px;min-width:100px;border-radius:8px;font-size:13px;transition:.15s;background:rgba(0, 0, 0, 0.3);color:#fff;border:none;cursor:pointer;">Feedback</button>
      </div>
    `;
    list.appendChild(card);
  });
}

// Helper to always show billing history
function showPurchaseHistoryWithType(guestId) {
  showPurchaseHistory(guestId, 'billing');
}


/* --------------------------
   Add Guest Modal & Functions
---------------------------*/

// Guest addition has been moved to the Reservation module

async function addGuest(event) {
  event.preventDefault();
  
  // Get form data
  const loyaltyTier = document.getElementById('guestLoyaltyTier')?.value || 'bronze';
  const autoLoyaltyTier = document.getElementById('guestAutoLoyaltyTier')?.checked ?? true;

  const guestData = {
    first_name: document.getElementById('guestFirstName')?.value.trim() || '',
    last_name: document.getElementById('guestLastName')?.value.trim() || '',
    email: document.getElementById('guestEmail')?.value.trim() || '',
    first_phone: document.getElementById('guestFirstPhone')?.value.trim() || '',
    second_phone: document.getElementById('guestSecondPhone')?.value.trim() || '',
    status: 'active',
    loyalty_tier: loyaltyTier,
    auto_loyalty_tier: autoLoyaltyTier
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
  document.getElementById('viewGuestStatus').value = guest.status || 'regular';
  
  // Update the status field style based on status
const statusField = document.getElementById('viewGuestStatus');
if (statusField) {
  const status = guest.status || 'regular';
  statusField.value = status;
  statusField.style.color = '#fff';
  statusField.style.fontWeight = status === 'vip' ? '600' : '400';
  }

  // Show modal with both display and class
  const modal = document.getElementById('viewGuestModal');
  if (modal) {
    modal.style.display = 'flex';
    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
  }

  // Focus on first input
  setTimeout(() => {
    const firstInput = document.getElementById('viewGuestFirstName');
    if (firstInput) firstInput.focus();
  }, 100);
}

// Guest deletion has been removed - guests are managed through the Reservation module

/* --------------------------
   Modal Management
---------------------------*/

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (!modal) return;
  
  // Hide the modal
  modal.classList.remove('active');
  modal.style.display = 'none';
  modal.setAttribute('aria-hidden', 'true');
  
  // Show guests section when closing complaint or feedback modals
  if (modalId === 'createComplaintModal' || modalId === 'createFeedbackModal') {
    // Show guests section
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    const guestsSection = document.getElementById('guests');
    if (guestsSection) {
      guestsSection.classList.add('active');
    }
    
    // Update menu highlighting
    document.querySelectorAll('.menu-item').forEach(item => {
      item.classList.remove('active');
      if (item.getAttribute('onclick')?.includes('guests')) {
        item.classList.add('active');
      }
    });
  }
  
  // Reset forms and states based on modal type
  switch(modalId) {
    case 'createComplaintModal':
      const complaintForm = document.getElementById('createComplaintForm');
      if (complaintForm) {
        complaintForm.reset();
        // Clear guest-specific fields
        document.getElementById('complaintGuestId').value = '';
        document.getElementById('complaintGuestName').value = '';
        document.getElementById('complaintComment').value = '';
      }
      break;
      
    case 'createFeedbackModal':
      const feedbackForm = document.getElementById('createFeedbackForm');
      if (feedbackForm) {
        feedbackForm.reset();
      }
      break;
      
    case 'viewGuestModal':
      const viewForm = document.getElementById('viewGuestForm');
      if (viewForm) {
        viewForm.reset();
      }
      break;
  }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
  if (event.target.classList.contains('modal') && event.target.classList.contains('active')) {
    const modalId = event.target.id;
    closeModal(modalId);
  }
});

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
  }

  // Update active complaints count on every section change if the element exists
  const activeComplaintsH3 = document.querySelector('#stat-active-complaints h3');
  if (activeComplaintsH3) {
    apiRequest('complaints.php').then(response => {
      if (response?.success) {
        activeComplaintsH3.textContent = response.data?.active_count || '0';
      }
    }).catch(err => console.error('Error updating active complaints:', err));
  }
}

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
  const modal = document.getElementById('createCampaignModal');
  const form = document.getElementById('createCampaignForm');
  if (!modal || !form) return;

  form.reset();
  editingCampaignId = null;

  document.getElementById('createCampaignTitle').textContent = "Create New Campaign";
  document.getElementById('campaignSaveBtn').style.display = "inline-block";
  document.getElementById('campaignCancelBtn').textContent = "Cancel";
  document.getElementById('campaignExtraStats').style.display = "none";
  document.getElementById('campaignAdminFields').style.display = "block";

  toggleCampaignFields(true);

  modal.style.display = 'flex';
  modal.classList.add('active');
  modal.setAttribute('aria-hidden', 'false');

  populateCampaignAudienceTiers();
}

function populateCampaignAudienceTiers() {
  const audienceSelect = document.getElementById('campaignAudience');
  if (!audienceSelect) return;

  // Clear existing options except the first ones
  audienceSelect.innerHTML = `
    <option value="">Select Target Audience</option>
    <option value="all">All Guests</option>
  `;

  // Get unique tiers from loyalty programs
  const uniqueTiers = [...new Set(loyaltyPrograms.map(p => p.tier))].filter(Boolean);

  // Add each tier as an option
  uniqueTiers.forEach(tier => {
    const option = document.createElement('option');
    option.value = tier.toLowerCase();
    option.textContent = `${tier.charAt(0).toUpperCase() + tier.slice(1)} Members`;
    audienceSelect.appendChild(option);
  });

  // If no loyalty programs exist, add default tiers
  if (uniqueTiers.length === 0) {
    ['bronze', 'silver', 'gold', 'platinum'].forEach(tier => {
      const option = document.createElement('option');
      option.value = tier;
      option.textContent = `${tier.charAt(0).toUpperCase() + tier.slice(1)} Members`;
      audienceSelect.appendChild(option);
    });
  }
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
  e.preventDefault(); // Important: prevent form submission

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

  // Validation
  if (!data.name || !data.type || !data.target_audience || !data.message) {
    showNotification('Please fill in all required campaign fields', 'error');
    return;
  }

  try {
    // Disable submit button
    const submitBtn = document.getElementById('campaignSaveBtn');
    if (submitBtn) submitBtn.disabled = true;

    if (editingCampaignId) {
      data.id = editingCampaignId;
      await apiRequest('campaigns.php', 'PUT', data);
      showNotification('Campaign updated successfully!');
      editingCampaignId = null;
    } else {
      await apiRequest('campaigns.php', 'POST', data);
      showNotification('Campaign created successfully!');
    }

    // Close modal and reset form
    closeModal('createCampaignModal');
    document.getElementById('createCampaignForm')?.reset();
    
    // Switch to campaigns section and reload data
    setTimeout(() => {
      showSection('campaigns');
      loadCampaigns();
    }, 100);

  } catch (err) {
    console.error('Campaign save error:', err);
    showNotification('Failed to save campaign', 'error');
  } finally {
    // Re-enable submit button
    const submitBtn = document.getElementById('campaignSaveBtn');
    if (submitBtn) submitBtn.disabled = false;
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

async function deleteCampaign(id) {
  if (!confirm('Are you sure you want to delete this campaign?')) return;
  try {
    const submitBtn = event?.target;
    if (submitBtn) submitBtn.disabled = true;
    
    // Try DELETE with query params first
    try {
      await apiRequest(`campaigns.php?id=${encodeURIComponent(id)}`, 'DELETE');
    } catch (err) {
      // Fallback: Some servers don't support DELETE, use POST with _method
      await apiRequest('campaigns.php', 'POST', { 
        _method: 'DELETE', 
        id: id
      });
    }
    
    await loadCampaigns();
    showNotification('Campaign deleted successfully!');
  } catch (e) {
    console.error('Delete campaign error:', e);
    showNotification(e.message || 'Failed to delete campaign', 'error');
  } finally {
    const submitBtn = event?.target;
    if (submitBtn) submitBtn.disabled = false;
  }
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

    // Use guest initials for avatar (e.g. "Juan Dela Cruz" => "JDC")
    let avatar = 'G';

    // Add guest profile link if available. Prefer explicit guest_id, otherwise try to match by email or full name.
    let guestProfileHtml = '';
    let guestLinkId = item.guest_id ?? null;
    if (!guestLinkId && Array.isArray(guests) && guests.length) {
      const match = guests.find(g => {
        const fullName = ((g.first_name || '') + ' ' + (g.last_name || '')).trim();
        const nameMatch = item.guest_name && fullName && fullName.toLowerCase() === String(item.guest_name).trim().toLowerCase();
        const emailMatch = item.guest_email && g.email && g.email.toLowerCase() === String(item.guest_email).trim().toLowerCase();
        return nameMatch || emailMatch;
      });
      if (match) guestLinkId = match.guest_id ?? match.id ?? null;
    }
    if (guestLinkId) {
      guestProfileHtml = `<button onclick="viewGuest(${guestLinkId})" style="color:#3b82f6;text-decoration:none;font-size:13px;margin-left:8px;background:none;border:none;cursor:pointer;padding:0;">View Profile</button>`;
    }

    const card = document.createElement('div');
    card.className = 'feedback-card';
    card.innerHTML = `
      <div class="feedback-header">
        <div class="feedback-avatar">G</div>
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
        ${!item.reply ? `
          <button class="btn btn-secondary" onclick="replyToFeedback(${item.id})" 
            style="margin-right: 8px; padding: 8px 16px; background:rgba(0, 0, 0, 0.3); color: white; border: none; border-radius: 6px; cursor: pointer;">Reply</button>
        ` : ''}
        ${!item.status || item.status !== 'approved' ? `
          <button class="btn btn-success" onclick="updateFeedbackStatus(${item.id}, 'approved')" 
            style="margin-right: 8px; padding: 8px 16px; background: rgba(0, 0, 0, 0.3); color: white; border: none; border-radius: 6px; cursor: pointer;">Approve</button>
        ` : ''}
      </div>`;
    list.appendChild(card);
  });

  updateFeedbackStats();
}

function updateFeedbackStats() {
  // Only get actual feedback/reviews (ignore complaints)
  const feedbackOnly = feedback.filter(f => f.type?.toLowerCase() === 'review');
  
  const totalReviews = feedbackOnly.length;

  // Calculate average rating only from reviews with valid ratings
  const validRatings = feedbackOnly.filter(f => f.rating && !isNaN(f.rating));
  const avgRating = validRatings.length
    ? (validRatings.reduce((sum, f) => sum + Number(f.rating), 0) / validRatings.length).toFixed(1)
    : 0;

  // Calculate feedback resolution rate only from feedback responses
  const resolved = feedbackOnly.filter(f => f.reply || f.status === 'approved').length;
  const feedbackResolutionRate = totalReviews > 0 ? Math.round((resolved / totalReviews) * 100) : 0;

  // Update stats display
  if (document.getElementById('averageRating')) {
    document.getElementById('averageRating').textContent = avgRating;
  }
  if (document.getElementById('totalReviews')) {
    document.getElementById('totalReviews').textContent = totalReviews;
  }
  if (document.getElementById('statAverageRating')) {
    document.getElementById('statAverageRating').textContent = avgRating;
  }
  if (document.getElementById('statTotalReviews')) {
    document.getElementById('statTotalReviews').textContent = totalReviews;
  }
  if (document.getElementById('statResolutionRate')) {
    document.getElementById('statResolutionRate').textContent = feedbackResolutionRate + '%';
  }

  // Debug log
  console.log('Feedback Stats:', {
    total: totalReviews,
    resolved: resolved,
    resolutionRate: feedbackResolutionRate,
    avgRating: avgRating
  });
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
    showNotification('Reply sent successfully');
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

function showComplaintType(type) {
  // Handle only "complaint" or "all"
  currentComplaintType = ['complaint', 'all'].includes(type.toLowerCase()) 
    ? type.toLowerCase() 
    : 'all';

  // Update active tab button
  document.querySelectorAll('#complaints .tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('#complaints .tab-btn').forEach(b => {
    const on = b.getAttribute('onclick') || '';
    if (on.includes(`'${type}'`) || on.includes(`"${type}"`)) {
      b.classList.add('active');
    }
  });

  renderComplaints();
}


/* --------------------------
   Complaints - Updated for guest_id - FIXED
---------------------------*/
async function loadComplaints() {
  try {
    const response = await apiRequest('complaints.php');
    if (response?.success) {
      complaints = response.data?.items || [];
      renderComplaints();
      
      // Update active complaints count
      const activeComplaintsH3 = document.querySelector('#stat-active-complaints h3');
      if (activeComplaintsH3) {
        const activeCount = response.data?.active_count;
        // Only update if we have a valid count
        if (activeCount !== undefined && activeCount !== null) {
          activeComplaintsH3.textContent = activeCount.toString();
          // Store the count in localStorage for persistence
          localStorage.setItem('activeComplaintsCount', activeCount.toString());
        } else {
          // Try to get from localStorage if API doesn't return count
          const storedCount = localStorage.getItem('activeComplaintsCount');
          if (storedCount) {
            activeComplaintsH3.textContent = storedCount;
          }
        }
      }
    }
  } catch (e) { 
    console.error('Error loading complaints:', e);
    showNotification('Failed to load complaints', 'error');
    // Try to restore count from localStorage on error
    const activeComplaintsH3 = document.querySelector('#stat-active-complaints h3');
    const storedCount = localStorage.getItem('activeComplaintsCount');
    if (activeComplaintsH3 && storedCount) {
      activeComplaintsH3.textContent = storedCount;
    }
  }
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
    setIf('statResolutionRate', stats.resolution_rate, true);
  } catch (err) {
    console.error("Failed to load complaint stats:", err);
  }
}

function renderComplaints() {
  const list = document.getElementById('complaintsList');
  if (!list) return;
  list.innerHTML = '';

  // Make sure complaints is always an array
  if (!Array.isArray(complaints)) {
    console.error('Complaints is not an array:', complaints);
    complaints = [];
  }

  // Simplified filtering - show all complaints
  let filtered = complaints;

  if (!filtered.length) {
    list.innerHTML = '<p style="color: white; text-align: center; padding: 40px;">No complaints available.</p>';
    updateComplaintStats();
    return;
  }

  filtered.forEach(item => {
    const avatar = 'G';
    const isPending = (item.status || '').toLowerCase() === 'pending';

    const card = document.createElement('div');
    card.className = 'feedback-card';
    card.innerHTML = `
      <div class="feedback-header">
        <div class="feedback-avatar">G</div>
        <div class="feedback-info">
          <h3>${escapeHtml(item.guest_name || '—')}</h3>
          <div class="feedback-meta">
            <span class="complaint-type-badge" style="background:#fbbf24;color:#222;padding:2px 10px;border-radius:8px;font-size:12px;font-weight:600;margin-right:8px;">
              ${(item.type || 'Complaints').toUpperCase()}
            </span>
            <span class="status-badge ${item.status || ''}">
              ${(item.status || '').replace(/^./, m => m.toUpperCase())}
            </span>
            <span>${item.created_at ? new Date(item.created_at).toLocaleDateString() : ''}</span>
          </div>
        </div>
      </div>
      <div class="feedback-message">
        ${escapeHtml(item.comment || item.message || '')}
      </div>
      ${item.reply ? `
        <div class="complaint-reply" style="margin-top:12px;padding:12px;background:rgba(255,255,255,0.1);border-radius:8px;border-left:4px solid #10b981;">
          <strong style="color:#10b981;">Response:</strong>
          <p style="margin:8px 0 0 0;color:#fff;">${escapeHtml(item.reply)}</p>
        </div>
      ` : ''}
      <div class="feedback-actions">
        ${isPending ? `
          <button class="btn btn-secondary" onclick="replyToComplaint(${item.id})" 
            style="margin-right:8px;padding:8px 16px;background:rgba(0,0,0,0.3);color:white;border:none;border-radius:6px;cursor:pointer;">
            Respond
          </button>
        ` : ''}
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

// ✅ FIXED: Auto-set type to 'complaint' when creating from modal
async function createComplaint(e) {
  e?.preventDefault();

  const select = document.getElementById('complaintGuestId');
  const manualNameInput = document.getElementById('complaintGuestName');
  const typeSelect = document.getElementById('complaintType'); // May dropdown ba?
  
  let guest_id = select?.value || null;
  let guest_name = '';
  
  // ✅ FIX: Auto-set to 'complaint' if no type field exists
  let complaint_type = 'complaint'; // Default value
  
  // Only use dropdown if it exists
  if (typeSelect && typeSelect.value) {
    complaint_type = typeSelect.value.toLowerCase();
  }

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

  // Validation
  if (!guest_name) {
    return showNotification('Guest name is required (select a guest or type a name)', 'error');
  }
  if (!comment) {
    return showNotification('Comment is required', 'error');
  }

  // ✅ PAYLOAD: Always include type='complaint'
  const payload = { 
    guest_id: guest_id || null, 
    guest_name, 
    comment, 
    status: status.toLowerCase(), // ✅ Force lowercase
    type: complaint_type // ✅ Always set type
  };

  console.log('Creating complaint with payload:', payload); // Debug log

  try {
    const response = await apiRequest('complaints.php', 'POST', payload);
    
    if (response.success) {
      // ✅ Reload complaints with updated count
      const complaintsRes = await apiRequest('complaints.php');
      complaints = complaintsRes?.data?.items || [];
      
      // ✅ Update active complaints stat card
      const activeComplaintsH3 = document.querySelector('#stat-active-complaints h3');
      if (activeComplaintsH3) {
        activeComplaintsH3.textContent = complaintsRes?.data?.active_count || '0';
        // Store in localStorage for persistence
        localStorage.setItem('activeComplaintsCount', complaintsRes?.data?.active_count || '0');
      }
      
      // ✅ Re-render complaints list
      renderComplaints();
      
      // ✅ Close modal and reset form
      const complaintForm = document.getElementById('createComplaintForm');
      if (complaintForm) complaintForm.reset();
      closeModal('createComplaintModal');
      
      // ✅ Show success message
      showNotification('Complaint submitted successfully!');
      
      // ✅ Return to guests section after delay
      setTimeout(() => {
        showSection('guests');
      }, 200);
    } else {
      throw new Error(response.error || 'Failed to submit complaint');
    }
  } catch (err) {
    console.error('Failed adding complaint:', err);
    showNotification(err?.message || 'Failed to submit complaint', 'error');
  }
}

async function replyToComplaint(id) {
  const item = complaints.find(c => Number(c.id) === Number(id));
  if (!item) return;

  // If modal already exists, reuse it
  const existing = document.getElementById('replyComplaintModal');
  if (existing) {
    const ta = existing.querySelector('#replyComplaintText');
    if (ta) ta.focus();
    return;
  }

  const modalHtml = `
    <div class="modal" id="replyComplaintModal" role="dialog" aria-modal="true" style="display:flex;position:fixed;top:0;left:0;width:100%;height:100%;justify-content:center;align-items:center;z-index:1000;">
  <div class="modal-content" style="background:rgba(30,30,30,0.95);padding:24px;border-radius:12px;width:90%;max-width:560px;position:relative;">
        <h3 style="color:white;margin-bottom:12px;font-size:18px;">Respond to ${escapeHtml(item.guest_name || 'Guest')}'s Complaint</h3>
        <form id="replyComplaintForm">
          <textarea id="replyComplaintText" required
            style="width:100%;min-height:140px;background:rgba(255, 255, 255, 0.1);color:white;border:1px solid #4b5563;border-radius:6px;padding:12px;margin-bottom:16px;font-size:14px;"
            placeholder="Type your response here..."></textarea>

          <div style="display:flex;gap:12px;align-items:center;">
            <button type="button" id="cancelComplaintReplyBtn" style="flex:1;padding:10px 14px;background:#262626;color:#fff;border:none;border-radius:8px;cursor:pointer;">Cancel</button>
            <button type="submit" id="sendComplaintReplyBtn" style="flex:1;padding:12px 14px;background:linear-gradient(90deg,#7c3aed,#4f46e5);color:white;border:none;border-radius:8px;cursor:pointer;font-weight:700;box-shadow:0 8px 24px rgba(79,70,229,0.25);">Send Reply</button>
          </div>
        </form>
      </div>
    </div>`;

  document.body.insertAdjacentHTML('beforeend', modalHtml);

  const modal = document.getElementById('replyComplaintModal');
  const textarea = document.getElementById('replyComplaintText');
  const form = document.getElementById('replyComplaintForm');
  const cancelBtn = document.getElementById('cancelComplaintReplyBtn');
  const sendBtn = document.getElementById('sendComplaintReplyBtn');

  // Focus
  if (textarea) textarea.focus();

  // Cancel handler
  cancelBtn?.addEventListener('click', () => {
    modal?.remove();
  });

  // Submit handler
  form.onsubmit = async function (e) {
    e.preventDefault();
    const reply = (textarea?.value || '').trim();
    if (!reply) {
      showNotification('Please enter a response', 'error');
      textarea?.focus();
      return;
    }
    sendBtn.disabled = true;
    try {
      // ✅ Update to "resolved" status when reply is sent
      await apiRequest('complaints.php', 'PUT', { id, status: 'resolved', reply });
      
      // ✅ Reload complaints to get updated counts
      const response = await apiRequest('complaints.php');
      if (response?.success) {
        complaints = response.data?.items || [];
        
        // ✅ Update active complaints count in stat card
        const activeComplaintsH3 = document.querySelector('#stat-active-complaints h3');
        if (activeComplaintsH3) {
          activeComplaintsH3.textContent = response.data?.active_count || '0';
        }
        
        // ✅ Re-render complaints list with updated data
        renderComplaints();
      }
      
      showNotification('Response sent and complaint marked as resolved!');
      modal?.remove();
    } catch (err) {
      console.error('Failed to send complaint reply:', err);
      showNotification('Failed to send response', 'error');
      sendBtn.disabled = false;
    }
  };
}


function updateComplaintStats() {
  // Only count items with type='complaint'
  const complaintsOnly = complaints.filter(c => c.type?.toLowerCase() === 'complaint');
  
  // Calculate stats from filtered complaints
  const totalComplaints = complaintsOnly.length;
  const activeComplaints = complaintsOnly.filter(c => c.status?.toLowerCase() === 'pending').length;
  const resolved = complaintsOnly.filter(c => c.status?.toLowerCase() === 'resolved').length;
  
  // Calculate resolution rate only from complaints
  const resolutionRate = totalComplaints > 0 ? Math.round((resolved / totalComplaints) * 100) : 0;
  
  // Helper function to safely set stat card values
  const setIf = (id, val, isPercent = false) => {
    const el = document.getElementById(id);
    if (el) {
      if (val !== undefined && val !== null && val !== "") {
        el.textContent = isPercent ? `${val}%` : val;
      } else {
        el.textContent = isPercent ? '0%' : '0';
      }
    }
  };
  
  // Update complaint stats
  setIf('statActiveComplaints', activeComplaints);
  setIf('statTotalComplaints', totalComplaints); 
  setIf('statResolvedComplaints', resolved);
  setIf('statResolutionRate', resolutionRate, true);
  
  // Update active complaints counter in header
  const activeComplaintsH3 = document.querySelector('#stat-active-complaints h3');
  if (activeComplaintsH3) {
    activeComplaintsH3.textContent = activeComplaints;
  }

  // Store in localStorage for persistence
  localStorage.setItem('activeComplaintsCount', activeComplaints.toString());
  localStorage.setItem('complaintResolutionRate', resolutionRate.toString());
  
  // Debug log
  console.log('Complaint Stats:', {
    total: totalComplaints,
    active: activeComplaints, 
    resolved: resolved,
    rate: resolutionRate
  });
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
  if (statRevenueImpact) statRevenueImpact.textContent = `₱${(stats.revenue_impact ?? 0).toLocaleString()}`;
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

  // Build a map of guest counts per tier
  const tierCounts = {};
  guests.forEach(g => {
    const tier = (g.loyalty_tier || '').toLowerCase();
    if (!tier) return;
    tierCounts[tier] = (tierCounts[tier] || 0) + 1;
  });

  // Compute total members
  const totalMembers = Object.values(tierCounts).reduce((a, b) => a + b, 0);

  // Update stats card
  const statTotalMembers = document.getElementById('statTotalMembers');
  if (statTotalMembers) statTotalMembers.textContent = totalMembers;

  if (!Array.isArray(loyaltyPrograms) || !loyaltyPrograms.length) {
    const noPrograms = document.createElement('p');
    noPrograms.style.cssText = 'color: white; text-align: center; padding: 40px; font-size: 16px;';
    noPrograms.textContent = 'No loyalty programs available.';
    container.appendChild(noPrograms);
    return;
  }

  loyaltyPrograms.forEach(p => {
    const tier = (p.tier || '').toLowerCase();
    const membersCount = tierCounts[tier] || 0;

    const card = document.createElement('div');
    card.className = `program-card ${p.tier || ''}`;
    card.style.cssText = `
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 20px;
      color: #fff;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      ${membersCount === 0 ? 'opacity:0.5;filter:grayscale(1);' : ''}
      cursor:pointer;
    `;

    let benefits = [];
    if (typeof p.benefits === 'string') {
      benefits = p.benefits.split(/[.,]/).map(b => b.trim()).filter(Boolean);
    } else if (Array.isArray(p.benefits)) {
      benefits = p.benefits;
    }

    card.innerHTML = `
      <div class="program-header" style="display: flex; align-items: center; margin-bottom: 16px;">
        <div class="program-icon" style="font-size: 32px; margin-right: 16px;">${getTierIcon(p.tier)}</div>
        <div class="program-info">
          <h3 style="margin: 0; font-size: 24px; font-weight: 600; color:#fff;">${escapeHtml(p.name || '—')}</h3>
          <p style="margin: 4px 0 0 0; opacity: 0.8; font-size: 16px; color:#ccc;">${membersCount} members</p>
        </div>
      </div>
      <div class="program-details">
        <div class="points-info" style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 8px; margin-bottom: 16px; color:#fff;">
          <span style="font-size: 16px;">Points per ₱1 Spent: <strong style="color: #fbbf24;">${p.points_rate || 0}x</strong></span>
          <span style="font-size: 16px; margin-left: 16px;">Discount Rate: <strong style="color: #10b981;">${p.discount_rate ? (p.discount_rate + '%') : '0%'}</strong></span>
        </div>
        ${p.description ? `<div class="program-description" style="margin-bottom:10px;color:#fbbf24;font-size:15px;">${escapeHtml(p.description)}</div>` : ''}
        ${benefits.length > 0 ? `
          <div class="benefits-section">
            <h4 style="margin: 0 0 12px 0; font-size: 18px; color: #fbbf24;">Benefits:</h4>
            <ul class="benefits-list" style="list-style: none; padding: 0; margin: 0;">
              ${benefits.map(b => `<li style="padding: 4px 0; font-size: 14px; color:#eee;">• ${escapeHtml(b)}</li>`).join('')}
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
    card.addEventListener('click', () => showPointsEarningModal(p));

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
    cards[3].querySelector('h3').textContent = `₱${stats.revenue_impact ?? 0}`;
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

  // Optionally get guest_id if you have a field for it (for validation)
  const guestIdField = document.getElementById('programGuestId');
  const guest_id = guestIdField ? guestIdField.value.trim() : '';

  if (!name || !tier || !pointsRate) {
    return showNotification('Please fill in all required fields', 'error');
  }

  const payload = {
    name,
    tier,
    points_rate: parseFloat(pointsRate),
    benefits,
    members_count: membersCount ? parseInt(membersCount, 10) : 0,
    discount_rate: discountRate ? parseFloat(discountRate) : 0.0
  };
  if (guest_id) payload.guest_id = guest_id; // Pass guest_id if present

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
  const modal = document.getElementById(id);
  if (modal) {
   modal.classList.remove('active');
  modal.style.display = 'none';
  modal.setAttribute('aria-hidden', 'true');
  }

  // ✅ if complaint modal ang sinara, balik sa guests page
  if (id === 'createComplaintModal') {
    setTimeout(() => {
      showSection('guests');
    }, 200);
  }

  // ✅ if feedback modal, same behavior (optional)
  if (id === 'createFeedbackModal') {
    setTimeout(() => {
      showSection('guests');
    }, 200);
  }
}


function closeAllModals() {
  document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
  editingGuestId = null; editingCampaignId = null; editingComplaintId = null;
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


window.addGuest = addGuest;
window.viewGuest = viewGuest;

window.filterGuests = filterGuests;

window.showCreateCampaignModal = showCreateCampaignModal;
window.createCampaign = createCampaign;
window.editCampaign = editCampaign;
window.deleteCampaign = deleteCampaign;
window.viewCampaign = viewCampaign;


window.showFeedbackType = showFeedbackType;
window.replyToFeedback = replyToFeedback;
window.updateFeedbackStatus = updateFeedbackStatus;

window.showCreateProgramModal = showCreateProgramModal;
window.createProgram = createProgram;

window.closeModal = closeModal;
window.closeAllModals = closeAllModals;

window.showCreateComplaintModal = showCreateComplaintModal;
window.createComplaint = createComplaint;
window.addComplaintForGuest = addComplaintForGuest;

window.replyToComplaint = replyToComplaint;

window.loadGuestOptions = loadGuestOptions;
window.showPurchaseHistory = showPurchaseHistory;
