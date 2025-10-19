<!--
  FRIENDLY SYSTEM: All users (guests, admin, users) see the same UI and data.
-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Guest CRM</title>

  <!-- Your CSS (update path if needed) -->
  <link rel="stylesheet" href="./css/crm.css" />

  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Chart.js for charts used in crm.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <nav class="sidebar" role="navigation" aria-label="Main navigation">
      <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon" aria-hidden="true">📊</div>
          </a>
          <div class="logo-text">
            <h1>Guest CRM</h1>
            <p>Guest Relationship Management</p>
          </div>
        </div>
      </div>

      <div class="sidebar-menu" role="menu">
        <button class="menu-item active" onclick="showSection('dashboard')" role="menuitem">
          <span class="icon">🏠</span><span>Dashboard</span>
        </button>
        <button class="menu-item" onclick="showSection('guests')" role="menuitem">
          <span class="icon">👤</span><span>Guests</span>
        </button>
        <button class="menu-item" onclick="showSection('loyalty')" role="menuitem">
          <span class="icon">🏆</span><span>Loyalty</span>
        </button>
        <button class="menu-item" onclick="showSection('campaigns')" role="menuitem">
          <span class="icon">📧</span><span>Campaigns</span>
        </button>
        <button class="menu-item" onclick="showSection('feedback')" role="menuitem">
          <span class="icon">💬</span><span>Feedback</span>
        </button>
        <button class="menu-item" onclick="showSection('complaints')" role="menuitem">
          <span class="icon">⚠️</span><span>Complaints</span>
        </button>
      </div>     
      <a href="../homepage/index.php" class="menu-item" role="menuitem"><span>← Home</span></a>
    </nav>

    <main class="main-content" role="main">
      <!-- DASHBOARD -->
      <section id="dashboard" class="section active" aria-labelledby="dashboard-heading">
        <div class="section-header">
          <div class="header-text">
            <h1 id="dashboard-heading">Dashboard</h1>
            <p>Welcome back! Here's what's happening with your guests.</p>
          </div>
        </div>

        <div class="stats-grid" aria-hidden="false">
          <div class="stat-card" id="stat-total-guests">
            <div class="stat-header">
              <div class="stat-icon blue">👤</div>
            </div>
            <h3>0</h3>
            <p>Total Guests</p>
          </div>

          <div class="stat-card" id="stat-loyalty-members">
            <div class="stat-header">
              <div class="stat-icon purple">🏆</div>
            </div>
            <h3>0</h3>
            <p>Loyalty Members</p>
          </div>

          <div class="stat-card" id="stat-active-campaigns">
            <div class="stat-header">
              <div class="stat-icon green">📧</div>
            </div>
            <h3>0</h3>
            <p>Active Campaigns</p>
          </div>

          <div class="stat-card" id="stat-avg-rating">
            <div class="stat-header">
              <div class="stat-icon yellow">⭐</div>
            </div>
            <h3>0.0</h3>
            <p>Avg Rating</p>
          </div>
          <div class="stat-card" id="stat-resolved-complaints">
            <div class="stat-header">
              <div class="stat-icon green">✅</div>
            </div>
            <h3>0</h3>
            <p>Resolved Complaints</p>
          </div>
        </div>
        <div class="chart-grid">
          <div class="chart-card">
            <canvas id="guestChart"></canvas>
          </div>
          <div class="chart-card">
            <canvas id="loyaltyChart"></canvas>
          </div>
        </div>
      </section>

      <!-- GUESTS -->
      <section id="guests" class="section" aria-labelledby="guests-heading">
        <div class="section-header">
          <div class="header-text">
            <h1 id="guests-heading">Guest Management</h1>
            <p>Manage your guest relationships and preferences</p>
          </div>
                    <div class="section-actions">
          </div>
        </div>

        <div class="search-bar">
          <div class="search-input">
            <label for="guestSearch" class="sr-only">Search guests</label>
            <span class="search-icon" aria-hidden="true">🔍</span>
            <input type="text" id="guestSearch" placeholder="Search guests by name or email..." onkeyup="filterGuests()" />
          </div>
          <button class="btn btn-secondary" onclick="filterGuests()">🔍</button>
        </div>

        <div id="guestsList" class="guests-grid" aria-live="polite"></div>
      </section>

      <!-- LOYALTY -->
      <section id="loyalty" class="section" aria-labelledby="loyalty-heading">
        <div class="section-header">
          <div class="header-text">
            <h1 id="loyalty-heading">Loyalty Programs</h1>
            <p>Manage your guest loyalty tiers and rewards</p>
          </div>
        </div>

        <div class="stats-grid" id="loyalty-stats">
          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon blue">👥</div>
            </div>
            <h3 id="statTotalMembers">0</h3>
            <p>Total Members</p>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon purple">⭐</div>
            </div>
            <h3 id="statPointsRedeemed">0</h3>
            <p>Points Redeemed</p>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon green">🎁</div>
            </div>
            <h3 id="statRewardsGiven">0</h3>
            <p>Rewards Given</p>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon orange">📈</div>
            </div>
            <h3 id="statRevenueImpact">₱0</h3>
            <p>Revenue Impact</p>
          </div>
        </div>

        <div class="loyalty-programs" aria-live="polite"></div>
      </section>

      <!-- CAMPAIGNS -->
      <section id="campaigns" class="section" aria-labelledby="campaigns-heading">
        <div class="section-header">
          <div class="header-text">
            <h1 id="campaigns-heading">Marketing Campaigns</h1>
            <p>Create and manage guest communication campaigns</p>
          </div>
          <button class="btn btn-primary" onclick="showCreateCampaignModal()"><span>➕</span> Create Campaign</button>
        </div>

        <div class="stats-grid" id="campaign-stats">
          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon blue">📤</div>
            </div>
            <h3 id="statTotalSent">0</h3>
            <p>Total Sent</p>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon green">📬</div>
            </div>
            <h3 id="statOpened">0</h3>
            <p>Opened</p>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon purple">🎯</div>
            </div>
            <h3 id="statClicked">0</h3>
            <p>Clicked</p>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon yellow">📊</div>
            </div>
            <h3 id="statClickRate">0%</h3>
            <p>Click Rate</p>
          </div>
        </div>

        <div id="campaignsList" class="campaigns-list" aria-live="polite"></div>
      </section>

      <!-- FEEDBACK -->
      <section id="feedback" class="section" aria-labelledby="feedback-heading">
        <div class="section-header">
          <div class="header-text">
            <h1 id="feedback-heading">Guest Feedback</h1>
            <p>Monitor and respond to guest reviews</p>
          </div>
        </div>

        <!-- Feedback Stats -->
        <div class="stats-grid" id="feedback-stats">
          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon yellow">⭐</div>
            </div>
            <h3 id="statAverageRating">0</h3>
            <p>Average Rating</p>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon blue">💬</div>
            </div>
            <h3 id="statTotalReviews">0</h3>
            <p>Total Reviews</p>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon green">✅</div>
            </div>
            <h3 id="statResolutionRate">0%</h3>
            <p>Resolution Rate</p>
          </div>
        </div>

    <!--    <div class="feedback-tabs" id="feedbackTabs">
          <button onclick="showFeedbackType('all')" class="tab-btn active">
            All Feedback
          </button>
          <button onclick="showFeedbackType('review')" class="tab-btn">
            Reviews 
          </button>
        </div>-->

        <div id="feedbackList" class="feedback-list" aria-live="polite"></div>
      </section>

      <!-- COMPLAINTS -->
      <section id="complaints" class="section" aria-labelledby="complaints-heading">
        <div class="section-header">
          <div class="header-text">
            <h1 id="complaints-heading">Guest Complaints</h1>
            <p>Manage and resolve guest complaints.</p>
          </div>
        </div>

        <div class="stats-grid" id="complaints-stats">
          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon red">⚠️</div>
            </div>
            <h3 id="statActiveComplaints">0</h3>
            <p>Active Complaints</p>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon green">✅</div>
            </div>
            <h3 id="statResolutionRate">0%</h3>
            <p>Resolution Rate</p>
          </div>
        </div>

        <!--<div class="feedback-tabs">
          <button onclick="showComplaintType('all')" class="tab-btn active">
            All
          </button>
          <button onclick="showComplaintType('complaint')" class="tab-btn">
            Complaints
          </button>

        </div>-->

        <div id="complaintsList" class="feedback-list" aria-live="polite"></div>

      </section>
    </main>
  </div>

  <!-- MODALS -->



