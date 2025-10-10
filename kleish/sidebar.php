<!-- sidebar.php -->
<div class="sidebar" id="sidebar" onmouseenter="expandSidebar()" onmouseleave="collapseSidebar()">
  <div class="logo-container">
    <img src="logo.png" alt="Kleish Collection Logo">
  </div>
  <a href="dashboard.php"><i class="fas fa-chart-pie"></i><span class="label"> Dashboard Overview</span></a>
  <a href="inventory.php"><i class="fas fa-boxes"></i><span class="label"> Inventory Management</span></a>
  <a href="pos.php"><i class="fas fa-cash-register"></i><span class="label"> Point of Sale (POS)</span></a>
  <a href="transactions.php"><i class="fas fa-receipt"></i><span class="label"> Transaction History</span></a>
  <a href="orders.php"><i class="fas fa-truck-loading"></i><span class="label"> Order Tracking</span></a>
  <a href="thriftdrop.php"><i class="fas fa-tshirt"></i><span class="label"> Thrift Drop Scheduling</span></a>
  <a href="customers.php"><i class="fas fa-users"></i><span class="label"> Customer Insights</span></a>
  <a href="marketing.php"><i class="fas fa-bullhorn"></i><span class="label"> Marketing Tools</span></a>
  <a href="staff.php"><i class="fas fa-user-cog"></i><span class="label"> Staff Management</span></a>
  <a href="analytics.php"><i class="fas fa-chart-line"></i><span class="label"> Reports and Analytics</span></a>
  <hr>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="label"> Logout</span></a>
</div>

<script>
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('main-content');
  const topbar = document.getElementById('topbar'); // Topbar must have this ID

  function collapseSidebar() {
    sidebar.classList.add('collapsed');
    if (mainContent) mainContent.classList.add('full');

    // When sidebar collapses, topbar should expand
    if (topbar) {
      topbar.classList.add('sidebar-expanded');
      topbar.classList.remove('sidebar-collapsed');
    }
  }

  function expandSidebar() {
    sidebar.classList.remove('collapsed');
    if (mainContent) mainContent.classList.remove('full');

    // When sidebar expands, topbar should collapse
    if (topbar) {
      topbar.classList.remove('sidebar-expanded');
      topbar.classList.add('sidebar-collapsed');
    }
  }

  // Initialize collapsed
  collapseSidebar();
</script>
