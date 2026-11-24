<?php
$page_title = 'Welcome to Ereteti';
include 'includes/header.php';
?>
<nav class="siderbar">
  <div class="logo-menu">
    <h2 class="logo-text">Veterinarian Dashboard</h2>
    <i class="bx bx-menu-toggle-btn"></i>
  </div>
<ul class="list">
    <li class="list-item">
        <a href="veterinarian-dashboard.html">
        <span class="link-name">Dashboard</span>
        </a>
        <a href="vaccine.html">
        <span class="link-name">Manage Vaccines</span>
        </a>
        <a href="schedule.html">
        <span class="link-name">Manage Schedule</span>
        </a>
        <a href="details.html">
          <span class="link-name">Details</span>
        </a>
    </li>
</ul>
</nav>
<?php include 'includes/footer.php'; ?>