<!-- View Guest Modal -->
<div id="viewGuestModal" class="modal" aria-hidden="true">
  <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="viewGuestTitle">
    <h3 id="viewGuestTitle">View Guest Information</h3>
    <form id="viewGuestForm">
      <label for="viewGuestFirstName">First Name *</label>
      <input type="text" id="viewGuestFirstName" placeholder="Enter first name" readonly />

      <label for="viewGuestLastName">Last Name *</label>
      <input type="text" id="viewGuestLastName" placeholder="Enter last name" readonly />

      <label for="viewGuestEmail">Email Address *</label>
      <input type="email" id="viewGuestEmail" placeholder="Enter email address" readonly/>

      <label for="viewGuestFirstPhone">First Phone Number *</label>
      <input type="tel" id="viewGuestFirstPhone" placeholder="Enter primary phone number" readonly />

      <label for="viewGuestSecondPhone">Second Phone Number</label>
      <input type="tel" id="viewGuestSecondPhone" placeholder="Enter secondary phone number (optional)" readonly/>

      <label for="viewGuestStatus">Status</label>
      <select id="viewGuestStatus" disabled>
            <option value="regular">Regular</option>
            <option value="vip">VIP</option>
            <option value="banned">Banned</option>
      </select>


      <div class="modal-actions">
        <button type="button" onclick="closeModal('viewGuestModal')" class="btn-secondary">Close</button>
      </div>
    </form>
  </div>
</div>


  <!-- Create Loyalty Program Modal -->
  <div id="createProgramModal" class="modal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createProgramTitle">
      <h3 id="createProgramTitle">Create Loyalty Program</h3>
      <form id="createProgramForm" onsubmit="createProgram(event)">
        
        <label for="programName">Program Name</label>
        <input type="text" id="programName" placeholder="e.g., Gold Rewards Program" required />

        <label for="programTier">Loyalty Tier</label>
        <select id="programTier" required>
          <option value="">Select Tier</option>
          <!-- JS will populate options dynamically from loyalty tiers -->
        </select>

        <label for="programPointsRate">Points per ₱1 Spent</label>
        <input type="number" step="0.1" min="0.1" max="10" id="programPointsRate" placeholder="e.g., 1.5" required />

        <label for="programBenefits">Program Benefits</label>
        <textarea id="programBenefits" placeholder="Enter benefits separated by commas (e.g., Free shipping, 10% discount, Priority support)" rows="3" required></textarea>

        <label for="programMembersCount">Initial Members Count</label>
        <input type="number" min="0" id="programMembersCount" placeholder="0" value="0" />

        <label for="programDescription">Description (Optional)</label>
        <textarea id="programDescription" placeholder="Brief description of this loyalty program" rows="2"></textarea>

        <div class="modal-actions">
          <button type="button" onclick="closeModal('createProgramModal')">Cancel</button>
          <button type="submit" class="btn-primary">Create Program</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Create/Edit Campaign Modal -->
  <div id="createCampaignModal" class="modal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createCampaignTitle" style="overflow-y:auto;scrollbar-width:none;-ms-overflow-style:none;">
    <style>
      /* Hide scrollbar for Chrome/Safari/Opera */
      #createCampaignModal .modal-content::-webkit-scrollbar {
        display: none;
      }
    </style>
      <h3 id="createCampaignTitle">Create New Campaign</h3>
      <form id="createCampaignForm" onsubmit="createCampaign(event)">
        <label for="campaignName">Campaign Name</label>
        <input type="text" id="campaignName" placeholder="Enter campaign name" required />

        <label for="campaignDescription">Campaign Description</label>
        <textarea id="campaignDescription" placeholder="Brief description of the campaign (optional)" rows="2"></textarea>

        <label for="campaignType">Campaign Type</label>
        <select id="campaignType" required>
          <option value="">Select Campaign Type</option>
          <option value="email">Email</option>
          <option value="sms">SMS</option>
          <option value="both">Email & SMS</option>
        </select>

        <label for="campaignStatus">Campaign Status</label>
        <select id="campaignStatus" required>
          <option value="">Select Campaign Status</option>
          <option value="draft">Draft</option>
          <option value="scheduled">Scheduled</option>
          <option value="active">Active</option>
          <option value="completed">Completed</option>
        </select>

        <label for="campaignAudience">Target Audience</label>
        <select id="campaignAudience" required>
          <option value="">Select Target Audience</option>
          <option value="all">All Guests</option>
          <option value="bronze">Bronze Members</option>
          <option value="silver">Silver Members</option>
          <option value="gold">Gold Members</option>
          <option value="platinum">Platinum Members</option>
          <!-- JS will populate options dynamically from loyalty tiers -->
        </select>
        <!--
          If you want to allow sending a campaign to a specific guest, 
          you can add a guest selection dropdown here (populated by JS):
        -->
        <!--
        <label for="campaignGuestId">Send to Specific Guest (Optional)</label>
        <select id="campaignGuestId">
          <option value="">-- Select Guest --</option>
          <!-- JS will populate with guest list -->
        </select>
        -->

        <label for="campaignMessage">Campaign Message</label>
        <textarea id="campaignMessage" placeholder="Enter your campaign message..." rows="4" required></textarea>

        <label for="campaignSchedule">Schedule (Optional)</label>
        <input type="datetime-local" id="campaignSchedule" />

        <!-- Admin fields for campaign stats -->
        <div id="campaignAdminFields" style="display:none;">
          <label for="campaignSentCount">Sent Count</label>
          <input type="number" id="campaignSentCount" min="0" value="0" />

          <label for="campaignOpenRate">Open Rate (%)</label>
          <input type="number" id="campaignOpenRate" min="0" max="100" step="0.1" value="0" />

          <label for="campaignClickRate">Click Rate (%)</label>
          <input type="number" id="campaignClickRate" min="0" max="100" step="0.1" value="0" />
        </div>

        <div id="campaignExtraStats" style="display:none; margin-top:10px; color:white;">
          <p id="viewCampaignStatus"></p>
          <p id="viewCampaignStats"></p>
          <p id="viewCampaignCreated"></p>
        </div>

        <div class="modal-actions">
          <button type="button" onclick="closeModal('createCampaignModal')" id="campaignCancelBtn">Cancel</button>
          <button type="submit" class="btn-primary" id="campaignSaveBtn">Save Campaign</button>
        </div>
      </form>
    </div>
  </div>



<!-- Complaint Modal -->
<!-- Add Guest Complaint Modal -->
<div id="createComplaintModal" class="modal" aria-hidden="true">
  <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createComplaintTitle">
    <h3 id="createComplaintTitle">Add Guest Complaint</h3>

    <form id="createComplaintForm">
      <!-- Hidden guest ID -->
      <input type="hidden" id="complaintGuestId" name="guest_id" />

      <!-- Guest Name (readonly) -->
      <label for="complaintGuestName">Guest Name *</label>
      <input type="text" id="complaintGuestName" name="guest_name" placeholder="Guest name" readonly required />

      <!-- Complaint Comment -->
      <label for="complaintComment">Complaint *</label>
      <textarea id="complaintComment" name="comment" rows="3" placeholder="Enter complaint details" required></textarea>

      <!-- Complaint Type -->
 <label for="complaintType">Complaint Type *</label>
      <select id="complaintType" name="type" required>
        <option value="complaint">COMPLAINTS</option>
      </select>

      <!-- Complaint Status (hidden or default pending) -->
      <input type="hidden" id="complaintStatus" name="status" value="pending" />

      <!-- Modal actions -->
      <div class="modal-actions">
        <button type="button" onclick="closeModal('createComplaintModal')" class="btn-secondary">Cancel</button>
        <button type="submit" class="btn-primary">Submit</button>
      </div>
    </form>
  </div>
</div>
<!-- Feedback Modal -->
<div id="createFeedbackModal" class="modal" aria-hidden="true">
  <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createFeedbackTitle">
    <h3 id="createFeedbackTitle">Add Guest Feedback</h3>

    <form id="createFeedbackForm" onsubmit="submitFeedback(event)">
      <input type="hidden" id="feedbackGuestId">

      <label for="feedbackGuestName">Guest Name *</label>
      <input type="text" id="feedbackGuestName" placeholder="Guest name" readonly />

      <label for="feedbackRating">Rating (1–5) *</label>
      <input type="number" id="feedbackRating" min="1" max="5" placeholder="Enter rating (1–5)" required />

      <label for="feedbackComment">Feedback *</label>
      <textarea id="feedbackComment" rows="3" placeholder="Enter feedback" required></textarea>

      <div class="modal-actions">
        <button type="button" onclick="closeModal('createFeedbackModal')" class="btn-secondary">Cancel</button>
        <button type="submit" class="btn-primary">Submit</button>
      </div>
    </form>
  </div>
</div>





  <!-- Feedback Response Modal -->
  <div id="feedbackModal" class="modal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="feedbackModalTitle">
      <h3 id="feedbackModalTitle">Feedback Response</h3>
      <div id="feedbackDetails"></div>
      
      <label for="responseText">Your Response</label>
      <textarea id="responseText" placeholder="Type your response to this feedback..." rows="4"></textarea>
      
      <div class="modal-actions">
        <button type="button" class="btn-secondary" onclick="closeModal('feedbackModal')">Cancel</button>
        <button type="button" class="btn-primary" onclick="sendFeedbackResponse && sendFeedbackResponse()">Send Response</button>
      </div>
    </div>
  </div>

  <!-- Reply Modal -->
  <div id="replyModal" class="modal">
    <div class="modal-content">
      <h3>Reply to Feedback</h3>
      <form id="replyForm">
        <input type="hidden" id="replyFeedbackId" name="id">
        
        <label for="replyMessage">Your Reply</label>
        <textarea id="replyMessage" name="reply" placeholder="Type your reply to this feedback..." rows="4" required></textarea>
        
        <div class="modal-actions">
          <button type="button" onclick="closeModal('replyModal')">Cancel</button>
          <button type="submit" class="btn-primary">Send Reply</button>
        </div>
      </form>
    </div>
  </div>

  <!-- View Guest Modal -->
  <div id="viewGuestModal" class="modal" style="display: none;">
    <div class="modal-content">
      <h3>Guest Information</h3>
      <div id="viewGuestForm" class="guest-info-grid">
        <div class="info-row">
          <div class="info-field">
            <label>First Name</label>
            <input type="text" id="viewGuestFirstName" readonly />
          </div>
          <div class="info-field">
            <label>Last Name</label>
            <input type="text" id="viewGuestLastName" readonly />
          </div>
        </div>

        <div class="info-row">
          <div class="info-field">
            <label>First Phone</label>
            <input type="tel" id="viewGuestFirstPhone" readonly />
          </div>
          <div class="info-field">
            <label>Second Phone</label>
            <input type="tel" id="viewGuestSecondPhone" readonly />
          </div>
        </div>

        <div class="info-row">
          <div class="info-field full-width">
            <label>Email Address</label>
            <input type="email" id="viewGuestEmail" readonly />
          </div>
        </div>

        <div class="info-row">
          <div class="info-field full-width">
            <label>Status</label>
            <input type="text" id="viewGuestStatus" readonly />
          </div>
        </div>

        <div class="modal-actions">
          <button type="button" onclick="closeModal('viewGuestModal')">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Program Modal (if needed for future functionality) -->
  <div id="editProgramModal" class="modal" style="display: none;">
    <div class="modal-content">
      <h3>Edit Loyalty Program</h3>
      <form id="editProgramForm">
        <label for="editProgramName">Program Name</label>
        <input id="editProgramName" placeholder="Program Name" required>
        
        <label for="editProgramTier">Tier</label>
        <select id="editProgramTier" required>
          <option value="bronze">Bronze</option>
          <option value="silver">Silver</option>
          <option value="gold">Gold</option>
          <option value="platinum">Platinum</option>
        </select>
        
        <label for="editProgramPointsRate">Points Rate</label>
        <input id="editProgramPointsRate" type="number" step="0.1" placeholder="Points Rate" required>
        
        <label for="editProgramBenefits">Benefits</label>
        <textarea id="editProgramBenefits" placeholder="Benefits (comma separated)" rows="3"></textarea>
        
        <div class="modal-actions">
          <button type="button" onclick="closeModal('editProgramModal')">Cancel</button>
          <button type="submit" class="btn-primary">Update Program</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Link to crm.js and active_complaints.js -->
  <script src="./js/crm.js"></script>

  <!-- Notification container (created dynamically by JS but can be pre-defined) -->
  <div id="notification" style="display: none;"></div>

  <!-- Hidden storage for active complaints count -->
  <div id="active-complaints-storage" style="display: none;"></div>

  <!-- Screen reader only content -->
  <style>
    .sr-only { 
      position: absolute !important; 
      width: 1px; 
      height: 1px; 
      padding: 0; 
      margin: -1px; 
      overflow: hidden; 
      clip: rect(0,0,0,0); 
      white-space: nowrap; 
      border: 0; 
    }
  </style>
</body>
</html>